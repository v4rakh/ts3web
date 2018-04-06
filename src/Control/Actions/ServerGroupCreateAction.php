<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ServerGroupCreateAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $body = $request->getParsedBody();
        $type = $body['type'];
        $name = $body['name'];
        $copy = $body['copy'];
        $template = $body['template'];

        $this->logger->debug('Body', $body);

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        if ($copy) $groupCreateResult = $this->ts->getInstance()->serverGroupAdd($name, $type);
        else $groupCreateResult = $this->ts->getInstance()->serverGroupCopy($template, 0, $name, $type);

        $this->flash->addMessage('success', $this->translator->trans('done'));

        return $response->withRedirect('/groups/' . $sid);
    }
}