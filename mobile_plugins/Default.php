<?php

class MobileAdmin 
{
	var $Name = "Default";
	
	var $MatchingUserAgents = array('2.0 MMP','240x320','AvantGo','BlackBerry',
		'Blazer','Cellphone','Danger','DoCoMo','Elaine/3.0','EudoraWeb',
		'hiptop','MMEF20','MOT-V','NetFront','Newt','Nokia','Opera Mini',
		'Palm','portalmmm','Proxinet','ProxiNet','SHARP-TQ-GX10','Small',
		'SonyEricsson','Symbian OS','SymbianOS','TS21i-10','UP.Browser',
		'UP.Link','Windows CE','WinWAP'
	);
	var $MatchingUserAgentPatterns = null;

	var $UseQuickTags = false;
	var $UseRichEdit = false;
	
	function GetCSSLinks() {

		$cssFiles = array('default.css');

		return $this->GetLinks($cssFiles,dirname(__FILE__) . "/");
	}
		
	function GetCurrentForm() {
	
		$uri = $_SERVER['REQUEST_URI'];
		
		if( MobileAdmin_CheckOverrideCookie() || strpos($uri, 'wp-admin') != false ) {
			
			if( strpos($uri, 'wp-admin/')==(strlen($uri)-strlen('wp-admin/')) ) {
				return $this->GetForm('MobileAdminDashboardForm');
			}
			else if( strpos($uri, 'wp-admin/index.php') != false ) {
				return $this->GetForm('MobileAdminDashboardForm');
			}
			else if( strpos($uri, 'wp-admin/post-new.php') != false ) {
				return $this->GetForm('MobileAdminPostNewForm');
			}
			else if( strpos($uri, 'wp-admin/post.php') != false ) {
				return $this->GetForm('MobileAdminPostForm');
			}
			else if( strpos($uri, 'wp-admin/moderation.php') != false ) {
				return $this->GetForm('MobileAdminCommentModerationForm');
			}
			else if( strpos($uri, 'wp-admin/edit.php') != false ) {
				return $this->GetForm('MobileAdminManageForm');
			}
			else if( strpos($uri, 'wp-admin/profile.php') != false ) {
				return $this->GetForm('MobileAdminProfileForm');
			}
			else {
				return $this->GetForm('MobileAdminForm');
			}
		}
		else {
			return null;
		}
	}
	
	function GetLinks($files,$basePath){
		
		$links = array();
		foreach ( $files as $file )
		{
			$fileDirPart = substr($basePath,strpos($basePath,'mobile_plugins'));
			$fileURL = get_bloginfo('wpurl') . '/' . PLUGINDIR . '/mobileadmin/' . $fileDirPart . $file;
			array_push($links,$fileURL);	
		}
		return $links;
	}
	
	function GetForm($name) {
		$formClassName =  $name . '_' . $this->Name;
		$form = null;
		if( class_exists($formClassName) ) {
			$form = new $formClassName();
		}
		else {
			$formClassName =  $name . '_Default';
			$form = new $formClassName();
		}
		$form->Controller = $this;
		return $form;
	}

	function GetScriptLinks() { return null; }
	
	function MatchesCurrentUserAgent() {
		
		// First check by patterns, if specified
		if( $this->MatchingUserAgentPatterns != null ) {
			if( $this->MatchesCurrentUserAgentByPatterns() ){
				return true;
			}
		}
		
		// Check by string parts
		if( $this->MatchingUserAgents != null ) {
			foreach ( $this->MatchingUserAgents as $ua ) {
				if ( strstr($_SERVER["HTTP_USER_AGENT"], $ua) ) {
					return true;
				}
			}
		}
		return false;
	}
	
	function MatchesCurrentUserAgentByPatterns() {
		// Checking by regular expression here, for more complex detection
		foreach ( $this->MatchingUserAgentPatterns as $ua ) {
			if ( preg_match($ua,$_SERVER["HTTP_USER_AGENT"]) ) {
				return true;
			}
		}
		return false;
	}

	function RenderHeaderTop() {
		?>
		<div id="mobilewphead"><a href="#"></a>
		<a href="<?php bloginfo('wpurl'); ?>/wp-admin/" title="Dashboard">Dashboard</a>
		<a href="<?php bloginfo('url') ?>" title="Home">Home</a>
		<h1><a href="<?php bloginfo('url') . '/'; ?>" title="<?php bloginfo('description'); ?>"><?php bloginfo('name'); ?></a></h1>
		</div>
		<?php
	}
}
$MAController->RegisterMobileAdminPlugin('MobileAdmin');

