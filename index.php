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
        // echo $link->href . PHP_EOL;
        getPdfLinks($link->href);
        break;
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
    $file_content = file_get_contents($pdfUrl);
    $file_destination = defineFileDestination($language, $file_content);



    // file_put_contents($file_destination,$file_content);
    // var_dump($pdfUrl);

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
        return null;
    }
}

function defineFileDestination($bookTitle)
{
    $destibation = '';

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
    return $arr;
}

$array_links = getAllLinksFromPagination($url);
// print("<pre>".print_r($array_links,true)."</pre>");

foreach($array_links as $link){
    getBooksLinksFromPage($link);
}

