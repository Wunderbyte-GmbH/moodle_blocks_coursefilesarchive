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
     *
     * @return string The toolbox instance.
     */
    public function getarchivefolder() {
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
        $blockarchivefolder = $CFG->dataroot.$archivelocation;
        if (!is_dir($blockarchivefolder)) {
            mkdir($blockarchivefolder, 0770, true);
        }

        return $blockarchivefolder;
    }
}
