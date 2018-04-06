<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ChannelGroupRenameAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $cgid = $args['cgid'];

        $body = $request->getParsedBody();
        $name = $body['name'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $groupRenameResult = $this->ts->getInstance()->channelGroupRename($cgid, $name);

        $this->flash->addMessage('success', $this->translator->trans('done'));

        return $response->withRedirect('/groups/' . $sid);
    }
}