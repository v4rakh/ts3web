<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ChannelSendAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $cid = $args['cid'];
        $body = $request->getParsedBody();

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $dataResult = $this->ts->getInstance()->sendMessage(2, $cid, $body['message']);
        $this->ts->checkCommandResult($dataResult);

        $this->flash->addMessage('success', $this->translator->trans('done'));
        return $response->withRedirect('/channels/' . $sid . '/' . $cid);
    }
}