class MobileAdminForm_Default
{
	var $Controller = null;
	var $CSSFiles = null;
	
	function AddCustomScript() {
		echo '<meta name="viewport" content="maximum-scale=1.0,width=device-width,initial-scale=1.0" />';
		if( !$this->IsRecentVersion() ) {
			echo '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/' . PLUGINDIR .'/mobileadmin/jquery/jquery.js"></script>';
			echo '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/' . PLUGINDIR .'/mobileadmin/jquery/interface.js"></script>';
		}
		echo $this->GetAdminScriptMarkup();
	}

	function AlwaysIncludeJQuery() {
		if($this->IsRecentVersion()){
			wp_enqueue_script( 'interface' );
		}
	}

	function DisableRichEdit($wp_rich_edit) {
		$wp_rich_edit = $this->Controller->UseRichEdit;
		return $wp_rich_edit;
	}
	
	function FilterAdminCSS($csslink) {
		$csslink = $this->GetAdminCSSMarkup($csslink);
		return $csslink;
	}
	
	function FilterContent($content) { return $content; }

	function FilterContentCommon($content) { 
		
		// Remove normal menus since many mobile browsers do not support CSS,
		// so they can't be hidden that way
		$content = $this->StripOutAdminMenu($content);
		$content = $this->StripOutSubMenu($content);
		
		if(!$this->IsRecentVersion()){
			
			// wp_admin_css filter is new in 2.3, so older versions will need to replace the CSS another way
			$selectingPattern = '/<link rel="stylesheet.*?\/>/mis';
			$csslink = $this->GetAdminCSSMarkup();
			$content = $this->ReplaceBySelectingPattern($content,$selectingPattern,$csslink);
			
			// jQuery is an older version than what we'd like, so replace it with 1.1.4
			$selectingPattern = '/wp-includes\/js\/jquery\//mis';
			$content = $this->ReplaceBySelectingPattern($content,$selectingPattern,'');
			
		}
		return $content;
	}
	
	function FormatNavigationLink($text,$href) {
		return '<a href="' . $href . '">' . $text . '</a>';
	}

	function GetAdminCSSMarkup($csslink) {
		
		$csslink = '';
		
		if($this->Controller != null) {
			$cssFiles = $this->Controller->GetCSSLinks();
			
			if($cssFiles!=null) {
				foreach ($cssFiles as $cssFile)
				{
					$csslink .= '<link rel="stylesheet" href="' . $cssFile . '" type="text/css" media="screen" />';
				}
			}
		}
		return $csslink;
	}
	
	function GetAdminScriptMarkup() {
		
		$scriptMarkup = '';
		
		if($this->Controller != null) {
			$scriptFiles = $this->Controller->GetScriptLinks();
			
			if($scriptFiles!=null) {
				foreach ($scriptFiles as $scriptFile)
				{
					$scriptMarkup .= '<script type="text/javascript" src="' . $scriptFile . '"></script>';
				}
			}
		}
		return $scriptMarkup;
	}
	
	function GetSelectFormPattern() {
		return "/<form[^>]*?method=['\"]post['\"].*?<\/form>/mis";
	}

	function GetSelectTopPattern() {
		return $this->GetSelectElementByIdPattern('div','user_info');
	}
	
	function Initialize_RegExReplace() {
		ob_start( 'ob_gzhandler' );
		ob_start('MobileAdmin_RegExReplace');
	}
	
	function IsRecentVersion() {
		return get_bloginfo('version') > '2.2.3';
	}
	
	function RenderDebugInfo() { 
		global $MA, $MAController, $MobileAdminDebugMessages, $MobileAdminPlugins;
		
		if($this->IsRecentVersion()){
			echo '<strong>Recent version </strong><pre>' . $this->IsRecentVersion() . '</pre>';
		}
		else {
			echo '<strong>Version </strong><pre>' . get_bloginfo('version') . '</pre>';
		}
		echo '<strong>REQUEST_URI</strong><pre>' . $_SERVER['REQUEST_URI'] . '</pre>';
		echo '<strong>HTTP_USER_AGENT</strong><pre>' . $_SERVER['HTTP_USER_AGENT'] . '</pre>';
		
		if(isset($_COOKIE['MobileAdminOverride'])) {
			echo '<strong>MobileAdminOverride Cookie</strong><pre>' . $_COOKIE['MobileAdminOverride'] . '</pre>';
		}
		else {
			echo '<strong>MobileAdminOverride Cookie</strong><pre> - not set</pre>';
		}
		
		foreach ($MAController->Plugins as $plugin)
		{
			echo '<strong>Plugin name:</strong><pre>' . $plugin->Name . '</pre>';
		}

		echo '<strong>Current Plugin name:</strong><pre>' . $MA->Name . '</pre>';
		
		echo '<strong>Debug messages:</strong><br/>';
		foreach ($MobileAdminDebugMessages as $msg)
		{
			echo '<pre>' . $msg . '</pre>';
		}
		
	}
	
