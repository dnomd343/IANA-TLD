<?php

require_once './load.php';
require_once './analyse.php';
require_once './punycode.php';

$temp_path = '../temp/';
$release_path = '../release/';

shell_exec('mkdir -p ' . $release_path);
shell_exec('mkdir -p ' . $temp_path . 'tlds/');

// Get IANA main page
echo 'Connect to IANA...';
if (!loadHtmlFile('https://www.iana.org/domains/root/db', $temp_path . 'main.html')) {
    die('error -> fail to load IANA main page');
}
echo "\033[32mOK\033[0m" . PHP_EOL;

// Get TLD list from IANA website
$html_content = file_get_contents($temp_path . 'main.html');
$tlds = getIanaTlds($html_content);
writeFile($release_path . 'tld-list.txt', implode(PHP_EOL, $tlds) . PHP_EOL);
echo "Found \033[33m" . count($tlds) . "\033[0m TLDs." . PHP_EOL;

// Fetch all tld's html file
foreach ($tlds as $index => $tld) {
    $tld = substr($tld, 1 - strlen($tld));
    $url = 'https://www.iana.org/domains/root/db/' . $tld . '.html';
    echo "\033[36m" . ($index + 1) . '/' . count($tlds) . "\033[0m -> \033[35m." . $tld . "\033[0m";
    if (!loadHtmlFile($url, $temp_path . 'tlds/' . $tld . '.html')) {
        die('error -> fail to load page');
    }
    echo PHP_EOL;
}

// Analyse all TLDs from html files
echo 'Analyse all pages...';
$data = getTldsInfo($tlds, $temp_path . 'tlds/');
echo "\033[32mOK\033[0m" . PHP_EOL;

// Output data by json format
echo 'Save as JSON format...';
writeFile($release_path . 'all-data.json', json_encode($data));
echo "\033[32mOK\033[0m" . PHP_EOL;

// Output whois server list by csv format
echo 'Dump the whois server list...';
$whoisStr = '';
foreach ($data as $index => $row) {
    if ($row['whois'] !== '') {
        $whoisStr .= $index . ',' . $row['whois'] . PHP_EOL;
    }
}
writeFile($release_path . 'whois-server.csv', $whoisStr);
echo "\033[32mOK\033[0m" . PHP_EOL;

// Output into sqlite3 database
echo 'Output into sqlite3 database...';
$init_sql =<<<EOF
CREATE TABLE data (
    tld            TEXT  NOT NULL,
    type           TEXT  NOT NULL,
    manager        TEXT  NOT NULL,
    admin_contact  TEXT  NOT NULL,
    tech_contact   TEXT  NOT NULL,
    nameserver     TEXT  NOT NULL,
    website        TEXT  NOT NULL,
    whois          TEXT  NOT NULL,
    last_updated   TEXT  NOT NULL,
    regist_date    TEXT  NOT NULL
);
EOF;
$db = new tldDB($release_path . 'iana-data.db');
$db->exec($init_sql);

$insert_sql = 'INSERT INTO data ';
$insert_sql .= '(tld,type,manager,admin_contact,tech_contact,nameserver,website,whois,last_updated,regist_date) ';
$insert_sql .= 'VALUES (';
foreach ($data as $tld => $info) {
    $sql = $insert_sql . '\'' . $tld . '\',';
    foreach ($info as $index => $field) {
        if ($index === 'manager' || $index === 'admin_contact' || $index === 'tech_contact' || $index === 'nameserver') {
            $field = base64_encode(json_encode($field));
        }
        $sql .= '\'' . $field . '\'';
        if ($index !== 'regist_date') {
            $sql .= ',';
        }
    }
    $db->exec($sql . ');');
}
$db->exec('VACUUM;');
echo "\033[32mOK\033[0m" . PHP_EOL;

// All done
echo "\033[32mdone\033[0m" . PHP_EOL;

?>
