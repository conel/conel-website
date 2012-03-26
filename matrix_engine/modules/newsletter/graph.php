<?php
	## =======================================================================
	##  module.php														
	## =======================================================================
	##  Version: 		0.02													
	##  Last change: 	12.02.2005												
	##  by: 			S. Elsner											
	## =======================================================================
	##
	##  this is the main statistic file- it's a switchbox, checks access rights
	##  and routes the users to the correct information
	## =======================================================================
	require("../framework.php");
	require("functions/newsletter.php");
		
	## include the subobjects
	require("functions/page.php");
	require("functions/delivery.php");
	require("functions/recipients.php");
	require("functions/reports.php");
	require("../clients/functions/groups.php");	
	
	## get the usermodule
	require("../../functions/structure.php");	
	
	require_once("../../matrix_frontend.php");

	##require('classes/class_html_calendar.php');
	##require('classes/class_html_pager.php');	
	
	require(ENGINE."/modules/statistik/jpgraph/jpgraph.php");
	require(ENGINE."/modules/statistik/jpgraph/jpgraph_pie.php");
	require(ENGINE."/modules/statistik/jpgraph/jpgraph_pie3d.php");
	require(ENGINE."/modules/statistik/jpgraph/jpgraph_line.php");
	require(ENGINE."/modules/statistik/jpgraph/jpgraph_bar.php");	


	## okay depending on the command we need to get the data and
	## display different graphs
	$type = $_GET['type'];
	$newsletter_id = intval($_GET['id']);
	
	switch($type) {
		case 'overview':
			## okay first get the data
			newsletter_reports_GraphOverview($newsletter_id);
			exit;
			break;
		case 'opens':
			## okay first get the data
			newsletter_reports_GraphOpens($newsletter_id);
			exit;	
			break;	
		case 'bounces':
			## okay first get the data
			newsletter_reports_GraphBounces($newsletter_id);
			exit;	
			break;		
		case 'unsubscribes':
			## okay first get the data
			newsletter_reports_GraphUnsubscribes($newsletter_id);
			exit;	
			break;				
	}
	
	## =======================================================================        
	## newsletter_reports_GraphOverview   
	## =======================================================================        
	## updates the newsletter's name
	##    
	## =======================================================================	
	function newsletter_reports_GraphOverview($newsletter_id) {
		## first get the info for this newsletter
		$newsletterInfo = newsletter_getNewsletter($newsletter_id);
	
		## get the number of opens for this newsletter
		$open_count = newsletter_reportsGetOpens($newsletter_id);

		$unopend = $newsletterInfo['recipients'] - $open_count;
	
		## gest we need to get the data
		$legend = array('Opened','Unopend','Bounced');
	
		$data = array($open_count,0,$unopend);

		$graph = new PieGraph(200, 200, 'auto');
		$graph->SetAntiAliasing(true);
	
		##$graph->legend->Pos(0.3, 0.9);
		$graph->legend->SetLayout(LEGEND_VERT);
		$graph->legend->Pos(0,0,"center","bottom");
		$graph->legend->SetColumns(8);
	
		$graph->legend->shadow=false;
		$graph->legend->fill_color=array(255,255,255);
		$graph->legend->color=array(255,255,255);
		$graph->legend->font_size=12;
		$graph->legend->SetFont(FF_MYRIAD,FS_BOLD,7);
		$graph->SetFrame(false);
		$plot = new PiePlot3d($data);
		
		$plot->SetTheme('earth');
		$plot->SetSliceColors(array('002lime','001aqua','003teal'));
		$plot->SetAngle(85);
	
		$plot->value->SetFont(FF_MYRIAD,FS_BOLD,11);
		$plot->value->SetColor('black');
		$graph->SetAntiAliasing();
		$plot->SetLegends($legend);
	
		$graph->Add($plot);
		$graph->Stroke();	
	}
 
	## =======================================================================        
	## newsletter_reports_GraphOpens  
	## =======================================================================        
	## updates the newsletter's name
	##    
	## =======================================================================	
	function newsletter_reports_GraphOpens($newsletter_id) {
		## first get the info for this newsletter
		$newsletterInfo = newsletter_getNewsletter($newsletter_id);
	
		## get the number of opens for this newsletter
		$raw_data = newsletter_reportsGetOpensOverTime($newsletter_id);

		## okay now we need to preare the data
		$data = array();
		for($i=0; $i<24; $i++) {
			$data[$i] = isset($raw_data[$i]['opens']) ? $raw_data[$i]['opens'] : 0;
		}
		
		## we need to generate sopme reaosnable data for a newsletter
		$datax = array('12 am','1 am','2 am','3 am','4 am','5 am','6 am','7 am','8 am','9 am','10 am','11 am','12 pm','1 pm','2 pm','3 pm','4 pm','5 pm','6 pm','7 pm','8 pm','9 pm','10 pm','11 pm','12 pm');
 		$graph = new Graph(515, 200, 'auto');
	
		$graph->img->SetMargin(35, 105, 10, 30);
		$graph->SetMarginColor(array(255,255,255));
		$graph->SetFrame(false);

		$graph->SetScale('textlin');
		$graph->xaxis->SetTickLabels($datax);
		$graph->xaxis->SetTextLabelInterval(2);	
		$graph->yaxis->HideTicks(true,false);
		$graph->yaxis->HideLine();
		$graph->SetMargin(40,10,20,20); 
		
		$visitors_plot = new BarPlot($data);
		$visitors_plot->SetColor('003teal');
		
		$visitors_plot->SetWeight(0);
		$visitors_plot->SetWidth(0.70);
		$visitors_plot->value->SetFont(FF_MYRIAD,FS_BOLD,11);
		$visitors_plot->value->SetColor("black");
		$graph->xaxis->SetFont(FF_MYRIAD,FS_BOLD,9); 
		$graph->yaxis->SetFont(FF_MYRIAD,FS_NORMAL,9);
		
		$visitors_plot->SetFillColor("003teal");

		$graph->Add($visitors_plot);
		$graph->legend->shadow=false;
		$graph->SetFrame(false);
		$graph->legend->Hide(); 
    	$graph->legend->fill_color=array(255,255,255);	
    	$graph->legend->SetFont(FF_MYRIAD,FS_BOLD,11);
		$graph->Stroke();	
		
	}
	
 
	## =======================================================================        
	## newsletter_reports_GraphOpens  
	## =======================================================================        
	## updates the newsletter's name
	##    
	## =======================================================================	
	function newsletter_reports_GraphBounces($newsletter_id) {
		## first get the info for this newsletter
		$newsletterInfo = newsletter_getNewsletter($newsletter_id);
	
		## get the number of opens for this newsletter
		$raw_data = newsletter_reportsGetBouncesOverTime($newsletter_id);

		## okay now we need to preare the data
		$data = array();
		for($i=0; $i<24; $i++) {
			$data[$i] = isset($raw_data[$i]['opens']) ? $raw_data[$i]['opens'] : 0;
		}
		
		## we need to generate sopme reaosnable data for a newsletter
		$datax = array('12 am','1 am','2 am','3 am','4 am','5 am','6 am','7 am','8 am','9 am','10 am','11 am','12 pm','1 pm','2 pm','3 pm','4 pm','5 pm','6 pm','7 pm','8 pm','9 pm','10 pm','11 pm','12 pm');
 		$graph = new Graph(515, 200, 'auto');
	
		$graph->img->SetMargin(35, 105, 10, 30);
		$graph->SetMarginColor(array(255,255,255));
		$graph->SetFrame(false);

		$graph->SetScale('textlin');
		$graph->xaxis->SetTickLabels($datax);
		$graph->xaxis->SetTextLabelInterval(2);	
		$graph->yaxis->HideTicks(true,false);
		$graph->yaxis->HideLine();
		$graph->SetMargin(40,10,20,20); 
		
		$visitors_plot = new BarPlot($data);
		$visitors_plot->SetColor('003teal');
		
		$visitors_plot->SetWeight(0);
		$visitors_plot->SetWidth(0.70);
		$visitors_plot->value->SetFont(FF_MYRIAD,FS_BOLD,11);
		$visitors_plot->value->SetColor("black");
		$graph->xaxis->SetFont(FF_MYRIAD,FS_BOLD,9); 
		$graph->yaxis->SetFont(FF_MYRIAD,FS_NORMAL,9);
		
		$visitors_plot->SetFillColor("003teal");

		$graph->Add($visitors_plot);
		$graph->legend->shadow=false;
		$graph->SetFrame(false);
		$graph->legend->Hide(); 
    	$graph->legend->fill_color=array(255,255,255);	
    	$graph->legend->SetFont(FF_MYRIAD,FS_BOLD,11);
		$graph->Stroke();	
		
	}
	
 
	## =======================================================================        
	## newsletter_reports_GraphOpens  
	## =======================================================================        
	## updates the newsletter's name
	##    
	## =======================================================================	
	function newsletter_reports_GraphUnsubscribes($newsletter_id) {
		## first get the info for this newsletter
		$newsletterInfo = newsletter_getNewsletter($newsletter_id);
	
		## get the number of opens for this newsletter
		$raw_data = newsletter_reportsGetUnsubscribesOverTime($newsletter_id);

		## okay now we need to preare the data
		$data = array();
		for($i=0; $i<24; $i++) {
			$data[$i] = isset($raw_data[$i]['opens']) ? $raw_data[$i]['opens'] : 0;
		}
		
		## we need to generate sopme reaosnable data for a newsletter
		$datax = array('12 am','1 am','2 am','3 am','4 am','5 am','6 am','7 am','8 am','9 am','10 am','11 am','12 pm','1 pm','2 pm','3 pm','4 pm','5 pm','6 pm','7 pm','8 pm','9 pm','10 pm','11 pm','12 pm');
 		$graph = new Graph(515, 200, 'auto');
	
		$graph->img->SetMargin(35, 105, 10, 30);
		$graph->SetMarginColor(array(255,255,255));
		$graph->SetFrame(false);

		$graph->SetScale('textlin');
		$graph->xaxis->SetTickLabels($datax);
		$graph->xaxis->SetTextLabelInterval(2);	
		$graph->yaxis->HideTicks(true,false);
		$graph->yaxis->HideLine();
		$graph->SetMargin(40,10,20,20); 
		
		$visitors_plot = new BarPlot($data);
		$visitors_plot->SetColor('003teal');
		
		$visitors_plot->SetWeight(0);
		$visitors_plot->SetWidth(0.70);
		$visitors_plot->value->SetFont(FF_MYRIAD,FS_BOLD,11);
		$visitors_plot->value->SetColor("black");
		$graph->xaxis->SetFont(FF_MYRIAD,FS_BOLD,9); 
		$graph->yaxis->SetFont(FF_MYRIAD,FS_NORMAL,9);
		
		$visitors_plot->SetFillColor("003teal");

		$graph->Add($visitors_plot);
		$graph->legend->shadow=false;
		$graph->SetFrame(false);
		$graph->legend->Hide(); 
    	$graph->legend->fill_color=array(255,255,255);	
    	$graph->legend->SetFont(FF_MYRIAD,FS_BOLD,11);
		$graph->Stroke();	
		
	}	
?>