	function RenderAdditionalFormData() { 
		global $MobileAdminDebugMode;
		if($MobileAdminDebugMode || $this->DebugMode) {
			$this->RenderDebugInfo();
		}
	}

	function RenderCommonHeader() {
		global $user_identity;
		$this->Controller->RenderHeaderTop();
		?>
		<div id="mobilenav">
			<ul>
				<li><?php echo $this->FormatNavigationLink('Write','post-new.php'); ?></li>
				<li><?php echo $this->FormatNavigationLink('Comments','moderation.php'); ?></li>
				<li><?php echo $this->FormatNavigationLink('Posts','edit.php'); ?></li>
				<li><?php echo $this->FormatNavigationLink('Profile','profile.php'); ?></li>
			</ul>
			
		</div>
		<?php echo $this->RenderExtraHeaderInfo(); ?>

		<div id="ToggleMobileAdminView"></div>		
		<?php
	}

	function RenderFooterData() {
		$this->RenderCommonHeader();
		$this->RenderAdditionalFormData();
	}
	
	function RenderExtraHeaderInfo() {	}
	
	function ReplaceDefaultHeader($content) {

		$topSelectPattern = $this->GetSelectTopPattern();

		// Move nav to the top
		$mobileNavSelectPattern = $this->GetSelectElementByIdPattern('div','mobilenav');
		$content = $this->MoveAfter($mobileNavSelectPattern,$topSelectPattern,$content);

		// Move nav to the top
		$toggleSelectPattern = $this->GetSelectElementByIdPattern('div','dashboard-top');
		$content = $this->MoveAfter($toggleSelectPattern,$mobileNavSelectPattern,$content);
		
		// Move custom header above nav, then delete current one
		$mobileHeaderSelectPattern = $this->GetSelectElementByIdPattern('div','mobilewphead');
		$content = $this->MoveBefore($mobileHeaderSelectPattern,$mobileNavSelectPattern,$content);
		$content = $this->StripWpHead($content);
		
		return $content;		
	}

	function StripOutAdminMenu($content) {
		$content = $this->StripOutElementById($content,'ul','adminmenu');
		return $content;		
	}

	function StripOutSubMenu($content) {
		$content = $this->StripOutElementById($content,'ul','submenu');
		return $content;		
	}

	function StripWpHead($content) { 
		$content = $this->StripOutElementById($content,'div','wphead');
		return $content;		
	}

	function StripUserInfo($content) { 
		$content = $this->StripOutElementById($content,'div','user_info');
		return $content;		
	}


	/* RegEx Utility Functions - Begin */
	function GetElementByClassName($content,$tagName,$className) {
		$selectingPattern = $this->GetSelectElementByClassNamePattern($tagName,$className);
		$matches = $this->GetMatches($selectingPattern,$content);
		
		// TODO: Bounds checking
		return $matches[0][0];
	}

	function GetElementById($content,$tagName,$id) {
		$selectingPattern = $this->GetSelectElementByIdPattern($tagName,$id);
		$matches = $this->GetMatches($selectingPattern,$content);
		
		// TODO: Bounds checking
		return $matches[0][0];
	}

	function GetMatches($pattern,$content) {
		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
		return $matches;
	}

	function GetSelectElementByClassNamePattern($tagName,$className) {
		// TODO: fix this to support multiple class names
		return "/<". $tagName . "[^>]*?class=['\"]" . $className . "['\"].*?<\/" . $tagName . ">/mis";
	}

	function GetSelectElementByIdPattern($tagName,$id) {
		return "/<". $tagName . "[^>]*?id=['\"]" . $id . "['\"].*?<\/" . $tagName . ">/mis";
	}

