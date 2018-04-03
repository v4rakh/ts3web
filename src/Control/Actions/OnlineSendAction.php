<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class OnlineSendAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $clid = $args['clid'];

        $body = $request->getParsedBody();

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $dataResult = $this->ts->getInstance()->sendMessage(1, $clid, $body['message']);
        $this->ts->checkCommandResult($dataResult);

        $this->flash->addMessage('success', $this->translator->trans('done'));
        return $response->withRedirect('/online/' . $sid . '/' . $clid);
    }
}