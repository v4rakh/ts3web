<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ServerStartAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $dataResult = $this->ts->getInstance()->serverStart($sid);

        $this->flash->addMessage('success', $this->translator->trans('servers.started.success', ['%sid%' => $sid]));
        return $response->withRedirect('/servers');
    }
}