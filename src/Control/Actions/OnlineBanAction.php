<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class OnlineBanAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $clid = $args['clid'];

        $body = $request->getParsedBody();

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $dataResult = $this->ts->getInstance()->banClient($clid, (!empty($body['time']) ? $body['time'] : 0), $body['reason']);
        $this->ts->checkCommandResult($dataResult);

        $this->flash->addMessage('success', $this->translator->trans('online.banned.success', ['%clid%' => $clid]));
        return $response->withRedirect('/online/' . $sid . '/' . $clid);
    }
}