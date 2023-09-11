<?php
global $title;
if (is_null($title)) $title = config("default_title");
?>
<html>
<head>
	<title><?=$title?></title>
	<style>
		* { font-family: sans-serif; }
	</style>
</head>
<body>
<div class="body">

