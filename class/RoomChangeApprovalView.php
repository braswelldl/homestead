<?php

PHPWS_Core::initModClass('hms', 'StudentFactory.php');
PHPWS_Core::initModClass('hms', 'RoomChangeListView.php');

/**
 * Named poorly, but shows an overview of all pending and inactive requests
 * for a given RD/coordinator/admin's residents.
 *
 * @author jbooker
 * @package hms
 */
class RoomChangeApprovalView extends View {

    private $needsApproval;
    private $allPending;
    private $inactive;

    private $hallNames;
    private $term;

    public function __construct(Array $needsApprovalRequests, Array $allPendingRequests, Array $inactiveRequests, Array $hallNames, $term)
    {
        $this->needsApproval = $needsApprovalRequests;
        $this->allPending = $allPendingRequests;
        $this->inactive = $inactiveRequests;

        $this->hallNames = $hallNames;
        $this->term = $term;
    }

    public function show()
    {
        $tpl = array();

        $tpl['HALL_NAMES'] = implode(', ', $this->hallNames);

        $needsActionList = new RoomChangeListView($this->needsApproval, $this->term);
        $tpl['NEEDS_ACTION'] = $needsActionList->show();


        $pendingList = new RoomChangeListView($this->allPending, $this->term); //TODO
        $tpl['PENDING'] = $pendingList->show();

        $inactiveList = new RoomChangeListView($this->inactive, $this->term); //TODO
        $tpl['INACTIVE'] = $inactiveList->show();

        return PHPWS_Template::process($tpl, 'hms', 'admin/RoomChangeApprovalView.tpl');
    }
}

?>