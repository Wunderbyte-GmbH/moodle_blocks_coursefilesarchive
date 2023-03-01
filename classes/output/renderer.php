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

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/repository/lib.php");

/**
 * Course files archive renderer.
 */
class renderer extends \plugin_renderer_base {

    public function render_filesform($courseid, $context) {
        global $CFG;

        $data = new \stdClass();
        $data->id = $courseid;
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

        $mform = new files_form(null, array('data' => $data, 'options' => $options));
        $redirecturl = course_get_url($courseid);
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
                $courseid);
            redirect($redirecturl);
        }

        return $mform->render();
    }

    public function render_actionsform($courseid, $contextid) {
        /* Returns an array of `stored_file` instances.
           Ref: https://moodledev.io/docs/apis/subsystems/files#list-all-files-in-a-particular-file-area. */
        $fs = get_file_storage();
        $files = $fs->get_area_files($contextid, 'block_coursefilesarchive', 'course', $courseid);

        $data = new \stdClass();
        $data->id = $courseid;
        $aform = new actions_form(null, array('data' => $data));
        if ($formdata = $aform->get_data()) {
            // What button was pressed?
            if (!empty($formdata->updatearchive)) {
                $redirecturl = course_get_url($courseid);
                // Get the folder for the files.
                $toolbox = \block_coursefilesarchive\toolbox::get_instance();
                $blockarchivefolder = $toolbox->getarchivefolder($courseid);

                // Copy the files.
                foreach ($files as $file) {
                    if (!$file->is_directory()) {
                        $filepath = $file->get_filepath();
                        $filename = $file->get_filename();

                        // Ensure the destination path exists.
                        $thepathparts = explode('/', $filepath);
                        $depth = '';
                        foreach ($thepathparts as $pathpart) {
                            $depth .= $pathpart.'/';
                            if (!is_dir($blockarchivefolder.$depth)) {
                                mkdir($blockarchivefolder.$depth, 0770, true);
                            }
                        }

                        // Timestamp.
                        $timestamp = \block_coursefilesarchive\cfafile::gettimestamp($file->get_timemodified());

                        // Copy content if new file.
                        $thefile = $blockarchivefolder.$filepath.$timestamp.$filename;
                        if (!is_readable($thefile)) {
                            $file->copy_content_to($thefile);
                            // Make read only.
                            chmod($thefile, 0440);
                        }
                    }
                }
            } else if (!empty($formdata->comparefiles)) {
                $redirecturl = new \moodle_url('/blocks/coursefilesarchive/filecompare.php',
                    array('courseid' => $courseid, 'contextid' => $contextid, 'sesskey' => sesskey()));
                redirect($redirecturl);
            } else {
                $redirecturl = course_get_url($courseid);
                redirect($redirecturl);
            }
        }
        return $aform->render();
    }

    /**
     * Method to render the file comparison.
     * @param filecompare $filecompare the filecompare widget.
     *
     * @return Markup.
     */
    public function render_filecompare(filecompare $filecompare) {
        $output = $this->output->header();
        $output .= $this->render_from_template('block_coursefilesarchive/filecompare', $filecompare->export_for_template($this));
        $output .= $this->output->footer();

        return $output;
    }
}
