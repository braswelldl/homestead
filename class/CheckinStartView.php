<?php

class CheckinStartView extends hms\View {

    private $halls;
    private $term;

    public function __construct(Array $halls, $term)
    {
        $this->halls = $halls;
        $this->term = $term;
    }

    public function show()
    {
        javascript('jquery');
        javascript('jquery_ui');
        javascriptMod('hms', 'jqueryCookie');
        javascriptMod('hms', 'checkinStart');

        Layout::addPageTitle('Check-in');

        $tpl = array();

        $form = new PHPWS_Form('checkin_form');

        $submitCmd = CommandFactory::getCommand('StartCheckinSubmit');
        $submitCmd->initForm($form);

        $form->addDropbox('residence_hall', array(0 => 'Select a hall..') + $this->halls);
        $form->setLabel('residence_hall', 'Residence Hall');
        $form->addCssClass('residence_hall', 'form-control');

        if(count($this->halls) == 1){
            $keys = array_keys($this->halls);
            $form->addHidden('residence_hall_hidden', $keys[0]);

            setcookie('hms-checkin-hall-id', $keys[0]); // Force the hall selection cookie to the one hall this user has
            setcookie('hms-checkin-hall-name', $this->halls[$keys[0]]);
        }else{
            $form->addHidden('residence_hall_hidden');
        }

        $form->addText('banner_id');
        $form->setLabel('banner_id', 'Resident');
        $form->setExtra('banner_id', 'placeholder = "Swipe AppCard or type Name/Email/Banner ID"');
	      $form->addCssClass('banner_id', 'form-control');

        $form->addSubmit('Begin Check-in');
        $form->setClass('submit', 'btn btn-primary');

        $form->mergeTemplate($tpl);
        $tpl = $form->getTemplate();

        return PHPWS_Template::process($tpl, 'hms', 'admin/checkinStart.tpl');
    }
}

?>
