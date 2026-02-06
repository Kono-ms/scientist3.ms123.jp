<?php

//exec("ps -x", $output);
//exec("ps aux | less -+S", $output);
//exec("ps aux | grep 'crawl_proc.php'", $output);
exec("ps ax | grep 'crawl_proc.php'", $output);

echo "<pre>";
var_dump($output);

echo "\n\n";

$path=__DIR__."/crawl_proc.php";
$cmd="php -c  $path";


foreach ($output as $val) {
	
	$pos=strpos($val, $cmd);
	//var_dump($pos);
	if($pos!=false) {
		echo $val."\n";
	}

}


echo "</pre>";
?>