<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ServerGroupInfoAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $sgid = $args['sgid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->selectServer($sid, 'serverId');
        $clientsResult = $this->ts->getInstance()->serverGroupClientList($sgid, true);
        $permissionsResult = $this->ts->getInstance()->serverGroupPermList($sgid, true);

        $this->view->render($response, 'servergroup_info.twig', [
            'title' => $this->translator->trans('servergroup_info.title') . ' ' . $sgid,
            'clients' => $this->ts->getInstance()->getElement('data', $clientsResult),
            'permissions' => $this->ts->getInstance()->getElement('data', $permissionsResult),
            'sid' => $sid,
            'sgid' => $sgid
        ]);
    }
}