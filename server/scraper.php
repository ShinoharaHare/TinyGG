<?php
require_once './vendor/autoload.php';
require_once './utils.php';


use voku\helper\HtmlDomParser;


function scrape($url)
{
    $dom = HtmlDomParser::file_get_html($url);

    $data = [];

    $data['title'] = extractData($dom, array(
        array('meta[property="og:title"]', 'content'),
        array('title', 'plaintext'),
        array('meta[property="og:site_name"]', 'content')
    ));

    $data['summary'] = extractData($dom, array(
        array('meta[name="description"]', 'content'),
        array('meta[property="og:description"]', 'content'),
        array('meta[property="og:site_name"]', 'content')
    ));

    $data['favicon'] = urljoin($url, extractData($dom, array(
        array('link[rel="icon"]', 'href'),
        array('link[rel="shortcut icon"]', 'href')
    ), 'favicon.ico'));

    $data['thumbnail'] = urljoin($url, extractData($dom, array(
        array('link[rel="image_src"]', 'href'),
        array('meta[property="og:image"]', 'content'),
        array('img[src]', 'src')
    )));

    return $data;
}

function extractData($dom, $selectors, $default = null)
{
    foreach ($selectors as $s) {
        $query = $s[0];
        $attr = $s[1];
        $el = $dom->find($query, 0);
        if (!empty($el) && !empty($el->$attr)) {
            return $el->$attr;
        }
    }
    return $default;
}

// $data = scrape($_GET['url']);
// sendJSON($data);
