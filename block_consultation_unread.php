<?php

// This file is part of Consultation module for Moodle
//
// Consultation is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Consultation is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Consultation.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unread consultation requests block implementation
 *
 * @package    block
 * @subpackage consultation_unread
 * @copyright  2009 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Unread consultation messages notification block
 */
class block_consultation_unread extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_consultation_unread');
    }

    function has_config() {
        return false;
    }

    function get_content() {
        global $USER, $CFG, $COURSE, $DB;

        if (!file_exists($CFG->dirroot.'/mod/consultation/lib.php')) {
            // bad luck, this can not work without the consultation module!
            return '';
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        if ($COURSE->id == $this->instance->pageid) {
            $course = $COURSE;
        } else {
            $course = $DB->get_record('course', array('id'=>$this->instance->pageid));
        }

        if (empty($course)) {
            return '';
        }

        require_once($CFG->dirroot.'/course/lib.php');

        $modinfo = get_fast_modinfo($course);
        $consultations = array();

        if (empty($modinfo->instances['consultation'])) {
            return '';
        }

        // this actually tests if consultaion installed
        foreach($modinfo->instances['consultation'] as $cm) {
            if (!$cm->uservisible) {
                continue;
            }
            $consultations[$cm->instance] = $cm;
        }

        if (!$consultations) {
            return '';
        }

        list($cids, $params) = $DB->get_in_or_equal(array_keys($consultations), SQL_PARAMS_NAMED);

        $sql = "
            SELECT c.consultationid AS id, COUNT(p.id) AS unread
              FROM {consultation_inquiries} c
              JOIN {user} uf ON (uf.id = c.userfrom AND uf.deleted = 0)
              JOIN {user} ut ON (ut.id = c.userto AND ut.deleted = 0)
              JOIN {consultation_posts} p ON (p.inquiryid = c.id AND p.seenon IS NULL AND p.userid <> :user0)
             WHERE c.consultationid $cids AND (uf.id = :user1 OR ut.id = :user2)
          GROUP BY c.consultationid";
        $params['user0'] = $USER->id;
        $params['user1'] = $USER->id;
        $params['user2'] = $USER->id;

        $this->content = new object();
        $this->content->footer = '';

        if (!$unreads = $DB->get_records_sql($sql, $params)) {
            $this->content->text = get_string('nounread', 'block_consultation_unread');

        } else {
            $this->content->text = '<ul>';
            foreach ($unreads as $unread) {
                $unread->link = $CFG->wwwroot.'/mod/consultation/unread.php?id='.$consultations[$unread->id]->id;
                $unread->name = format_string($consultations[$unread->id]->name);
                $this->content->text .= '<li>'.get_string('unreaditem', 'block_consultation_unread', $unread).'</li>';
            }
            $this->content->text .= '</ul>';
        }

        return $this->content;
    }
}

