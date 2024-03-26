<?php
global $title;
if (is_null($title)) $title = config("default_title");
?>
<html>
<head>
	<title><?=$title?></title>
	<style>
		* { font-family: sans-serif; }
		.wrapper { margin: 20px auto; max-width: min(90%, 800px); }
	</style>
</head>
<body>
<div class="wrapper">

