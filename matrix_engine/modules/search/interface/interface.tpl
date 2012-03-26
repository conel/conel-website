<!-- BEGIN body -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">

<html lang="de">
	<head>
	<title>elsner statistics</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta name="MSSmartTagsPreventParsing" content="true">
	<meta name="description" content="elsner.elsner">
	<meta name="keywords" content="elsner">
	<meta name="robots" content="index,FOLLOW">
	<script src="/matrix_engine/interface/script/jquery.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="../../interface/layout.css">
	
	<style>
	.tab {
	font-family: Trebuchet,Trebuchet MS,Verdana,Arial,Geneva,Helvetica,sans-serif;
	font-size: 12px;
	color: black;
	font-weight: bold;
	text-decoration: none;
	}
	input
	{
		font-family: Verdana,Arial,Geneva,Helvetica,sans-serif;
		font-size: 9px;
		color: black;
	}	
</style>

	<link type="text/css" href="/matrix_engine/modules/search/interface/style_search.css" rel="stylesheet">
	
	<script type="text/javascript">
		function calendarSelectWeek(vId,vDay,vMonth,vYear) {
			// first highlight the correct week
  			$(".day_active").removeClass("day_active");$(vId).children().addClass("day_active");
  			
  			// then update the data component
  			$("#datacomponent").html("<img src=\"/matrix_engine/interface/images/indicator.gif\">");
  			$.get("/matrix_engine/modules/search/overview/component/data?Session={SESSION}", {day: vDay,month:vMonth,year:vYear,week:"1"}, function(xml) { $("#datacomponent").html(xml);} );
		}

		function calendarSelectDay(vElement,vDay,vMonth,vYear) {
			// first highlight the correct week
  			$(".day_active").removeClass("day_active");
  			$(vElement).parent().addClass("day_active");
  			
  			// then update the data component
  			$("#datacomponent").html("<img src=\"/matrix_engine/interface/images/indicator.gif\">");
  			$.get("/matrix_engine/modules/search/overview/component/data?Session={SESSION}", {day: vDay,month:vMonth,year:vYear}, function(xml) { $("#datacomponent").html(xml);} );	
		}	

		function dataSelectPage(vUrl) {  			
  			// then update the data component
  			$("#datacomponent").html("<img src=\"/matrix_engine/interface/images/indicator.gif\">");
  			$.get("/matrix_engine/modules/search/overview/component/data?Session={SESSION}&"+vUrl, {}, function(xml) { $("#datacomponent").html(xml);} );	
		}		
	</script>
	
</head>
	
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#dfe1e5">

<div id="wrap">
<table border="0" cellspacing="0" cellpadding="0" width="824" bgcolor="#dfe1e5">
	<tr><td>
	
					<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						{NAVIGATION}
					</tr>
					</table>	
	
	</td></tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" width="824" bgcolor="#ffffff">
	<tr><td colspan="5" bgcolor="#c3c7ce"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="2" alt="" border="0"></td></tr>
	<tr>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="9" height="15" alt="" border="0"></td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="174" height="1" alt="" border="0"></td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="14" height="1" alt="" border="0"></td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="614" height="1" alt="" border="0"></td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="13" height="1" alt="" border="0"></td>
	</tr>
	<tr>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
		<td valign="top">
		
		<!-- KALENDER -->
			{DATESELECTOR}	
		<!-- /KALENDER -->
		</td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
		<td valign="top">
			<div id="datacomponent">
			{DATACOMPONENT}
			</div>
		</td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
	</tr>
	<tr>
		<td colspan="5"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="9" height="15" alt="" border="0"></td>
	</tr>	
</table>
</div>

</body>
</html>
<!-- END body -->

<!-- BEGIN item -->
				<tr><td colspan="5"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="3" alt="" border="0"></td></tr>
				<tr><td colspan="5" bgcolor="#f4f5f6"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td></tr>
				<tr bgcolor="#f4f5f6">
					<td class="norm">{TERM}</td>
					<td class="norm">{SEARCH_COUNT}</td>
					<td class="norm">{RESULT_COUNT}</td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
				</tr>
<!-- END item -->

<!-- BEGIN datacomponent -->
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td class="headline" colspan="3">Search Terms<br></td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
	</tr>
	<tr>
		<td class="norm" colspan="3">Below you can find the search terms entered during the 13th November 2006.</td>
		<td align="right"><img src="/matrix_engine/modules/search/interface/images/export.gif" width="75" height="16" alt="" border="0"></td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
	</tr>
	<tr><td colspan="5"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="5" alt="" border="0"></td></tr>
	<tr><td colspan="5" bgcolor="#c3c7ce"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td></tr>
	<tr><td colspan="5"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="13" alt="" border="0"></td></tr>
	<tr>
		<td class="norm">Searches: 120<br>
						Keywords: 95</td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
	</tr>
	<tr><td colspan="5"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="11" alt="" border="0"></td></tr>
	<tr>
		<td class="statistic_headline" bgcolor="#eceef0">Term</td>
		<td class="statistic_headline" bgcolor="#eceef0">Number of searches</td>
		<td class="statistic_headline" bgcolor="#eceef0">Results</td>
		<td class="statistic_headline" bgcolor="#eceef0" align="right">{PAGER}</td>
		<td bgcolor="#eceef0"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
	</tr>
	<tr><td colspan="5"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="8" alt="" border="0"></td></tr>
	{ENTRIES}
	<tr>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="160" height="1" alt="" border="0"></td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="168" height="1" alt="" border="0"></td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="137" height="1" alt="" border="0"></td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="145" height="1" alt="" border="0"></td>
		<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="4" height="1" alt="" border="0"></td>
	</tr>
