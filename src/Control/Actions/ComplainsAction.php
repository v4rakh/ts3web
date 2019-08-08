<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ComplainsAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->selectServer($sid, 'serverId');
        $dataResult = $this->ts->getInstance()->complainList();

        $this->view->render($response, 'complains.twig', [
            'title' => $this->translator->trans('complains.title'),
            'data' => $this->ts->getInstance()->getElement('data', $dataResult),
            'sid' => $sid
        ]);
    }
}