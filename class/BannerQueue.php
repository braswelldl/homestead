<?php

/**
 * BannerQueue - Manages the Banner Queue
 * Queues up assignments so if we can't SOAP it over to Banner, Housing
 * can still do their jobs
 * @author Jeff Tickle <jtickle at tux dot appstate dot edu>
 */
PHPWS_Core::initModClass('hms', 'BannerQueueItem.php');

class BannerQueue {

    /**
     * Queues a Create Assignment
     */
    public static function queueAssignment(Student $student, $term, HMS_Residence_Hall $hall, HMS_Bed $bed, $mealPlan, $mealCode)
    {
        $entry = new BannerQueueItem(0, BANNER_QUEUE_ASSIGNMENT, $student, $term, $hall, $bed, $mealPlan, $mealCode);

        if(BannerQueue::processImmediately($term)) {
            return $entry->process();
        }else{
            return $entry->save();
        }
    }

    /**
     * Queues a Remove Assignment
     *
     * NOTE: If the queue contains a Create Assignment for the same
     * user to the same room, this will NOT queue a room assignment,
     * but rather will delete the original assignment, UNLESS the
     * $force_queue flag is set.  The $force_queue flag being true will
     * queue a removal no matter what.
     *
     * MORE NOTE: If this requires immediate processing because banner
     * commits are enabled, the it will be sent straight to Banner,
     * and so the force_queue flag will be ignored.
     */
    public static function queueRemoveAssignment(Student $student, $term, HMS_Residence_Hall $hall, HMS_Bed $bed, $refund)
    {
        $entry = new BannerQueueItem(0, BANNER_QUEUE_REMOVAL, $student, $term, $hall, $bed, null, null, $refund);

        if(BannerQueue::processImmediately($term)) {
            return $entry->process();
        }

        // Otherwise, look for an corresponding assignment
        $db = new PHPWS_DB('hms_banner_queue');
        $db->addWhere('type',           BANNER_QUEUE_ASSIGNMENT);
        $db->addWhere('banner_id',      $student->getBannerId());
        $db->addWhere('building_code',  $hall->getBannerBuildingCode());
        $db->addWhere('bed_code',       $bed->getBannerId());
        $db->addWhere('term',           $term);
        $result = $db->count();

        if(PHPWS_Error::logIfError($result)) {
            throw new DatabaseException($result->toString());
        }

        if($result == 0) {
            return $entry->save();
        }else{
            return $db->delete();
        }
    }

    /**
     * Returns TRUE if an action should be processed immediately (queue is disabled)
     * or FALSE if an action should be queued
     */
    public static function processImmediately($termCode) {
        $term = new Term($termCode);
        $queue = $term->getBannerQueue();
        return $queue == 0;
    }

    public static function processAll($term)
    {
        $db = new PHPWS_DB('hms_banner_queue');
        $db->addWhere('term', $term);
        $db->addOrder('id');
        $items = $db->getObjects('BannerQueueItem');

        $errors = array();
        foreach($items as $item) {
            $result = null;

            try{
                $result = $item->process();
            }catch(Exception $e){
                $error = array();
                $error['bannerid']  = $item->banner_id;
                $error['username']  = $item->asu_username;
                $error['code']      = $e->getCode();
                $error['message']   = $e->getMessage();
                $errors[] = $error;
                continue;
            }

            if($result === TRUE){
                $item->delete();
            }
        }

        if(empty($errors)){
            return TRUE;
        }

        return $errors;
    }
}
