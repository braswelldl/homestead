<?php

class LotteryShowRoommateRequestCommand extends Command {

    private $requestId;

    public function setRequestId($id){
        $this->requestId = $id;
    }

    public function getRequestVars(){
        $vars = array('action'=>'LotteryShowRoommateRequest');

        $vars['requestId'] = $this->requestId;

        return $vars;
    }

    public function execute(CommandContext $context)
    {
        PHPWS_Core::initModClass('hms', 'HMS_Lottery.php');
        PHPWS_Core::initModClass('hms', 'LotteryRoommateRequestView.php');
        PHPWS_Core::initModClass('hms', 'HousingApplication.php');
        PHPWS_Core::initModClass('hms', 'StudentFactory.php');
        PHPWS_Core::initModClass('hms', 'RlcMembershipFactory.php');

        $request = HMS_Lottery::get_lottery_roommate_invite_by_id($context->get('requestId'));
        $term = PHPWS_Settings::get('hms', 'lottery_term');
        $housingApp = HousingApplication::getApplicationByUser(UserStatus::getUsername(), $term);
        
        $student = StudentFactory::getStudentByUsername(UserStatus::getUsername(), $term);
        
        // Check for a self-select RLC membership for the logged-in student
        $rlcAssign = RlcMembershipFactory::getMembership($student, $term);
        
        if($rlcAssign == false) {
        	$rlcAssign = null;
        }

        $view = new LotteryRoommateRequestView($request, $term, $housingApp, $rlcAssign);
        $context->setContent($view->show());
    }
}


