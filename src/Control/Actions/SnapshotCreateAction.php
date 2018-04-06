<?php

use Carbon\Carbon;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Filesystem\Filesystem;

final class SnapshotCreateAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $snapshotCreateResult = $this->ts->getInstance()->serverSnapshotCreate();

        $fileSystem = new Filesystem();
        $name = Carbon::now()->getTimestamp();
        $path = FileHelper::SNAPSHOTS_PATH . DIRECTORY_SEPARATOR . $sid . DIRECTORY_SEPARATOR . $name;

        if ($fileSystem->exists($path)) {
            $this->flash->addMessage('error', $this->translator->trans('file.exists'));
        } else {
            $fileSystem->appendToFile($path, trim($snapshotCreateResult['data']));
            $this->flash->addMessage('success', $this->translator->trans('done'));
        }

        return $response->withRedirect('/snapshots/' . $sid);
    }
}