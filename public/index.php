<?php

error_reporting(E_ALL);

/**
 * Step 1: Require the Slim Framework using Composer's autoloader
 *
 * If you are not using Composer, you need to load Slim Framework with your own
 * PSR-4 autoloader.
 */

use Carbon\Carbon;
use JeremyKendall\Slim\Auth\ServiceProvider\SlimAuthProvider;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Middleware\Session;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use SlimSession\Helper;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Twig\Extension\DebugExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

// To help the built-in PHP dev server, check if the request was actually for
// something which should probably be served as a static file
if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

require __DIR__ . DIRECTORY_SEPARATOR . '../vendor/autoload.php';

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
Carbon::setLocale(getenv(EnvConstants::SITE_LANGUAGE));
Carbon::setToStringFormat(getenv(EnvConstants::SITE_DATE_FORMAT));

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
    $adapter = new TSAuthAdapter(getenv(EnvConstants::TEAMSPEAK_HOST),
        getenv(EnvConstants::TEAMSPEAK_QUERY_PORT), $container['logger'], $container['ts']);
    return $adapter;
};
$container['acl'] = function () {
    return new ACL();
};
$container->register(new SlimAuthProvider());
$app->add($app->getContainer()->get('slimAuthRedirectMiddleware'));

// session
$app->add(new Session([
    'name' => 'dummy_session',
    'autorefresh' => true,
    'lifetime' => '1 hour'
]));
$container['session'] = function () {
    return new Helper;
};

// view
$container['flash'] = function () {
    return new Slim\Flash\Messages;
};
$container['view'] = function ($container) use ($app) {
    $themeDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . getenv(EnvConstants::THEME);

    if (!empty(getenv(EnvConstants::THEME_CACHE) && getenv(EnvConstants::THEME_CACHE) == 'true')) {
        $themeCacheDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache';
    } else {
        $themeCacheDir = false;
    }

    $view = new Twig($themeDir, ['cache' => $themeCacheDir]);
    $view->addExtension(new TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));
    $view->addExtension(new DebugExtension());

    // dynamically apply filters
    $view->getEnvironment()->addExtension(new ApplyFilterExtension());

    // encode url
    $encodeUrl = new TwigFilter('escape_url', function($str) {
        return urlencode($str);
    });
    $view->getEnvironment()->addFilter($encodeUrl);

    // translation
    $view->addExtension(new TranslationExtension($container['translator']));
    $view->getEnvironment()->getExtension('Twig_Extension_Core')->setDateFormat(getenv(EnvConstants::SITE_DATE_FORMAT));

    // env
    $view->getEnvironment()->addFunction(new TwigFunction('getenv', function($value) {
        $res = getenv($value);
        return $res;
    }));

    // constants
    $view->getEnvironment()->addFunction(new TwigFunction('getconstant', function($value) {
        return Constants::get($value);
    }));

    // session exist
    $view->getEnvironment()->addFunction(new TwigFunction('session_exists', function($key) use ($container) {
        return $container['session']->exists($key);
    }));

    // session get
    $view->getEnvironment()->addFunction(new TwigFunction('session_get', function($key) use ($container) {
        return $container['session']->get($key);
    }));

    // file size
    $fileSizeFilter = new TwigFilter('file', function($bytes, $decimals = 2) {
        return FileHelper::humanFileSize($bytes, $decimals);
    });
    $view->getEnvironment()->addFilter($fileSizeFilter);

    // ts specific: time in seconds to human readable
    $timeInSecondsFilter = new TwigFilter('timeInSeconds', function($seconds) use ($container) {
        return $container['ts']->getInstance()->convertSecondsToStrTime($seconds);
    });
    $view->getEnvironment()->addFilter($timeInSecondsFilter);

    $timeInMillisFilter = new TwigFilter('timeInMillis', function($millis) use ($container) {
        return $container['ts']->getInstance()->convertSecondsToStrTime(floor($millis/1000));
    });
    $view->getEnvironment()->addFilter($timeInMillisFilter);

    // ts specific: timestamp to carbon
    $timestampFilter = new TwigFilter('timestamp', function($timestamp) {
        return Carbon::createFromTimestamp($timestamp);
    });
    $view->getEnvironment()->addFilter($timestampFilter);

    // ts specific: token type
    $tokenTypeFilter = new TwigFilter('tokentype', function($type) {
        $tokenTypes = TSInstance::getTokenTypes();

        foreach ($tokenTypes as $name => $tokenType) {
            if ($type == $tokenType) return $name;
        }

        return $type;
    });
    $view->getEnvironment()->addFilter($tokenTypeFilter);

    // ts specific: group type
    $groupTypeFilter = new TwigFilter('permgrouptype', function($type) {
        $groupTypes = TSInstance::getPermGroupTypes();

        foreach ($groupTypes as $name => $groupType) {
            if ($type == $groupType) return $name;
        }

        return $type;
    });
    $view->getEnvironment()->addFilter($groupTypeFilter);

    // flash
    $view['flash'] = $container['flash'];

    // currentUser, currentRole, ACL.isPermitted
    $view['currentUser'] = ($container['authenticator']->hasIdentity() ? $container['authenticator']->getIdentity() : NULL); // currentUser in twig
    $view['currentRole'] = (!empty($user) ? $role = $user->role : $role = ACL::ACL_DEFAULT_ROLE_GUEST);

    $view->getEnvironment()->addFunction(new TwigFunction('isPermitted', function ($currentRole, $targetRole) use ($container) {
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

        // handle all TeamSpeak exceptions with a flash message
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
        } else {
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
