<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class TokensAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $dataResult = $this->ts->getInstance()->tokenList();

        // channels
        $channelsResult = $this->ts->getInstance()->channelList();
        $channelsResult = $this->ts->getInstance()->getElement('data', $channelsResult);
        $channels = [];
        foreach ($channelsResult as $channel) {
            $channels[$channel['channel_name']] = $channel['cid'];
        }

        // groups
        $serverGroups = [];

        $serverGroupsResult = $this->ts->getInstance()->serverGroupList();
        $serverGroupsResult = $this->ts->getInstance()->getElement('data', $serverGroupsResult);

        foreach ($serverGroupsResult as $group) {
            $serverGroups[$group['name']] = $group['sgid'];
        }
        arsort($serverGroups);

        $channelGroups = [];
        $channelGroupsResult = $this->ts->getInstance()->channelGroupList();
        $channelGroupsResult = $this->ts->getInstance()->getElement('data', $channelGroupsResult);

        foreach ($channelGroupsResult as $group) {
            $channelGroups[$group['name']] = $group['cgid'];
        }
        arsort($channelGroups);

        // render GET
        $this->view->render($response, 'tokens.twig', [
            'title' => $this->translator->trans('tokens.title'),
            'data' => $this->ts->getInstance()->getElement('data', $dataResult),
            'tokentypes' => TSInstance::getTokenTypes(),
            'channels' => $channels,
            'serverGroups' => $serverGroups,
            'channelGroups' => $channelGroups,
            'sid' => $sid
        ]);
    }
}