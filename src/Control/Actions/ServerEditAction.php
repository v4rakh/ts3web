<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ServerEditAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $body = $request->getParsedBody();

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $dataResult = $this->ts->getInstance()->serverEdit($body);
        $this->ts->checkCommandResult($dataResult);

        $this->flash->addMessage('success', $this->translator->trans('server_edit.edited.success', ['%sid%' => $sid]));
        return $response->withRedirect('/servers/' . $sid);
    }
}