<?php

/**
 * Unread consultation messages notification
 */
class block_consultation_unread extends block_base {
    function init() {
        $this->title = get_string('blockname', 'block_consultation_unread');
        $this->version = 2009112800;
    }

    function has_config() {
        return false;
    }

    function get_content() {
        global $USER, $CFG, $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        if ($COURSE->id == $this->instance->pageid) {
            $course = $COURSE;
        } else {
            $course = get_record('course', 'id', $this->instance->pageid);
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

        $ids = array_keys($consultations);
        if (count($ids) > 1) {
            $ids = "IN (".implode(',', $ids).")";
        } else {
            $ids = "= ".reset($ids);
        }

        $sql = "
            SELECT c.consultationid AS id, COUNT(p.id) AS unread
              FROM {$CFG->prefix}consultation_inquiries c
              JOIN {$CFG->prefix}user uf ON (uf.id = c.userfrom AND uf.deleted = 0)
              JOIN {$CFG->prefix}user ut ON (ut.id = c.userto AND ut.deleted = 0)
              JOIN {$CFG->prefix}consultation_posts p ON (p.inquiryid = c.id AND p.seenon IS NULL AND p.userid <> $USER->id)
             WHERE c.consultationid $ids AND (uf.id = $USER->id OR ut.id = $USER->id)
          GROUP BY c.consultationid";

        $this->content = new object();
        $this->content->footer = '';

        if (!$unreads = get_records_sql($sql)) {
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
