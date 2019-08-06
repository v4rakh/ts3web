<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class OnlineAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');

        $dataResult = $this->ts->getInstance()->clientList('-ip -times -info');
        $this->ts->getInstance()->logout(); // avoid showing currently used user twice

        $treeView = null;
        $serverPort = $this->session->get("sport");

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

        // render GET
        $this->view->render($response, 'online.twig', [
            'title' => $this->translator->trans('online.title'),
            'data' => $this->ts->getInstance()->getElement('data', $dataResult),
            'sid' => $sid,
            'treeView' => $treeView
        ]);
    }
}