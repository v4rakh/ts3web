<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ServerSendAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $body = $request->getParsedBody();

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $dataResult = $this->ts->getInstance()->sendMessage(3, $sid, $body['message']);

        $this->flash->addMessage('success', $this->translator->trans('done'));
        return $response->withRedirect('/servers/' . $sid);
    }
}