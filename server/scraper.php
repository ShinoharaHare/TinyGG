<?php
require_once('./simple_html_dom.php');
require_once('./utils.php');

function scrape($url)
{
    $html = file_get_html($url);
    $parsed = parse_url($url);
    $data = [];
    $data['title'] = $html->find('title', 0)->plaintext;
    $data['favicon'] = $parsed['scheme'].'://'.$parsed['host'].'/favicon.ico';
    $data['summary'] = $html->find('meta[name="description"]', 0)->content;
    $data['cover'] = $html->find('link[rel="image_src"]', 0)->href;
    $data['img'] = $html->find('img[src]', 0)->src;
    return $data;
}

sendJSON(scrape(''));