	function MoveAfter($elementToMoveSelectingPattern,$moveAfterSelectingPattern,$content) {

		$elementToMoveMatches = $this->GetMatches($elementToMoveSelectingPattern,$content);
		$elementToMove = $elementToMoveMatches[0][0];
		
		$moveAfterMatches =  $this->GetMatches($moveAfterSelectingPattern,$content);
		$moveAfter = $moveAfterMatches[0][0];
		
		// Remove element from content
		$content = $this->ReplaceBySelectingPattern($content,$elementToMoveSelectingPattern, '');
		
		// Insert After
		$content = $this->ReplaceBySelectingPattern($content,$moveAfterSelectingPattern, $moveAfter . $elementToMove );
		
		return $content;
	}

	function MoveBefore($elementToMoveSelectingPattern,$moveBeforeSelectingPattern,$content) {

		$elementToMoveMatches = $this->GetMatches($elementToMoveSelectingPattern,$content);
		$elementToMove = $elementToMoveMatches[0][0];
		
		$moveBeforeMatches =  $this->GetMatches($moveBeforeSelectingPattern,$content);
		$moveBefore = $moveBeforeMatches[0][0];
		
		// Remove element from content
		$content = $this->ReplaceBySelectingPattern($content,$elementToMoveSelectingPattern, '');
		
		// Insert Before
		$content = $this->ReplaceBySelectingPattern($content,$moveBeforeSelectingPattern, $elementToMove . $moveBefore);
		
		return $content;
	}

	function ReplaceBySelectingPattern($content,$selectingPattern,$replacementValue) {
		$content = preg_replace($selectingPattern, $replacementValue, $content);
		return $content;		
	}

	function ReplaceElementByClassName($content,$tagName,$className,$replacementValue) {
		$selectingPattern = $this->GetSelectElementByClassNamePattern($tagName,$className);
		return $this->ReplaceBySelectingPattern($content,$selectingPattern, $replacementValue);
	}

	function ReplaceElementById($content,$tagName,$id,$replacementValue) {
		$selectingPattern = $this->GetSelectElementByIdPattern($tagName,$id);
		return $this->ReplaceBySelectingPattern($content,$selectingPattern, $replacementValue);
	}

	function StripOutElementById($content,$tagName,$id) {
		$replacementValue = "";
		return $this->ReplaceElementById($content,$tagName,$id,$replacementValue);
	}

	function StripOutElementByClassName($content,$tagName,$className) {
		$replacementValue = "";
		return $this->ReplaceElementByClassName($content,$tagName,$className,$replacementValue);
	}

	/* RegEx Utility Functions - End */
	
}

class MobileAdminCommentModerationForm_Default extends MobileAdminForm_Default
{
	function FilterContent($content) {
		$content = $this->FilterContentCommon($content);

		$content = $this->ReplaceDefaultHeader($content);

		return $content;
	}
	
	function FormatNavigationLink($text,$href) {
		if($text=="Comments") {
			return '<a class="activeNavLink" href="' . $href . '">' . $text . '</a>';
		}
		else {
			return parent::FormatNavigationLink($text,$href);
		}
	}
	
}

class MobileAdminDashboardForm_Default extends MobileAdminForm_Default
{
	function FilterContent($content) { 

		$content = $this->FilterContentCommon($content);

		$content = $this->ReplaceDefaultHeader($content);

		// Strip out uneccesary items
		$content = $this->StripOutAdminMenu($content);
		$content = $this->StripOutSubMenu($content);

		$content = $this->StripOutElementById($content,'div','devnews');
		$content = $this->StripOutElementById($content,'div','planetnews');
		
		// Strip out remaining end stuff from dashboard page
		$selectingPattern = "/<p>Use these links.*?forums<\/a>\.<\/p>/mis";
		$content = $this->ReplaceBySelectingPattern($content,$selectingPattern,"");
		
		// Take out link to comments page in Comments heading
		$selectingPattern = "/<h3>Comments.*?<\/h3>/mis";
		$content = $this->ReplaceBySelectingPattern($content,$selectingPattern,"<h3 class='ToggleLink'>Comments</h3>");
		
		// Take out link to comments page in Comments heading
		$selectingPattern = "/<h3>Posts.*?<\/h3>/mis";
		$content = $this->ReplaceBySelectingPattern($content,$selectingPattern,"<h3 class='ToggleLink'>Posts</h3>");

		// Add class to Blog Stats heading
		$selectingPattern = "/<h3>Blog Stats.*?<\/h3>/mis";
		$content = $this->ReplaceBySelectingPattern($content,$selectingPattern,"<h3 class='ToggleLink'>Blog Stats</h3>");

		// Create Incoming Links toggle
		$selectingPattern = $this->GetSelectElementByIdPattern('div','incominglinks');
		$content = $this->ReplaceBySelectingPattern($content,$selectingPattern,"<h3 id='incominglinksToggle' class='ToggleLink'>Incoming Links</h3><div id='incominglinks'></div>");

		// Add class to div wrap
		$selectingPattern = '/<div class="wrap">/mis';
		$content = $this->ReplaceBySelectingPattern($content,$selectingPattern,'<div class="dashboard wrap">');
		
		
		// Add toggle link to switch back to normal admin view
		$content = $this->ReplaceElementById($content,'div','ToggleMobileAdminView', 
			'<form method="post"><input type="submit" id="ToggleMobileAdminView" name="ToggleMobileAdminView" method="post" value="Revert to Normal Admin View" /></form>');

		
		return $content; 
	}	
		
}

