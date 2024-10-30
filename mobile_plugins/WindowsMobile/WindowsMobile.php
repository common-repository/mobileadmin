<?php

class WindowsMobile_MobileAdmin extends MobileAdmin {
	
	/*
	 * Note: this was tested on WM5, on an HTC Wizard.
	 * YMMV, and please give feedback (or better yet, patches)
	 * if it doesn't seem to work right for you.
	 * 
	 * It's meant to work on the "One Column" as well as
	 * the "Default display modes.
	 */
	
	var $Name = "WindowsMobile";
	
	// Checking by regular expression here, for more complex detection
	// So these can be patterns, not just strings
	var $MatchingUserAgentPatterns = array('/Windows CE/mis');

	var $MatchingUserAgents = null; //array('Mozilla'); just for testing
	
	function GetCSSLinks() {

		$cssFiles = array('windowsmobile.css');

		return $this->GetLinks($cssFiles,dirname(__FILE__) . "/css/");
	}

	function GetScriptLinks() {

		// No script for this round
		/*
		$jsFiles = array('windowsmobile.js');

		return $this->GetLinks($jsFiles,dirname(__FILE__) . "/js/");
		*/
		return null;
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
$MAController->RegisterMobileAdminPlugin('WindowsMobile_MobileAdmin');

// To override form pages, plugins should extend the _Default counterparts (using the _Name convention) and make the appropriate changes
class MobileAdminDashboardForm_WindowsMobile extends MobileAdminDashboardForm_Default
{
		function FilterContent($content) { 

			$content = parent::FilterContent($content);
			
			// Create Incoming Links toggle
			$content = $this->StripOutElementById($content,'div','incominglinks');
			$content = $this->StripOutElementById($content,'h3','incominglinksToggle');

			return $content;
		}
}
class MobileAdminPostForm_WindowsMobile extends MobileAdminPostForm_Default
{
		function FilterContent($content) { 

			$content = parent::FilterContent($content);
			
			// Break PostTimestamp line
			$selectingPattern = '/@.*?<input type=\"text\" id=\"hh\"/mis';
			$replacement = '<br />@ <input type="text" id="hh"';
			$content = $this->ReplaceBySelectingPattern($content,$selectingPattern,$replacement);

			return $content;
		}
}
class MobileAdminPostNewForm_WindowsMobile extends MobileAdminPostNewForm_Default
{
		function FilterContent($content) { 

			$content = parent::FilterContent($content);
			
			// Break PostTimestamp line
			$selectingPattern = '/@.*?<input type=\"text\" id=\"hh\"/mis';
			$replacement = '<br />@ <input type="text" id="hh"';
			$content = $this->ReplaceBySelectingPattern($content,$selectingPattern,$replacement);

			return $content;
		}
}


?>