# Local plugin for DD

Local plugin that ...

## Compatibility

This plugin version is tested for:

* Moodle 3.10.1+ (Build: 20210219) - 2020110901.06

## Requeriments

* ...

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

  * ...


## CLI

...