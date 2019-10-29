<?php
require_once '../base_code/curl.php';
$parma['val1'] = 'content1';
$parma['val2'] = 'content2';
$url           = 'http://localhost/demo1/create_html/show.php';
$str           = http_build_query($parma);
$html          = file_get_contents_by_curl($url, $str);
if ($html) {
    @file_put_contents('index1.html', $html);
}
