<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class PasswordDeleteAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $body = $request->getParsedBody();
        $password = $body['pw_clear'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $passwordDeleteResult = $this->ts->getInstance()->serverTempPasswordDel($password);

        $this->flash->addMessage('success', $this->translator->trans('done'));

        return $response->withRedirect('/passwords/' . $sid);
    }
}