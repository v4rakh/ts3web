<?php

use JeremyKendall\Slim\Auth\Authenticator;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Symfony\Component\Translation\Translator;

abstract class AbstractAction
{
    /**
     * @var Twig
     */
    protected $view;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Messages
     */
    protected $flash;

    /**
     * @var Authenticator
     */
    protected $auth;

    /**
     * @var ACL
     */
    protected $acl;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var \SlimSession\Helper
     */
    protected $session;

    /**
     * @var TSInstance
     */
    protected $ts;

    /**
     * AbstractAction constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->view = $container->get('view');
        $this->logger = $container->get('logger');
        $this->flash = $container->get('flash');
        $this->auth = $container->get('authenticator');
        $this->acl = $container->get('acl');
        $this->translator = $container->get('translator');

        $this->session = $container->get('session');
        $this->ts = $container->get('ts');
    }

    public abstract function __invoke(Request $request, Response $response, $args);
}