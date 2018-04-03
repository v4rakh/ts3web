<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ClientSendAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $cldbid = $args['cldbid'];

        $body = $request->getParsedBody();

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $getResult = $this->ts->getInstance()->clientGetNameFromDbid($cldbid);
        $this->ts->checkCommandResult($getResult);

        $dataResult = $this->ts->getInstance()->messageAdd(
            $this->ts->getInstance()->getElement('data', $getResult)['cluid'],
            $body['subject'],
            $body['message']
        );
        $this->ts->checkCommandResult($dataResult);

        $this->flash->addMessage('success', $this->translator->trans('client_info.sent.success', ['%cldbid%' => $cldbid]));
        return $response->withRedirect('/clients/' . $sid . '/' . $cldbid);
    }
}