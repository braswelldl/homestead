<?php

PHPWS_Core::initModClass('hms', 'HousingApplication.php');
PHPWS_Core::initModClass('hms', 'FallApplication.php');
PHPWS_Core::initModClass('hms', 'SpringApplication.php');
PHPWS_Core::initModClass('hms', 'SummerApplication.php');

class HousingApplicationFactory {

	public static function getApplicationFromContext(CommandContext $context, $term, Student $student)
	{

		// Data integrity check on the cell phone
		$doNotCall  = $context->get('do_not_call');
		$areaCode 	= $context->get('area_code');
		$exchange 	= $context->get('exchange');
		$number		= $context->get('number');

		if(is_null($doNotCall)){
			// do not call checkbox was not selected, so check the number
			if(is_null($areaCode) || is_null($exchange) || is_null($number)){
				throw new InvalidArgumentException('Please provide a cell-phone number or click the checkbox stating that you do not wish to share your number with us.');
			}
		}

		$sem = Term::getTermSem($term);

		switch($sem){
			case TERM_FALL:
				$application = HousingApplicationFactory::getFallApplicationFromContext($context, $term, $student);
				break;
			case TERM_SPRING:
				$application = new SpringApplication();
				break;
			case TERM_SUMMER1:
			case TERM_SUMMER2:
                $application = HousingApplicationFactory::getSummerApplicationFromContext($context, $term, $student);
				break;
		}

		return $application;
	}

	public static function getFallApplicationFromContext(CommandContext $context, $term, Student $student)
	{
		$doNotCall  = $context->get('do_not_call');
		$areaCode 	= $context->get('area_code');
		$exchange 	= $context->get('exchange');
		$number		= $context->get('number');
		$mealOption			= $context->get('meal_option');
		$lifestyleOption	= $context->get('lifestyle_option');
		$preferredBedtime	= $context->get('preferred_bedtime');
		$roomCondition		= $context->get('room_condition');

		$specialNeeds = $context->get('special_needs');

		$physicalDisability = isset($specialNeeds['physical_disability'])?$specialNeeds['physical_disability']: NULL;
		$psychDisability	= isset($specialNeeds['psych_disability'])?$specialNeeds['psych_disability']: NULL;
		$genderNeed			= isset($specialNeeds['gender_need'])?$specialNeeds['gender_need']: NULL;
		$medicalNeed		= isset($specialNeeds['medical_need'])?$specialNeeds['medical_need']: NULL;

		$rlcInterest 		= $context->get('rlc_interest');

		$international = null;

		if(is_null($doNotCall)){
			$cellPhone = $areaCode . $exchange . $number;
		}else{
			$cellPhone = NULL;
		}

		if(!is_numeric($mealOption) || !is_numeric($lifestyleOption) || !is_numeric($preferredBedtime) || !is_numeric($roomCondition))
		{
			throw new InvalidArgumentException('Invalid values were submitted. Please try again.');
		}

		return new FallApplication(0, $term, $student->getBannerId(), $student->getUsername(), $student->getGender(), $student->getType(), $student->getApplicationTerm(), $cellPhone, $mealOption, $physicalDisability, $psychDisability, $genderNeed, $medicalNeed, $international, $lifestyleOption, $preferredBedtime, $roomCondition, $rlcInterest);
	}
    public static function getSummerApplicationFromContext(CommandContext $context, $term, Student $student)
    {
        // Ooh look at me!  Cut, Copy, and Paste, Oh My!
		$doNotCall  = $context->get('do_not_call');
		$areaCode 	= $context->get('area_code');
		$exchange 	= $context->get('exchange');
		$number		= $context->get('number');
		$mealOption	= $context->get('meal_option');
        $roomType   = $context->get('room_type');

		$specialNeeds = $context->get('special_needs');

		$physicalDisability = isset($specialNeeds['physical_disability'])?$specialNeeds['physical_disability']: NULL;
		$psychDisability	= isset($specialNeeds['psych_disability'])?$specialNeeds['psych_disability']: NULL;
		$genderNeed			= isset($specialNeeds['gender_need'])?$specialNeeds['gender_need']: NULL;
		$medicalNeed		= isset($specialNeeds['medical_need'])?$specialNeeds['medical_need']: NULL;

		$rlcInterest 		= $context->get('rlc_interest');

		$international = null;

		if(is_null($doNotCall)){
			$cellPhone = $areaCode . $exchange . $number;
		}else{
			$cellPhone = NULL;
		}

		return new SummerApplication(0, $term, $student->getBannerId(), $student->getUsername(), $student->getGender(), $student->getType(), $student->getApplicationTerm(), $cellPhone, $mealOption, $physicalDisability, $psychDisability, $genderNeed, $medicalNeed, $international, $roomType);
    }

    public static function getApplicationById($id)
    {
        PHPWS_Core::initModClass('hms', 'HousingApplication.php');
        PHPWS_Core::initModClass('hms', 'FallApplication.php');
        PHPWS_Core::initModClass('hms', 'SpringApplication.php');
        PHPWS_Core::initModClass('hms', 'SummerApplication.php');
        PHPWS_Core::initModClass('hms', 'LotteryApplication.php');
        PHPWS_Core::initModClass('hms', 'WaitingListApplication.php');

        $application = new HousingApplication();
        $db = new PHPWS_DB('hms_new_application');
        $db->addWhere('id', $id);
        $result = $db->loadObject($application);

        if(PHPWS_Error::logIfError($result)){
            throw new Exception("There was an retreiving the HousingApplication object from the database.");
        }

        if(is_null($application)){
            return null;
        }

        switch($application->application_type){
            case 'fall':
                $app = new FallApplication($application->id);
                break;
            case 'spring':
                $app = new SpringApplication($application->id);
                break;
            case 'summer':
                $app = new SummerApplication($application->id);
                break;
            case 'lottery':
                $app = new LotteryApplication($application->id);
                break;
            case 'offcampus_waiting_list':
                $app = new WaitingListApplication($application->id);
                break;
            default:
                //throw new InvalidArgumentException('Unknown application type: ' . $application->application_type);
                $app = new FallApplication($application->id);
        }

        return $app;
    }

}

?>
