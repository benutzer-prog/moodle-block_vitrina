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
 * Class containing renderers for the block.
 *
 * @package   block_vitrina
 * @copyright 2023 David Herney @ BambuCo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_vitrina\output;

use renderable;
use renderer_base;
use templatable;

/**
 * Class containing data for the block.
 *
 * @copyright 2023 David Herney @ BambuCo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main implements renderable, templatable {

    /**
     * @var array Courses list to show.
     */
    private $courses = null;

    /**
     * @var array List of tabs to print.
     */
    private $tabs;

    /**
     * Constructor.
     *
     * @param array $courses A courses list
     */
    public function __construct($courses = [], $tabs) {
        global $CFG, $OUTPUT;

        // Load the course image.
        foreach ($courses as $course) {
            \block_vitrina\controller::course_preprocess($course);
        }

        $this->courses = $courses;
        $this->tabs = $tabs;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return array Context variables for the template
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;

        $icons = [
            'default' => 'address-card',
            'premium' => 'sort-amount-desc',
            'recents' => 'trophy',
            'greats' => 'calendar-check-o'
        ];

        $showtabs = [];
        foreach ($this->tabs as $k => $tab) {
            $one = new \stdClass();
            $one->title = get_string('tabtitle_' . $tab, 'block_vitrina');
            $one->key = $tab;
            $one->icon = $icons[$tab];
            $one->state = $k == 0 ? 'active' : '';
            $showtabs[] = $one;
        }

        $activetab = false;
        $uniqueid = \block_vitrina\controller::get_uniqueid();

        if (in_array('default', $this->tabs)) {
            $defaultvariables['hasdefault'] = true;
            $defaultvariables['defaultstate'] = !$activetab ? 'active' : '';
            $activetab = true;
        }

        if (in_array('premium', $this->tabs)) {
            $defaultvariables['haspremium'] = true;
            $defaultvariables['premiumstate'] = !$activetab ? 'active' : '';
            $activetab = true;
        }

        if (in_array('recents', $this->tabs)) {
            $defaultvariables['hasrecents'] = true;
            $defaultvariables['recentsstate'] = !$activetab ? 'active' : '';
            $activetab = true;
        }

        if (in_array('greats', $this->tabs)) {
            $defaultvariables['hasgreats'] = true;
            $defaultvariables['greatsstate'] = !$activetab ? 'active' : '';
            $activetab = true;
        }

        $defaultvariables = [
            'courses' => array_values($this->courses),
            'baseurl' => $CFG->wwwroot,
            'hastabs' => count($this->tabs) > 1,
            'tabs' => $showtabs,
            'uniqueid' => $uniqueid
        ];

        return $defaultvariables;
    }
}
