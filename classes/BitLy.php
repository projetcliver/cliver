<?php

class BitLy {
	private static $login = 'o_5a1d3r70pn';
	private static $apiKey = 'R_ad794a7b9cd86439c53cb548363d8b60';
	private static $connectURL = 'http://api.bit.ly/v3/shorten?login={{LOGIN}}&apiKey={{APIKEY}}&uri={{URL}}&format={{FORMAT}}';

	public static function shorten($url, $format = "txt") {
		$connectURL = str_replace(
			['{{URL}}', '{{LOGIN}}', '{{APIKEY}}', '{{FORMAT}}'],
			[urlencode($url), self::$login, self::$apiKey, $format], 
			self::$connectURL
		);
		return trim(self::curl_get_result($connectURL));
	}

	protected static function curl_get_result($url) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}
?>
