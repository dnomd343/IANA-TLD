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

// Output data by json format
writeFile($release_path . 'all-data.json', json_encode($data));

// Output whois server list by csv format
$whoisStr = '';
foreach ($data as $index => $row) {
    if ($row['whois'] !== '') {
        $whoisStr .= $index . ',' . $row['whois'] . PHP_EOL;
    }
}
writeFile($release_path . 'whois-server.csv', $whoisStr);

// Output into sqlite3 database
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

function writeFile($filename, $data) {
    $file = fopen($filename, 'w');
    fwrite($file, $data);
    fclose($file);
}

class tldDB extends SQLite3 {
    public function __construct($filename) {
        $this->open($filename);
    }
    public function __destruct() {
        $this->close();
    }
}

?>
