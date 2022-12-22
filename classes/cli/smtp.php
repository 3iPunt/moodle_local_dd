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

use dml_exception;

/**
 * Class smtp
 *
 * @package local_dd\cli
 */
class smtp {

    /**
     * Execute.
     *
     * @param string $smtphost
     * @param string $smtpport
     * @param string $smtpuser
     * @param string $smtppass
     * @param string $smtpprotocol
     * @param string $noreply
     * @param string $emailprefix
     * @throws dml_exception
     */
    public static function execute(
        string $smtphost, string $smtpport, string $smtpuser,
        string $smtppass, string $smtpprotocol, string $noreply, string $emailprefix) {
        // Core.
        cfg::set(null, 'smtphosts', $smtphost . ':' . $smtpport);
        cfg::set(null, 'smtpuser', $smtpuser);
        cfg::set(null, 'smtppass', $smtppass);
        cfg::set(null, 'smtpsecure', $smtpprotocol);
        cfg::set(null, 'noreplyaddress', $noreply);
        cfg::set(null, 'emailsubjectprefix', $emailprefix);

    }

}
