<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ChannelFilesDeleteAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $cid = $args['cid'];
        $file = null;

        if (array_key_exists('file', $request->getQueryParams())) {
            $file = urldecode($request->getQueryParams()['file']);
        }

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->selectServer($sid, 'serverId');

        $files = [$file];
        $this->ts->getInstance()->ftDeleteFile($cid, '', $files);
        $this->flash->addMessage('success', $this->translator->trans('channel_info.files.delete.success', ['%file%' => $file]));

        return $response->withRedirect('/channels/' . $sid . '/' . $cid);
    }
}