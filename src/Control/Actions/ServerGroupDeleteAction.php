<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ServerGroupDeleteAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $sgid = $args['sgid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->getInstance()->serverGroupDelete($sgid);
        $this->flash->addMessage('success', $this->translator->trans('done'));

        return $response->withRedirect('/groups/' . $sid);
    }
}