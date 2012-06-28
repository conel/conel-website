<!-- BEGIN body --><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>conel - {HEADLINE}</title>
	<meta name="keywords" content="conel, college, college of haringey enfield and north east london, further education, london, north east london, short courses, esol, adult learning, employer courses" />
	<meta name="description" content="The College of Haringey, Enfield and North East London" />
	<meta name="robots" content="index,follow" />
	<link href="/layout/css/styles.css" rel="stylesheet" type="text/css" media="all" />
	<link href="/layout/css/rotator.css" rel="stylesheet" type="text/css" media="screen" />
	{ENFIELD_CSS}
	<link href="/layout/css/print.css" rel="stylesheet" type="text/css" media="print" />
	<!--[if lte IE 6]><link href="/layout/css/ie6.css" rel="stylesheet" type="text/css" media="all" /><![endif]-->
	<link rel="alternate" type="application/rss+xml" title="Conel - Latest News &amp; Events - RSS" href="http://www.conel.ac.uk/rss_news-events.php" />
	<script type="text/javascript" src="/layout/js/jquery-1.6.min.js"></script>
</head>
<body class="home">
		<div class="canvas">
			<div id="canvash">
				<div id="header_home" class="clearfix">
					<div id="header_left">
						<div id="home_top">
							<div id="home_logo">
								<h1>The College of Haringey, Enfield and North East London</h1><span><a id="access-navigation" href="/home" name="access-navigation"></a></span>
							</div>
							<div id="quotes">{RANDOM_QUOTE}</div>
							<br class="clear_both" />
						</div>
						<div id="navigation" class="clearfix">
						<div class="fl">
							<ul class="clearfix{SECTION_TOP}">{LEVEL_1}</ul>
						</div>
					</div>
					</div>
                    <div id="header_right"><a href="/contact_us/online_enquiry"><img src="/images/online-enquiry.jpg" alt="Online Enquiry" width="227" height="221" style="border:0;" /></a></div>
					<br class="clear_both" />
				</div>
			</div>
			<!-- banner_holder -->
			<div id="banner_holder">{BANNERS}</div>
			<!-- //banner_holder -->
			<div id="key_search_bar">
				<div id="key_fact">
					{FACTS_OR_MSG}
				</div>
				<div id="searchbar_home">
					<form action="/home/search_results" method="get">
						<input type="hidden" name="search" value="all" />
						<div class="clearfix">
							<label for="query" class="hidden">Search:</label>
							<input type="text" name="query" id="query" value="Search term" class="fl mr10" />
							<input type="image" src="/layout/img/go.gif" alt="Search" class="fl" />
						</div>
					</form>
				</div>
				<br class="clear_both" />
			</div>
			
			<div id="hpcontent">
				<ul id="subnav" class="clearfix tab4">
					<li><a href="/home/home_learners"><span>Information for Learners</span></a></li>
					<li><a href="/home/home_employer"><span>Information for Employers</span></a></li>
					<li><a href="/for_schools"><span>Information for Schools</span></a></li>
					<li><a href="/home/home_staff" class="active"><span>Work For Us</span></a></li>
				</ul>
				{#SUBNAV}
				<div class="home_border_top"></div>
				<div class="clearfix mc">
					<div class="colhp">
						<h2>{HEAD1}</h2>
						<p>{DESC}</p>
						<div class="hrcolhp"><hr /></div>
						{LINKS}
					</div>
					<div class="colhp">
						<h2>{HEAD2}</h2>
						{VACANCIES}
						<div class="hrcolhp"><hr /></div>
						<!--
						<p>{CONTACT}</p>
						<div class="hrcolhp"><hr /></div>
						-->
						{LINKSC}
					</div>
					<div class="colhp end">
						<h2>News / Events</h2>
						{NEWS}
						<br class="clear_both" /><br />
						{#SUBSCRIBE}
						<ul>
						<li><a href="/news_events/news"><span>More News Stories</span></a></li>
						<li><a href="/our_college/college_newsletter"><span>College Newsletter</span></a></li>
						{LINKSC3}
						</ul>
					</div>
				</div>
			</div>
			<div class="hrhpe"><hr /></div>
			<div id="specialf">{FOOTER}</div>
		</div>
	{FOOTER_CODE}
	<script type="text/javascript" src="/layout/js/jquery.easing.1.3.min.js"></script>
	<script type="text/javascript" src="/layout/js/jquery.wt-rotator.min.js"></script>
	<script type="text/javascript" src="/layout/js/jquery-rotate-banner.js"></script>
	<script type="text/javascript" src="/layout/js/jquery.bxSlider.min.js"></script>
	<script type="text/javascript" src="/layout/js/keyfacts.js"></script>
	<script type="text/javascript" src="/layout/js/system.js"></script>
	{ENFIELD_INC}
</body>
</html><!-- END body -->
