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

namespace block_coursefilesarchive\output;

use moodle_url;
use stdClass;

class filecompare implements \renderable, \templatable {

    private $courseid;
    private $blockcontextid;

    public function __construct($courseid, $blockcontextid) {
        $this->courseid = $courseid;
        $this->blockcontextid = $blockcontextid;
    }

    public function export_for_template(\renderer_base $output) {
        global $USER;

        $data = new stdClass();

        // Page heading and iframe data.
        $data->heading = get_string('pluginname', 'block_coursefilesarchive');

        // TEMPORARY CODE FOR DEVELOPMENT.
        $toolbox = \block_coursefilesarchive\toolbox::get_instance();
        $cfafiles = $toolbox->filecompare($this->courseid, $this->blockcontextid);
        $data->cfafiles = array();
        foreach ($cfafiles as $cfafile) {
            $entry = new stdClass();
            $entry->pathname = $cfafile->getpathname();
            $entry->state = $cfafile->getstate();
            $entry->timestamp = $cfafile->gettimestamp();

            $data->cfafiles[] = $entry;
        }

        $data->returnlink = new moodle_url('/course/view.php', ['id' => $this->courseid]);

        return $data;
    }
}
