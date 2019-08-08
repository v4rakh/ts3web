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
        $this->ts->getInstance()->selectServer($sid, 'serverId');

        $detailsResult = $this->ts->getInstance()->clientDbInfo($cldbid);

        $serverGroupsResult = $this->ts->getInstance()->serverGroupsByClientID($cldbid);

        $channelGroupsResult = $this->ts->getInstance()->channelGroupClientList(null, $cldbid, null);

        $permissionsResult = $this->ts->getInstance()->clientPermList($cldbid, true);

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