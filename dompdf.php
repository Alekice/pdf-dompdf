<?php

require "vendor/autoload.php";
require  "db.php";

// reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

// $data = get_data();
$mktu_urls = get_mktu_urls();
$urls= filter_mktu_urls($mktu_urls, 16);

// set options for pdf file
$options = new Options();
$options->setDefaultFont('"DejaVu Sans", sans-serif');

// instantiate and use the dompdf class
$dompdf = new Dompdf($options);

$style = file_get_contents('./styles.html');
$start_content = $style . '<body>';
$end_content = '</body>';

$page_break_start = '<div class="page-break">';
$page_break_end = '</div>';

$content = $start_content;

$count = 0;

foreach ($urls as $url) {
	echo $url . PHP_EOL;
	echo $count . PHP_EOL;

	if ($count < 301) {
		$count = $count + 1;
		continue;
	}

	$html = file_get_contents($url);
	$title = '<h1 class="main-title">' . mb_split('</h1>', mb_split('<h1 class="main-title">', $html)[1])[0] . '</h1>';
	$page_content = mb_split('<div class="mktu__init-content">', $html)[1];
	$chat_answer_with_links = mb_split('<p class="init__title">В данном классе также смотрят:</p>', $page_content)[0];
	$chat_answer = preg_replace('/<a href="\/class([\d]{1,2})">([\w\W]*?)<\/a>/iu', '$1', $chat_answer_with_links);
	$content .= $page_break_start . $title . $chat_answer . $page_break_end;

	$count = $count + 1;
}

$content .= $end_content;

$dompdf->loadHtml($content);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();
$output = $dompdf->output();
file_put_contents('класс16_4.pdf', $output);

// // Output the generated PDF to Browser
// $dompdf->stream();


function get_mktu_urls(): array {
	$sitemap = file_get_contents('https://mktu.org/sitemap.xml');
	preg_match_all('/<loc>([\w\W]*?)<\/loc>/iu', $sitemap, $matches);
	return $matches[1];
}

function filter_mktu_urls(array $array, int $classNumber): array {
	return array_filter(
		$array,
		function ($url) use ($classNumber) {
			if (strpos($url, "class$classNumber/") == true && strpos($url, "class$classNumber/category") == false) {
				return $url;
			};
		}
	);
}