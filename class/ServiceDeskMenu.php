<?php
PHPWS_Core::initModClass('hms', 'CommandMenu.php');
PHPWS_Core::initModClass('hms', 'HMS_Permission.php');


class ServiceDeskMenu extends CommandMenu {

    public function __construct()
    {
        parent::__construct();

        // Check-in
        if (Current_User::allow('hms', 'checkin')) {
            $this->addCommandByName('Check-in', 'ShowCheckinStart');
        }

        // Check-out
        if (Current_User::allow('hms', 'checkin')) {
            $this->addCommandByName('Check-out', 'ShowCheckoutStart');
        }

        // Room Damage Assessment
        if (Current_User::allow('hms', 'damage_assessment')) {
            $this->addCommandByName('Damage Assessment', 'ShowRoomDamageAssessment');
        }

        // Room Damage Notifications
        if (Current_User::allow('hms', 'damage_notification')) {
            $this->addCommandByName('Send Room Damage Notices', 'SendRoomDamageNotifications');

            $cmd = CommandFactory::getCommand('JSConfirm');
            $cmd->setLink('Send Room Damage Notices');
            $cmd->setTitle('Send Room Damage Notices');
            $cmd->setQuestion('Send room damage notification emails for the selected term?');

            $cmd->setOnConfirmCommand(CommandFactory::getCommand('SendRoomDamageNotifications'));

            $this->addCommand('Send Room Damage Notices', $cmd);
        }

        /*
        if (UserStatus::isAdmin()) {

            if(Current_User::allow('hms', 'package_desk')){
                $this->addCommandByName('Package Desk', 'ShowPackageDeskMenu');
            }
        }
        */
    }

    public function show()
    {
        if (empty($this->commands)) {
            return '';
        }

        $tpl = array ();

        $tpl['MENU'] = parent::show();
        $tpl['LEGEND_TITLE'] = 'Service Desk';

        return PHPWS_Template::process($tpl, 'hms', 'admin/menus/AdminMenuBlock.tpl');
    }
}
