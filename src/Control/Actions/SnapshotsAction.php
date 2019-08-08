<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class SnapshotsAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->selectServer($sid, 'serverId');

        $snapshots = FileHelper::getFiles(FileHelper::SNAPSHOTS_PATH . DIRECTORY_SEPARATOR . $sid);

        $this->view->render($response, 'snapshots.twig', [
            'title' => $this->translator->trans('snapshots.title'),
            'data' => $snapshots,
            'sid' => $sid
        ]);
    }
}