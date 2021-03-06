<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ServerGroupRemoveAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $sgid = $args['sgid'];
        $cldbid = $args['cldbid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->selectServer($sid, 'serverId');

        $this->ts->getInstance()->serverGroupDeleteClient($sgid, $cldbid);

        $this->flash->addMessage('success', $this->translator->trans('removed'));

        return $response->withRedirect('/servergroups/' . $sid . '/' . $sgid);
    }
}