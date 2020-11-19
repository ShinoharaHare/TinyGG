<?php
require_once('./simple_html_dom.php');
require_once('./utils.php');

function scrape($url)
{
    $html = file_get_html($url);
    $data = [];
    $data['title'] = $html->find('title', 0)->plaintext;
    $data['favicon'] = urljoin($url, 'favicon.ico');
    $data['summary'] = $html->find('meta[name="description"]', 0)->content;
    $tmp = $html->find('link[rel="image_src"]', 0)->href;
    $data['cover'] = urljoin($url, $tmp);
    if (empty($data['cover'])) {
        $tmp = $html->find('img[src]', 0)->src;
        $data['cover'] = urljoin($url, $tmp);
    }
    return $data;
}

$data = scrape($_GET['url']);
sendJSON($data);
