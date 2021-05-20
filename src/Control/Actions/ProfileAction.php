<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ProfileAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);

        if ($this->session->exists('sid')) {
            $this->ts->getInstance()->selectServer($this->session->get('sid'), 'serverId');
        }

        $whoisResult = $this->ts->getInstance()->whoAmI();

        $this->view->render($response, 'profile.twig', [
            'title' => $this->translator->trans('profile.title'),
            'whois' => $this->ts->getInstance()->getElement('data', $whoisResult),
        ]);
    }
}
