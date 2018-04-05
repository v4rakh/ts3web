<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ChannelEditAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $cid = $args['cid'];

        $body = $request->getParsedBody();

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $channelResult = $this->ts->getInstance()->channelEdit($cid, $body);

        $this->flash->addMessage('success', $this->translator->trans('done'));

        return $response->withRedirect('/channels/' . $sid . '/' . $cid);
    }
}