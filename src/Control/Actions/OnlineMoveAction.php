<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class OnlineMoveAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $clid = $args['clid'];

        $body = $request->getParsedBody();

        $channel = $body['channel'];
        $channelPassword = null;
        if (array_key_exists('channel_password', $body)) $channelPassword = $body['channel_password'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        if (empty($channelPassword)) $dataResult = $this->ts->getInstance()->clientMove($clid, $channel);
        else $dataResult = $this->ts->getInstance()->clientMove($clid, $channel, $channelPassword);


        $this->flash->addMessage('success', $this->translator->trans('online.moved.success', ['%clid%' => $clid]));
        return $response->withRedirect('/online/' . $sid . '/' . $clid);
    }
}