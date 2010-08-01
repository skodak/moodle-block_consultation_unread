// This file is part of Consultation module for Moodle.
//
// Consultation is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 2 of the License, or
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
 * @package   mod-consultation
 * @copyright 2009 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

Consultation Module (v0.91beta)

This module was inspired by Dialogue module by Ray Kingdon.

------------------------------------
Development funded by:
* MediaTouch 2000 Srl - http://mediatouch.moodle.com/moodle/index.php
* Technical University of Liberec, Centre of Continuing Education - http://www.cdv.tul.cz/

------------------------------------
Expected uses:
Activity is intended for student to teacher communication (one to one support).

General design and coding style should serve as an example for new developers
of Moodle modules.

Why not messaging? Unlike messaging the consultations are part of the course
and are more restricted. Consultation module allows you to limit number of support
requests. It makes managing of support requests easier and transparent.
Messaging does not support group modes.

Imagine you are supporting multiple classes with hundreds of students using
built-in messaging ;-)

Do not forget to install the consultation_unread module too.

------------------------------------
Design decisions:
1/ No student to student communication by default, can be enabled via overrides.
2/ Users may edit only own posts, there is no option for editing of all posts.

------------------------------------
Maybe in future:
 * My Moodle support
 * disable notification user preference
 * printing support
 * richer ajax UI

Known problems:
 * notification mail is send in the current language of the posting user,
   hopefully will be possible to fix in Moodle 2.0
 * 