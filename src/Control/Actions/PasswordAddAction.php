<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class PasswordAddAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $body = $request->getParsedBody();
        $password = $body['password'];
        $duration = $body['duration'];
        $description = $body['description'];
        $channel = $body['channel'];
        $channelPassword = $body['channel_password'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->selectServer($sid, 'serverId');

        $this->ts->getInstance()->serverTempPasswordAdd(
            $password,
            $duration,
            $description,
            $channel,
            $channelPassword
        );

        $this->flash->addMessage('success', $this->translator->trans('done'));

        return $response->withRedirect('/passwords/' . $sid);
    }
}