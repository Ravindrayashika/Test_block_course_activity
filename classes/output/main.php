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

namespace block_course_activities\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use completion_info;

use stdClass;
use moodle_url;

class main implements renderable, templatable {
    /**
     * Store block instance so it can be accessed later.
     */
    protected $block_instance;

    /**
     * Constructor.
     *
     * @param $block_instance The block instance.
     */
    public function __construct($block_instance) {
        $this->block_instance = $block_instance;
    }

    /**
     * Export data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return array Mustache template context
     */
    public function export_for_template(renderer_base $output, $addl_info = []) {
        global $CFG, $USER, $PAGE, $SESSION;

        $templatecontext = [];

        // Convenience variables
        $course = $PAGE->course;
        $cid = $course->id;
        $userid = $USER->id;

        // Initialize with block settings
        $block_config = $this->initialize_config($this->block_instance->config);

        // Use modinfo because it's faster than $DB calls,
        // get_array_of_activities(), and get_course_mods()
     
        $modinfo = get_fast_modinfo($cid);
        $cms = $modinfo->get_cms();
        $sections = $modinfo->get_sections();
		
        $section_info = $modinfo->get_section_info_all();

        // If there's a title, a spacer will be added so it's not so cramped
        if ($block_config->blocktitle !== '') {
            $templatecontext['blocktitle'] = true;
        }

        // Check if we should add the code for accordion-style display
        if ($block_config->accordion === 'yes') {
                if ($block_config->display_activities === 'yes') {
                    $templatecontext['accordion'] = true;
                }
            }
        

        $templatecontext['sections'] = [];
        foreach ($sections as $sectionid => $sequence) {
            // Make sure section is visible and not restricted
            if (!$section_info[$sectionid]->visible || !$section_info[$sectionid]->available) {
                continue;
            }

            $thissection = [];
            $thissection['section_id'] = $sectionid;

            // Flag for whether current activity is in this section
            $currentsection = false;

           

            // Activities

            // Loop through activities of the section
            $sectionactivities = [];
            $visiblecount = 0;
            if ($block_config->display_activities === 'yes') {
                foreach ($sequence as $modid) {
                    $thisactivity = [];

                    $mod = $cms[$modid];
					
                    $url = $mod->url;
                    $modname = $mod->modname; // i.e. "forum", "quiz", "page"
                    $visible = $mod->visible;
                    $uservisible = $mod->uservisible;
                    $available = $mod->available;
                    $name = $mod->get_formatted_name();

                    // Make sure activity is not hidden or restricted
                    if ($visible && $available) {
                        $visiblecount++;

                        // Prepare to build up this activity's content
                        $activityname_extraclasses = '';
                        $activity_li_extraclasses = '';

                        // Activity completion
                        if ($block_config->display_activity_completion_toggles === 'yes') {
                            $completioninfo = new completion_info($course);
                       //     $list = get_array_of_activities($course->id);
						//	echo "<pre>";var_dump($list); exit;
						 $completions = $completioninfo->get_completions($USER->id);
						  /* foreach ($completions as $completion) {
						 $complete = $completion->is_complete();
						 $criteria = $completion->get_criteria(); */
						 //echo "<pre>";var_dump($criteria); exit;
                            // Use completion icon code from course renderer
                            $course_renderer = $PAGE->get_renderer('core', 'course');
                            $thisactivity['completion_toggle'] = $course_renderer->course_section_cm_completion($course, $completioninfo, $mod);
							//print_object($thisactivity);exit;
							//$thisactivity['completion_toggle'] = $complete;
							//}
                        }
                        else {
                            $activityname_extraclasses .= ' no-completiontoggle';
						
                        }

                        // Activity name (i.e. forum, quiz, page)
						if($mod->completion == 1){
								$thisactivity['activity_name'] = $mod->module." - ".$name." - ".date('d-M-Y')." - Completed";
							}
                        else {
                            // Check that the user can view the activity
                            if ($uservisible) {
                                $thisactivity['activity_url'] = $url;
                            }
							
                            else {
                                $activityname_extraclasses .= ' dimmed_text';
                            }
                            $thisactivity['activity_name'] = $mod->module." - ".$name." - ".date('d-M-Y');
                        }

                        // Add modname to classes so you can customize styling
                        $activity_li_extraclasses .= ' activityli-' . $modname;

                        $thisactivity['activity_name_extra_classes'] = $activityname_extraclasses;
                        $thisactivity['activity_li_extra_classes'] = $activity_li_extraclasses;
                        $sectionactivities[] = $thisactivity;
                    }
                }
            }

            $thissection['section_activities_count'] = count($sectionactivities);
            $thissection['section_activities'] = $sectionactivities;


            // Add section depending on config and whether activities exist
            if (
                ($visiblecount > 0)
                ||
                ($block_config->display_activities === 'no')
                ||
                ($thissection['section_activities_count'] != 0)
            ) {
                $templatecontext['sections'][] = $thissection;
            }
        }

        

        return $templatecontext;
    }

    /**
     * Initialize block config settings with defaults if not set.
     *
     * @param $config Block instance's config settings
     * @return string Initialized block instance's config settings
     */
    private function initialize_config($config) {
        if ($config == null) {
            $config = new stdClass();
        }

        if (!isset($config->display_activities)) {
            $config->display_activities = 'yes';
        }
		
		 if (!isset($config->display_activity_completion_toggles)) {
            $config->display_activity_completion_toggles = 'yes';
        }
       
        return $config;
    }
}
