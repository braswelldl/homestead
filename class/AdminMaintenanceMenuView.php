<?php

PHPWS_Core::initModClass('hms', 'AssignmentMenu.php');
PHPWS_Core::initModClass('hms', 'ResidenceHallMenu.php');
PHPWS_Core::initModClass('hms', 'RLCMenu.php');
PHPWS_Core::initModClass('hms', 'ReapplicationMaintenanceMenu.php');
PHPWS_Core::initModClass('hms', 'MessagingMenu.php');
PHPWS_Core::initModClass('hms', 'ServiceDeskMenu.php');


class AdminMaintenanceMenuView extends hms\View {

    public function show()
    {
        $tpl = array();

        $hallMenu = new ResidenceHallMenu();
        $tpl['RESIDENCE_HALL'] = $hallMenu->show();

        $assignMenu = new AssignmentMenu();
        $tpl['ASSIGNMENT'] = $assignMenu->show();

        $rlcs = new RLCMenu();
        $tpl['RLCS'] = $rlcs->show();

        $reapp = new ReapplicationMaintenanceMenu();
        $tpl['REAPP'] = $reapp->show();

        $messaging = new MessagingMenu();
        $tpl['MESSAGING'] = $messaging->show();

        $serviceDesk = new ServiceDeskMenu();
        $tpl['SERVICE_DESK'] = $serviceDesk->show();

        Layout::addPageTitle("Main Menu");

        return PHPWS_Template::process($tpl, 'hms', 'AdminMaintenanceMenu.tpl');
    }
}
