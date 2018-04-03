<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ProfileCredentialsChangeAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $dataResult = $this->ts->getInstance()->clientSetServerQueryLogin($this->auth->getIdentity()['user']);
        $this->ts->checkCommandResult($dataResult);

        $this->flash->addMessage('success', $this->translator->trans('profile_credentials.changed.success', ['%password%' => $dataResult['data']['client_login_password']]));
        $this->auth->logout();
        return $response->withRedirect('/login');
    }
}