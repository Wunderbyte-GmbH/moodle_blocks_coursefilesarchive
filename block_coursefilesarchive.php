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

        $courseid = $this->page->course->id;
        $context = context_block::instance($this->instance->id, MUST_EXIST);

        $renderer = $this->page->get_renderer('block_coursefilesarchive');
        $this->content->text = $renderer->render_filesform($courseid, $context);
        $this->content->text .= $renderer->render_actionsform($courseid, $context->id, $this->page->category->id);

        $this->content->footer = '';

        return $this->content;
    }

    /**
     * This is a list of places where the block may or may not be added.
     */
    public function applicable_formats() {
        $canaddtocourse = false;
        if (defined('BEHAT_SITE_RUNNING')) {
            $canaddtocourse = true;
        } else {
            global $CFG;
            if (!empty($this->page->category->id)) {
                $categoryids = get_config('block_coursefilesarchive' , 'blockcategories');
                $canaddtocourse = in_array($this->page->category->id, explode(',' , $categoryids));
            } else if (isset($CFG->upgraderunning)) {
                $canaddtocourse = true; // Has to be true as blocks/moodlebloc.class.php '_self_test()' method will fail when upgrading.
            }
        }
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
