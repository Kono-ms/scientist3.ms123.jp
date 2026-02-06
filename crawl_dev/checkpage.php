<?php
require("func.php");

$url="https://macrogen-japan.co.jp/";
$html_curl=file_get_by_curl($url);

file_put_contents("test_curl.html", $html_curl);




$context = stream_context_create(
	[
		"http"=>
		[
			"ignore_errors"=>true
		]
	]
);

$html_fgetcontent=file_get_contents($url, false, $context);
$pos=strpos($http_response_header[0], "200");
echo "http status:".$http_response_header[0]."<br><br>";


file_put_contents("test_fgetcontent.html", $html_fgetcontent);

?>