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

defined('MOODLE_INTERNAL') || die();

class block_course_activities_edit_form extends block_edit_form {
    protected function specific_definition($mform) {
        $blockname = $this->block->blockname;
        $yesnooptions = [ 'yes' => get_string('yes'), 'no' => get_string('no') ];

        // Activity settings
        $mform->addElement('header', 'configheader', get_string('activity_settings', $blockname));
        $activity_yesnooptions = ['display_activities' => 'yes',  'display_activity_completion_toggles' => 'yes' ];
        foreach ($activity_yesnooptions as $option => $default) {
            $mform->addElement('select', 'config_' . $option, get_string('config_' . $option, $blockname), $yesnooptions);
            $mform->setDefault('config_' . $option, $default);
        }
    }
}
