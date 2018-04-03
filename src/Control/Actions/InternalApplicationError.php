<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class InternalApplicationError extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        return $this->view->render($response, 'error.twig', [
            'title'     => $this->translator->trans('error.500.title'),
            'content'   => $this->translator->trans('error.500.content')
        ]);
    }
}