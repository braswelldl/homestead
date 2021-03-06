<?php

/**
 * HTML View for UnassignedStudents report
 *
 * @author Jeremy Booker
 * @package HMS
 */

class UnassignedStudentsHtmlView extends ReportHtmlView {

    protected function render()
    {
        parent::render();

        $this->tpl['TERM'] = Term::toString($this->report->getTerm());
        $this->tpl['TOTAL'] = $this->report->getTotal();
        $this->tpl['MALE'] = $this->report->getMale();
        $this->tpl['FEMALE'] = $this->report->getFemale();
        
        // Copy results into the template
        foreach($this->report->getData() as $row){
            $this->tpl['rows'][] = $row;
        }
        
        return PHPWS_Template::process($this->tpl, 'hms', 'admin/reports/UnassignedStudents.tpl');
    }
}

