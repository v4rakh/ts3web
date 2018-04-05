<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class TokenDeleteAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $token = rawurldecode($args['token']);

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $tokenDeleteResult = $this->ts->getInstance()->tokenDelete($token);

        $this->flash->addMessage('success', $this->translator->trans('done'));

        return $response->withRedirect('/tokens/' . $sid);
    }
}