<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ServerStopAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $dataResult = $this->ts->getInstance()->serverStop($sid);
        $this->ts->checkCommandResult($dataResult);

        $this->flash->addMessage('success', $this->translator->trans('servers.stopped.success', ['%sid%' => $sid]));
        return $response->withRedirect('/servers');
    }
}