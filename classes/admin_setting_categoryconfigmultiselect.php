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

namespace block_coursefilesarchive;

require_once($CFG->libdir.'/blocklib.php');

/**
 * Category config multi select setting that adjusts the shown blocks when the selected categories change.
 *
 * @copyright  &copy; 2023-onwards G J Barnard.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class admin_setting_categoryconfigmultiselect extends \admin_setting_configmultiselect {

    /**
     * Post write settings.
     *
     * @param mixed $originalcats original value before write_setting()
     * @return bool true if changed, false if not.
     */
    public function post_write_settings($originalcats) {
        $parent = parent::post_write_settings($originalcats);
        if ($parent) {
            $newvalues = $this->get_setting();

            $removedcats = array(); // Array of removed category id's from the setting.
            foreach($originalcats as $originalcat) {
                if (!in_array($originalcat, $newvalues)) {
                    $removedcats[] = $originalcat;
                }
            }
            if (!empty($removedcats)) {
                /*
                    The block has a parent context, for this block, that will be the course.  The parent context
                    instance id is the course id.  If we get all of the courses for the removed category, we can
                    get their course id's, then get their context ids.  Then with a list of our block instances,
                    we can see if the course context is in our list of mapped parent id's to block instance id's.
                    If it is then we know that block needs to be deleted.  We do this by adding the id to list of
                    id's to delete, then call the appropriate blocklib function.
                */
                global $DB;
                $blockinstances = $DB->get_recordset('block_instances', array('blockname' => 'coursefilesarchive'));
                if (!empty($blockinstances)) {
                    $blockinstancesparentcontext = array();
                    foreach($blockinstances as $blockinstance) {
                        $blockinstancesparentcontext[$blockinstance->parentcontextid] = $blockinstance->id;
                    }
                    $blockstodelete = array();
                    foreach($removedcats as $removedcat) {
                        $category = \core_course_category::get($removedcat);
                        $catcourses = $category->get_courses();
                        foreach($catcourses as $catcourse) {
                            $catcoursescontextid = $catcourse->get_context()->id;
                            if (array_key_exists($catcoursescontextid, $blockinstancesparentcontext)) {
                                $blockstodelete[] = $blockinstancesparentcontext[$catcoursescontextid];
                            }
                        }
                    }
                    if (!empty($blockstodelete)) {
                        blocks_delete_instances($blockstodelete);
                    }
                }
            }
        } else {
            return false;
        }

        return true;
    }
}
