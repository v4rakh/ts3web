<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class InstanceEditAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $dataResult = $this->ts->getInstance()->instanceEdit($body);

        $this->flash->addMessage('success', $this->translator->trans('instance_edit.edited.success'));
        return $response->withRedirect('/instance');
    }
}