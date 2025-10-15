<?php
// if you are using composer, just use this
include 'Config.php';
include 'Base.php';
include 'Html.php';
include 'Pdf.php';

// initiate
$pdf = new Gufy\PdfToHtml\Pdf('123.pdf');

// convert to html string
$html = $pdf->html();

// convert to html and return it as [Dom Object](https://github.com/paquettg/php-html-parser)
$dom = $pdf->getDom();


var_dump($dom);die();