<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ClientDeleteAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $cldbid = $args['cldbid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $clientDeleteResult = $this->ts->getInstance()->clientDbDelete($cldbid);

        $this->flash->addMessage('success', $this->translator->trans('done'));

        return $response->withRedirect('/clients/' . $sid);
    }
}