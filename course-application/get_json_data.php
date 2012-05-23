<?php
    // User must be signed in
	session_start();
	if (!isset($_SESSION['ca']['logged_in'])) {
        exit;
    }

    // Security: script must be posted to and only accepts one post key - this random string of text
    // Not foolproof but doubt it could be guessed.
    $valid_types = array('incomplete', 'complete');
    $security = 'wcY2E7kmMKDfyMB0E2';

    if (isset($_POST[$security]) && in_array($_POST[$security], $valid_types)) {
        $type = $_POST[$security];
    } else {
        header("HTTP/1.0 404 Not Found");
        exit;
    }

    switch ($type) {
        case 'incomplete':
            $filename = 'incomplete.txt';
            break;
        case 'complete';
            $filename = 'complete.txt';
            break;
    }

    $filepath = dirname($_SERVER['DOCUMENT_ROOT']) . '/secure/' . $filename;
    $json_data = file_get_contents($filepath);

    // Return JSON data
    echo $json_data;
?>
	
