<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class OnlineInfoAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $clid = $args['clid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $dataResult = $this->ts->getInstance()->clientInfo($clid);
        $this->ts->checkCommandResult($dataResult);

        // render GET
        $this->view->render($response, 'online_info.twig', [
            'title' => $this->translator->trans('online_info.title') . ' ' . $clid,
            'data' => $this->ts->getInstance()->getElement('data', $dataResult),
            'sid' => $sid,
            'clid' => $clid,
        ]);
    }
}