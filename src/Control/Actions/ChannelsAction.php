<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ChannelsAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $dataResult = $this->ts->getInstance()->channelList();
        $channels = $this->ts->getInstance()->getElement('data', $dataResult);
        $channelParents = [];

        $channelOrders = [];
        $channelOrders['---'] = 0;

        foreach ($channels as $channel) {
            $channelParents[$channel['channel_name']] = $channel['cid'];
            $channelOrders[$channel['channel_name']] = $channel['cid'];
        }

        // render GET
        $this->view->render($response, 'channels.twig', [
            'title' => $this->translator->trans('channels.title'),
            'channels' => $channels,
            'channelParents' => $channelParents,
            'channelOrders' => $channelOrders,
            'sid' => $sid
        ]);
    }
}