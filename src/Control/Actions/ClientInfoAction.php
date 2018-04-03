<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ClientInfoAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $cldbid = $args['cldbid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $detailsResult = $this->ts->getInstance()->clientDbInfo($cldbid);
        $this->ts->checkCommandResult($detailsResult);

        $serverGroupsResult = $this->ts->getInstance()->serverGroupsByClientID($cldbid);
        $this->ts->checkCommandResult($serverGroupsResult);

        $channelGroupsResult = $this->ts->getInstance()->channelGroupClientList(null, $cldbid, null);
        $this->ts->checkCommandResult($channelGroupsResult);

        $permissionsResult = $this->ts->getInstance()->clientPermList($cldbid, true);
        $this->ts->checkCommandResult($permissionsResult);

        // render GET
        $this->view->render($response, 'client_info.twig', [
            'title' => $this->translator->trans('client_info.title') . ' ' . $cldbid,
            'details' => $this->ts->getInstance()->getElement('data', $detailsResult),
            'serverGroups' => $this->ts->getInstance()->getElement('data', $serverGroupsResult),
            'channelGroups' => $this->ts->getInstance()->getElement('data', $channelGroupsResult),
            'permissions' => $this->ts->getInstance()->getElement('data', $permissionsResult),
            'sid' => $sid,
            'cldbid' => $cldbid,
        ]);
    }
}