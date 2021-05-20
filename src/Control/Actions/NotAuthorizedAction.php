<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class NotAuthorizedAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        return $this->view->render($response, 'error.twig', [
            'title' => $this->translator->trans('error.401.title'),
            'content' => $this->translator->trans('error.401.content')
        ]);
    }
}
