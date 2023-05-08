# Moodle - Course Activities Block

Display course activities in a block with links, Optionally, you can also render a Mark As Complete button.

Purpose
--------
The purpose of this plugin is to facilitate: 1) activity navigation, and 2) completion toggling. A student should be able to intuitively navigate between activities and mark activities as complete on the activity pages themselves. This block can be placed on activity pages to allow this.

As for completion toggling, Moodle currently requires users to return to the course page to mark an activity as complete or not complete.

This Plugin
-----------
Features:
* Optional display of activities
* Optional display of clickable activity completion toggles
* Mustache template

Install
=======
Create the folder `blocks/block_course_activities` in your Moodle installation and copy the contents of this repository there. Login as the Moodle admin and proceed through the normal installation of this new plugin. If the plugin is not automatically found, you may have to go to Site Administration > Notifications.
