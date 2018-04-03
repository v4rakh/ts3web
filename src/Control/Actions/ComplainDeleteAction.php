<?php

use Slim\Http\Request;
use Slim\Http\Response;

final class ComplainDeleteAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $sid = $args['sid'];
        $tcldbid = $args['tcldbid'];

        $this->ts->login($this->auth->getIdentity()['user'], $this->auth->getIdentity()['password']);
        $selectResult = $this->ts->getInstance()->selectServer($sid, 'serverId');
        $this->ts->checkCommandResult($selectResult);

        // search fcldbid
        $fcldbid = null;

        $searchResult = $this->ts->getInstance()->complainList();
        $this->ts->checkCommandResult($searchResult);

        foreach ($this->ts->getInstance()->getElement('data', $searchResult) as $complain) {
            if ($complain['tcldbid'] === $tcldbid) {
                $fcldbid = $complain['fcldbid'];
                break;
            }
        }

        if (!empty($fcldbid)) {
            $complainDeleteResult = $this->ts->getInstance()->complainDelete($tcldbid, $fcldbid);
            $this->ts->checkCommandResult($complainDeleteResult);
        }

        $this->flash->addMessage('success', $this->translator->trans('done'));

        return $response->withRedirect('/complains/' . $sid);
    }
}