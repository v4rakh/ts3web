<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ChannelDeleteAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $cid = $args['cid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $channelDeleteResult = $this->ts->getInstance()->channelDelete($cid);
        $this->ts->checkCommandResult($channelDeleteResult);

        $this->flash->addMessage('success', $this->translator->trans('done'));

        return $response->withRedirect('/channels/' . $sid);
    }
}