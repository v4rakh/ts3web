<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class OnlineAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $dataResult = $this->ts->getInstance()->clientList();
        $this->ts->checkCommandResult($dataResult);

        // render GET
        $this->view->render($response, 'online.twig', [
            'title' => $this->translator->trans('online.title'),
            'data' => $this->ts->getInstance()->getElement('data', $dataResult),
            'sid' => $sid
        ]);
    }
}