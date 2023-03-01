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
 * The course file archive file.
 *
 * @copyright  &copy; 2023-onwards G J Barnard.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class cfafile {
    /**
     * @var array File path.
     */
    private $path = array();

    /**
     * @var string File path.
     */
    private $filename = '';

    /**
     * @var string File timestamp.
     */
    private $timestamp = '';

    public const CFA_UNKNOWN = 0;
    public const CFA_COURSE_ARCHIVE = 1;
    public const CFA_ARCHIVE = 2;
    public const CFA_COURSE = 3;

    /**
     * @var int State of the file as per the CFA constants.
     */
    private $state = self::CFA_UNKNOWN;

    /**
     * Constructor.
     *
     * @param string $filename The filename.
     * @param string $path The path which must have the block archive folder all the way to the course id removed.
     *                     And use Unix directory separator of the forward slash.
     * @param int $timestamp The timestamp.
     * @param boolean $readonly Read only?
     */
    public function __construct($filename, $path, $timestamp) {
        if (empty($timestamp)) {
            $timestamp = substr($filename, 0, strpos($filename, '_'));
            $filename = str_replace($timestamp.'_', '', $filename);
        }
        $this->filename = $filename;
        $pathtrimmed = trim($path, '/');
        if (!empty($pathtrimmed)) {
            $this->path = explode('/', $pathtrimmed);
        }
        $this->timestamp = $timestamp;
    }

    /**
     * File compare.
     *
     * @param cfafile $filea File a.
     * @param cfafile $fileb File b.
     *
     * @return int The comparision between the two, where -1 is less, 0 is equal and +1 is greater.
     */
    public static function compare($filea, $fileb) {
        // Path compare.
        if ((empty($filea->path)) && (!empty($fileb->path))) {
            return -1;
        } else if ((!empty($filea->path)) && (empty($fileb->path))) {
            return 1;
        } else if ((!empty($filea->path)) && (!empty($fileb->path))) {
            // Path compare.
            $index = 0;
            while ((!empty($filea->path[$index])) && (!empty($fileb->path[$index]))) {
                if ($filea->path[$index] == $fileb->path[$index]) {
                    // Compare the next level.
                    $index++;
                    if ((empty($filea->path[$index])) && (!empty($fileb->path[$index]))) {
                        return -1;
                    } else if ((!empty($filea->path[$index])) && (empty($fileb->path[$index]))) {
                        return 1;
                    }
                } else {
                    $alen = strlen($filea->path[$index]);
                    $blen = strlen($fileb->path[$index]);
                    
                    if ($alen < $blen) {
                        return -1;
                    } else if ($alen > $blen) {
                        return 1;
                    } // Same length path.

                    if ($filea->path[$index] < $fileb->path[$index]) {
                        return -1;
                    }
                    // Logically now 'a' must be greater than 'b' as equal case already done.
                    return 1;
                }
            }
        } // Else both paths are empty so file is in the root.

        // File and time stamp compare.
        if ($filea->filename < $fileb->filename) {
            return -1;
        } else if ($filea->filename > $fileb->filename) {
            return 1;
        }

        // This is a string compare!  So not sure if will be correct!  May need to convert back to Unix epoch integer.
        if ($filea->timestamp < $fileb->timestamp) {
            return -1;
        } else if ($filea->timestamp > $fileb->timestamp) {
            return 1;
        }
        // Equal!
        return 0;
    }

    /**
     * Gets the time stamp from the time.
     *
     * @param int $time The time.
     * @param boolean $sep Add the separator.
     *
     * @return string The timestamp.
     */
    public static function gettimestamp($time, $sep = true) {
        $timestamp = userdate($time, "%F-%H-%M"); // Ref: https://www.php.net/manual/en/function.strftime.php.
        if ($sep) {
            $timestamp .= '_';
        }
        return $timestamp;
    }

    /**
     * Get the state of the file.
     *
     * @return int CFA state.
     */
    public function getstate() {
        return $this->state;
    }

    /**
     * Set the state of the file.
     *
     * @param int $state CFA state.
     * @throws moodle_exception If invalid state.
     */
    public function setstate($state) {
        switch ($state) {
            case self::CFA_UNKNOWN:
            case self::CFA_COURSE_ARCHIVE:
            case self::CFA_ARCHIVE:
            case self::CFA_COURSE:
                $this->state = $state;
                break;
            default:
                throw new \moodle_exception('invalidcfastate', 'block_coursefilesarchive', '', $state);
        }
    }
}
