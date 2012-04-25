<?php

PHPWS_Core::initModClass('hms', 'View.php');

class RoomView extends View {

    private $hall;
    private $floor;
    private $room;

    public function __construct(HMS_Residence_Hall $hall, HMS_Floor $floor, HMS_Room $room){
        $this->hall		= $hall;
        $this->floor	= $floor;
        $this->room		= $room;
    }

    public function show()
    {
        PHPWS_Core::initModClass('hms', 'HMS_Residence_Hall.php');
        PHPWS_Core::initModClass('hms', 'HMS_Floor.php');
        PHPWS_Core::initModClass('hms', 'HMS_Bed.php');
        PHPWS_Core::initModClass('hms', 'HMS_Assignment.php');
        PHPWS_Core::initModClass('hms', 'HMS_Util.php');

        /*** Header Info ***/
        $tpl['TERM'] = Term::getPrintableSelectedTerm();
        $tpl['HALL_NAME']           = $this->hall->getLink();
        $tpl['FLOOR_NUMBER']        = $this->floor->getLink('Floor');
        
        /*** Page Title ***/
        $tpl['ROOM'] = $this->room->getRoomNumber();
        
        /*** Room Attributes Labels ***/
        if($this->room->isOffline()){
            $tpl['OFFLINE_ATTRIB'] = 'Offline';
        }
        
        if($this->room->isReserved()){
            $tpl['RESERVED_ATTRIB'] = 'Reserved';
        }
        
        if($this->room->isRa()){
            $tpl['RA_ATTRIB'] = 'RA';
        }
        
        if($this->room->isPrivate()){
            $tpl['PRIVATE_ATTRIB'] = 'Private';
        }
        
        if($this->room->isOverflow()){
            $tpl['OVERFLOR_ATTRIB'] = 'Overflow';
        }
        
        if($this->room->isParlor()){
            $tpl['PARLOR_ATTRIB'] = 'Parlor';
        }
        
        if($this->room->isADA()){
            $tpl['ADA_ATTRIB'] = 'ADA';
        }
        
        if($this->room->isHearingImpaired()){
            $tpl['HEARING_ATTRIB'] = 'Hearing Impaired';
        }
        
        if($this->room->bathEnSuite()){
            $tpl['BATHENSUITE_ATTRIB'] = 'Bath en Suite';
        }
        
        $number_of_assignees    = $this->room->get_number_of_assignees();

        $tpl['NUMBER_OF_BEDS']      = $this->room->get_number_of_beds();
        $tpl['NUMBER_OF_ASSIGNEES'] = $number_of_assignees;

        $form = new PHPWS_Form;

        $submitCmd = CommandFactory::getCommand('EditRoom');
        $submitCmd->setRoomId($this->room->id);
        $submitCmd->initForm($form);

        $form->addText('room_number', $this->room->getRoomNumber());


        if($number_of_assignees == 0){
            # Room is empty, show the drop down so the user can change the gender
            $form->addDropBox('gender_type', array(FEMALE => FEMALE_DESC, MALE => MALE_DESC, COED=>COED_DESC));
            $form->setMatch('gender_type', $this->room->gender_type);
        }else{
            # Room is not empty so just show the gender (no drop down)
            if($this->room->gender_type == FEMALE){
                $tpl['GENDER_MESSAGE'] = "Female";
            }else if($this->room->gender_type == MALE){
                $tpl['GENDER_MESSAGE'] = "Male";
            }else if($this->room->gender_type == COED){
                $tpl['GENDER_MESSAGE'] = "Coed";
            }else{
                $tpl['GENDER_MESSAGE'] = "Error: Undefined gender";
            }
            # Add a hidden variable for 'gender_type' so it will be defined upon submission
            $form->addHidden('gender_type', $this->room->gender_type);
            # Show the reason the gender could not be changed.
            if($number_of_assignees != 0){
                $tpl['GENDER_REASON'] = 'Remove occupants to change room gender.';
            }
        }

        //Always show the option to set the default gender
        $form->addDropBox('default_gender', array(FEMALE => FEMALE_DESC, MALE => MALE_DESC, COED => COED_DESC));
        $form->setMatch('default_gender', $this->room->default_gender);

        $form->addCheck('offline', 1);
        $form->setLabel('offline', 'Offline');
        $form->setMatch('offline', $this->room->isOffline());

        $form->addCheck('reserved', 1);
        $form->setLabel('reserved','Reserved');
        $form->setMatch('reserved', $this->room->isReserved());

        $form->addCheck('ra', 1);
        $form->setLabel('ra','Reserved for RA');
        $form->setMatch('ra', $this->room->isRa());

        $form->addCheck('private', 1);
        $form->setLabel('private','Private');
        $form->setMatch('private', $this->room->isPrivate());

        $form->addCheck('overflow', 1);
        $form->setLabel('overflow','Overflow');
        $form->setMatch('overflow', $this->room->isOverflow());

        $form->addCheck('parlor', 1);
        $form->setLabel('parlor','Parlor');
        $form->setMatch('parlor', $this->room->isParlor());
        
        $form->addCheck('ada', 1);
        $form->setLabel('ada', 'ADA');
        $form->setMatch('ada', $this->room->isAda());
        
        $form->addCheck('hearing_impaired', 1);
        $form->setLabel('hearing_impaired', 'Hearing Impaired');
        $form->setMatch('hearing_impaired', $this->room->isHearingImpaired());
        
        $form->addCheck('bath_en_suite', 1);
        $form->setLabel('bath_en_suite', 'Bath en Suite');
        $form->setMatch('bath_en_suite', $this->room->bathEnSuite());
        
        $form->addSubmit('submit', 'Submit');

        # TODO: add an assignment pager here
        $tpl['BED_PAGER'] = HMS_Bed::bed_pager_by_room($this->room->id);

        # if the user has permission to view the form but not edit it then
        # disable it
        if(    Current_User::allow('hms', 'room_view')
        && !Current_User::allow('hms', 'room_attributes')
        && !Current_User::allow('hms', 'room_structure'))
        {
            $form_vars = get_object_vars($form);
            $elements = $form_vars['_elements'];

            foreach($elements as $element => $value){
                $form->setDisabled($element);
            }
        }

        $form->mergeTemplate($tpl);
        $tpl = $form->getTemplate();

        Layout::addPageTitle("Edit Room");

        return PHPWS_Template::process($tpl, 'hms', 'admin/edit_room.tpl');
    }
}

?>