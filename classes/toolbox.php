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

/**
 * The block's toolbox.
 *
 * @copyright  &copy; 2023-onwards G J Barnard.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class toolbox {
    /**
     * @var toolbox Singleton instance of us.
     */
    protected static $instance = null;

    /**
     * This is a lonely object.
     */
    private function __construct() {
    }

    /**
     * Gets the toolbox singleton.
     *
     * @return toolbox The toolbox instance.
     */
    public static function get_instance() {
        if (!is_object(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Gets the archive folder and creates it if it does not exist.
     * @param int $courseid The Course id.
     *
     * @return string The toolbox instance.
     */
    public function getarchivefolder($courseid) {
        global $CFG;

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
        $blockarchivefolder = $archivelocation.'course/'.$courseid;

        // Ensure the destination path exists.
        $thepathparts = explode('/', $blockarchivefolder);
        $depth = '';
        foreach ($thepathparts as $pathpart) {
            $depth .= $pathpart.'/';
            if (!is_dir($CFG->dataroot.$depth)) {
                mkdir($CFG->dataroot.$depth, 0770, true);
            }
        }

        return $CFG->dataroot.$blockarchivefolder;
    }

    /**
     * Compares the course files and archive.
     *
     * @param int $courseid The course id.
     * @param int $contextid The context id.
     *
     * @return array Array of sorted cfafile objects (if any) with their CFA state set.
     */
    public function filecompare($courseid, $contextid) {
        $fs = get_file_storage();

        $files = $fs->get_area_files($contextid, 'block_coursefilesarchive', 'course', $courseid);
        $blockarchivefolder = $this->getarchivefolder($courseid);

        $areafilescfa = array();
        foreach ($files as $file) {
            if (!$file->is_directory()) {
                $cfafile = new cfafile(
                    $file->get_filename(),
                    $file->get_filepath(),
                    $file->get_timemodified()
                );
                $areafilescfa[] = $cfafile;
            }
        }

        $archivefilescfa = array();
        $this->archivewalkcfa($blockarchivefolder, $blockarchivefolder, $archivefilescfa);

        // Sort the file arrays.
        if (!empty($areafilescfa)) {
            usort($areafilescfa, '\block_coursefilesarchive\cfafile::compare');
        }
        if (!empty($archivefilescfa)) {
            usort($archivefilescfa, '\block_coursefilesarchive\cfafile::compare');
        }

        // Create the compare output.
        $compareoutfiles = array();
        $coursefilesindex = 0;
        $archivefilesindex = 0;

        while ((!empty($areafilescfa[$coursefilesindex])) && (!empty($archivefilescfa[$archivefilesindex]))) {
            $compareresult = cfafile::compare($areafilescfa[$coursefilesindex], $archivefilescfa[$archivefilesindex]);

            if ($compareresult == -1) {
                $areafilescfa[$coursefilesindex]->setstate(cfafile::CFA_COURSE);
                $compareoutfiles[] = $areafilescfa[$coursefilesindex];
                $coursefilesindex++;
            } else if ($compareoutfiles == 1) {
                $archivefilescfa[$archivefilesindex]->setstate(cfafile::CFA_ARCHIVE);
                $compareoutfiles[] = $archivefilescfa[$archivefilesindex];
                $archivefilesindex++;
            } else {
                $areafilescfa[$coursefilesindex]->setstate(cfafile::CFA_COURSE_ARCHIVE);
                $archivefilescfa[$archivefilesindex]->setstate(cfafile::CFA_COURSE_ARCHIVE);
                $compareoutfiles[] = $areafilescfa[$coursefilesindex];
                $coursefilesindex++;
                $archivefilesindex++;
            }
        }
        // We should now only have possibly remaining files in either array.
        while (!empty($areafilescfa[$coursefilesindex])) {
            $areafilescfa[$coursefilesindex]->setstate(cfafile::CFA_COURSE);
            $compareoutfiles[] = $areafilescfa[$coursefilesindex];
            $coursefilesindex++;
        }
        while (!empty($archivefilescfa[$archivefilesindex])) {
            $archivefilescfa[$archivefilesindex]->setstate(cfafile::CFA_ARCHIVE);
            $compareoutfiles[] = $archivefilescfa[$archivefilesindex];
            $archivefilesindex++;
        }

        return $compareoutfiles;
    }

    private function archivewalkcfa($blockarchivefolder, $root, &$archivefiles) {
        $iterator = new \FilesystemIterator($root);
        foreach ($iterator as $entry) {
            if ($entry->isDir()) {
                $this->archivewalkcfa($blockarchivefolder, $entry->getPathname(), $archivefiles);
            } else {
                $thepath = $entry->getPath();
                $thepath = str_replace($blockarchivefolder, '', $thepath);
                $thepath = str_replace('\\', '/', $thepath);
                $cfafile = new cfafile(
                    $entry->getFilename(),
                    $thepath
                );
                $archivefiles[] = $cfafile;
            }
        }
    }
}