</table>
<!-- END datacomponent -->

<!-- BEGIN calendar -->
	<div id="dateselector">
	
			<table border="0" cellspacing="0" cellpadding="0">
				<tr bgcolor="#eceef0">
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="6" height="2" alt="" border="0"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="18" height="1" alt="" border="0"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="126" height="1" alt="" border="0"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="18" height="1" alt="" border="0"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="6" height="1" alt="" border="0"></td>
				</tr>
				<tr bgcolor="#eceef0">
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td class="calendar_headline"><a href="#">&lt;&lt;</a></td>
					<td class="calendar_headline" align="center"><a href="#">November 2006</a></td>
					<td class="calendar_headline"><a href="#">&gt;&gt;</a></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
				</tr>
				<tr bgcolor="#eceef0"><td colspan="5"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="2" height="2" alt="" border="0"></td></tr>
			</table>
			<table border="0" cellspacing="0" cellpadding="0" id="calendar">
				<tr>
					<td>&nbsp;</td>
					<td class="dayname">M</td>
					<td class="dayname">D</td>
					<td class="dayname">M</td>
					<td class="dayname">D</td>
					<td class="dayname">F</td>
					<td class="dayname">S</td>
					<td class="dayname">S</td>
					<td>&nbsp;</td>
				</tr>
				{CALENDAR}
				</table>

			<table border="0" cellspacing="0" cellpadding="0">
				<tr><td colspan="18"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="10" alt="" border="0"></td></tr>
				<tr><td colspan="18"><a href="#" onclick='$("#dateselector").html("<img src=\"/matrix_engine/interface/images/indicator.gif\">");$.post("{actionURL}", {rating: "test"}, function(xml) { $("#dateselector").html(xml);} );'><img src="/matrix_engine/modules/search/interface/images/enterdaterange.gif" width="174" height="16" alt="" border="0"></a></td></tr>
				<tr><td colspan="18"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="25" alt="" border="0"></td></tr>
			</table>
			</div>
<!-- END calendar -->

<!-- BEGIN daterange -->
<div id="outline">
			<form action="#" method="post">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="6" height="6" alt="" border="0"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="17" height="1" alt="" border="0"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="24" height="1" alt="" border="0"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="10" height="1" alt="" border="0"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="24" height="1" alt="" border="0"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="10" height="1" alt="" border="0"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="10" height="1" alt="" border="0"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="42" height="1" alt="" border="0"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="31" height="1" alt="" border="0"></td>
				</tr>
				<tr>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td colspan="4" class="calendar_headline">Startdate</td>
					<td colspan="4"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
				</tr>
				<tr><td colspan="9"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="8" alt="" border="0"></td></tr>
				<tr>
					<td colspan="2"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td class="norm2">day:</td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td class="norm2" colspan="3">month:</td>
					<td class="norm2">year:</td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
				</tr>
				<tr>
					<td colspan="2"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td><input type="text" id="sday" name="day" size="1" style="width:24px" value="{sday}"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td><input type="text" id="smonth" name="month" size="1" style="width:24px" value="{smonth}"></td>
					<td colspan="2"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td><input type="text" id="syear" name="year" size="1" style="width:42px" value="{syear}"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
				</tr>
				<tr><td colspan="9"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="12" alt="" border="0"></td></tr>
				<tr>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td colspan="4" class="calendar_headline">Enddate</td>
					<td colspan="4"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
				</tr>
				<tr>
					<td colspan="2"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td class="norm2">day:</td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td class="norm2" colspan="3">month:</td>
					<td class="norm2">year:</td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
				</tr>
				<tr>
					<td colspan="2"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td><input type="text" id="eday" name="day" size="1" style="width:24px" value="{eday}"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td><input type="text" id="emonth" name="month" size="1" style="width:24px" value="{emonth}"></td>
					<td colspan="2"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td><input type="text" id="eyear" name="year" size="1" style="width:42px" value="{eyear}"></td>
					<td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
				</tr>
				<tr><td colspan="9"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="12" alt="" border="0"></td></tr>
				<tr>
					<td colspan="5"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="1" alt="" border="0"></td>
					<td colspan="4"><a href="#" onclick='var params = {};
		$(this)
		.find("input[@checked], input[@type='text'], input[@type='hidden'], input[@type='password'], input[@type='submit'], option[@selected], textarea")
		.filter(":enabled")
		.each(function() {
			params[ this.name || this.id || this.parentNode.name || this.parentNode.id ] = this.value;
		});alert(params);'><img src="/matrix_engine/modules/search/interface/images/viewdata.gif" width="82" height="16" alt="" border="0"></a></td>
				</tr>
				<tr><td colspan="9"><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="8" alt="" border="0"></td></tr>
			</table>
			</form>
			</div>
			<table border="0" cellspacing="0" cellpadding="0">
				<tr><td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="10" alt="" border="0"></td></tr>
				<tr><td><a href="#" onclick='$("#dateselector").html("<img src=\"/matrix_engine/interface/images/indicator.gif\">");$.post("{actionURL}", {rating: "test"}, function(xml) { $("#dateselector").html(xml);} );'><img src="/matrix_engine/modules/search/interface/images/calendar.gif" width="174" height="16" alt="" border="0"></a></td></tr>
				<tr><td><img src="/matrix_engine/modules/search/interface/images/blank.gif" width="1" height="100" alt="" border="0"></td></tr>
			</table>
<!-- END daterange -->
