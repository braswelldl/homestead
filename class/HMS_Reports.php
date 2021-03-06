<?php

PHPWS_Core::initModClass('hms', 'StudentFactory.php'); // Just go ahead and do this here, since a lot of reports use it

class HMS_Reports{

    /**
     * Finds and lists all currently assigned students who have a banner type of F
     */
    public static function run_assigned_type_f(){

        $term = Term::getSelectedTerm();

        $db = new PHPWS_DB('hms_assignment');
        $db->addWhere('term', $term);

        $result = $db->select();

        if(PHPWS_Error::logIfError($result)){
            PHPWS_Core::initModClass('hms', 'exception/DatabaseException.php');
            throw new DatabaseException($result->toString());
        }

        $content = '<table>
                     <tr>
                        <th>User name</th>
                        <th>Banner ID</th>
                        <th>Entry Term</th>
                        <th>Class</th>
                        <th>Type</th>
                        <th>Credit Hours</th>
                        <th>DOB</th>
                     </tr>
                    ';

        foreach($result as $assignment){
            $student = StudentFactory::getStudentByUsername($assignment['asu_username'], $term);
            if($student->getType() == TYPE_FRESHMEN){
                $content .= '<tr>';
                $content .= '<td>' . $student->getUsername() . '</td>';
                $content .= '<td>' . $student->getBannerId() . '</td>';
                $content .= '<td>' . $student->getApplicationTerm() . '</td>';
                $content .= '<td>' . $student->getClass() . '</td>';
                $content .= '<td>' . $student->getType() . '</td>';
                $content .= '<td>' . $student->getCreditHours() . '</td>';
                $content .= '<td>' . $student->getDob() . '</td>';
                $content .= '</tr>';
            }
        }

        $content .= '</table';

        return $content;
    }

    /**
     * Report lists rooms in each residence hall that are still available, along with
     * the available beds in the room.  Also, show the number of beds allocated to the
     * lotter for each residence hall.
     *
     */
    public static function reappAvailability()
    {
        $term = Term::getSelectedTerm();

        // Available rooms in each residence hall.
        $db = new PHPWS_DB('hms_bed');
        $db->addJoin('LEFT', 'hms_bed', 'hms_room', 'room_id', 'id');
        $db->addJoin('LEFT', 'hms_room', 'hms_floor', 'floor_id', 'id');
        $db->addJoin('LEFT', 'hms_floor', 'hms_residence_hall', 'residence_hall_id', 'id');
        //$db->addWhere('hms_bed.ra_bed', 0);
        $db->addWhere('hms_room.private', 0);
        $db->addWhere('hms_room.overflow', 0);
        $db->addWhere('hms_room.reserved', 0);
        $db->addWhere('hms_room.offline', 0);
        $db->addWhere('hms_bed.term', $term);
        $db->addColumn('hms_room.room_number');
        $db->addColumn('hms_bed.bed_letter', null, null, True);
        $db->addColumn('hms_residence_hall.hall_name');
        $db->addGroupBy('hms_residence_hall.hall_name');
        $db->addGroupBy('hms_room.room_number');
        $db->addOrder('hms_residence_hall.hall_name');
        $availRooms = $db->select();

        // Allocated beds for lottery.
        $db = new PHPWS_DB('hms_bed');
        $db->addJoin('LEFT' , 'hms_bed', 'hms_room', 'room_id', 'id');
        $db->addJoin('LEFT' , 'hms_room', 'hms_floor', 'floor_id', 'id');
        $db->addJoin('LEFT' , 'hms_floor', 'hms_residence_hall', 'residence_hall_id', 'id');
        $db->addJoin('RIGHT', 'hms_bed', 'hms_lottery_reservation', 'id', 'bed_id');
        $db->addWhere('hms_lottery_reservation.term', $term);
        $db->addColumn('hms_residence_hall.hall_name');
        $db->addColumn('hms_bed.id', null, null, True);
        $db->addGroupBy('hms_residence_hall.hall_name');
        $db->setIndexBy('hall_name');
        $lotteryBeds = $db->select();

        $tpl = new PHPWS_Template('hms');
        $tpl->setFile('admin/reports/reapp_availability.tpl');

        //
        // "The parent row must be parsed after the child rows."

        // Preload currHall with first residence hall name
        $currHall = $availRooms[0]['hall_name'];
        foreach($availRooms as $row){
            // Change halls, create new block.
            if($currHall != $row['hall_name'] || $currHall == null){
                $tpl->setCurrentBlock('halls');
                // Get allocated beds for the residence hall.
                $lottCount = isset($lotteryBeds[$currHall]['count']) ? $lotteryBeds[$currHall]['count'] : 0;
                $tpl->setData(array('HALL_NAME' => $currHall,
                                    'LOTTERY_BEDS' => $lottCount));

                $tpl->parseCurrentBlock();
                $currHall = $row['hall_name'];
            }
            // Add room to residence hall template block.
            $tpl->setCurrentBlock('rooms');
            $tpl->setData(array('ROOM_NUM' => $row['room_number'],
                                'BED_COUNT' => $row['count']));
            $tpl->parseCurrentBlock();
        }

        // Get last residence hall. Can't parse parent before child with template class.
        $tpl->setCurrentBlock('halls');
        $tpl->setData(array('HALL_NAME' => $currHall));
        $tpl->parseCurrentBlock();

        return $tpl->get();
    }
}
