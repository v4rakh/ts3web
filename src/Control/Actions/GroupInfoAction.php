<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class GroupInfoAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $sgid = $args['sgid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $clientsResult = $this->ts->getInstance()->serverGroupClientList($sgid, true);
        $this->ts->checkCommandResult($clientsResult);

        $permissionsResult = $this->ts->getInstance()->serverGroupPermList($sgid, true);
        $this->ts->checkCommandResult($permissionsResult);

        // render GET
        $this->view->render($response, 'group_info.twig', [
            'title' => $this->translator->trans('group_info.title') . ' ' . $sgid,
            'clients' => $this->ts->getInstance()->getElement('data', $clientsResult),
            'permissions' => $this->ts->getInstance()->getElement('data', $permissionsResult),
            'sid' => $sid,
            'sgid' => $sgid
        ]);
    }
}