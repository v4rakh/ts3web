<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class AuthLogoutAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $this->flash->addMessage('success', $this->translator->trans('logout.flash.success'));
        
        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->logout();

        $this->auth->logout();
        return $response->withRedirect('/login');
    }
}