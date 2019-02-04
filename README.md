# Wolf Interactive Vars Plugin for Joomla 3
This plugin allows you to output certain default variables and custom variables anywhere on your joomla website.
Output a code snippet like `{{wi_var:my_variable_name}}` anywhere in code/html and the system plugin will find and replace it with it's value.
It is even possible to access data of active menuitem using {{wi_var:menuitem:var_name}}. Works together with wi_menuitem_params! 
A really basic if clause is possible too - but without nesting! Use {{wi_var:if:var_name}} stuff... {{wi_var:endif}}

# Building
Make sure [NodeJS](https://nodejs.org/en/download/) is installed, then download or clone the repository: `git clone https://github.com/jbjhjm/wi_vars.git`

Change to development directory and run `npm install`.

use `grunt build` to create a package for joomla installer at ../build.

# Installation
Use joomla installer to install. Change to plugin manager and enable wi_vars plugin.

# Usage
After installation, following variables are immediately available to use with `{{wi_var:name_of_var}}`:

| Name | Description |
| --- | --- |
| sitename | Name of your Website as specified in Joomla configuration |
| baseURI | URL of your Joomla installation |
| year | Current yeat |
| date | Current date in d.m.Y format |
| time | Current time in h:i format |
| datetime | Current date and time in d.m.Y - h:i format |
| if:var_name | Hides content until endif, if var_name evaluates to false (false, 0, empty string, empty array) |
| endif | Ends content area controlled by if statement. |
| menuitem:alias | Alias of current menuitem (defaults to home item if no menuitem is active) |
| menuitem:title | Name of current menuitem (defaults to home item if no menuitem is active) |
| menuitem:some_var_name | Get any menu item parameter of current menuitem. Works with wi_menuitem_params plugin, too! (defaults to home item if no menuitem is active) |

# Custom variables and custom code

locate /plugins/system/wi_vars/custom within your joomla installation.
In there, you'll find two files named vars_.php/.xml. Remove the underscore to allow the plugin to access their contents.
After renaming them you can also safely update the plugin without loosing your custom data.

## XML
Using the xml file, you can create parameters which can be edited in the wi_vars plugin settings.
The xml file already contains a few example fields. Refer to [https://docs.joomla.org/Standard_form_field_types](Joomla form field types) to see what's possible.

## PHP
Using the php file, you can declare your own variables or even execute PHP code.
Adding key/value pairs to the `static $vars` array is the easiest possibility.
Modify `static function getVars` to return any key/value array you'd like.
Define static function(s) with a naming scheme of `processVar_[my_functions_name]` and use `{{wi_var:my_functions_name}}` to execute them.
You can even use `{{wi_var:my_functions_name:A_STRING}}` and receive A_STRING as function argument.

