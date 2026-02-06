<!DOCTYPE html>
<?php
	ini_set('max_execution_time', 0);
?>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet"
	href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"
	integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4"
	crossorigin="anonymous">
	<script src="js/gps.js"></script>
	<title>クローリング依頼フォーム</title>
	<style type="text/css">
		.max-width-c{
			max-width: 1200px;
		}
	</style>
</head>
<body>
	<div class="container-fluid max-width-c">
		<div class="jumbotron text-center mb-4" style="background:#449; color:white;">
			<h3 class="mb-5">クローリング依頼フォーム</h3>
			<form autocomplete="off" action="crawl.php" method="post">
				<div class="form-group mb-4">
					<input type="text" class="form-control" id="url_top" name="url_top" placeholder="https://ms123.co.jp/">
				</div>
				<input type="hidden" name="inputLat" id="inputLat" value="">
				<input type="hidden" name="inputLon" id="inputLon" value="">
				<button type="submit" class="btn btn-primary w-25">URLを送信</button>
			</form>

		</div>

		<?php
		exec("ps ax | grep 'crawl_proc.php'", $output);

		$path=__DIR__."/crawl_proc.php";
		$cmd="php -c  $path";
		$cou=0;
		$process=array();
		foreach ($output as $val) {
			$pos=strpos($val, $cmd);
			if($pos!=false) {
				$process[]= $val;
				$cou++;
			}
		}

		?>
		
		<h4>処理中のプロセス</h4>
		<p class="text-danger">※現在、クローリング依頼により、以下の&nbsp;<span class="mark"><?php echo $cou ?></span>&nbsp;件の処理が実行中です。依頼された処理が多すぎた場合処理が困難になるため注意してください。</p>
		<?php
		foreach ($process as $key=>$val) {
			echo "<span>[$key] ".$val."<span><br>";
			$exploded_str=explode( ' ', trim($val) );
			$link="https://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'])."/".end($exploded_str);
			echo "<a href='$link'>進捗状況確認：$link</a><br><br>";
		}
		?>


	</div> <!--全体を囲むcontainer-fluid-->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
	integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
	crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"
	integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ"
	crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"
	integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm"
	crossorigin="anonymous"></script>
</body>
</html>