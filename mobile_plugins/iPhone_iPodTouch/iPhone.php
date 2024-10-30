<?php

class iPhone_MobileAdmin extends MobileAdmin {
	
	var $Name = "iPhone";
	
	var $MatchingUserAgents = array('iPhone','iPod');
	
	// Rich Editor not supported due to complications with iPhone Safari
	var $UseQuickTags = false;
	var $UseRichEdit = false;
	
	function GetCSSLinks() {

		$cssFiles = array('iphone.css');

		return $this->GetLinks($cssFiles,dirname(__FILE__) . "/css/");
	}

	function GetScriptLinks() {

		$jsFiles = array('iphone.js');

		return $this->GetLinks($jsFiles,dirname(__FILE__) . "/js/");
	}
	
	function RenderHeaderTop() {
		
		$imageFiles = array('dashboard.png','site.png');
		$imageLinks = $this->GetLinks($imageFiles,dirname(__FILE__) . "/images/");
		
		?>
		<div id="mobilewphead"><a href="#"></a>
		<a href="<?php bloginfo('wpurl'); ?>/wp-admin/" title="Dashboard"><img class="dashboard" src="<?php echo $imageLinks[0]; ?>"></a>
		<a href="<?php bloginfo('url') ?>" title="Home"><img class="home" src="<?php echo $imageLinks[1]; ?>"></a>
		<h1><a href="<?php bloginfo('url') . '/'; ?>" title="<?php bloginfo('description'); ?>"><?php bloginfo('name'); ?></a></h1>
		</div>
		<?php
	}
	
}
$MAController->RegisterMobileAdminPlugin('iPhone_MobileAdmin');

// To override form pages, plugins should extend the _Default counterparts (using the _Name convention) and make the appropriate changes
class MobileAdminDashboardForm_iPhone extends MobileAdminDashboardForm_Default
{
	function RenderExtraHeaderInfo() {
		$imageFiles = array('logo.png');
		$imageLinks = $this->Controller->GetLinks($imageFiles,dirname(__FILE__) . "/images/");
		?>
		<div id="dashboard-top"><img class="logo" src="<?php echo $imageLinks[0]; ?>"></div>
		<?php
	}
}
?>