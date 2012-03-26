<?php
## =======================================================================        
##  newsletter.php        
## =======================================================================        
##  newsletter is the main switchbox for all functions related to the
##  newsletter object. 
##
##	it handles a) the creation of a new newsletter 
##                  -> display input for name
##					-> store name, create a new newsletter object
##					-> display the main page for the newsletter
##				b) modification of a newsletter
##					-> open an existing newsletter, call the subobjects
##					-> and display the overview page
##				c) deletion of a newsletter
##					-> calls all subobjects 
##					-> deletes the main object
##					-> displays confirmation page
##
##  TODO:   
##     - check if it works    
## =======================================================================
require("../framework.php");
require("functions/newsletter.php");

## get the usermodule
require_once("../../../user_modules.php");
require("../../functions/structure.php");	

require_once("../../matrix_frontend.php");

require_once("../../datatypes/linklist/linklist_editor.php");
require_once("../../datatypes/image/image.php");
require_once("../../datatypes/date/date.php");
require_once("../../datatypes/text/text.php");
require_once("../../datatypes/copytext/copytext.php");
require_once("../../datatypes/linklist/linklist.php");
require_once("../../datatypes/include/include.php");
require_once("../../datatypes/link/link.php");
require_once("../../datatypes/file/file.php");
require_once("../../datatypes/listview/listview.php");
require_once("../../datatypes/box/box.php");

## include the subobjects
require("functions/page.php");
require("functions/delivery.php");
require("functions/recipients.php");
require("functions/reports.php");
require("../clients/functions/groups.php");	




## we need to load the local language file
include("interface/lang/".$Auth->auth["language"].".php");

	## process the input vars
	$op = isset($_GET['op']) ? $_GET['op'] : (isset($_POST['op']) ? $_POST['op'] : '');

	## this is the main switchbox
	switch($op) {
		case "opens":
    		$newsletter_id = intval($_GET['id']);
			module_newsletter_reportsOpens($newsletter_id);
			break;	
		case "unsubscribes":
    		$newsletter_id = intval($_GET['id']);
			module_newsletter_reportsUnsubscribes($newsletter_id);
			break;			
		case "bounces":
    		$newsletter_id = intval($_GET['id']);
			module_newsletter_reportsBounces($newsletter_id);
			break;					
		case "link":
    		$newsletter_id = intval($_GET['id']);
			module_newsletter_reportsLink($newsletter_id);
			break;			
		case 'overview':
    	default:
    		$newsletter_id = intval($_GET['id']);
			module_newsletter_reportsOverview($newsletter_id);
      	break;
    }

