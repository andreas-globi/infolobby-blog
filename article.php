<?php
global $articleId;
$article = load($articleId);
if ( ! $article ) {
	header("Location: ".config("base_path"));
	exit();
}

$title = $article[config("title_field")]??config("default_title");
include "config/header.php";

$tpl = file_get_contents("config/article.tpl");
$tpl_detail = file_get_contents("config/detail.tpl");

$html = $tpl_detail;
$html = str_replace("{{title}}", $title, $html);
$html = str_replace("{{body}}", $article[config("body_field")]??"", $html);
foreach ( $article as $field => $value ) {
	$html = str_replace("{{record.".$field."}}", $value, $html);
}

$html = str_replace("{{article-detail}}", $html, $tpl);
echo $html;

include "config/footer.php";
