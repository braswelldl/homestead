<?php

  /**
   * @author theFergus <kevin at tux dot appstate dot edu>
   */

function hms_update(&$content, $currentVersion)
{
    switch ($currentVersion) {
        case version_compare($currentVersion, '0.1.4', '<'):
            $files = array();
            $files[] = 'templates/admin/display_final_rlc_assignments.tpl';
            $files[] = 'templates/admin/display_rlc_student_detail_form_questions.tpl';
            $files[] = 'templates/admin/make_new_rlc_assignments.tpl';
            $files[] = 'templates/admin/make_new_rlc_assignments_summary.tpl';
            $files[] = 'templates/admin/display_rlc_student_detail.tpl';
            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = _('+ RLC administration templates');
        
        case version_compare($currentVersion, '0.1.3', '<'):
            $files = array();
            $files[] = 'templates/student/rlc_signup_confirmation.tpl';
            PHPWS_Boost::updateFiles($files, 'hms');

            $content[] = _('+ Complete system for RLC applications');

        case version_compare($currentVersion, '0.1.2', '<'):
            $db = & new PHPWS_DB;
            $result = $db->importFile(PHPWS_SOURCE_DIR . 'mod/hms/boost/0.1.2.sql');
            if (PEAR::isError($result)) {
                return $result;
            }
            
            $files = array();
            $files[] = 'templates/student/rlc_signup_form_page2.tmp';
            PHPWS_Boost::updateFiles($files, 'hms');
            
            $content[] = _('+ RLC application form template');
            $content[] = _('+ RLC application table');
    }

    return TRUE;
}

?>