## =======================================================================        
##  module_newsletter_reportsOverview        
## =======================================================================        
function module_newsletter_reportsOverview($newsletter_id) {
	global $gSession;

	## first get the info for this newsletter
	$newsletterInfo = newsletter_getNewsletter($newsletter_id);

	## get the number of opens for this newsletter
	$open_count = newsletter_reportsGetOpens($newsletter_id);
	
	## get the number links clicked 
	$links_clicked = newsletter_reportsGetTotalLinks($newsletter_id);
	
	## get the number links clicked 
	$unsubscribes = newsletter_reportsGetUnsubscribes($newsletter_id);
	
	## calculate the percentage of links clicked
	if($links_clicked['total_clients'] > 0 && $newsletterInfo['recipients'] > 0) {
		$links_percent = round(100/$newsletterInfo['recipients']*$links_clicked['total_clients'],2);
	}
	## we need to display the overview page
	$select_template = new Template("interface/");
	$select_template->set_templatefile(array("body" => "reports_overview.tpl"));

	$tabs = '';	
	$newsletterURL = $gSession->url('newsletter.php?op=edit&id='.$newsletter_id);
	$reportsURL = $gSession->url('reports.php?op=overview&id='.$newsletter_id);	
	
	$tabs .= ui_renderSectionTab('Campaign Content',$newsletterURL,0,1,true);
	$tabs .= ui_renderSectionTab('Reports',$reportsURL,1,1,false);	
	
	$select_template->set_var('TABS',$tabs);
	
	## prpeare the links at the bottom of the page
	$reportsURL = $gSession->url('reports.php?op=opens&id='.$newsletter_id);
	$select_template->set_var('opensURL',$reportsURL);
	
	$linkURL = $gSession->url('reports.php?op=link&id='.$newsletter_id);
	$select_template->set_var('linkURL',$linkURL);	
	
	$bouncesURL = $gSession->url('reports.php?op=bounces&id='.$newsletter_id);
	$select_template->set_var('bouncesURL',$bouncesURL);
	
	$unsubscribesURL = $gSession->url('reports.php?op=unsubscribes&id='.$newsletter_id);
	$select_template->set_var('unsubscribesURL',$unsubscribesURL);	
	
	## preare the graph url
	$graphURL = $gSession->url('graph.php?type=overview&id='.$newsletter_id);
	$select_template->set_var('graphURL',$graphURL);
	
	
	## fill out the data for this newsletter
	$select_template->set_var('newsletter_name',$newsletterInfo['name']);
	$select_template->set_var('newsletter_opens',$open_count);
	
	$select_template->set_var('newsletter_recipients_clicked',$links_clicked['total_clients']);
	$select_template->set_var('newsletter_recipients_clicked_percent',$links_percent);
	$select_template->set_var('newsletter_links_clicked',$links_clicked['total_links']);
	$select_template->set_var('newsletter_unsubscribes',$unsubscribes);
	$select_template->set_var('recipients_delivered',$newsletterInfo['recipients']);
	
	## prepare the date:
	$date = utility_prepareDate($newsletterInfo['created'],"d.m.Y H:i");
	
	$select_template->set_var('newsletter_summary',$newsletterInfo['recipients'].' recipients on '.$date);

	
	
	$select_template->pfill_block("body");
}

## =======================================================================        
##  portlets_promptDelete        
## =======================================================================        
function module_newsletter_reportsOpens($newsletter_id) {
	global $gSession;

	## first get the info for this newsletter
	$newsletterInfo = newsletter_getNewsletter($newsletter_id);
	

	## we need to display the overview page
	$select_template = new Template("interface/");
	$select_template->set_templatefile(array("body" => "reports_opens.tpl"));

	$tabs = '';	
	$newsletterURL = $gSession->url('newsletter.php?op=edit&id='.$newsletter_id);
	$reportsURL = $gSession->url('reports.php?op=overview&id='.$newsletter_id);	
	
	$tabs .= ui_renderSectionTab('Campaign Content',$newsletterURL,0,1,true);
	$tabs .= ui_renderSectionTab('Reports',$reportsURL,1,1,false);	
	
	$select_template->set_var('TABS',$tabs);
	
	## fill out the data for this newsletter
	$select_template->set_var('newsletter_name',$newsletterInfo['name']);
	
	## preare the graph url
	$graphURL = $gSession->url('graph.php?type=opens&id='.$newsletter_id);
	$select_template->set_var('graphURL',$graphURL);	
	
	## set the time
	$date = utility_prepareDate($newsletterInfo['created'],"d.m.Y H:i");
	$select_template->set_var('newsletter_summary','Sent on '.$date);
	
	## now preare the output of the tabluar data
	$raw_data = newsletter_reportsGetOpensOverTime($newsletter_id);

	## okay now we need to preare the data
	$data = array();
	$datax = array('12 am','1 am','2 am','3 am','4 am','5 am','6 am','7 am','8 am','9 am','10 am','11 am','12 pm','1 pm','2 pm','3 pm','4 pm','5 pm','6 pm','7 pm','8 pm','9 pm','10 pm','11 pm','12 pm');

	$tabular_data = '';
	for($i=0; $i<24; $i++) {
		if(isset($raw_data[$i]['opens'])) {
			$tabular_data .= '			<tr>
					<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top">'.$datax[$i].'</td>
					<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top">'.intval($raw_data[$i]['opens']).'</td>
					<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top">&nbsp;</td>				
				</tr>
				<tr>
					<td align="left" valign="top" colspan="6"><img src="interface/images/blank.gif" alt="" width="1" height="3" border="0"></td>
				</tr>
				<tr>
					<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top" colspan="5"><img src="interface/images/seperator.gif" alt="" width="555" height="1" border="0"></td>
				</tr>';
			}
	}
		
	$select_template->set_var('TABULARDATA',$tabular_data);
	
	
	
	$select_template->pfill_block("body");
}

