<!-- BEGIN body --><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>College of North East London - {HEADLINE}</title>
	<meta name="keywords" content="conel, college, college of haringey enfield and north east london, further education, london, north east london, short courses, esol, adult learning, employer courses" />
	<meta name="description" content="The College of Haringey, Enfield and North East London" />
	<meta name="robots" content="index,follow" />
	<meta name="revisit-after" content="4 DAYS">
	<link href="/layout/css/styles.css?1" rel="stylesheet" type="text/css" media="all" />
	<link href="/layout/css/rotator.css" rel="stylesheet" type="text/css" media="screen" />{ENFIELD_CSS}
	<link href="/layout/css/print.css" rel="stylesheet" type="text/css" media="print" />
	<!--[if lte IE 6]><link href="/layout/css/ie6.css" rel="stylesheet" type="text/css" media="all" /><![endif]-->
	<link rel="alternate" type="application/rss+xml" title="Conel - Latest News &amp; Events - RSS" href="http://www.conel.ac.uk/rss_news-events.php" />
	<script type="text/javascript" src="/layout/js/jquery-1.6.min.js"></script>
</head>
<body class="home">
<div id="acceskeys">
	<div id="skiplinks">
		<ul>
			<li><a href="#access-navigation" accesskey="n">Jump to Navigation [Accesskey 'n']</a></li>
		</ul>
	</div>
</div>
    <div class="canvas">
        <div id="canvash">
            <div id="header_home" class="clearfix">
                <div id="header_left">
                    <div id="home_top">
                        <div id="home_logo"><h1>The College of Haringey, Enfield and North East London</h1></div>
						<div id="home_buttons">
							<a href="/contact_us/online_enquiry"><img src="/images/online-enquiry.gif" alt="Online Enquiry" width="300" height="140" style="border:0;" /></a>
							<a href="/how-to-apply-online"><img src="/images/How-to-apply.gif" alt="Online Application" width="363" height="140" style="border:0;" /></a>
							<!--a href="/course-application"><img src="/images/Enrol-now-button.gif" width="300" height="140" alt="Enroll Now" style="border:0;" /></a-->
							<!--a href="http://www.conel.ac.uk/our_courses/enrolment"><img src="/images/Enrolbigger.gif" width="450" height="140" alt="Apply Now" style="border:0;" /></a-->
						</div>
                        <br class="clear_both" />
                    </div>
                    <div id="navigation" class="clearfix">
                    <a id="access-navigation" href="/home" name="access-navigation"></a>
                    <div class="fl">
                        <ul class="clearfix{SECTION_TOP}">{LEVEL_1}</ul>
                    </div>
                </div>
                </div>

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
            <ul id="subnav" class="clearfix tab1">
                <li><a href="/home/home_learners" class="active"><span>Information for Learners</span></a></li>
                <li><a href="/home/home_employer"><span>Information for Employers</span></a></li>
                <li><a href="/for_schools"><span>Information for Schools</span></a></li>
                <li><a href="/home/home_staff"><span>Work For Us</span></a></li>
            </ul>
            {#SUBNAV}
            <div class="home_border_top"></div>
            <div class="clearfix mc">
                <div class="colhp">
                    <h2>{HEAD1}</h2>
                    <p>{DESC}</p>
                    <!--div class="hrcolhp"><hr /></div-->
                    {LINKS}
                </div>
                <div class="colhp">
                    <h2>{HEAD2}</h2>
                    <div id="home_search">
                        <form action="/our_courses/course_search" method="get" class="ml28">
                            <label for="url">What are you interested in?</label>
                            <br />
                            <div class="clearfix">
                                <select class="fl w235 mr10 home_spacing" name="interest" id="url">
                                {SUBJECT_INTEREST_LIST}
                                </select>
                            </div>
                            <input type="text" name="keyword" id="keyword" value="Search term" class="w230" />
                            <div class="clearfix">
                                <label for="location" class="fl hidden">Location you wish to study at</label>
                                <select name="location" id="location" class="fl w235 mr10 home_spacing">
                                    <option value="">Select a location</option>
                                    <option value="tottenham centre">Tottenham Centre</option>
                                    <option value="enfield centre">Enfield Centre</option>
                                </select>
                            </div>
                            <div class="clearfix">
                                <input type="image" src="/layout/img/go.gif" alt="Search" class="fl go_home" />
                            </div>
                        </form>
                    </div>
                    <!--div class="hrcolhp"><hr /></div-->
                    <p>{CONTACT}</p>
                    <div class="hrcolhp"><hr /></div>
					<br />
					<br />
                    {LINKSC}
                </div>
                <div class="colhp end">
                    <div class="home_rc_news_events">
                    <h2>News and Events</h2>
                    {NEWS}
                    <br class="clear_both" />
                    </div>
                    {#SUBSCRIBE}
                    <div class="home_rc_links">
                    <ul style="margin-top:-11px;">
                        <li><a href="/news_events/news"><span>More News Stories</span></a></li>
                        <li><a href="/our_college/college_newsletter"><span>College Newsletter</span></a></li>
                        {LINKSC3}
                    </ul>
                    </div>
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

<script type="text/javascript" src="/layout/js/cookies-directive.js"></script>
<script type="text/javascript">cookiesDirective('bottom',0,'privacy.html');</script>

<!-- Google Code for Remarketing tag -->
<!-- Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. For instructions on adding this tag and more information on the above requirements, read the setup guide: google.com/ads/remarketingsetup -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1036380037;
var google_conversion_label = "U9HiCKfFvQQQhc-X7gM";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/1036380037/?value=0&amp;label=U9HiCKfFvQQQhc-X7gM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

</body>
</html><!-- END body -->
