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
 *             Based on mod_folder
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2023 G J Barnard.
 * @author     G J Barnard - {@link http://about.me/gjbarnard} and
 *                           {@link http://moodle.org/user/profile.php?id=442195}
 * @copyright 2010 Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coursefilesarchive\output;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class files_form extends \moodleform {
    public function definition() {
        $mform = $this->_form;

        $data = $this->_customdata['data'];
        $options = $this->_customdata['options'];

        $mform->addElement('hidden', 'id', $data->id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('filemanager', 'coursefilesarchive_filemanager', get_string('pluginname', 'block_coursefilesarchive'), null, $options);
        $submit = get_string('savechanges');
        $this->add_action_buttons(true, $submit);

        $this->set_data($data);
    }
}
