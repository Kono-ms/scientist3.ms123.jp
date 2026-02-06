<?php
echo "test sendPDF";


require_once('../TCPDF/tcpdf.php');

// 文字エンコーディングを設定
//mb_language("uni");
mb_language("Japanese");
//mb_internal_encoding("UTF-8");


//pdfデータ生成
$font_size = 12;
$pdf = new TCPDF('P');// 縦向き
$pdf ->AddFont('caveatvariablefont_wght');//caveat(全ウェイト、CSS上の名称 Caveat-Regular)
$pdf ->AddFont('kozminpro');//kozminpro(全ウェイト、CSS上の名称 KozMinPro-Regular-Acro, Kozuka Mincho Pro)
$pdf ->AddFont('genshingothic');// GenShinGothic(全ウェイト、CSS上の名称 GenShinGothic-Regular)
$pdf ->SetFont('genshingothic', '', $font_size);// GenShinGothic(bodyにfontプロパティ設定とほぼ同義)
$pdf ->SetMargins(12,8,12);// ページマージン（left,top,right）
$pdf ->setPrintHeader(false);// ページヘッダー（なし）
$pdf ->setPrintFooter(false);// ページフッター（なし）
$pdf ->setListIndentWidth(4);// リストタグのインデント調整
$pdf ->addPage();


$str="<p>テストで生成したpdfデータです。test!!</p>";
$pdf ->writeHTML($str);


//To Avoid the PDF Error
ob_end_clean();


$filename=date('Ymd-his')."-test.pdf";
$dirname="pdf_files/";

//$pdfData=$pdf->Output($dirname.$filename, "S");
$pdfData=$pdf->Output($filename, "S");


//file_put_contents($dirname.$filename, $pdfData);

//ダウンロードの場合
//$pdf->Output(date('Ymd-his')."-test.pdf", "D");



//メールの基本情報
$to = 'h.tsurumi@ms123.co.jp';
$from = 'info@scientist-cube.com';
$fromName = '送信者名';
$subject = 'pdfメール送信テスト';
$body = "pdfメール送信テスト。\n添付のPDFファイルをご確認ください。";
$attachFileName = $filename; // 添付ファイル名

// ----------------------------------------------------------------

// ヘッダー情報
$headers = [
	'From' => mb_encode_mimeheader($fromName) . ' <' . $from . '>',
	'MIME-Version' => '1.0',
];

// バウンダリを生成
$boundary = '----=' . md5(uniqid(rand(), true));
$headers['Content-Type'] = 'multipart/mixed; boundary="' . $boundary . '"';

// メッセージ全体のボディを作成
$message = '';

// テキスト部分
$message .= '--' . $boundary . "\r\n";
$message .= 'Content-Type: text/plain; charset=ISO-2022-JP' . "\r\n";
//$message .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";
$message .= 'Content-Transfer-Encoding: 7bit' . "\r\n";
//$message .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
$message .= "\r\n";
$message .= $body . "\r\n";
$message .= "\r\n";

// 添付ファイル部分
$encodedPdf = chunk_split(base64_encode($pdfData)); // TCPDFから得たPDFデータをエンコード

$message .= '--' . $boundary . "\r\n";
$message .= 'Content-Type: application/pdf; name="' . $attachFileName . '"' . "\r\n";
$message .= 'Content-Disposition: attachment; filename="' . $attachFileName . '"' . "\r\n";
$message .= 'Content-Transfer-Encoding: base64' . "\r\n";
$message .= "\r\n";
$message .= $encodedPdf . "\r\n"; // エンコードしたPDFデータを追加
$message .= "\r\n";

// 最後のバウンダリ
$message .= '--' . $boundary . '--' . "\r\n";

// メール送信
if (mb_send_mail($to, $subject, $message, $headers)) {
	echo 'PDFを生成し、メールを送信しました。';
} else {
	echo 'メールの送信に失敗しました。';
}



?>
