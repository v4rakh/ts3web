<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ChannelGroupInfoAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $cgid = $args['cgid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $clientsResult = $this->ts->getInstance()->channelGroupClientList(null, null, $cgid);

        $permissionsResult = $this->ts->getInstance()->channelGroupPermList($cgid, true);

        // render GET
        $this->view->render($response, 'channelgroup_info.twig', [
            'title' => $this->translator->trans('channelgroup_info.title') . ' ' . $cgid,
            'clients' => $this->ts->getInstance()->getElement('data', $clientsResult),
            'permissions' => $this->ts->getInstance()->getElement('data', $permissionsResult),
            'sid' => $sid,
            'cgid' => $cgid
        ]);
    }
}