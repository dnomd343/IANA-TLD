<?php

require_once './load.php';
require_once './analyse.php';
require_once './punycode.php';

$html_path = '../html/';
$release_path = '../release/';

// Get TLD list from IANA website
// main.html -> https://www.iana.org/domains/root/db
$tlds = getIanaTlds($html_path . 'main.html');
writeFile($release_path . 'tld-list.txt', implode(PHP_EOL, $tlds) . PHP_EOL);

// Analyse all TLDs from html files
$data = getTldsInfo($tlds, $html_path . 'tlds/');

// $str = $data['.to']['admin_contact']['org'];
// echo preg_replace('/[\s]+/', ' ', $str);
// echo $str;
// exit;

// var_dump($data['.com']);
// exit;
foreach ($data as $index => $row) {
    // if ($row['admin_contact']['voice'] === false) {
        // echo $index . ' -> ' . implode(' | ', $row['manager']['addr']) . PHP_EOL;
        // if (count($row['nameserver']) === 0) { continue; }
        // echo $row['website'] . PHP_EOL;
        // echo $row['whois'] . PHP_EOL;
        // echo $row['last_updated'] . PHP_EOL;
        // echo $row['regist_date'] . PHP_EOL;
        // echo $index . ' -> ' . $row['tech_contact']['name'] . PHP_EOL;
        // echo $index . ' -> ' . $row['tech_contact']['org'] . PHP_EOL;
        // echo $index . ' -> ' . implode(' | ', $row['tech_contact']['addr']) . PHP_EOL;
        // echo $index . ' -> ' . $row['tech_contact']['email'] . PHP_EOL;
        // echo $index . ' -> ' . $row['tech_contact']['voice'] . PHP_EOL;
        // echo $index . ' -> ' . $row['tech_contact']['fax'] . PHP_EOL;

    // }
}

// return;

// Output data by json format
writeFile($release_path . 'all-data.json', json_encode($data));

// Output whois server list by csv format
$whoisStr = '';
foreach ($data as $index => $row) {
    if ($row['whois'] !== '') {
        $whoisStr .= $index . ',' . $row['whois'] . PHP_EOL;
    }
}
writeFile('../release/whois-server.csv', $whoisStr);

function writeFile($filename, $data) {
    $file = fopen($filename, 'w');
    fwrite($file, $data);
    fclose($file);
}

?>
