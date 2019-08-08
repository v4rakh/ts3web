<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class LogsAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = null;
        if (array_key_exists('sid', $args)) $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);

        $appLog = [];
        if (empty($sid)) {
            $dataResult = $this->ts->getInstance()->logView(getenv(EnvConstants::TEAMSPEAK_LOG_LINES), 1, 1);
            $appLog = explode("\n", file_get_contents(BootstrapHelper::getLogFile()));
        } else {
            $this->ts->getInstance()->selectServer($sid, 'serverId');
            $dataResult = $this->ts->getInstance()->logView(getenv(EnvConstants::TEAMSPEAK_LOG_LINES), 1, 0);
        }

        $this->view->render($response, 'logs.twig', [
            'title' => empty($sid) ? $this->translator->trans('instance_logs.title') : $this->translator->trans('server_logs.title'),
            'log' => $this->ts->getInstance()->getElement('data', $dataResult),
            'appLog' => $appLog,
        ]);
    }
}