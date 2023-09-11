<?php
ini_set("error_reporting", 0);
ini_set("display_errors", 0);
require_once "inc/functions.php";
start();

$url = $_GET["q"]??"";
$parts = explode("/", $url);

if ($parts[0] == "_update") {
	include("update.php");
} elseif ( ! empty($parts[0]) ) {
	$articleId = $parts[0];
	include("article.php");
} else {
	include("home.php");
}


