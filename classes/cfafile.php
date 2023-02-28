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

    /**
     * @var boolean Read only?
     */
    private $readonly = false;

    /**
     * Constructor.
     *
     * @param string $filename The filename.
     * @param string $path The path which must have the block archive folder all the way to the course id removed.
     *                     And use Unix directory separator of the forward slash.
     * @param int $timestamp The timestamp.
     * @param boolean $readonly Read only?
     */
    public function __construct($filename, $path, $timestamp, $readonly) {
        if (empty($timestamp)) {
            $timestamp = substr($filename, 0, strpos($filename, '_'));
            $filename = str_replace($timestamp.'_', '', $filename);
        }
        $this->filename = $filename;
        $this->path = explode('/', $path);
        $this->timestamp = $timestamp;
        $this->readonly = $readonly;
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
}
