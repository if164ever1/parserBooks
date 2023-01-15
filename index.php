<?php
require_once("simple_html_dom.php");
$url = "https://library.fimsschools.com/all-pdf-books/";

$html = file_get_html($url);
$array_links = [];
global $pdf_links_array;

function getBooksLinksFromPage($url)
{
    $html = file_get_html($url);
    foreach($html->find('a.elementor-post__thumbnail__link') as $link){
        getPdfLinks($link->href);
    }
}

function getPdfLinks($url)
{
    $html = file_get_html($url);
    $bookTitle = $html->find('h1.elementor-heading-title', 0)->plaintext;

    foreach($html->find('a.elementor-button') as $link){
        if (pathinfo($link->href , PATHINFO_EXTENSION) == 'pdf') {
            savePdfToFolder($link->href, $bookTitle);
        }
    }
}

function savePdfToFolder($pdfUrl, $bookTitle)
{
    $language = defineLanguage($pdfUrl);
    $file_destination = '';
    if($language !== ''){
        ini_set('memory_limit', '-1');
        $file_content = file_get_contents($pdfUrl);
        $file_destination = defineFileDestination($language, $bookTitle);
        file_put_contents($file_destination,$file_content);
        unset($file_content);
    }
}

function defineLanguage($url)
{
    $german = 'German';
    $english = 'English';
    if(str_contains($url, $german)){
        return 'german';
    } else if (str_contains($url, $english)){
        return 'english';
    } else {
        return "";
    }
}

function defineFileDestination($directoryName, $bookTitle)
{
    $destibation = '';
    $folder = "data/uploads/books/";
    if (!file_exists($folder . $directoryName)) {
        mkdir($folder . $directoryName, 0777, true);
    }
    $destibation = $folder . $directoryName . "/" . $bookTitle . ".pdf";
    return $destibation;
}

function getAllLinksFromPagination($url)
{
    $html = file_get_html($url);
    $navigation = $html->find('nav.elementor-pagination', 0);
    $arr = [];
    $arr[] = $url;
    foreach($navigation->find('a.page-numbers') as $link){
        if($link->href){
            $arr[] = $link->href;
        }
    }
    array_pop($arr);
    unset($html);
    return $arr;
}

$array_links = getAllLinksFromPagination($url);

foreach($array_links as $link){
    getBooksLinksFromPage($link);
}