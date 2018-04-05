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
        $dataResult = $this->ts->getInstance()->serverInfo();

        $this->session->set('sid', $sid);
        $this->session->set('sname', $this->ts->getInstance()->getElement('data', $dataResult)['virtualserver_name']);

        $this->flash->addMessage('success', $this->translator->trans('done'));
        return $response->withRedirect('/servers');
    }
}