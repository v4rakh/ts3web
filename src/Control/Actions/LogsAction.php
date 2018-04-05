<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class LogsAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        if (empty($sid)) {
            $dataResult = $this->ts->getInstance()->logView(100, 1, 1);
        } else {
            $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
            $dataResult = $this->ts->getInstance()->logView(100, 1, 0);
        }

        // render GET
        $this->view->render($response, 'logs.twig', [
            'title' => $this->translator->trans('logs.title'),
            'data' => $this->ts->getInstance()->getElement('data', $dataResult),
        ]);
    }
}