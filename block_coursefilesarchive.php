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

require_once("$CFG->dirroot/blocks/coursefilesarchive/block_coursefilesarchive_form.php");
require_once("$CFG->dirroot/repository/lib.php");

/**
 * Class coursefilesarchive.
 *
 */

class block_coursefilesarchive extends block_base {

    /**
     * Initialize our block with a language string.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_coursefilesarchive');
    }

    /**
     * Add some text content to our block.
     */
    public function get_content() {
        global $CFG;

        // Do we have any content?
        if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        // Content.
        $this->content = new stdClass();

        $context = context_block::instance($this->instance->id, MUST_EXIST);
        $data = new stdClass();
        $data->id = $this->page->course->id;
        $maxbytes = get_user_max_upload_file_size($context, $CFG->maxbytes);
        $options = array('subdirs' => 1, 'maxbytes' => $maxbytes, 'maxfiles' => -1, 'accepted_types' => '*');
        file_prepare_standard_filemanager(
            $data,
            'coursefilesarchive',
            $options,
            $context,
            'block_coursefilesarchive',
            'course',
            $this->page->course->id);

        $mform = new block_coursefilesarchive_edit_form(null, array('data' => $data, 'options' => $options));
        $redirecturl = course_get_url($this->page->course->id);
        if ($mform->is_cancelled()) {
            redirect($redirecturl);
        } else if ($formdata = $mform->get_data()) {
            $formdata = file_postupdate_standard_filemanager(
                $formdata,
                'coursefilesarchive',
                $options,
                $context,
                'block_coursefilesarchive',
                'course',
                $this->page->course->id);

            redirect($redirecturl);
        }

        $this->content->text = $mform->render();

        $this->content->footer = '';

        /* Returns an array of `stored_file` instances.
           Ref: https://moodledev.io/docs/apis/subsystems/files#list-all-files-in-a-particular-file-area. */
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'block_coursefilesarchive', 'course', $this->page->course->id);

        $uform = new block_coursefilesarchive_update_form(null, array('data' => $data));
        if ($formdata = $uform->get_data()) {
            // Make the folder for the files.
            $archivelocation = get_config('block_coursefilesarchive', 'archivelocation');
            if (!empty($archivelocation)) {
                if ($archivelocation[0] != '/') {
                    $archivelocation = '/'.$archivelocation;
                }
                if ($archivelocation[strlen($archivelocation) - 1] != '/') {
                    $archivelocation = $archivelocation.'/';
                }
            } else {
                // Use default.
                $archivelocation = '/repository/archive/';
            }
            $blockarchivefolder = $CFG->dataroot.$archivelocation;
            if (!is_dir($blockarchivefolder)) {
                mkdir($blockarchivefolder, 0770, true);
            }
            $courseid = $this->page->course->id;

            // Copy the files.
            foreach ($files as $file) {
                if (!$file->is_directory()) {
                    $filepath = $file->get_filepath();
                    $filename = $file->get_filename();
                    $thedir = 'course/'.$courseid.$filepath;

                    // Ensure the destination path exists.
                    $thepathparts = explode('/', $thedir);
                    $depth = '';
                    foreach ($thepathparts as $pathpart) {
                        $depth .= $pathpart.'/';
                        if (!is_dir($blockarchivefolder.$depth)) {
                            mkdir($blockarchivefolder.$depth, 0770, true);
                        }
                    }

                    // Timestamp.
                    $timemodified = $file->get_timemodified();
                    $timestamp = userdate($timemodified, "%F-%H-%M"); // Ref: https://www.php.net/manual/en/function.strftime.php.

                    // Copy content.
                    $file->copy_content_to($blockarchivefolder.$thedir.$timestamp.'_'.$filename);
                }
            }
            redirect($redirecturl);
        }
        $this->content->text .= $uform->render();

        return $this->content;
    }

    /**
     * This is a list of places where the block may or may not be added.
     */
    public function applicable_formats() {
        $categoryids = get_config('block_coursefilesarchive' , 'blockcategories');
        $canaddtocourse = in_array($this->page->category->id, explode(',' , $categoryids));
        return array(
            'all' => false,
            'site' => false,
            'site-index' => false,
            'course-view' => $canaddtocourse,
            'mod' => false,
            'my' => false
        );
    }

    /**
     * Allow multiple instances of the block.
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Allow block configuration.
     */
    public function has_config() {
        return true;
    }
}
