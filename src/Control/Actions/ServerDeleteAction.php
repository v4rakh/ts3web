<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ServerDeleteAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $dataResult = $this->ts->getInstance()->serverDelete($sid);

        // remove selected server from session
        if ($this->session->exists('sid') && $this->session->get('sid') == $sid) {
            $this->session->delete('sid');
            $this->session->delete('name');
        }

        $this->flash->addMessage('success', $this->translator->trans('done'));
        return $response->withRedirect('/servers');
    }
}