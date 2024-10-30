<?php

/*
Plugin Name: mobileadmin
Version: 2.0.1
Plugin URI: http://wordpress.org/extend/plugins/mobileadmin/
Description: Gives a mobile-friendly admin UI to browsers by user agent, using a plugin architecture. Includes support for iPhone/iPod-Touch.
Author: Jared Bangs and Dan Cameron
Author URI: http://wordpress.org/extend/plugins/mobileadmin/
*/ 

/*  Copyright 2007 Jared Bangs and Dan Cameron 

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

$MobileAdminDebugMessages = array();
$MobileAdminDebugMode = false;

class MobileAdminController {

	var $CurrentUserAgent = null;
	var $Plugins = array();
	
	function LoadPlugins() {
		
		// This function will check the plugins directory for installed plugins
		
		global $MAController, $MobileAdminDebugMessages;
		$pluginBasePath = dirname(__FILE__) . "/mobile_plugins/";
		
		if ( !is_dir($pluginBasePath) )
		{
			die("Cannot get file list: '%s' is not a valid folder path");
		}
		elseif ( !is_readable($pluginBasePath) )
		{
			die("Cannot get folder content: '%s'. Please make sure it is readable by everybody (chmod 755).");
		}
		else {
			
			// Always load default plugin first, since all others extend it
			include_once($pluginBasePath . 'Default.php');	
	
			// Other plugin files
			$files = array();
			$dir_ptr = dir($pluginBasePath);
			if ( $dir_ptr )
			{
				while( ($subdir = $dir_ptr->read()) !== false ) {
					
					// Look in subdirectories which should contain mobileadmin plugin files
					if ( ($subdir{0} != '.')  && is_dir($pluginBasePath . $subdir) ) {
	
						$file_ptr = dir($pluginBasePath . '/' . $subdir);
						while( ($file = $file_ptr->read()) !== false ) {
							
						 	if ( ($file{0} != '.')  && (substr($file, -strlen('.php')) == '.php') ) {
								include_once($pluginBasePath . '/' . $subdir . '/' . $file);	
						 	}
						}
					}
				}
			}
		}
	}

	function RegisterMobileAdminPlugin($pluginClassName) {
		array_push($this->Plugins,new $pluginClassName());
	}

	function GetByUserAgent() {
		if($CurrentUserAgent==null) {

			$defaultPlugin = null;

			foreach ( $this->Plugins as $plugin )
			{
				if( $plugin->Name==='Default' ) {
					$defaultPlugin = $plugin;
				}
				else if( $plugin->MatchesCurrentUserAgent() ) {
					$CurrentUserAgent = $plugin;
					break;
				}
			}
			if( $CurrentUserAgent==null && $defaultPlugin->MatchesCurrentUserAgent() ) {
				$CurrentUserAgent = $defaultPlugin;
			}
		}
		return $CurrentUserAgent;
	}
}

// Initialization
$MAController = new MobileAdminController();
$MAController->LoadPlugins();
$MA = $MAController->GetByUserAgent();
if( $MA!=null ) {
	$MAForm = $MA->GetCurrentForm();		
}
if( $MAForm!=null && !MobileAdmin_CheckOverrideCookie() ) {

	add_action('admin_footer', array(&$MAForm, 'RenderFooterData'));
	add_action('init', array(&$MAForm, 'Initialize_RegExReplace'), 11);
	add_filter('wp_admin_css',array(&$MAForm, 'FilterAdminCSS'));
	add_action('admin_head', array(&$MAForm, 'AddCustomScript'), 11);
	add_action('_admin_menu', array(&$MAForm, 'AlwaysIncludeJQuery'), 11);
	add_filter('user_can_richedit',array(&$MAForm, 'DisableRichEdit'));
}
else {
	add_action('admin_menu', 'MobileAdmin_AddStandardUIMenu');
}

function MobileAdmin_RegExReplace($content) {
	global $MAForm;
	if( $MAForm!=null ) {
		$content = $MAForm->FilterContent($content);
	}
	return $content;
}

function MobileAdmin_AddStandardUIMenu() {
	add_options_page('Mobile Admin', 'Mobile Admin', 1, 'MobileAdminNormalUI', 'MobileAdmin_RenderStandardUIMenu');
}
function MobileAdmin_RenderStandardUIMenu() {
	echo '<form method="post"><input style="font-size: 4em;padding: 10px 15px; margin: 30px;" type="submit" id="ToggleMobileAdminView" name="ToggleMobileAdminView" method="post" value="Switch to Mobile Admin View" /></form>';
}

function MobileAdmin_CheckOverrideCookie() {
	
	$overrideValue = false;
	
	if( $_POST['ToggleMobileAdminView'] === 'Revert to Normal Admin View' ) {
		$overrideValue = true;
	}
	else if( isset($_COOKIE['MobileAdminOverride']) ) {
		if( $_COOKIE['MobileAdminOverride'] === 'true' ) {
			$overrideValue = true;
		}
	}
	
	return $overrideValue;
}
if( $_POST['ToggleMobileAdminView'] === 'Revert to Normal Admin View' ) {
	setCookie('MobileAdminOverride','true');
}
else if( $_POST['ToggleMobileAdminView'] === 'Switch to Mobile Admin View' ) {
	setCookie('MobileAdminOverride','false');
	header( 'Location: index.php' ) ;
}

/* Workarounds for output buffer bug with PHP 5.2.x - Begin */
/* See PHP bug #39381 (http://bugs.php.net/bug.php?id=39381)
 * And WordPress Ticket#3354 (http://trac.wordpress.org/ticket/3354)
 * WP Trac seems to indicate that recent versions contain a fix for this
 * (Change 5463 - http://trac.wordpress.org/changeset/5463)
 * but I'm still seeing it in WPMU 1.2.1, so this is added just in case
 * people are using versions without the fix. It's a renamed copy of the 5463 change.
 */
if ( !function_exists('mobileAdmin_ob_end_flush_all') ) :
function mobileAdmin_ob_end_flush_all() 
{ 
	while ( @ob_end_flush() ); 
}
endif;
if ( version_compare(phpversion(), '5.2.0', '>=') ){
	add_action( 'shutdown', 'mobileAdmin_ob_end_flush_all', 1);
}
/* Workarounds for ob_start bug with PHP 5.2.x - End */
?>