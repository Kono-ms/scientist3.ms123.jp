<?php
require_once('../../../m_m1/func.php');
$page = isset($_GET['p']) ? $_GET['p'] : '';
$page = $page==='j' ? '../../../m_m1/Agreements2j_print.html' : '../../../m_m1/Agreements2e_print.html';
$html = file_get_contents($page);

echo getKinsokuHTML($html);

?>
