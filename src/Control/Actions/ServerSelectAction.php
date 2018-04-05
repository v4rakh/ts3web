<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ServerSelectAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $this->session->set('sid', $sid);

        $this->flash->addMessage('success', $this->translator->trans('done'));
        return $response->withRedirect('/servers');
    }
}