<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ChannelCreateAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $body = $request->getParsedBody();
        $inherit = $body['inherit'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        if (!$inherit) {
            unset($body['cpid']);
        }

        unset($body['inherit']);

        $channelCreateResult = $this->ts->getInstance()->channelCreate($body);

        $this->flash->addMessage('success', $this->translator->trans('done'));

        return $response->withRedirect('/channels/' . $sid);
    }
}