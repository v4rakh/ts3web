<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ChannelGroupDeleteAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $cgid = $args['cgid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $groupDeleteResult = $this->ts->getInstance()->channelGroupDelete($cgid);

        $this->flash->addMessage('success', $this->translator->trans('done'));

        return $response->withRedirect('/groups/' . $sid);
    }
}