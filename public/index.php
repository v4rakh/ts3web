<?php

error_reporting(E_ALL);

/**
 * Step 1: Require the Slim Framework using Composer's autoloader
 *
 * If you are not using Composer, you need to load Slim Framework with your own
 * PSR-4 autoloader.
 */

use Carbon\Carbon;
use Slim\Http\Request;
use Slim\Http\Response;

// To help the built-in PHP dev server, check if the request was actually for
// something which should probably be served as a static file
if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

require __DIR__ . DIRECTORY_SEPARATOR . '../vendor/autoload.php';

session_start();

/**
 * Step 2: Bootstrap database, ACL, Twig, FlashMessages, Logger
 */
$app = new Slim\App(['settings' => ['slim_settings' => [
    'displayErrorDetails' => true,
    'determineRouteBeforeAppMiddleware' => true,
]]]);

// container
$container = $app->getContainer();

// environment
try {
    $env = BootstrapHelper::bootEnvironment();
} catch (Exception $e) {
    die('Error bootstrapping environment: ' . $e->getMessage());
}
$container['env'] = function() use ($env) {
    return $env;
};

// translation
$translator = BootstrapHelper::bootTranslator();
$container['translator'] = function () use ($translator) {
    return $translator;
};

// date
Carbon::setLocale(getenv('site_language'));
Carbon::setToStringFormat(getenv('site_date_format'));

// logger
try {
    $logger = BootstrapHelper::bootLogger();

    $container['logger'] = function () use ($logger) {
        return $logger;
    };
} catch (Exception $e) {
    die('Error bootstrapping logger: ' . $e->getMessage());
}

// teamspeak
$container['ts'] = function () use ($logger) {
    return new TSInstance($logger);
};

// auth
$container['authAdapter'] = function ($container) {
    $adapter = new TSAuthAdapter(getenv('teamspeak_default_host'), getenv('teamspeak_default_query_port'), $container['logger'], $container['ts']);
    return $adapter;
};
$container['acl'] = function () {
    return new ACL();
};
$container->register(new \JeremyKendall\Slim\Auth\ServiceProvider\SlimAuthProvider());
$app->add($app->getContainer()->get('slimAuthRedirectMiddleware'));

// session
$app->add(new \Slim\Middleware\Session([
    'name' => 'dummy_session',
    'autorefresh' => true,
    'lifetime' => '1 hour'
]));
$container['session'] = function () {
    return new \SlimSession\Helper;
};

// view
$container['flash'] = function () {
    return new Slim\Flash\Messages;
};
$container['view'] = function ($container) use ($app) {
    // theme
    $themeDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . getenv('theme');

    if (!empty(getenv('theme_cache')) && getenv('theme_cache') == 'true') {
        $themeCacheDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache';
    } else {
        $themeCacheDir = false;
    }

    $view = new \Slim\Views\Twig($themeDir, ['cache' => $themeCacheDir]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));
    $view->addExtension(new Twig_Extension_Debug());

    // file size
    $fileSizeFilter = new Twig_SimpleFilter('file', function($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    });
    $view->getEnvironment()->addFilter($fileSizeFilter);

    // time in seconds to human readable
    $timeInSecondsFilter = new Twig_SimpleFilter('timeInSeconds', function($seconds) use ($container) {
        return $container['ts']->getInstance()->convertSecondsToStrTime($seconds);
    });
    $view->getEnvironment()->addFilter($timeInSecondsFilter);

    // timestamp to carbon
    $timestampFilter = new Twig_SimpleFilter('timestamp', function($timestamp) {
        return Carbon::createFromTimestamp($timestamp);
    });
    $view->getEnvironment()->addFilter($timestampFilter);

    // dynamically apply filters
    $view->getEnvironment()->addExtension(new ApplyFilterExtension());

    // translation
    $view->addExtension(new \Symfony\Bridge\Twig\Extension\TranslationExtension($container['translator']));
    $view->getEnvironment()->getExtension('Twig_Extension_Core')->setDateFormat(getenv('site_date_format'));

    // env
    $view->getEnvironment()->addFunction(new Twig_SimpleFunction('getenv', function($value) {
        $res = getenv($value);
        return $res;
    }));

    // session exist
    $view->getEnvironment()->addFunction(new Twig_SimpleFunction('session_exists', function($key) use ($container) {
        return $container['session']->exists($key);
    }));

    // session get
    $view->getEnvironment()->addFunction(new Twig_SimpleFunction('session_get', function($key) use ($container) {
        return $container['session']->get($key);
    }));

    // flash
    $view['flash'] = $container['flash'];

    // currentUser, currentRole, ACL.isPermitted
    $view['currentUser'] = ($container['authenticator']->hasIdentity() ? $container['authenticator']->getIdentity() : NULL); // currentUser in twig
    $view['currentRole'] = (!empty($user) ? $role = $user->role : $role = ACL::ACL_DEFAULT_ROLE_GUEST);

    $view->getEnvironment()->addFunction(new Twig_SimpleFunction('isPermitted', function ($currentRole, $targetRole) use ($container) {
        return $container['acl']->isPermitted($currentRole, $targetRole);
    }));

    return $view;
};

// comment out for specific info
// error handling
$container['notFoundHandler'] = function ($container) {
    return function (Request $request, Response $response) use ($container) {
        return $response->withRedirect('404');
    };
};
$container['errorHandler'] = function ($container) {
    return function (Request $request, Response $response, $exception) use ($container) {

        // handle all teamspeak exceptions with a flash message
        if ($exception instanceof TSException) {
            $container->flash->addMessage('error', $exception->getMessage());

            $refererHeader = $request->getHeader('HTTP_REFERER');

            if ($refererHeader) {
                $referer = array_shift($refererHeader);
                return $response->withRedirect($referer);
            } else {
                return $container['view']->render($response, 'error.twig', [
                    'title'     => $container['translator']->trans('error.500.title'),
                    'content'   => $exception->getMessage()
                ]);
            }
        }
        // all others are 500
        else {
            $container['logger']->error($container['translator']->trans('log.internal.application.error'), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'previous' => $exception->getPrevious(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return $container['view']->render($response, 'error.twig', [
                'title'     => $container['translator']->trans('error.500.title'),
                'content'   => $exception->getMessage()
            ]);
        }
    };
};
$container['phpErrorHandler'] = function ($container) {
    return function (Request $request, Response $response) use ($container) {
        return $response->withRedirect('500');
    };
};

/**
 * Step 3: Define the Slim application routes
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . '../config/routes.php';

/**
 * Step 4: Run the Slim application
 */
try {
    $app->run();
} catch (Exception $e) {
    die($e->getMessage());
}
