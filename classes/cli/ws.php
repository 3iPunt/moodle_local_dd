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
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright   2022 Tresipunt
 */

namespace local_dd\cli;

use coding_exception;
use context_system;
use dml_exception;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir.'/externallib.php');

/**
 * Class ws
 *
 * @package local_dd\cli
 */
class ws {

    const USERNAME_WS = 'dd_ws';
    const ROLE_WS = 'dd_ws';

    /**
     * Execute.
     *
     * @param string $emailws
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function execute(string $emailws = 'moodle-ws@mymailserver.xxx') {

        // 1. Create WS Role.
        $roleid = self::create_role();

        if ($roleid > 0) {
            // 2. Role Permissions
            self::add_capability($roleid, 'webservice/rest:use', self::ROLE_WS);

            // 3. Create WS User
            $userid = self::create_user($roleid, $emailws);

            if ($userid > 0) {
                // 4. Enable webservices.
                set_config('enablewebservices', 1);

                // 5. Activate REST protocol.
                set_config('webserviceprotocols', 'rest');

                // 6. Enable webservices documentation.
                set_config('enablewsdocumentation', 1);

                // 7. User External Service.
                $externalserviceid = self::auth_service($userid);

                if ($externalserviceid > 0) {
                    // 8. Create Token
                    self::create_token($userid, $externalserviceid);
                }
            }
        }
    }

    /**
     * Create.
     *
     * @return int
     * @throws dml_exception
     */
    public static function create_role(): int {
        global $DB;
        $name = 'DD WS';
        $desc = 'Role for DD synchronization';
        $arc = 'manager';
        $record = $DB->get_record('role', ['shortname' => self::ROLE_WS], '*');
        if (empty($record)) {
            try {
                $res = create_role($name, self::ROLE_WS, $desc, $arc);
                cli_writeln('Create role: ' . self::ROLE_WS);
                return $res;
            } catch (moodle_exception $e) {
                cli_writeln('ERROR Create role (' . self::ROLE_WS . '): ' . $e->getMessage());
                return 0;
            }
        } else {
            cli_writeln('Role already exist: ' . self::ROLE_WS);
            return $record->id;
        }
    }

    /**
     * Add capability.
     *
     * @param int $roleid
     * @param string $capability
     * @param string $rolename
     * @throws dml_exception
     */
    public static function add_capability(int $roleid, string $capability, string $rolename) {
        capability::add($capability, $rolename, $roleid);
    }

    /**
     * Create User.
     *
     * @param int $roleid
     * @param string $emailws
     * @return int
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function create_user(int $roleid, string $emailws): int {
        global $DB, $CFG;

        $email = $emailws;
        $name = 'DD';
        $lastname = 'WS';
        $desc = 'User of the Web Service for the synchronization with DD';
        $userrecord = $DB->get_record('user', ['username' => self::USERNAME_WS], '*');
        if (empty($userrecord)) {
            try {
                require_once($CFG->dirroot . '/user/lib.php');
                $user = new stdClass();
                $user->username = self::USERNAME_WS;
                $user->password = generate_password(10);
                $user->firstname = $name;
                $user->lastname = $lastname;
                $user->email = $email;
                $user->description = $desc;
                $user->confirmed = 1;
                $userid = user_create_user($user);
                cli_writeln('Create User: ' . self::USERNAME_WS);
            } catch (moodle_exception $e) {
                cli_writeln('ERROR Create user (' . self::USERNAME_WS . '): ' . $e->getMessage());
                $userid = 0;
            }
        } else {
            cli_writeln('User already exist: ' . self::USERNAME_WS);
            $userid = $userrecord->id;
        }

        if ($userid > 0) {
            self::add_role_to_user($roleid, $userid);
        }

        return $userid;
    }

    /**
     * Add Role to User.
     *
     * @param int $roleid
     * @param int $userid
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function add_role_to_user(int $roleid, int $userid) {
        role_assign($roleid, $userid, context_system::instance());
        cli_writeln('Role to user: ' . $roleid . ' -> ' . $userid);
    }

    /**
     * Auth Service.
     *
     * @param int $userid
     * @return int
     * @throws dml_exception
     */
    protected static function auth_service(int $userid): int {
        global $DB;
        $externalserviceid = $DB->get_field(
            'external_services', 'id', array('component' => 'local_dd'));
        if ($externalserviceid) {
            try {
                $servuserrecord = $DB->get_record('external_services_users',
                    ['externalserviceid' => $externalserviceid, 'userid' => $userid], '*');
                if (empty($servuserrecord)) {
                    $userauthorized = new stdClass();
                    $userauthorized->externalserviceid = $externalserviceid;
                    $userauthorized->userid = $userid;
                    $userauthorized->iprestriction = '';
                    $userauthorized->validuntil = '';
                    $userauthorized->timecreated = time();
                    $DB->insert_record('external_services_users', $userauthorized);
                    cli_writeln('Auth service: ' . $externalserviceid . ' - ' . $userid);
                } else {
                    cli_writeln('Auth Service already exist! (' . $externalserviceid . ' - ' . $userid . ')');
                }
            } catch (moodle_exception $e) {
                cli_writeln('ERROR Auth Service (' . $externalserviceid . ' - ' . $userid . '): ' .
                    $e->getMessage());
            }
            return $externalserviceid;
        } else {
            cli_writeln('ERROR Auth Service - Local DAA external service not exist!!');
            return 0;
        }
    }

    /**
     * Create Token.
     *
     * @param int $userid
     * @param int $externalserviceid
     * @throws dml_exception
     */
    protected static function create_token(int $userid, int $externalserviceid) {
        global $DB;
        $token = null;
        $usertokens = $DB->get_records('external_tokens', array(
            'userid' => $userid,
            'tokentype' => EXTERNAL_TOKEN_PERMANENT,
            'externalserviceid' => $externalserviceid
        ));
        if ($usertokens) {
            foreach ($usertokens as $usertoken) {
                $token = $usertoken->token;
            }
        }
        if ($token === null) {
            try {
                external_generate_token(
                    EXTERNAL_TOKEN_PERMANENT,
                    $externalserviceid,
                    $userid, context_system::instance());

                cli_writeln('Create Token: ' . $externalserviceid . ' - ' . $userid);
            } catch (\Exception $e) {
                cli_writeln('ERROR Create Token (' . $externalserviceid . ' - ' . $userid . '): ' .
                    $e->getMessage());
            }
        } else {
            cli_writeln('Token already exist!: ' . $externalserviceid . ' - ' . $userid);
        }
    }

}
