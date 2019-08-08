<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class BanDeleteAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $banId = $args['banId'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->getInstance()->banDelete($banId);
        $this->flash->addMessage('success', $this->translator->trans('done'));

        return $response->withRedirect('/bans/' . $sid);
    }
}