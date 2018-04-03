<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class BansAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);

        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $banListResult = $this->ts->getInstance()->banList();
        $this->ts->checkCommandResult($banListResult);

        // render GET
        $this->view->render($response, 'bans.twig', [
            'title' => $this->translator->trans('bans.title'),
            'data' => $this->ts->getInstance()->getElement('data', $banListResult),
            'sid' => $sid
        ]);
    }
}