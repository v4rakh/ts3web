<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ServerInfoAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $infoResult = $this->ts->getInstance()->serverInfo();
        $this->ts->checkCommandResult($infoResult);

        $uploadsResult = $this->ts->getInstance()->ftList();
        $this->ts->checkCommandResult($uploadsResult);

        // render GET
        $this->view->render($response, 'server_info.twig', [
            'title' => $this->translator->trans('server_info.title') . ' ' . $sid,
            'info' => $this->ts->getInstance()->getElement('data', $infoResult),
            'uploads' => $this->ts->getInstance()->getElement('data', $uploadsResult),
            'sid' => $sid
        ]);
    }
}