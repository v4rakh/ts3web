<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ClientsAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->selectServer($sid, 'serverId');

        $dataResult = $this->ts->getInstance()->clientDbList();

        $this->view->render($response, 'clients.twig', [
            'title' => $this->translator->trans('clients.title'),
            'data' => $this->ts->getInstance()->getElement('data', $dataResult),
            'sid' => $sid
        ]);
    }
}