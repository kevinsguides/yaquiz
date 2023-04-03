<?php
//this goes in root package folder
//should handle install/uninstall events...


class Com_YAQuizInstallerScript
{
    function install($parent) 
    {
        echo '<p>The component has been installed! If you find this component useful, please consider leaving a few dollars in the Tip Jar at https://kevinsguides.com - Thanks!</p>';
    }
 
    function uninstall($parent) 
    {
        echo '<p>The component has been uninstalled</p>';
    }
 
    function update($parent) 
    {
        echo '<p>The component has been updated to version' . $parent->get('manifest')->version . '</p>';
    }
 
    function preflight($type, $parent) 
    {
        echo '<p>Anything here happens before the installation/update/uninstallation of the component</p>';
    }
 
    function postflight($type, $parent) 
    {
        echo '<p>Anything here happens after the installation/update/uninstallation of the component</p>';
    }
}