This folder can be used to create your own custom theme
You need to understand HTML/CSS and a little PHP to get started.

How to use:
- You can copy/paste layout files directly into this directory and they will not be overridden in future component updates
- The better way is probably to override the entire com_yaquiz layout at the template level from your site's template with Joomla's template override tool
- After copying the files from "default" to "custom" you can make your changes
- Set the theme to custom in the global yaquiz component config options

If you ONLY want to change the CSS styling
You can just create a new style.css in this folder and not include the php layout files
Any missing php files in the custom folder will be replaced with the files from default

