<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class GroupsAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $serverGroupsResult = $this->ts->getInstance()->serverGroupList();
        $serverGroups = $this->ts->getInstance()->getElement('data', $serverGroupsResult);
        $serverGroupsTemplate = [];

        foreach ($serverGroups as $serverGroup) {
            $serverGroupsTemplate[$serverGroup['name']] = $serverGroup['sgid'];
        }

        $channelGroupsResult = $this->ts->getInstance()->channelGroupList();
        $channelGroups = $this->ts->getInstance()->getElement('data', $channelGroupsResult);
        $channelGroupsTemplate = [];

        foreach ($channelGroups as $channelGroup) {
            $channelGroupsTemplate[$channelGroup['name']] = $channelGroup['cgid'];
        }

        // render GET
        $this->view->render($response, 'groups.twig', [
            'title' => $this->translator->trans('groups.title'),
            'serverGroups' => $serverGroups,
            'serverGroupsTemplate' => $serverGroupsTemplate,
            'channelGroups' => $channelGroups,
            'channelGroupsTemplate' => $channelGroupsTemplate,
            'groupTypes' => TSInstance::getGroupTypes(),
            'sid' => $sid
        ]);
    }
}