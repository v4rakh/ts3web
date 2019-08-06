<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ServerDeselectAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        // remove selected server from session
        $this->session->delete('sid');
        $this->session->delete('sport');
        $this->session->delete('sname');

        $this->flash->addMessage('success', $this->translator->trans('done'));
        return $response->withRedirect('/servers');
    }
}