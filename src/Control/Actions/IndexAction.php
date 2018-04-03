<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class IndexAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        return $response->withRedirect(getenv('site_index'));
    }
}