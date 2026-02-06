<?php
header('X-Accel-Buffering: no'); //nginxでのバッファリングをさせない
header('Cache-Control: no-store');
ob_end_clean(); //バッファクリア


@ob_end_flush();
@ob_implicit_flush();

//ob_start();
$cou=0;
while(1){

	//echo $cou."回目<br>";
	//flush();
	//ob_flush();

	$out = fopen('php://output', 'w'); //php://stdout も同じ結果になる
	fwrite($out, $cou."回目<br>" . PHP_EOL);
	fflush($out);
	fclose($out);

	

	sleep(1);
	if($cou>=10) break;
	$cou++;
}

?>