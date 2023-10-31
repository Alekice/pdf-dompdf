<?php

require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf();
ob_start(); // Буферизация
require 'test.html';
$html = ob_get_clean();
$mpdf->WriteHTML($html);
$mpdf->Output('mpdf.pdf');