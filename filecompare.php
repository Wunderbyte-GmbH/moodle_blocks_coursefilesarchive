<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Course files archive block.
 *
 * @package    block_coursefilesarchive
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2023 G J Barnard.
 * @author     G J Barnard - {@link http://about.me/gjbarnard} and
 *                           {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
$courseid = required_param('courseid', PARAM_INT);
require_course_login($courseid);

$sesskey = required_param('sesskey', PARAM_RAW);
if (confirm_sesskey($sesskey)) {
    $blockcontextid = required_param('contextid', PARAM_INT);

    $blockcontext = context::instance_by_id($blockcontextid);
    $PAGE->set_context($blockcontext);
    $PAGE->set_url('/blocks/coursefilesarchive/filecompare.php',
        array('courseid' => $courseid, 'contextid' => $blockcontextid, 'sesskey' => $sesskey));
    $PAGE->set_heading($SITE->fullname);
    $PAGE->set_pagelayout('popup');
    $PAGE->set_title(get_string('pluginname', 'block_coursefilesarchive'));

    $renderer = $PAGE->get_renderer('block_coursefilesarchive');
    $renderable = new block_coursefilesarchive\output\filecompare($courseid, $blockcontextid);
    echo $renderer->render($renderable);
} else {
    redirect(course_get_url($courseid));
}
