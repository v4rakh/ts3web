<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

final class SnapshotDeleteAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $name = $args['name'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->selectServer($sid, 'serverId');

        $fileSystem = new Filesystem();
        $path = FileHelper::SNAPSHOTS_PATH . DIRECTORY_SEPARATOR . $sid . DIRECTORY_SEPARATOR . $name;

        if (!$fileSystem->exists($path)) {
            $this->flash->addMessage('error', $this->translator->trans('file.notexists'));
        } else {
            try {
                $fileSystem->remove($path);
                $serverPath = FileHelper::SNAPSHOTS_PATH . DIRECTORY_SEPARATOR . $sid;

                if (count(FileHelper::getFiles($serverPath)) == 0) {
                    $fileSystem->remove($serverPath);
                }

                $this->flash->addMessage('success', $this->translator->trans('done'));
            } catch (IOException $e) {
                $this->logger->error('Could not delete ' . $path . '. Cause: ' . $e->getMessage());
                $this->flash->addMessage('error', $this->translator->trans('snapshots.error.delete'));
            }
        }

        return $response->withRedirect('/snapshots/' . $sid);
    }
}
