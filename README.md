# Local plugin for DD

Local plugin that automatically configures Moodle in the DD project.

## Compatibility

This plugin version is tested for:

* Moodle 3.10.1+ (Build: 20210219) - 2020110901.06

## Languagues

* English

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/local/dd

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## Global Configuration

Go to the URL:

    {your/moodle/dirroot}/admin/settings.php?section=local_dd

  * No settings


## CLI

      php local/dd/postinstall.php --wwwroot=moodle.dd.3ip.eu --ncadmin=admin --ncpass=1234 --sitename="Digital Democratic"--contactname="Contact Name"--contactemail="contact@test.xxx"


   - wwwroot: domain without https://
   - ncadmin: NextCloud Admin User
   - ncpass: Nextcloud Admin Pass
   - sitename: Site Name
   - contactname: Contact Name
   - contactemail: Contact Email

## CUSTOM LANGS

When making changes to text string translations, you must export the .zip file for each language and leave it in this folder

``local/dd/custom_langs``

The file name must have this structure

``customlang_ca.zip``

``customlang_es.zip``

``customlang_en.zip``

These files will be used for automatic DD deployment

### Export file

To perform the export, we must go to the page:

      {your/moodle/dirroot}/admin/tool/customlang/index.php

Select lang and click in 'Export custom strings' button