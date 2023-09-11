<?php
$title = config("default_title");
include "config/header.php";

$tpl = file_get_contents("config/home.tpl");

$tpl_list = file_get_contents("config/summary.tpl");
$html = "";

$list = load(0);
if ( ! $list ) {
	$html .= '<p>Nothing to see here. Have you run the update yet?</p>';
	$list = [];
}
foreach ( $list as $article ) {
	$one = $tpl_list;
	$url = config("base_path")."/".$article["id"]."/".slugify($article["title"]);
	$one = str_replace("{{id}}", $article["id"], $one);
	$one = str_replace("{{title}}", $article["title"], $one);
	$one = str_replace("{{summary}}", $article["summary"], $one);
	$one = str_replace("{{url}}", $url, $one);
	$html .= $one;
}

$html = str_replace("{{summary-list}}", $html, $tpl);
echo $html;

include "config/footer.php";
