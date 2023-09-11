<?php

function start() {
	global $config;
	set_exception_handler(function($e) {
		echo '<p style="color:red;">Exception: '.$e->getMessage().'</p>';
	});
	require_once __DIR__."/../config/config.php";
}

function config($key, $default="") {
	global $config;
	if ( ! is_array($config) ) start();
	if ( ! is_array($config) ) $config = [];
	if ( ! isset($config[$key]) ) return $default;
	return $config[$key];
}

function api_call($method, $params=null) {
	$url = "https://infolobby.com/api".$method;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	if ( $params ) {
	  	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	if ( curl_error($ch) ) throw new Exception("CURL returned false");
	$info = curl_getinfo($ch);
	if ( $info["http_code"] != 200 ) throw new Exception("CURL returned ".$info["http_code"]);
	curl_close($ch);
	$data = json_decode($result, true);
	if ( is_array($data) && ! json_last_error() ) {
		return $data;
	}
	return $result;
}

function check_cache_perms() {
	$dir = __DIR__."/../data/";
	if ( ! is_writeable($dir) ) return false;
	return true;
}

function save($id, $data) {
	if ( ! check_cache_perms() ) throw new Exception("data dir not writeable");
	$data = json_encode($data);
	$file = __DIR__."/../data/".$id;
	file_put_contents($file, $data);
}

function load($id) {
	$file = __DIR__."/../data/".$id;
	$data = @file_get_contents($file);
	$data = @json_decode($data, true);
	return $data;
}

function xflush() {
	if ( ! headers_sent() ) {
		@ini_set("zlib.output_compression", "Off");
		@ini_set("output_buffering", "Off");
	}
	echo(str_repeat(' ',1024*64));
	if (ob_get_length()){
		@ob_flush();
		@flush();
		@ob_end_flush();
	}
	@ob_start();
}

function slugify($text) {
	$text = preg_replace('~[^\pL\d]+~u', '-', $text);
	$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	$text = preg_replace('~[^-\w]+~', '', $text);
	$text = trim($text, '-');
	$text = preg_replace('~-+~', '-', $text);
	$text = strtolower($text);
	if (empty($text)) return 'n-a';
	return $text;
}

