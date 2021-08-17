<?php

require_once './icp.php';
require_once './iana.php';
require_once './tld-list.php';
require_once './punycode.php';
require_once './interface.php';

$release_path = '../temp/';
shell_exec('mkdir -p ' . $release_path);

// Load ICP info
// data from: http://icp.chinaz.com/suffix
$csv_content = file_get_contents('../data/icp.csv');
$icpInfo = (new ICP)->getIcpTld($csv_content);

// Get TLD list
// data from: https://tld-list.com/tlds-from-a-z
// remark: this page can't be fetch on some IP address
$html_content = file_get_contents('../data/tld-list.html');
$tldList = (new TldList)->getTlds($html_content);

// Get TLD list from IANA website
$html_content = curl('https://www.iana.org/domains/root/db');
if (!$html_content) {
    die('error -> fail to load IANA main page');
}
$tlds = (new IANA)->getTlds($html_content);
echo "Found \033[33m" . count($tlds) . "\033[0m TLDs." . PHP_EOL;

// Fetch all TLD's info
$tldInfo = array();
$iana = new IANA;
foreach ($tlds as $index => $tld) {
    $tld = substr($tld, 1 - strlen($tld));
    $url = 'https://www.iana.org/domains/root/db/' . $tld . '.html';
    echo "\033[36m" . ($index + 1) . '/' . count($tlds) . "\033[0m -> \033[35m." . $tld . "\033[0m";
    $html_content = curl($url);
    if (!$html_content) {
        die('error -> fail to load page');
    }
    $info = $iana->getTldInfo('.' . $tld, $html_content);
    $tldInfo['.' . $tld] = $info;
    echo PHP_EOL;
}

// Remove the TLD which not exist in IANA
foreach ($icpInfo as $index => $row) {
    $tld = explode('.', $index);
    $tld = '.' . $tld[count($tld) - 1];
    if (!isset($tldInfo[$tld])) {
        unset($icpInfo[$index]);
    }
}
foreach ($tldList as $index => $tld) {
    $tld = explode('.', $tld);
    $tld = '.' . $tld[count($tld) - 1];
    if (!isset($tldInfo[$tld])) {
        unset($tldList[$index]);
    }
}

// Format TLD-List
$temp = array();
foreach ($tldList as $index => $tld) {
    $tld = explode('.', $tld);
    $tld = '.' . $tld[count($tld) - 1];
    $temp[$tldList[$index]] = array(
        'tld' => $tld,
        'type' => substr_count($tldList[$index], '.')
    );
}
$tldList = $temp;

// Add some ICP host for TLD list
// Such as .edu.cn / .gov.cn
foreach ($icpInfo as $index => $row) {
    if (!isset($tldList[$index])) {
        $tld = explode('.', $index);
        $tld = '.' . $tld[count($tld) - 1];
        $tldList[$index] = array(
            'tld' => $tld,
            'type' => substr_count($index, '.')
        );
    }
}

// Output data by json format
echo 'Save as JSON format...';
ini_set('date.timezone', 'Asia/Hong_Kong');
$data = array(
    'icp' => $icpInfo,
    'list' => $tldList,
    'iana' => $tldInfo,
    'version' => date('Y-m-d')
);
writeFile($release_path . 'tldInfo.min.json', json_encode($data));
echo "\033[32mOK\033[0m" . PHP_EOL;

// Output whois server list by csv format
echo 'Dump the whois server list...';
$whoisStr = '';
foreach ($tldInfo as $index => $row) {
    if ($row['whois'] !== '') {
        $whoisStr .= $index . ',' . $row['whois'] . PHP_EOL;
    }
}
writeFile($release_path . 'whois-server.csv', $whoisStr);
echo "\033[32mOK\033[0m" . PHP_EOL;

// Output into sqlite3 database
echo 'Output into sqlite3 database...';
$init_iana_sql =<<<EOF
CREATE TABLE iana (
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
$init_list_sql =<<<EOF
CREATE TABLE list (
    record         TEXT  NOT NULL,
    tld            TEXT  NOT NULL,
    type           INT   NOT NULL
);
EOF;
$init_icp_sql =<<<EOF
CREATE TABLE icp (
    tld            TEXT  NOT NULL,
    org            TEXT  NOT NULL,
    site           TEXT  NOT NULL
);
EOF;
$db = new tldDB($release_path . 'tldInfo.db');
$db->exec($init_iana_sql);
$db->exec($init_list_sql);
$db->exec($init_icp_sql);

$insert_sql = 'INSERT INTO iana ';
$insert_sql .= '(tld,type,manager,admin_contact,tech_contact,nameserver,website,whois,last_updated,regist_date) ';
$insert_sql .= 'VALUES (';
foreach ($tldInfo as $tld => $info) {
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

$insert_sql = 'INSERT INTO list ';
$insert_sql .= '(record,tld,type) ';
$insert_sql .= 'VALUES (';
foreach ($tldList as $tld => $info) {
    $sql = $insert_sql . '\'' . $tld . '\',';
    $sql .= '\'' . $info['tld'] . '\',' . $info['type'] . ');';
    $db->exec($sql);
}

$insert_sql = 'INSERT INTO icp ';
$insert_sql .= '(tld,org,site) ';
$insert_sql .= 'VALUES (';
foreach ($icpInfo as $tld => $info) {
    $sql = $insert_sql . '\'' . $tld . '\',';
    $sql .= '\'' . $info['org'] . '\',';
    $sql .= '\'' . base64_encode(json_encode($info['site'])) . '\');';
    $db->exec($sql);
}
$db->exec('VACUUM;');
echo "\033[32mOK\033[0m" . PHP_EOL;

// All done
echo "\033[32mdone\033[0m" . PHP_EOL;
writeFile($release_path . 'success', date('Y-m-d'));

?>
