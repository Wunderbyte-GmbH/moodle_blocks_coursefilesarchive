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

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading('block_coursefilesarchive_header',
        get_string('settingsheader', 'block_coursefilesarchive'),
        get_string('settingsheaderdesc', 'block_coursefilesarchive')));

    // The archive path.
    $settings->add(new admin_setting_configtext('block_coursefilesarchive/archivelocation',
        get_string('archivelocation', 'block_coursefilesarchive'),
        get_string('archivelocationdesc', 'block_coursefilesarchive'),
        'repository/archive', PARAM_PATH)
    );

    // Categories to show the blocks in.
    $name = 'block_coursefilesarchive/blockcategories';
    $title = get_string('blockcategories', 'block_coursefilesarchive');
    $description = get_string('blockcategoriesdesc', 'block_coursefilesarchive');
    $choices = array();
    $topcategories = core_course_category::get(0)->get_children(); // Parent = 0 i.e. top-level categories only.
    foreach ($topcategories as $topcategory) {
        $choices[$topcategory->id] = $topcategory->name;
        $children = $topcategory->get_children();
        foreach ($children as $child) {
            $choices[$child->id] = $topcategory->name.' / '.$child->name;
        }
    }
    $default = array();
    $settings->add(new \block_coursefilesarchive\admin_setting_categoryconfigmultiselect(
        $name, $title, $description, $default, $choices)
    );

    $settings->add(new admin_setting_configcheckbox(
        'block_coursefilesarchive/deleteblocksinunsupportedcategories',
        get_string('deleteblocksinunsupportedcategories', 'block_coursefilesarchive'),
        get_string('deleteblocksinunsupportedcategoriesdesc', 'block_coursefilesarchive'),
        0)
    );
}
