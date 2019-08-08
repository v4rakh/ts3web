<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class BansAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);

        $this->ts->getInstance()->selectServer($sid, 'serverId');

        $banListResult = $this->ts->getInstance()->banList();

        $this->view->render($response, 'bans.twig', [
            'title' => $this->translator->trans('bans.title'),
            'data' => $this->ts->getInstance()->getElement('data', $banListResult),
            'sid' => $sid
        ]);
    }
}