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
        $this->ts->checkCommandResult($selectResult);

        $clientsResult = $this->ts->getInstance()->channelGroupClientList(null, null, $cgid);
        $this->ts->checkCommandResult($clientsResult);

        $permissionsResult = $this->ts->getInstance()->channelGroupPermList($cgid, true);
        $this->ts->checkCommandResult($permissionsResult);

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