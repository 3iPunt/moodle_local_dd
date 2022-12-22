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
 * @package     local_dd
 * @copyright   3iPunt <https://www.tresipunt.com/>
 */

defined('MOODLE_INTERNAL') || die();

$functions = [

];
$services = [
    'local_dd' => [
        'functions' => [
            'core_course_update_courses',
            'core_user_get_users',
            'core_user_get_users_by_field',
            'core_user_update_picture',
            'core_user_update_users',
            'core_user_delete_users',
            'core_user_create_users',
            'core_cohort_get_cohort_members',
            'core_cohort_add_cohort_members',
            'core_cohort_delete_cohort_members',
            'core_cohort_create_cohorts',
            'core_cohort_delete_cohorts',
            'core_cohort_search_cohorts',
            'core_cohort_update_cohorts',
            'core_role_assign_roles',
            'core_role_unassign_roles',
            'core_cohort_get_cohorts'
        ],
        'restrictedusers' => 1,
        'enabled' => 1
    ]
];
