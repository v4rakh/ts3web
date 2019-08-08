<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ServersAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $dataResult = $this->ts->getInstance()->serverList();

        $this->view->render($response, 'servers.twig', [
            'title' => $this->translator->trans('servers.title'),
            'data' => $this->ts->getInstance()->getElement('data', $dataResult)
        ]);
    }
}