## =======================================================================        
##  portlets_promptDelete        
## =======================================================================        
function module_newsletter_reportsLink($newsletter_id) {
	global $gSession;

	## first get the info for this newsletter
	$newsletterInfo = newsletter_getNewsletter($newsletter_id);
	
	## get the data
	$raw_data = newsletter_reportsGetLinksClicked($newsletter_id);
	
	## okay for each supplied page we need to get its name
	##foreach($raw_data as $current_page=>$current_data) {
		## we need to get the page name 
	##	$pageInfo = structure_getStructureID($current_page);
		
	##	$raw_data[$current_page]['pagename'] = $pageInfo['text'];
	##	$raw_data[$current_page]['url'] = getTargetURL($current_page);
	##}
	
	## we need to display the overview page
	$select_template = new Template("interface/");
	$select_template->set_templatefile(array("body" => "reports_link.tpl"));

	$tabs = '';	
	$newsletterURL = $gSession->url('newsletter.php?op=edit&id='.$newsletter_id);
	$reportsURL = $gSession->url('reports.php?op=overview&id='.$newsletter_id);	
	
	$tabs .= ui_renderSectionTab('Campaign Content',$newsletterURL,0,1,true);
	$tabs .= ui_renderSectionTab('Reports',$reportsURL,1,1,false);	
	
	$select_template->set_var('TABS',$tabs);
	
	## fill out the data for this newsletter
	$select_template->set_var('newsletter_name',$newsletterInfo['name']);
	
	## prepare the date:
	$date = utility_prepareDate($newsletterInfo['created'],"d.m.Y H:i");
	
	$select_template->set_var('newsletter_summary',$newsletterInfo['recipients'].' recipients on '.$date);

	## okay we have prepared all data now we will generate the output
	$tabular_data = '';
	foreach($raw_data as $current_element) {
		$tabular_data .= '<tr>
				<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top"><a href="'.$current_element['url'].'" target="_blank">'.$current_element['url'].'</a></td>
				<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top">'.$current_element['pagename'].'</td>
				<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top">'.$current_element['clicks'].'</td>				
			</tr>
			<tr>
				<td align="left" valign="top" colspan="6"><img src="interface/images/blank.gif" alt="" width="1" height="3" border="0"></td>
			</tr>
			<tr>
				<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
				<td align="left" valign="top" colspan="5"><img src="interface/images/seperator.gif" alt="" width="555" height="1" border="0"></td>
			</tr>';
	}	
	
	$select_template->set_var('TABULARDATA',$tabular_data);
	
	$select_template->pfill_block("body");
}

## =======================================================================        
##  portlets_promptDelete        
## =======================================================================        
function module_newsletter_reportsBounces($newsletter_id) {
	global $gSession;

	## first get the info for this newsletter
	$newsletterInfo = newsletter_getNewsletter($newsletter_id);
	
	## we need to display the overview page
	$select_template = new Template("interface/");
	$select_template->set_templatefile(array("body" => "reports_bounces.tpl"));

	$tabs = '';	
	$newsletterURL = $gSession->url('newsletter.php?op=edit&id='.$newsletter_id);
	$reportsURL = $gSession->url('reports.php?op=overview&id='.$newsletter_id);	
	
	$tabs .= ui_renderSectionTab('Campaign Content',$newsletterURL,0,1,true);
	$tabs .= ui_renderSectionTab('Reports',$reportsURL,1,1,false);	
	
	$select_template->set_var('TABS',$tabs);
	
	## fill out the data for this newsletter
	$select_template->set_var('newsletter_name',$newsletterInfo['name']);
	
	## prepare the date:
	$date = utility_prepareDate($newsletterInfo['created'],"d.m.Y H:i");
	
	$select_template->set_var('newsletter_summary',$newsletterInfo['recipients'].' recipients on '.$date);

	## set the time
	$date = utility_prepareDate($newsletterInfo['created'],"d.m.Y H:i");
	$select_template->set_var('newsletter_summary','Sent on '.$date);
	
	## preare the graph url
	$graphURL = $gSession->url('graph.php?type=bounces&id='.$newsletter_id);
	$select_template->set_var('graphURL',$graphURL);		
	
	## now preare the output of the tabluar data
	$raw_data = newsletter_reportsGetBouncesOverTime($newsletter_id);

	## okay now we need to preare the data
	$data = array();
	$datax = array('12 am','1 am','2 am','3 am','4 am','5 am','6 am','7 am','8 am','9 am','10 am','11 am','12 pm','1 pm','2 pm','3 pm','4 pm','5 pm','6 pm','7 pm','8 pm','9 pm','10 pm','11 pm','12 pm');

	$tabular_data = '';
	for($i=0; $i<24; $i++) {
		if(isset($raw_data[$i]['opens'])) {
			$tabular_data .= '			<tr>
					<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top">'.$datax[$i].'</td>
					<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top">'.intval($raw_data[$i]['opens']).'</td>
					<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top">&nbsp;</td>				
				</tr>
				<tr>
					<td align="left" valign="top" colspan="6"><img src="interface/images/blank.gif" alt="" width="1" height="3" border="0"></td>
				</tr>
				<tr>
					<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top" colspan="5"><img src="interface/images/seperator.gif" alt="" width="555" height="1" border="0"></td>
				</tr>';
			}
	}
		
	$select_template->set_var('TABULARDATA',$tabular_data);

	
	
	$select_template->pfill_block("body");
}


