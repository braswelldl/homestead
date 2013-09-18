<?php

class RoomChangeParticipantState {

    const STATE_NAME = 'ParentState'; // Text state name

    private $request; // Reference fo the request object

    private $effectiveDate; // Unix timestamp where object enetered this state
    private $effectiveUntilDate; // Unix timestamp where object left this state
    private $committedBy; // User who changed to this state

    /**
     * Constructor
     * @param RoomChangeRequest $request
     * @param unknown $effectiveDate
     * @param unknown $effectiveUntilDate
     * @param unknown $committedBy
     */
    public function __construct(RoomChangeParticipant $participant, $effectiveDate, $effectiveUntilDate = null, $committedBy)
    {
        $this->participant          = $participant;
        $this->effectiveDate        = $effectiveDate;
        $this->effectiveUntilDate   = $effectiveUntilDate;
        $this->committedBy          = $committedBy;
    }

    public function save()
    {
        $db = PdoFactory::getPdoInstance();

        $query = "INSERT INTO hms_room_change_participant_state (participant_id, state, effective_date, effective_until_date, committed_by) VALUES (:participantId, :state, :effectiveDate, :effectiveUntilDate, :committedBy)";
        $stmt = $db->prepare($query);

        $params = array(
                'participantId'         => $this->participant->getId(),
                'state'                 => $this->getName(),
                'effectiveDate'         => $this->getEffectiveDate(),
                'effectiveUntilDate'    => $this->getEffectiveUntilDate(),
                'committedBy'           => $this->getCommittedBy()
        );

        $stmt->execute($params);
    }

    public function update()
    {
        $db = PdoFactory::getPdoInstance();

        $query = "UPDATE hms_room_change_participant_state SET effective_until_date = :effectiveUntilDate WHERE participant_id = :participantId AND state = :state AND effective_date = :effectiveDate";
        $stmt = $db->prepare($query);

        $params = array(
                'participantId'         => $this->participant->getId(),
                'state'                 => $this->getName(),
                'effectiveDate'         => $this->getEffectiveDate(),
                'effectiveUntilDate'    => $this->getEffectiveUntilDate(),
        );

        $stmt->execute($params);
    }

    public function getValidTransitions()
    {
        throw new Exception('No transitions implemented.');
    }

    public function canTransition(RoomChangeParticipantState $toState)
    {
        return in_array(get_class($toState), $this->getValidTransitions());
    }

    public function getName()
    {
        return static::STATE_NAME;
    }

    public function getEffectiveDate()
    {
        return $this->effectiveDate;
    }

    public function getEffectiveUntilDate()
    {
        return $this->effectiveUntilDate;
    }

    public function setEffectiveUntilDate($date)
    {
        $this->effectiveUntilDate = $date;
    }

    public function getCommittedBy()
    {
        return $this->committedBy;
    }

    public function sendNotification()
    {
        // By default, don't send any notifications.
    }
}


class ParticipantStateNew extends RoomChangeParticipantState {
    const STATE_NAME = 'New';

    public function getValidTransitions()
    {
        return array('ParticipantStateStudentApproved');
    }
}

class ParticipantStateStudentApproved extends RoomChangeParticipantState {
    const STATE_NAME = 'StudentApproved';

    public function getValidTransitions()
    {
        return array();
    }

    //TODO Send notification to current RD
}

class ParticipantStateCurrRdApproved extends RoomChangeParticipantState {
    const STATE_NAME = 'CurrRdApproved';

    public function getValidTransitions()
    {
        return array();
    }

    // TODO send notification to future RD
}

class ParticipantStateFutureRdApproved extends RoomChangeParticipantState {
    const STATE_NAME = 'FutureRdApproved';

    public function getValidTransitions()
    {
        return array();
    }

    // TODO If all participants are approved, send notification to Housing
}

class ParticipantStateHousingApproved extends RoomChangeParticipantState {
    const STATE_NAME = 'HousingApproved';

    public function getValidTransitions()
    {
        return array();
    }

    // TODO If all participants are in HousingApproved, move Request to Approved (which will notify everyone)
}

class ParticipantStateInProcess extends RoomChangeParticipantState {
    const STATE_NAME = 'InProcess';

    public function getValidTransitions()
    {
        return array();
    }
}

class ParticipantStateCheckedOut extends RoomChangeParticipantState {
    const STATE_NAME = 'CheckedOut';

    public function getValidTransitions()
    {
        return array();
    }

    // TODO Notify "old" RD and Housing
}

class ParticipantStateDeclined extends RoomChangeParticipantState {
    const STATE_NAME = 'Declined';

    public function getValidTransitions()
    {
        return array();
    }

    // TODO Move Request to Cancelled, which will notify everyone
}

class ParticipantStateDenied extends RoomChangeParticipantState {
    const STATE_NAME = 'Denied';

    public function getValidTransitions()
    {
        return array();
    }

    // TODO Move Request to Denied, which will notify everyone
}

class ParticipantStateCancelled extends RoomChangeParticipantState {
    const STATE_NAME = 'Cancelled';

    public function getValidTransitions()
    {
        return array();
    }

    // TODO Move request to cancelled, which will notify everyone
}

?>