<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class OnlineAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $this->ts->getInstance()->selectServer($sid, 'serverId');
        $dataResult = $this->ts->getInstance()->clientList('-ip -times -info');
        $serverInfoResult = $this->ts->getInstance()->serverInfo();

        $treeView = null;
        $serverPort = null;
        if ($this->ts->getInstance()->succeeded($serverInfoResult)) {
            $serverInfoDataResult = $this->ts->getInstance()->getElement('data', $serverInfoResult);
            if (array_key_exists('virtualserver_port', $serverInfoDataResult)) {
                $serverPort = $serverInfoDataResult['virtualserver_port'];
            }
        }

        $this->ts->getInstance()->logout(); // avoid showing currently used user twice

        if ($serverPort) {
            $uri = sprintf('serverquery://%s:%s@%s:%s/?server_port=%s',
                $this->auth->getIdentity()['user'],
                $this->auth->getIdentity()['password'],
                getenv('teamspeak_host'),
                getenv('teamspeak_query_port'),
                $serverPort);

            $ts = new TeamSpeak3();
            $tsServer = $ts->factory($uri);
            $treeView = $tsServer->getViewer(new TeamSpeak3_Viewer_Html("/images/viewer/", "/images/flags/", "data:image"));
        }

        $this->view->render($response, 'online.twig', [
            'title' => $this->translator->trans('online.title'),
            'data' => $this->ts->getInstance()->getElement('data', $dataResult),
            'sid' => $sid,
            'treeView' => $treeView
        ]);
    }
}