## =======================================================================        
##  portlets_promptDelete        
## =======================================================================        
function module_newsletter_reportsUnsubscribes($newsletter_id) {
	global $gSession;

	## first get the info for this newsletter
	$newsletterInfo = newsletter_getNewsletter($newsletter_id);
	
	## we need to display the overview page
	$select_template = new Template("interface/");
	$select_template->set_templatefile(array("body" => "reports_unsubscribes.tpl"));

	$tabs = '';	
	$newsletterURL = $gSession->url('newsletter.php?op=edit&id='.$newsletter_id);
	$reportsURL = $gSession->url('reports.php?op=overview&id='.$newsletter_id);	
	
	$tabs .= ui_renderSectionTab('Campaign Content',$newsletterURL,0,1,true);
	$tabs .= ui_renderSectionTab('Reports',$reportsURL,1,1,false);	
	
	$select_template->set_var('TABS',$tabs);
	
	## fill out the data for this newsletter
	$select_template->set_var('newsletter_name',$newsletterInfo['name']);
	
	## preare the graph url
	$graphURL = $gSession->url('graph.php?type=unsubscribes&id='.$newsletter_id);
	$select_template->set_var('graphURL',$graphURL);	
	
	## set the time
	$date = utility_prepareDate($newsletterInfo['created'],"d.m.Y H:i");
	$select_template->set_var('newsletter_summary','Sent on '.$date);
	
	## now preare the output of the tabluar data
	$raw_data = newsletter_reportsGetUnsubscribesOverTime($newsletter_id);
	## okay now we need to preare the data
	$data = array();
	$datax = array('12 am','1 am','2 am','3 am','4 am','5 am','6 am','7 am','8 am','9 am','10 am','11 am','12 pm','1 pm','2 pm','3 pm','4 pm','5 pm','6 pm','7 pm','8 pm','9 pm','10 pm','11 pm','12 pm');

	$tabular_data = '';
	for($i=0; $i<24; $i++) {
		if(isset($raw_data[$i]['opens'])) {
			$tabular_data .= '			<tr>
					<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top">'.$datax[$i].'</td>
					<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top">'.intval($raw_data[$i]['opens']).'</td>
					<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top">&nbsp;</td>				
				</tr>
				<tr>
					<td align="left" valign="top" colspan="6"><img src="interface/images/blank.gif" alt="" width="1" height="3" border="0"></td>
				</tr>
				<tr>
					<td align="left" valign="top"><img src="interface/images/blank.gif" alt="" width="25" height="1" border="0"></td>
					<td align="left" valign="top" colspan="5"><img src="interface/images/seperator.gif" alt="" width="555" height="1" border="0"></td>
				</tr>';
			}
	}
		
	$select_template->set_var('TABULARDATA',$tabular_data);
	
	
	
	$select_template->pfill_block("body");
}
?>
