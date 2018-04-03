<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class GroupAddAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $sgid = $args['sgid'];

        $body = $request->getParsedBody();
        $cldbid = $body['cldbid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $groupAddResult = $this->ts->getInstance()->serverGroupAddClient($sgid, $cldbid);
        $this->ts->checkCommandResult($groupAddResult);

        $this->flash->addMessage('success', $this->translator->trans('added'));

        return $response->withRedirect('/groups/' . $sid . '/' . $sgid);
    }
}