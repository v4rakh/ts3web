<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Filesystem\Filesystem;

final class SnapshotDeployAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $name = $args['name'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $fileSystem = new Filesystem();
        $path = FileHelper::SNAPSHOTS_PATH . DIRECTORY_SEPARATOR . $sid . DIRECTORY_SEPARATOR . $name;

        if (!$fileSystem->exists($path)) {
            $this->flash->addMessage('error', $this->translator->trans('file.notexists'));
        } else {
            try {
                $snapshotData = file_get_contents($path);
                $this->ts->getInstance()->serverSnapshotDeploy($snapshotData, true);
                $this->flash->addMessage('success', $this->translator->trans('done'));
            } catch (Exception $e) {
                $this->logger->error('Could not deploy ' . $path . '. Cause: ' . $e->getMessage());
                $this->flash->addMessage('error', $this->translator->trans('snapshots.error.deploy'));
            }
        }

        return $response->withRedirect('/snapshots/' . $sid);
    }
}