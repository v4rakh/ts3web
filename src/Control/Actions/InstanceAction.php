<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class InstanceAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $hostResult = $this->ts->getInstance()->hostInfo();
        $this->ts->checkCommandResult($hostResult);
        $instanceResult = $this->ts->getInstance()->instanceInfo();
        $this->ts->checkCommandResult($instanceResult);

        $data['data'] = array_merge($hostResult['data'], $instanceResult['data']);

        // render GET
        $this->view->render($response, 'instance.twig', [
            'title'     => $this->translator->trans('instance.title'),
            'data' => $this->ts->getInstance()->getElement('data', $data)
        ]);
    }
}