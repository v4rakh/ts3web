<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class OnlinePokeAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $clid = $args['clid'];

        $body = $request->getParsedBody();

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $dataResult = $this->ts->getInstance()->clientPoke($clid, $body['message']);

        $this->flash->addMessage('success', $this->translator->trans('online.poked.success', ['%clid%' => $clid]));
        return $response->withRedirect('/online/' . $sid . '/' . $clid);
    }
}