class MobileAdminManageForm_Default extends MobileAdminForm_Default
{
	function DeTableizeMagagementList($content) {
		
		// Rows
		$output = '';
		$rowSelectingPattern = "/<tr.*?(class=?.*?|.*?)>.*?<\/tr>/mis";
		$rows = array();
		preg_match_all($rowSelectingPattern, $content, $rows);
		
		$rowCount = count($rows[0]); 
		
		$replacementOutput = "";
		for($i = 1; $i < $rowCount; $i++) {

			$rowContent = $rows[0][$i];
			$rowMetaData = $rows[1][$i];

			// Begin "row"
			$replacementOutput .= "<div".$rowMetaData.">";
			
			// Cells within row
			$cellSelectingPattern = "/<(th|td).*?(class=?.*?|.*?)>.*?<\/(th|td)>/mis";
			$cells = array();
			preg_match_all($cellSelectingPattern, $rowContent, $cells);
			
			// ID
			$replacementOutput .= "<span class=\"ID\">";
				$replacementOutput .= $this->GetCellInnerHTML($cells[0][0]);
			$replacementOutput .= "</span>";

			// PostName
			$replacementOutput .= "<span class=\"PostName\">";
				$replacementOutput .= $this->GetCellInnerHTML($cells[0][2]);
			$replacementOutput .= "</span>";

			// Comment Count
			$replacementOutput .= "<span class=\"Comments\">";
				$replacementOutput .= "(" . trim($this->GetCellInnerHTML($cells[0][4])) . " comments)";
			$replacementOutput .= "</span>";

			$replacementOutput .= "<br />";
			
			// View, Edit, Delete Links
			$replacementOutput .= "<span class=\"ManageActionLink View\">";
				$replacementOutput .= trim($this->GetCellInnerHTML($cells[0][6]));
			$replacementOutput .= "</span>";
			$replacementOutput .= "<span class=\"ManageActionLink Edit\">";
				$replacementOutput .= trim($this->GetCellInnerHTML($cells[0][7]));
			$replacementOutput .= "</span>";
						$replacementOutput .= "<span class=\"ManageActionLink Delete\">";
				$replacementOutput .= trim($this->GetCellInnerHTML($cells[0][8]));
			$replacementOutput .= "</span>";

			// End "row"
			$replacementOutput .= "</div>";
		}
		
		// Replace the table with the new content
		$selectingPattern = "/<table[^>]*?class=['\"]widefat['\"].*?>.*?<\/table>/mis";
		$replacementValue = "<div class=\"ManageTable\">". $replacementOutput . "</div>";
		$content = $this->ReplaceBySelectingPattern($content,$selectingPattern,$replacementValue);
		
		return $content;
	}

	function GetCellInnerHTML($cellHTML) {
		$cellContentSelectingPattern = "/<(th|td).*?(class=?.*?|.*?)>(.*).*?<\/(th|td)>/mis";
		$cellContents = array();
		preg_match_all($cellContentSelectingPattern, $cellHTML, $cellContents);
		return $cellContents[3][0];
	}
	
	function FilterContent($content) {
		$content = $this->FilterContentCommon($content);

		$content = $this->ReplaceDefaultHeader($content);

		$content = $this->DeTableizeMagagementList($content);
		
		return $content;
	}
	
	function FormatNavigationLink($text,$href) {
		if($text=="Posts") {
			return '<a class="activeNavLink" href="' . $href . '">' . $text . '</a>';
		}
		else {
			return parent::FormatNavigationLink($text,$href);
		}
	}
}

class MobileAdminPostForm_Default extends MobileAdminForm_Default
{
	function FilterContent($content) { 
		
		$content = $this->FilterCommonPostContent($content);

		return $content; 
	}	

