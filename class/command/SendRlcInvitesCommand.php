<?php

/**
 * SendRlcInvitesComands
 *
 * Transitions all new rlc assignments to the 'Invited' state.
 *
 * @author jbooker
 * @package HMS
 */
class SendRlcInvitesCommand extends Command {

    public function getRequestVars()
    {
        return array('action'=>'SendRlcInvites');
    }

    public function execute(CommandContext $context)
    {
        $resultCmd = CommandFactory::getCommand('ShowSendRlcInvites');

        $respondByDate = $context->get('respond_by_date');
        $respondByTime = $context->get('time');

        if(!isset($respondByDate) || $respondByDate == ''){
            NQ::simple('hms', hms\NotificationView::ERROR, 'Please choose a \'respond by\' date.');
            $resultCmd->redirect();
        }

        $dateParts = explode('/', $respondByDate);
        $respondByTimestamp = mktime($respondByTime, null, null, $dateParts[0], $dateParts[1], $dateParts[2]);

        $term = Term::getSelectedTerm();

        $studentType = $context->get('type');

        if(!isset($studentType)){
            NQ::simple('hms', hms\NotificationView::ERROR, 'Please choose a student type.');
            $resultCmd->redirect();
        }

        PHPWS_Core::initModClass('hms', 'RlcAssignmentFactory.php');
        PHPWS_Core::initModClass('hms', 'RlcAssignmentInvitedState.php');

        $assignments = RlcAssignmentFactory::getAssignmentsByTermStateType($term, 'new', $studentType);

        if(sizeof($assignments) == 0){
            NQ::simple('hms', hms\NotificationView::WARNING, 'No invites needed to be sent.');
            $resultCmd->redirect();
        }

        foreach($assignments as $assign){
            $assign->changeState(new RlcAssignmentInvitedState($assign, $respondByTimestamp));
        }

        NQ::simple('hms', hms\NotificationView::SUCCESS, 'Learning community invites sent.');
        $resultCmd->redirect();
    }
}


