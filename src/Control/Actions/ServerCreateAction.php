<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ServerCreateAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $dataResult = $this->ts->getInstance()->serverCreate($body);
        $this->ts->checkCommandResult($dataResult);

        $this->flash->addMessage('success', $this->translator->trans('server_create.created.success', ['%token%' => $dataResult['data']['token']]));
        return $response->withRedirect('/servers');
    }
}