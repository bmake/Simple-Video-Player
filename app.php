<?php

require_once('vendor/autoload.php');

if(isset($_GET['id'])){
  $lectureId = $_GET['id'];
}
else{
  die('Parameter missing');
}

use Seld\JsonLint\JsonParser;

$parser = new JsonParser();

$json = file_get_contents('./data/'.$lectureId.'.json');
$json = $str = str_replace("\xEF\xBB\xBF",'',$json);

$obj = json_decode($json);

if (is_null($obj)) {
  echo '<h3>Invalid config!</h3><b>';
  switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - No errors';
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
        default:
            echo ' - Unknown error';
        break;
    }
    echo '</b><br />';
    if($parser->lint($json) != NULL){
      echo '<pre>' . $parser->lint($json)->getMessage() . '</pre>';
      echo '<hr>';
    }


    die("");
}

//echo '<pre>' . print_r($obj, true) . '</pre>';



$course = $obj->courses[0];

$chapters = $course->chapters;




function getVimeoIdFromUrl($url){
  if (filter_var($url, FILTER_VALIDATE_URL) == TRUE) {
    $parsedUrl = parse_url($url, PHP_URL_PATH);
    $id = filter_var($parsedUrl, FILTER_SANITIZE_NUMBER_INT);
  }
  else{
    $id = $url;
  }


  if(is_numeric($id)){
    return $id;
  }
  else{
    die('Not found');
  }
}

function callVimeoApiByVideoId($id){
  $context = stream_context_create(array(
    'http' => array('ignore_errors' => true),
  ));
  $json = file_get_contents('https://player.vimeo.com/video/'.$id.'/config', false, $context);

  //var_dump($http_response_header);

  $obj = json_decode($json);
  return $obj;
}

function getValuesFromVimeo($obj){


    // Deprecated
    // $url = $obj->request->files->h264->sd->url;
	usort($obj->request->files->progressive, function($a, $b) { 
	    return $a->profile > $b->profile ? -1 : 1;
	});
 
 	//$arrProgressiveLength = count($obj->request->files->progressive);
    $url = $obj->request->files->progressive[0]->url;

    /* Deprecated
    if($obj->video->allow_hd == 1 && isset($obj->request->files->h264->hd->url)){
      $url_hd = $obj->request->files->h264->hd->url;
      $height = $obj->request->files->h264->hd->height;
      $width = $obj->request->files->h264->hd->width;
    }
    else{
      $url_hd = "null";
      $height = $obj->request->files->h264->sd->height;
      $width = $obj->request->files->h264->sd->width;
    }*/
	
    if($obj->video->allow_hd == 1 && isset($obj->request->files->progressive[1])){
      $url_hd = $obj->request->files->progressive[1]->url;
      $height = $obj->request->files->progressive[1]->height;
      $width = $obj->request->files->progressive[1]->width;
    }
    else{
      $url_hd = "null";
      $height = $obj->request->files->progressive[0]->height;
      $width = $obj->request->files->progressive[0]->width;
    }

    $duration = $obj->video->duration;


    $size = '1280';
    if(isset($obj->video->thumbs->$size)){

    }
    else{
      $size = 960;
    }

    $thumb = $obj->video->thumbs->$size;

    if(strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad')) {
        $video = array(
            "url" => "$url",
            "url_hd" => $url_hd,
            "poster" => $thumb,
            "width" => $width,
            "height" => $height,
            "duration" => $duration
        );
    } else {
        $video = array_merge(
            array("sd" => $url,
                ($url_hd == null ? array() : array('hd' => $url_hd)),
                "poster" => $thumb,
                "width" => $width,
                "height" => $height,
                "duration" => $duration)
        );
    }


    if(phpversion() <= 5.4){
      $json = str_replace('\\/', '/', json_encode($video));
    }
    else{
      $json = json_encode($video, JSON_UNESCAPED_SLASHES);
    }
    return $json;
  }
