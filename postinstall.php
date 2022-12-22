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
use local_dd\cli\smtp;
use local_dd\cli\ws;

define('CLI_SCRIPT', true);

global $CFG;

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once(__DIR__.'/classes/cli/capability.php');
require_once(__DIR__.'/classes/cli/cfg.php');
require_once(__DIR__.'/classes/cli/functionality.php');
require_once(__DIR__.'/classes/cli/nextcloud.php');
require_once(__DIR__.'/classes/cli/registre.php');
require_once(__DIR__.'/classes/cli/role.php');
require_once(__DIR__.'/classes/cli/saml2.php');
require_once(__DIR__.'/classes/cli/langs.php');
require_once(__DIR__.'/classes/cli/smtp.php');
require_once(__DIR__.'/classes/cli/ws.php');

$usage = 'Automation of the Digital Democratic environment. Site configuration and plugins,
Roles, Capabilities and other functionalities.

Usage:
    # php postinstall.php
    --wwwroot=<wwwroot>  www Root.
    --createcertsaml=<createcertsaml> Create SAML certificate - 1: yes, 0: not
    --timezone=<timezone>  Time Zone
    --ncadmin=<ncadmin>  Name Admin NextCloud.
    --ncpass=<ncpass>  Password Admin NextCloud.
    --sitename=<sitename>  Moodle Site Name.
    --contactname=<contactname>  Contact Name.
    --contactemail=<contactemail>  Contact Email.
    --smtphost=<smtphost>  SMTP Host.
    --smtpport=<smtpport>  SMTP Port.
    --smtpuser=<smtpuser>  SMTP User.
    --smtppass=<smtppass>  SMTP User.
    --smtpprotocol=<smtpprotocol>  SMTP Protocol.
    --noreply=<noreply>  EMAIL No Reply.
    --emailprefix=<emailprefix>  Email Subject Prefix.
    --emailws=<emailws> Email User Web Service
    --updatelangs=<updatelangs> Update Languages Strings - 1: yes, 0: not

Options:
    -h --help                   Print this help.

Description.

Examples:

    # php local/dd/postinstall.php
    --wwwroot="moodle.mydomain.com"
    --createcertsaml=1
    --timezone="Europe/Madrid"
    --ncadmin="admin"
    --ncpass="SuperSecret"
    --sitename="DD"
    --contactname="DD"
    --contactemail="moodle-info@mymailserver.com"
    --smtphost="smtp.mymailserver.com"
    --smtpport="587"
    --smtpuser="your_email@mymailserver.com"
    --smtppass="SuperSecret"
    --smtpprotocol="tls"
    --noreply="noreply@mymailserver.com"
    --emailprefix="[moodle]"
    --emailws="moodle-ws@mymailserver.com"
    --updatelangs=1
';

try {
    list($options, $unrecognised) = cli_get_params([
        'help' => false,
        'wwwroot' => null,
        'createcertsaml' => null,
        'timezone' => null,
        'ncadmin' => null,
        'ncpass' => null,
        'sitename' => null,
        'contactname' => null,
        'contactemail' => null,
        'smtphost' => null,
        'smtpport' => null,
        'smtpuser' => null,
        'smtppass' => null,
        'smtpprotocol' => null,
        'noreply' => null,
        'emailprefix' => null,
        'emailws' => null,
        'updatelangs' => null,
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

    $wwwroot = isset($options['wwwroot']) ? $options['wwwroot'] : null;
    $createcertsaml = isset($options['createcertsaml']) ? $options['createcertsaml'] == 1 : false;
    $timezone = isset($options['timezone']) ? $options['timezone'] : null;
    $ncadmin = isset($options['ncadmin']) ? $options['ncadmin'] : null;
    $ncpass = isset($options['ncpass']) ? $options['ncpass'] : null;
    $sitename = isset($options['sitename']) ? $options['sitename'] : null;
    $contactname = isset($options['contactname']) ? $options['contactname'] : null;
    $contactemail = isset($options['contactemail']) ? $options['contactemail'] : null;
    $smtphost = isset($options['smtphost']) ? $options['smtphost'] : null;
    $smtpport = isset($options['smtpport']) ? $options['smtpport'] : null;
    $smtpuser = isset($options['smtpuser']) ? $options['smtpuser'] : null;
    $smtppass = isset($options['smtppass']) ? $options['smtppass'] : null;
    $smtpprotocol = isset($options['smtpprotocol']) ? $options['smtpprotocol'] : null;
    $noreply = isset($options['noreply']) ? $options['noreply'] : null;
    $emailprefix = isset($options['emailprefix']) ? $options['emailprefix'] : null;
    $emailws = isset($options['emailws']) ? $options['emailws'] : null;
    $updatelangs = isset($options['updatelangs']) ? $options['updatelangs'] == 1 : false;

    if (!is_null($wwwroot)) {

        // 1. Registre
        cli_writeln('##### 1. Registre');
        cli_writeln('#####');
        if (!is_null($sitename) && !is_null($contactname) && !is_null($contactemail)) {
            registre::execute($sitename, $contactname, $contactemail);
        } else {
            cli_writeln('Registre not executed!');
        }
        // 2. Configuration
        cli_writeln('##### 2. Configuration');
        cli_writeln('#####');
        cfg::execute($wwwroot, $timezone);
        // 3. Roles
        cli_writeln('##### 3. Roles');
        cli_writeln('#####');
        role::execute();
        // 4. Capabilities
        cli_writeln('##### 4. Capabilities');
        cli_writeln('#####');
        capability::execute();
        // 5. Functionalities
        cli_writeln('##### 5. Funcionalities');
        cli_writeln('#####');
        functionality::execute();
        // 6. SMTP
        cli_writeln('##### 6. SMTP');
        cli_writeln('#####');
        if (!is_null($smtphost) && !is_null($smtpport) && !is_null($smtpuser) && !is_null($smtppass)
            && !is_null($smtpprotocol) && !is_null($noreply) && !is_null($emailprefix)) {
            smtp::execute($smtphost, $smtpport, $smtpuser, $smtppass, $smtpprotocol, $noreply, $emailprefix);
        } else {
            cli_writeln('SMTP not executed!');
        }
        // 7. NextCloud
        cli_writeln('##### 7. NextCloud');
        cli_writeln('#####');
        if (!is_null($ncadmin) && !is_null($ncpass)) {
            nextcloud::execute($wwwroot, $ncadmin, $ncpass);
        } else {
            cli_writeln('NextCloud not executed!');
        }
        // 8. SAML2
        cli_writeln('##### 8. SAML2');
        cli_writeln('#####');
        if ($createcertsaml && !is_null($sitename) && !is_null($contactname) && !is_null($contactemail)) {
            saml2::execute($sitename, $contactname, $contactemail);
        } else {
            cli_writeln('SAML2 not executed!');
        }
        // 9. WebServices
        cli_writeln('##### 9. Webservices');
        cli_writeln('#####');
        if (!is_null($emailws)) {
            ws::execute($emailws);
        } else {
            cli_writeln('Webservices not executed!');
        }
        // 10. Languages
        cli_writeln('##### 10. Languages');
        cli_writeln('#####');
        if ($updatelangs) {
            langs::execute();
        } else {
            cli_writeln('Languages not executed!');
        }
        // 11. Final.
        purge_caches();
        cli_writeln('##### 11. Purge Cache');
        cli_writeln('#####');

    } else {
        cli_error('param wwwroot is required!');
    }
} catch (moodle_exception $e) {
    cli_error($e->getMessage());
}



