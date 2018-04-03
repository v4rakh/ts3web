<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class AuthLoginAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();

        if ($request->isPost()) {

            // Form validation
            $validator = new Validator();
            $body = $validator->sanitize($body);
            $validator->filter_rules([
                'username'     => 'trim',
            ]);
            $validator->validation_rules([
                'username'     => 'required|min_len,1',
                'password'     => 'required|min_len,1',
            ]);
            if (!$validator->run($body)) {
                $validator->addErrorsToFlashMessage($this->flash);
                return $response->withRedirect('/login');
            }

            $username = $body['username'];
            $password = $body['password'];

            $result = $this->auth->authenticate($username, $password);

            if ($result->isValid()) {

                $this->flash->addMessage('success', $this->translator->trans('login.flash.success', ['%username%' => $username]));
                return $response->withRedirect('/servers');
            } else {
                $this->flash->addMessage('error', implode('. ', $result->getMessages()));
                return $response->withRedirect('/login');
            }
        }

        // render GET
        $this->view->render($response, 'login.twig', [
            'title'     => $this->translator->trans('login.title'),
        ]);
    }
}