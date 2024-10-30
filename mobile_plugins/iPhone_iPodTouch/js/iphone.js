var $j = jQuery;
var CollapseExpandDelayMS = 400;

function Initialize() {
	CollapseDashboardSections();
	CollapseExtraPostSections();
	CollapseSearchTermsForm();
    setTimeout(updateLayout, 0);
}

function CollapseSearchTermsForm() {

	$j('#searchform').each(
		function(index,searchform) {

			$j(searchform).before('<h3 id="SearchFiltersToggleLink" class="dbx-handle ToggleLink">Search Filters</h3>');
		
			var handle = $j('#SearchFiltersToggleLink');
		
			$j(handle).bind("click",{itemToToggle: searchform,toggleHandle: handle[0]},ToggleItem);
			
			// Collapse by default
			CollapseItem(searchform,handle[0]);
		}
	);
}

function CollapseExtraPostSections() {

	$j('fieldset.dbx-box').each( 
		function (index,box) {
			if(box!=null) {
		
				var handles = box.getElementsByTagName('h3');
		
				if(handles!=null) {
					var handle = handles[0];
					 	
		 	        $j(handle).addClass("ToggleLink");
			        
					var content = $j('#' + box.id + ' .dbx-content');
					if(content!=null) {
		
						content = content[0];
		
						$j(handle).bind("click",{itemToToggle: content,toggleHandle: handle},ToggleItem);
						
						// Collapse by default
						CollapseItem(content,handle);
					}
				}
			}
		}
	);
}

function CollapseDashboardSections() {

	$j('div.dashboard h3.ToggleLink').each( 
		function (index,heading) {
			
			// Get corresponding div
			var parent = null;
			
			if(heading.id=="incominglinksToggle") {
				parent = $j("#incominglinks");
			}
			else {
				parent = $j(heading).parent();	
				
				// Move Heading out of parent
				parent[0].removeChild(heading);
				parent[0].parentNode.insertBefore(heading,parent[0]);
			}
			
			if(parent!=null) {
				$j(heading).bind("click",{itemToToggle: parent[0],toggleHandle: heading},ToggleItem);
				
				// Collapse by default
				CollapseItem(parent[0],heading);
			}
		}
	);
}

/* Shared functions for collapsing / expanding - BEGIN */
function ToggleItem(event) {
	
	var item = event.data.itemToToggle;
	var handle = event.data.toggleHandle;
	
	if(item!=null) {
		if(item.className.indexOf('collapsed')!=-1) {
			ExpandItem(item,handle);
		}
		else {
			CollapseItem(item,handle);
		}
		return false;
	}
}

function CollapseItem(itemToToggle,toggleHandle) {
	
	var item = $j(itemToToggle);
	var handle = $j(toggleHandle);
	
	if(item!=null) {

		item.hide(CollapseExpandDelayMS);
 	    item.addClass("collapsed");

		if(handle!=null) {
 		    handle.addClass("collapsed");
		}
	}
}
function ExpandItem(itemToToggle,toggleHandle) {
	
	var item = $j(itemToToggle);
	var handle = $j(toggleHandle);
	
	if(item!=null) {

		item.show(CollapseExpandDelayMS);
 	    item.removeClass("collapsed");

		if(handle!=null) {
 		    handle.removeClass("collapsed");
		}
	}
}
/* Shared functions for collapsing / expanding - END */

/* Adjusting for screen rotation - BEGIN */
var currentWidth = 0;
function updateLayout()
{
    if (window.innerWidth != currentWidth)
    {
        currentWidth = window.innerWidth;

        var orient = currentWidth == 320 ? "profile" : "landscape";
        document.body.setAttribute("orient", orient);
        setTimeout(function()
        {
            window.scrollTo(0, 1);
        }, 100);            
    }
}
//setInterval(updateLayout, 400);
/* Adjusting for screen rotation - END */

jQuery(document).ready(Initialize);