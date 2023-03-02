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

// General.
$string['pluginname'] = 'Course files archive';

// Actions.
$string['comparefiles'] = 'Compare files';
$string['cfafilecomparison'] = 'Course files archive file comparison';
$string['cfapathname'] = 'Name';
$string['cfastate'] = 'State';
$string['cfatimestamp'] = 'Timestamp';
$string['nofilestocompare'] = 'No files to compare';
$string['returncourse'] = 'Return to course';
$string['updatearchive'] = 'Update archive';

// Settings.
$string['settingsheader'] = 'Course files archive settings';
$string['settingsheaderdesc'] = 'Settings for the Course files archive.  If empty then the default will be used.';

$string['archivelocation'] = 'Archive location';
$string['archivelocationdesc'] = 'The archive location within the Moodle data folder.';

$string['blockcategories'] = 'Block categories';
$string['blockcategoriesdesc'] = 'Allow the the block to be shown in the following categories. Use the \'Ctrl\' key in combination with the mouse to select more than one or none.';

// Course file archive states.
$string['cfastateunknown'] = 'Unknown course file archive file state';
$string['cfastatecoursearchive'] = 'Course files and archive';
$string['cfastatecourse'] = 'Course files';
$string['cfastatearchive'] = 'Archive';

// Errors.
$string['invalidcfastate'] = 'Invalid CFA state: {$a}.';

// Capability strings.
$string['coursefilesarchive:addinstance'] = 'Add a new course files archive block';

// Privacy.
$string['privacy:nop'] = 'To be completed';
