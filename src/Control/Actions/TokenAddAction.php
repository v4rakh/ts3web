<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class TokenAddAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $body = $request->getParsedBody();
        $type = $body['tokentype'];
        $serverGroup = $body['serverGroup'];
        $channelGroup = $body['channelGroup'];
        $channel = $body['channel'];
        $description = $body['description'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->getInstance()->tokenAdd(
            $type,
            ($type == ts3admin::TokenServerGroup ? $serverGroup : $channelGroup),
            $channel,
            $description
        );

        $this->flash->addMessage('success', $this->translator->trans('added'));

        return $response->withRedirect('/tokens/' . $sid);
    }
}