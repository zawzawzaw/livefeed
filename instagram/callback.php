<?php
/**
 * IMPORTANT!!!!!!!!!!!!!!!!!!!
 * THERE ARE 2 LINES OF CODES SET BELOW. 
 * YOU NEED TO UNCOMMENT IT BEFORE YOU EXECUTE THE subscription.php, AND COMMENT OUT THE OTHER CODES
 * AFTER YOU EXECUTED THE subscription.php, YOU CAN NOW COMMENT OUT THE LAST 2 LINES OF CODES AND UNCOMMENT YOUR CODES.
 * 
 * MORE INSTRUCTIONS: BELOW
 */
header('Access-Control-Allow-Origin: *');
 
require 'instagram.class.php';


$tags = array('montigoresortsnongsa');

function getTags($tag, $next_max_id = NULL) {
	$config = array();
	$config['apiKey'] = 'e9e853cde7bf4e7eb4fb036b2a495e97';
	$config['apiSecret'] = 'c624d87e4032487b8f370b5ebe23d8ab';
	$config['apiCallback'] = 'http://extraordinarymanicmachine.com/montigo/instagram/callback.php';
	$instagram = new Instagram($config);
	$tagMedia = $instagram->getTagMedia2($tag, $next_max_id);

	return $tagMedia;	
}

function getUser() {
	$config = array();
	$config['apiKey'] = 'e9e853cde7bf4e7eb4fb036b2a495e97';
	$config['apiSecret'] = 'c624d87e4032487b8f370b5ebe23d8ab';
	$config['apiCallback'] = 'http://extraordinarymanicmachine.com/montigo/instagram/callback.php';
	$instagram = new Instagram($config);
	$tagMedia = $instagram->getUserMedia(397267900, 20);

	return $tagMedia;	
}

/* get all instagram feeds */
function getData($tags, $next_max_id = NULL){
	$rows = array();
	$data = array();
	$i = 0;
	$a = 0;

	// foreach ($tags as $key => $tag) {
	// 	$data[] = getTags($tag, $next_max_id);
	// }

	$data[] = getUser();
	// print_r(getUser()); exit();

	foreach ($data as $key => $eachData) {
		foreach ($eachData->data as $entry) {

			if (!empty($entry)) {
				$data_tags = array();
				foreach($entry->tags as $tag) {
					// if($tag=="distillerysg" || $tag=="manicmachine")
						$data_tags[] = $tag;
				}

				$url = $entry->images->standard_resolution->url;
				$m_id = $entry->id;
				$c_time = $entry->created_time;
				$user = $entry->user->username;
				$filter = $entry->filter;
				$comments = $entry->comments->count;
				$caption = $entry->caption->text;
				$new_caption = str_replace('#distillerysg', '', $caption);
				$link = $entry->link;
				$low_res=$entry->images->low_resolution->url;
				$thumb=$entry->images->thumbnail->url;
				$lat = $entry->location->latitude;
				$long = $entry->location->longitude;
				$loc_id = $entry->location->id;
				$media_id = $entry->id;

				$rows[$a][$i]['media_id'] = $media_id;
				$rows[$a][$i]['m_id'] = $m_id;
				$rows[$a][$i]['title'] = $new_caption;
				// $rows[$a][$i]['username'] = $user;
				$rows[$a][$i]['image'] = $url;
				$rows[$a][$i]['tags'] = $data_tags;
				$rows[$a][$i]['time'] = date('m.d.Y, h.iA', $c_time);
				$rows[$a][$i]['link'] = $link;
			}
			$i++;
		}
		$a++;
		$i=0;
	}

	return $rows;
}

function compareTime($a, $b){
    $ad = strtotime($a['time']);
    $bd = strtotime($b['time']);

    return ($bd-$ad);
}

/* combine array and sort if there's two hash tags */
function sortArray($arrayA, $arrayB) {
	/* sorting array by latest */
	$sorted_data = array_merge($arrayA, $arrayB);
	usort($sorted_data, 'compareTime');

	return $sorted_data;
}

 // get first 10 instagram feeds 
function filterData($arr, $filter_value = 50){ 
	$res = array();
	$i=0;
	foreach ($arr as $key => $value) {
		if($i<$filter_value) {
			$res[] = $value;
		}
		$i++;
	}

	return $res;
}

/* get rest 10 instagram feeds */
function filterRest($arr, $filter_value = 50, $filter_array){
	$res = array();
	$diff = array();
	$i=0;

	foreach($arr as $val1) {
	    $contained = false;
	    foreach($filter_array as $val2) {
	        if(count(array_diff($val1, $val2)) == 0) {
	            $contained = true; 
	            break;
	        }
	    }
	    if(!$contained) {
	        $diff[] = $val1;
	    }
	}

	foreach ($diff as $key => $value) {
		if($i<$filter_value) {
			$res[] = $value;
		}
		$i++;
	}

	// print_r($filter_value);
	// print_r($diff);
	// print_r($res);

	return $res;
}

function arrayCount($arr) {
	return count($arr);
}

function getMaxId($arr) {
	$count = arrayCount($arr);

	$next_max_id = $arr[$count-1]['m_id'];

	$max_id = explode('_', $next_max_id);

	return $max_id[0];
}

function outputJSON($arr) {
	if(!empty($arr))
		echo json_encode($arr);
	else
		echo 'Error on creating json...';
}

$data = $_GET;
$filtered_result = array();

$returnData = getData($tags);

if($data['first']) {

	//$sorted_data = sortArray($returnData[0], $returnData[1]); //combine two arrays if there's two hashtags
	
	$sorted_data = $returnData[0];

	$filtered_result = filterData($sorted_data, $data['first']); // first 10

	if($filtered_result)
		outputJSON($filtered_result);
	else
		outputJSON(array('error'=>'no feeds'));

}
elseif($data['rest']) {

	//$sorted_data = sortArray($returnData[0], $returnData[1]); //combine two arrays if there's two hashtags
	
	$sorted_data = $returnData[0]; 

	$filtered_data = filterData($sorted_data, 10); // first 10	
	$filtered_result = filterRest($sorted_data, $data['rest'], $filtered_data); // find rest 10

	if($filtered_result)
		outputJSON($filtered_result);
	elseif($filtered_data)
		outputJSON($filtered_data);
	else
		outputJSON(array('error'=>'no feeds'));

}
else {

	//$sorted_data = sortArray($returnData[0], $returnData[1]); //combine two arrays if there's two hashtags

	$sorted_data = $returnData[0];

	if($sorted_data)
		outputJSON($sorted_data);
	else
		outputJSON(array('error'=>'no feeds'));

}



// uncomment when first time receiving instagram subscription
// $challenge = $_GET['hub_challenge'];
// echo $challenge;

?>