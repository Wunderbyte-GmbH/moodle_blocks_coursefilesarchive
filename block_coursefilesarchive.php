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

//require('../../config.php');
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
        //$data->id = $this->instance->id;
        $data->id = $this->page->course->id;
        $maxbytes = get_user_max_upload_file_size($context, $CFG->maxbytes);
        $options = array('subdirs' => 1, 'maxbytes' => $maxbytes, 'maxfiles' => -1, 'accepted_types' => '*');
        file_prepare_standard_filemanager($data, 'coursefilesarchive', $options, $context, 'block_coursefilesarchive', 'course', $this->page->course->id);

        $mform = new block_coursefilesarchive_edit_form(null, array('data' => $data, 'options' => $options));
        $redirecturl = course_get_url($this->page->course->id);
        if ($mform->is_cancelled()) {
            redirect($redirecturl);
        } else if ($formdata = $mform->get_data()) {
            $formdata = file_postupdate_standard_filemanager($formdata, 'coursefilesarchive', $options, $context, 'block_coursefilesarchive', 'course', $this->page->course->id);
            //$folder = $DB->get_record('folder', array('id'=>$cm->instance), '*', MUST_EXIST);
            //$folder->timemodified = time();
            //$folder->revision = $folder->revision + 1;

            //$DB->update_record('folder', $folder);

            /* $params = array(
                'context' => $context,
                'objectid' => $folder->id
            ); */
            //$event = \mod_folder\event\folder_updated::create($params);
            //$event->add_record_snapshot('folder', $folder);
            //$event->trigger();

            redirect($redirecturl);
        }

        $this->content->text = $mform->render();

        //$this->content->footer = 'CTX: '.$context->id.' CRS: '.$this->page->course->id.'<br>';
        //$this->content->footer .= 'MD: '.$CFG->dataroot.'<br>';
        $this->content->footer = '';

        // Returns an array of `stored_file` instances - ref: https://moodledev.io/docs/apis/subsystems/files#list-all-files-in-a-particular-file-area.
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'block_coursefilesarchive', 'course', $this->page->course->id);
        foreach ($files as $file) {
            $this->content->footer .= $file->get_filepath().$file->get_filename().'<br>';
        }

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

            foreach ($files as $file) {
                if (!$file->is_directory()) {
                    $filepath = $file->get_filepath();
                    $filename = $file->get_filename();
                    $fileuserid = $file->get_userid();
                    if (!$fileuserid) {
                        $fileuserid = '0';
                    }
                    $thedir = $fileuserid.'/'.$courseid.$filepath;

                    // Ensure the destination path exists.
                    $thepathparts = explode('/', $thedir);
                    $depth = '';
                    foreach ($thepathparts as $pathpart) {
                        $depth .= $pathpart.'/';
                        if (!is_dir($blockarchivefolder.$depth)) {
                            mkdir($blockarchivefolder.$depth, 0770, true);
                        }                        
                    }

                    $file->copy_content_to($blockarchivefolder.$thedir.$filename); 
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
        return array(
            'all' => false,
            'site' => false,
            'site-index' => false,
            'course-view' => true,
            'mod' => false,
            'my' => false
        );
    }

    /**
     * Allow multiple instances of the block.
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * Allow block configuration.
     */
    public function has_config() {
        return true;
    }
}
