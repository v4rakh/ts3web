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
        $this->ts->checkCommandResult($selectResult);

        $serverGroupsResult = $this->ts->getInstance()->serverGroupList();
        $this->ts->checkCommandResult($selectResult);

        $channelGroupsResult = $this->ts->getInstance()->channelGroupList();
        $this->ts->checkCommandResult($channelGroupsResult);

        // render GET
        $this->view->render($response, 'groups.twig', [
            'title' => $this->translator->trans('groups.title'),
            'serverGroups' => $this->ts->getInstance()->getElement('data', $serverGroupsResult),
            'channelGroups' => $this->ts->getInstance()->getElement('data', $channelGroupsResult),
            'sid' => $sid
        ]);
    }
}