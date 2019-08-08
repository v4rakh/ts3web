<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class PasswordsAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->selectServer($sid, 'serverId');

        $dataResult = $this->ts->getInstance()->serverTempPasswordList();
        $channelsResult = $this->ts->getInstance()->channelList();
        $channelsResult = $this->ts->getInstance()->getElement('data', $channelsResult);
        $channels = [];
        $channels['---'] = 0;

        foreach ($channelsResult as $channel) {
            $channels[$channel['channel_name']] = $channel['cid'];
        }

        $this->view->render($response, 'passwords.twig', [
            'title' => $this->translator->trans('passwords.title'),
            'data' => $this->ts->getInstance()->getElement('data', $dataResult),
            'channels' => $channels,
            'sid' => $sid
        ]);
    }
}