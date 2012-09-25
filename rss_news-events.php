<?php

  include("matrix_engine/classes/FeedWriter.php");
  include("matrix_engine/config.php");
  include("matrix_engine/classes/db_mysql.php");
  
	// Build URL
	function build_url_from_date($identifier='', $month='', $year='', $page_url='') {

		$url = '';

		// News Articles
		if ($identifier == 'news') {
			// nkowald - 2010-10-27 - The news link was incorrect, fixed here.
			//$folder = "/news_archive/$year/".strtolower($month)."_$year/$page_url";
			$folder = "/news/$year/".strtolower($month)."_$year/$page_url";
		}

		// Event Articles
		if ($identifier == 'event') {
			$folder = "/event_calendar/".strtolower($month)."_$year/$page_url";
		}

		$url = 'http://www.conel.ac.uk/news_events' . $folder;

		return $url;

	} // buildURL()

  // Creating an instance of FeedWriter class. 
  $TestFeed = new FeedWriter(RSS2);
  
  // Setting the channel elements
  // Use wrapper functions for common channel elements
  $TestFeed->setTitle('College of Haringey, Enfield and North East London - News & Events');
  $TestFeed->setLink('http://www.conel.ac.uk/news_events');
  $TestFeed->setDescription('College of Haringey, Enfield and North East London - Latest News & Events');
  
  // Image title and link must match with the 'title' and 'link' channel elements for valid RSS 2.0
  $TestFeed->setImage('College of Haringey, Enfield and North East London - News & Events','http://www.conel.ac.uk/news_events','http://www.conel.ac.uk/layout/img/logo.gif');
  
	// Detriving informations from database addin feeds
	// Get all News and Events Items - ordered by latest first
  $query = "SELECT 
				webmatrix_user_pages.template,
				webmatrix_user_pages.title, 
				webmatrix_page_content.text, 
				webmatrix_structure.structure_url, 
				webmatrix_user_pages.created, 
				DATE_FORMAT(webmatrix_user_pages.created, '%Y') AS 'year', 
				DATE_FORMAT(webmatrix_user_pages.created, '%M') AS 'month', 
				webmatrix_image_data.filename, 
				webmatrix_image_data.alt 

				FROM webmatrix_user_pages

				LEFT JOIN webmatrix_page_content ON webmatrix_user_pages.page_id = webmatrix_page_content.page_id
				LEFT JOIN webmatrix_structure ON webmatrix_user_pages.page_id = webmatrix_structure.page_id
				LEFT JOIN webmatrix_image ON webmatrix_user_pages.page_id = webmatrix_image.page_id 
				LEFT JOIN webmatrix_image_data ON webmatrix_image_data.image_id = webmatrix_image.image_id 

				WHERE webmatrix_user_pages.template IN ('65','66') 
				AND webmatrix_page_content.identifier = 'CONTENT' 
				AND webmatrix_structure.structure_flag <> 0 

				ORDER BY webmatrix_user_pages.created DESC
				LIMIT 25";

	$sql = new DB_Sql();
	$sql->connect();
	$sql->query($query, $debug=true);


	if ($sql->num_rows() > 0) {
	
		while($sql->next_record()) {
			// Create an empty FeedItem
			$newItem = $TestFeed->createNewItem();
			
			// Add elements to the feed item
			$title = htmlentities($sql->Record['title']);
			$newItem->setTitle($title);

			$identifier = ($sql->Record['template'] == 65) ? 'news' : 'event';
			$article_url = build_url_from_date($identifier, $sql->Record['month'], $sql->Record['year'], $sql->Record['structure_url']);
			$newItem->setLink($article_url);
			$newItem->setGUID($article_url);
			
			$created = $sql->Record['created'];
			$newItem->setDate($created);

			$image_title = ($sql->Record['alt'] != '') ? $sql->Record['alt'] : '';
			$image_url = ($sql->Record['filename'] != '') ? "http://www.conel.ac.uk/images/". $sql->Record['filename'] : '';
			if ($image_url != '') {
				// We need the smaller version of the image url
				$filename = strrchr($image_url,'.');
				$image_url = substr($image_url,0,strrpos($image_url,'.')) . "_440_0" . $filename;
				$image = '<img src="'.$image_url.'" alt="'.$image_title.'" width="440" border="0" /><br />';
			} else {
				$image = '';
			}
			
			$description = $image . $sql->Record['text'];
			$newItem->setDescription($description);
			
			// Now add the feed item
			$TestFeed->addItem($newItem);
		}
		
	}
	
  // OK. Everything is done. Now genarate the feed.
  $TestFeed->genarateFeed();
  
?>
