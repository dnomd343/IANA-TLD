<?php

require_once './load.php';
require_once './analyse.php';
require_once './punycode.php';

// function writeFile($filename, $data) {
//     $file = fopen($filename, 'w');
//     fwrite($file, $data);
//     fclose($file);
// }

// $data = getIanaTlds('../html/main.html'); // https://www.iana.org/domains/root/db
// // writeFile('tlds.txt', implode(PHP_EOL, $data) . PHP_EOL);
// $urls = '';
// foreach ($data as $tld) {
//     $urls .= 'https://www.iana.org/domains/root/db/';
//     $urls .= substr($tld, 1, strlen($tld) - 1);
//     $urls .= '.html' . PHP_EOL;
// }
// writeFile('urls.txt', $urls);

$tlds = getIanaTlds('../html/main.html');
$data = getTldsInfo($tlds, '../html/tlds/');
// echo count($data);

foreach ($data as $index => $row) {
    if ($row['whois'] !== '') {
        echo $index . ' -> ' . $row['whois'] . PHP_EOL;
    }
}

?>
