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
 * CLI script Post Install.
 *
 *
 * @package     local_dd
 * @copyright   2022 Tresipunt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_dd\cli\capability;
use local_dd\cli\cfg;
use local_dd\cli\functionality;
use local_dd\cli\langs;
use local_dd\cli\nextcloud;
use local_dd\cli\registre;
use local_dd\cli\role;
use local_dd\cli\saml2;

define('CLI_SCRIPT', true);

global $CFG;

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');
require(__DIR__.'/classes/cli/capability.php');
require(__DIR__.'/classes/cli/cfg.php');
require(__DIR__.'/classes/cli/functionality.php');
require(__DIR__.'/classes/cli/nextcloud.php');
require(__DIR__.'/classes/cli/registre.php');
require(__DIR__.'/classes/cli/role.php');
require(__DIR__.'/classes/cli/saml2.php');
require(__DIR__.'/classes/cli/langs.php');

$usage = 'Automation of the Digital Democratic environment. Site configuration and plugins,
Roles, Capabilities and other functionalities.

Usage:
    # php postinstall.php --wwwroot=<wwwroot>  --ncadmin=<ncadmin> --ncpass=<ncpass>

    --wwwroot=<wwwroot>  www Root.
    --ncadmin=<ncadmin>  Name Admin NextCloud.
    --ncpass=<ncpass>  Password Admin NextCloud.
    --sitename=<sitename>  Moodle Site Name.
    --contactname=<contactname>  Contact Name.
    --contactemail=<contactemail>  Contact Email.

Options:
    -h --help                   Print this help.

Description.

Examples:

    # php local/dd/postinstall.php
    --wwwroot=moodle.test1.digitaldemocratic.net
    --ncadmin=admin
    --ncpass=1234
    --sitename="Digital Democratic"
    --contactname="Contact Name"
     --contactemail="contact@test.xxx"
';

try {
    list($options, $unrecognised) = cli_get_params([
        'help' => false,
        'wwwroot' => null,
        'ncadmin' => null,
        'ncpass' => null,
        'sitename' => null,
        'contactname' => null,
        'contactemail' => null,
    ], [
        'h' => 'help'
    ]);

    if ($unrecognised) {
        $unrecognised = implode(PHP_EOL.'  ', $unrecognised);
        cli_error(get_string('cliunknowoption', 'core_admin', $unrecognised));
    }

    if ($options['help']) {
        cli_writeln($usage);
        exit(2);
    }

    $wwwroot = $options['wwwroot'] ?? null;
    $ncadmin = $options['ncadmin'] ?? null;
    $ncpass = $options['ncpass'] ?? null;
    $sitename = $options['sitename'] ?? null;
    $contactname = $options['contactname'] ?? null;
    $contactemail = $options['contactemail'] ?? null;

    if (isset($wwwroot)) {

        // 1. Registre
        registre::execute($sitename, $contactname, $contactemail);
        // 2. Configuration
        cfg::execute($wwwroot);
        // 3. Roles
        role::execute();
        // 4. Capabilities
        capability::execute();
        // 5. Functionalities
        functionality::execute();
        // 6. NextCloud
        nextcloud::execute($wwwroot, $ncadmin, $ncpass);
        // 7. SAML2
        saml2::execute($sitename, $contactname, $contactemail);
        // 8. Languages
        langs::execute();

        // 9. Final.
        purge_caches();
        cli_writeln('Purge Cache');

    } else {
        cli_error('param wwwroot is required!');
    }
} catch (moodle_exception $e) {
    cli_error($e->getMessage());
}



