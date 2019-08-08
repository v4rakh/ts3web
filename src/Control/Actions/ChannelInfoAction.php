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
        $this->ts->getInstance()->selectServer($sid, 'serverId');
        $channelResult = $this->ts->getInstance()->channelInfo($cid);
        $clientsResult = $this->ts->getInstance()->channelClientList($cid);

        $files = [];
        $files['data'] = $this->getAllFilesIn($sid, $cid, '/');

        $this->view->render($response, 'channel_info.twig', [
            'title' => $this->translator->trans('channel_info.title') . ' ' . $cid,
            'files' => $this->ts->getInstance()->getElement('data', $files),
            'channel' => $this->ts->getInstance()->getElement('data', $channelResult),
            'clients' => $this->ts->getInstance()->getElement('data', $clientsResult),
            'codecs' => TSInstance::getCodecs(),
            'codecsquality' => TSInstance::getCodecsQuality(),
            'sid' => $sid,
            'cid' => $cid
        ]);
    }

    private function getAllFilesIn($sid, $cid, $path, &$files = [])
    {
        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->selectServer($sid, 'serverId');
        $fileResult = $this->ts->getInstance()->ftGetFileList($cid, '', $path);
        $foundFiles = $fileResult['data'];

        if (!empty($foundFiles)) {
            foreach ($foundFiles as $file) {

                if ($file['type'] !== "0") { // a file
                    $file['path'] = $path;
                    $files[] = $file;
                }

                if ($file['type'] === "0") { // a directory

                    if ($path === '/') {
                        $newPath = $path . $file['name'];
                    } else {
                        $newPath = $path . '/' . $file['name'];
                    }

                    $file['path'] = $path;
                    $files[] = $file;

                    $files = $this->getAllFilesIn($sid, $cid, $newPath, $files);
                }
            }
        }

        return $files;
    }
}