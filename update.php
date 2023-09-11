<?php
// we want to see errors on this page
ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);

require_once "inc/functions.php";
start();

// make sure our data and img folders are writable
if ( ! is_writeable(__DIR__."/data/") ) {
  throw new Exception("data folder is not writeable");
}

if ( ! is_writeable(__DIR__."/img/") ) {
  throw new Exception("img folder is not writeable");
}

echo "Updating...";
xflush();

// setup
$methodQuery = "/table/" . config("table_id") . "/records/query";
$methodGet = "/table/" . config("table_id") . "/record/";
$methodFile = "/table/" . config("table_id") . "/record/";
$summary = [];
$limit = 2;
$offset = 0;
$params = ["limit"=>$limit, "offset"=>$offset];

// get first batch
$data = api_call($methodQuery, ["limit"=>$limit, "offset"=>$offset]);

while ( true ) {
	if ( ! is_array($data) || empty($data) ) break;

	foreach ( $data as $row ) {
		$title = $row[config("title_field")]??config("default_title")??"Untitled";
		$shortDescription = substr($row[config("body_field")]??"", 0, 250);
		if ( strlen($shortDescription) > 240 ) {
			$words = explode(" ", $shortDescription);
			unset($words[sizeof($words)-1]);
			$shortDescription = implode(" ", $words)."...";
		}
		// add to summary
		$summary[] = ["id"=>$row["item_id"], "title"=>$title, "summary"=>$shortDescription];
		echo $title."<br>";

		// get actual record
		$record = api_call($methodGet.$row["item_id"]."/get");
		if ( ! $record || ! is_array($record) || empty($record["data"]??[]) ) {
		  throw new Exception("Failed fetching ".$title);
		}
		$body = $record["data"][config("body_field")]??"";

		// find images
		$check = preg_match_all("/<img src=\"\/file\/(.*?)\/(.*?)\"/ism", $body, $matches);
		if ( $matches && ! empty($matches[1]??false) ) {
			foreach ( $matches[1] as $k => $fileId ) {

				echo " - ".$fileId."-".($matches[2][$k]??"image")."<br>";
				$filename = __DIR__."/img/".$fileId;

				if ( ! file_exists($filename) ) {
					// get and save image
					$file = api_call($methodFile.$row["item_id"]."/file_get_base64", ["fileId"=>$fileId]);
					$raw = @base64_decode($file["data"]??"");
					if ( ! $raw ) continue;
					file_put_contents(__DIR__."/img/".$fileId, $raw);
				}

				// fix html
				$replace = '<img src="'.config("base_path").'/img/'.$fileId.'"';
				$body = str_replace($matches[0][$k], $replace, $body);
			}
			$record["data"][config("body_field")] = $body;
		}

		// save record
		xflush();
		save($row["item_id"], $record["data"]);
	}

	// get next batch
	$offset += $limit;
	$data = api_call($methodQuery, ["limit"=>$limit, "offset"=>$offset]);
}

// save summary
save(0, $summary);

echo "done";