	function FilterCommonPostContent($content) { 
		
		$content = $this->FilterContentCommon($content);

		$content = $this->ReplaceDefaultHeader($content);
	
		if(!$this->Controller->UseQuickTags) {
			$content = $this->StripOutElementById($content,'div','quicktags');
		}

		$content = $this->StripUploadFrame($content);
		$content = $this->StripTrackbacks($content);
		$content = $this->StripCustomFields($content);
				
		$content = $this->MovePostMetaItemsBelowPostText($content);

		return $content;
	}

	function FormatNavigationLink($text,$href) {
		if($text=="Write") {
			return '<a class="activeNavLink" href="' . $href . '">' . $text . '</a>';
		}
		else {
			return parent::FormatNavigationLink($text,$href);
		}
	}

	function MovePostMetaItemsBelowPostText($content) {
		$submitParagraphSelectPattern = $this->GetSelectElementByClassNamePattern('p','submit');

		// Post Status
		$poststatusSelectPattern = $this->GetSelectElementByIdPattern('fieldset','poststatusdiv');
		$content = $this->MoveBefore($poststatusSelectPattern,$submitParagraphSelectPattern,$content);

		// Categories
		$categorydivSelectPattern = $this->GetSelectElementByIdPattern('fieldset','categorydiv');
		$content = $this->MoveBefore($categorydivSelectPattern,$submitParagraphSelectPattern,$content);

		// Discussion
		$commentstatusdivSelectPattern = $this->GetSelectElementByIdPattern('fieldset','commentstatusdiv');
		$content = $this->MoveBefore($commentstatusdivSelectPattern,$submitParagraphSelectPattern,$content);

		// Post Slug
		$slugdivSelectPattern = $this->GetSelectElementByIdPattern('fieldset','slugdiv');
		$content = $this->MoveAfter($slugdivSelectPattern,$submitParagraphSelectPattern,$content);


		// Post Password
		$passworddivSelectPattern = $this->GetSelectElementByIdPattern('fieldset','passworddiv');
		$content = $this->MoveAfter($passworddivSelectPattern,$submitParagraphSelectPattern,$content);

		// Post Timestamp
		$posttimestampdivSelectPattern = "/<fieldset[^>]*?id=['\"]posttimestampdiv['\"].*?<\/fieldset>.*?<\/div>.*?<\/fieldset>/mis";
		$content = $this->MoveAfter($posttimestampdivSelectPattern,$submitParagraphSelectPattern,$content);


		return $content;		
	}

	function StripCustomFields($content) { 
		return $this->StripOutElementById($content,'fieldset','postcustom');
	}
	function StripPostExcerpt($content) { 
		return $this->StripOutElementById($content,'fieldset','postexcerpt');
	}
	function StripPostPassword($content) { 
		return $this->StripOutElementById($content,'fieldset','passworddiv');
	}
	function StripPostSlug($content) { 
		return $this->StripOutElementById($content,'fieldset','slugdiv');
	}
	function StripPostTimeStamp($content) { 

		// The Posttimestamp fieldset has a nested fieldset, which makes this a bit more complicated
		$selectingPattern = "/<fieldset[^>]*?id=['\"]posttimestampdiv['\"].*?<\/fieldset>.*?<\/div>.*?<\/fieldset>/mis";

		return $this->ReplaceBySelectingPattern($content,$selectingPattern,'');
	}
	function StripTrackbacks($content) { 
		return $this->StripOutElementById($content,'fieldset','trackbacksdiv');
	}
	function StripUploadFrame($content) { 
		return $this->StripOutElementById($content,'iframe','uploading');
	}
}

class MobileAdminPostNewForm_Default extends MobileAdminPostForm_Default
{
	function FilterContent($content) { 
		
		$content = $this->FilterCommonPostContent($content);

		return $content; 
	}	

}

class MobileAdminProfileForm_Default extends MobileAdminForm_Default
{
	function FilterContent($content) {
		$content = $this->FilterContentCommon($content);

		$content = $this->ReplaceDefaultHeader($content);

		// Strip out visual editor option and first update profile button
		$selectingPattern = "/<h3>Personal.*?name=\"submit\" \/><\/p>/mis";
		$content = $this->ReplaceBySelectingPattern($content,$selectingPattern,"");
		
		return $content;
	}
	
	function FormatNavigationLink($text,$href) {
		if($text=="Profile") {
			return '<a class="activeNavLink" href="' . $href . '">' . $text . '</a>';
		}
		else {
			return parent::FormatNavigationLink($text,$href);
		}
	}
	
}

?>