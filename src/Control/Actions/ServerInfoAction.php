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

        $infoResult = $this->ts->getInstance()->serverInfo();

        $uploadsResult = $this->ts->getInstance()->ftList();

        // render GET
        $this->view->render($response, 'server_info.twig', [
            'title' => $this->translator->trans('server_info.title') . ' ' . $sid,
            'info' => $this->ts->getInstance()->getElement('data', $infoResult),
            'uploads' => $this->ts->getInstance()->getElement('data', $uploadsResult),
            'sid' => $sid
        ]);
    }
}