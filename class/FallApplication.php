<?php

PHPWS_Core::initModClass('hms', 'HousingApplication.php');

class FallApplication extends HousingApplication{

    public $lifestyle_option    = null;
    public $preferred_bedtime   = null;
    public $room_condition      = null;
    public $smoking_preference  = null;

    public $rlc_interest;

    public function __construct($id = 0, $term = null, $banner_id = null, $username = null, $gender = null, $student_type = null, $application_term = null,
            $cell_phone = null, $meal_plan = null, $international = null, $lifestyle_option = null, $preferred_bedtime = null, $room_condition = null, $smoking_preference = null, $rlc_interest = null){

        /**
         * If the id is non-zero, then we need to load the other member variables
         * of this object from the database
         */
        if($id != 0){
            $this->id = (int)$id;
            $this->load();
            return;
        }

        $this->application_type = 'fall';

        parent::__construct($term, $banner_id, $username, $gender, $student_type, $application_term, $cell_phone, $meal_plan, $international);

        $this->setLifestyleOption($lifestyle_option);
        $this->setPreferredBedtime($preferred_bedtime);
        $this->setRoomCondition($room_condition);
        $this->setSmokingPreference($smoking_preference);
        $this->setRlcInterest($rlc_interest);

    }


    /**
     * Loads the FallApplication object with the corresponding id. Requires that $this->id be non-zero.
     */
    protected function load()
    {
        if($this->id == 0){
            return;
        }

        # Load the core application data using the parent class
        if(!parent::load()){
            return false;
        }

        # Load the application-specific data
        $db = new PHPWS_DB('hms_fall_application');

        if(PHPWS_Error::logIfError($db->loadObject($this))){
            $this->id = 0;
            throw new DatabaseException($result->toString());
        }

        return true;
    }

    /**
     * Saves this object
     */
    public function save()
    {
        $is_new = $this->getId() == 0 ? true : false;

        # Save the core application data using the parent class
        if(!parent::save()){
            return false;
        }

        # Save the application-specific data
        $db = new PHPWS_DB('hms_fall_application');

        /* If this is a new object, call saveObject with the third parameter as 'false' so
         * the database class will insert the object with the ID set by the parent::save() call.
        * Otherwise, call save object as normal so that the database class will detect the ID and
        * update the object.
        */
        if($is_new){
            $result = $db->saveObject($this, false, false);
        }else{
            $result = $db->saveObject($this);
        }

        if(PHPWS_Error::logIfError($result)){
            throw new DatabaseException($result->toString());
        }

        return true;
    }

    public function delete()
    {
        $db = new PHPWS_DB('hms_fall_application');
        $db->addWhere('id', $this->id);
        $result = $db->delete();
        if(!$result || PHPWS_Error::logIfError($result)){
            return $result;
        }

        if(!parent::delete()){
            return false;
        }

        return TRUE;
    }

    /**
     * Returns the fields specific to the FallApplications (used in the UnassignedStudents Report).
     *
     * @return Array Array of fields for this FallApplication.
     */
    public function unassignedStudentsFields()
    {
        $fields = parent::unassignedStudentsFields();

        $fields['lifestyle']            = $this->getLifestyleOption()   == 1 ? 'Single gender' : 'Co-ed';
        $fields['bedtime']              = $this->getPreferredBedtime()  == 1 ? 'Early'         : 'Late';
        $fields['room_condition']       = $this->getRoomCondition()     == 1 ? 'Neat'          : 'Cluttered';
        $fields['smoking_preference']   = $this->getSmokingPreference() == 1 ? 'No'            : 'Yes';
        return $fields;
    }

    /************************
     * Accessors & Mutators *
    ************************/

    public function getLifestyleOption(){
        return $this->lifestyle_option;
    }

    public function setLifestyleOption($option){
        $this->lifestyle_option = $option;
    }

    public function getPreferredBedtime(){
        return $this->preferred_bedtime;
    }

    public function setPreferredBedtime($bedtime){
        $this->preferred_bedtime = $bedtime;
    }

    public function getRoomCondition(){
        return $this->room_condition;
    }

    public function setRoomCondition($condition){
        $this->room_condition = $condition;
    }

    public function getSmokingPreference(){
        return $this->smoking_preference;
    }

    public function setSmokingPreference($preference){
        $this->smoking_preference = $preference;
    }

    public function getRlcInterest(){
        return $this->rlc_interest;
    }

    public function setRlcInterest($interest){
        $this->rlc_interest = $interest;
    }
}

class RestoredFallApplication extends FallApplication {
    public function __construct(){
    } // Empty constructor

}
