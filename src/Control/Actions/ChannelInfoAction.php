<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ChannelInfoAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $cid = $args['cid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $channelResult = $this->ts->getInstance()->channelInfo($cid);
        $this->ts->checkCommandResult($channelResult);

        $clientsResult = $this->ts->getInstance()->channelClientList($cid);
        $this->ts->checkCommandResult($selectResult);

        $files = [];
        $files['data'] = $this->getAllFilesIn($sid, $cid, '/');

        // render GET
        $this->view->render($response, 'channel_info.twig', [
            'title' => $this->translator->trans('channel_info.title') . ' ' . $cid,
            'files' => $this->ts->getInstance()->getElement('data', $files),
            'channel' => $this->ts->getInstance()->getElement('data', $channelResult),
            'clients' => $this->ts->getInstance()->getElement('data', $clientsResult),
            'sid' => $sid,
            'cid' => $cid
        ]);
    }

    private function getAllFilesIn($sid, $cid, $path, &$files = [])
    {
        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        $fileResult = $this->ts->getInstance()->ftGetFileList($cid, '', $path);
        $this->ts->checkCommandResult($fileResult);

        $foundFiles = $fileResult['data'];

        if (!empty($foundFiles)) {
            foreach ($foundFiles as $file) {

                if ($file['type'] !== "0") {
                    $file['path'] = $path;
                    $files[] = $file;
                }

                if ($file['type'] === "0") {

                    if ($path === '/') {
                        $newPath = $path . $file['name'];
                    } else {
                        $newPath = $path . '/' . $file['name'];
                    }

                    $files = $this->getAllFilesIn($sid, $cid, $newPath, $files);
                }
            }
        }

        return $files;
    }
}