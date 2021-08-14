<?php

$ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.131 Safari/537.36 Edg/92.0.902.67';

class tldDB extends SQLite3 { // Sqlite3数据库
    public function __construct($filename) {
        $this->open($filename);
    }
    public function __destruct() {
        $this->close();
    }
}

function writeFile($filename, $data) { // 写入文件
    $file = fopen($filename, 'w');
    fwrite($file, $data);
    fclose($file);
}

function curl($url) { // curl模拟 20s超时
    global $ua;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($curl, CURLOPT_USERAGENT, $ua);
    $content = curl_exec($curl);
    curl_close($curl);
    return $content;
}

function loadHtmlFile($url, $filename) {
    $content = curl($url);
    if (!$content) {
        return false;
    } else {
        writeFile($filename, $content);
        return true;
    }
}

function getTldsInfo($tldList, $htmlDir) { // 抓取各个TLD数据
    foreach ($tldList as $tld) {
        $html_content = file_get_contents($htmlDir . substr($tld, 1 - strlen($tld)) . '.html');
        $html = splitHtml($html_content);
        unset($html['report']);
        if (getHtmlTitle($html['title']) !== $tld) {
            die('error analyse -> title');
        }
        $info['type'] = getHtmlType($html['type']);
        $info['manager'] = getHtmlManager($html['manager']);
        $info['admin_contact'] = getHtmlContact($html['admin']);
        $info['tech_contact'] = getHtmlContact($html['tech']);
        $info['nameserver'] = getHtmlNS($html['ns']);
        $web = getHtmlInfo($html['info']);
        $info['website'] = $web['website'];
        $info['whois'] = $web['whois'];
        $date = getHtmlDate($html['date']);
        $info['last_updated'] = $date['update'];
        $info['regist_date'] = $date['regist']; 
        $data[$tld] = $info;  
    }
    return $data;
}

function getIanaTlds($html) { // 获取IANA上所有TLD
    $html = explode('tbody>', $html)[1];
    $html = explode('</tr>', $html);
    unset($html[count($html) - 1]);
    $punycode = new Punycode;
    foreach ($html as $row) {
        preg_match('/<a [\s\S]+<\/a>/', $row, $match);
        preg_match('/>[\s\S]+</', $match[0], $match);
        $match = substr($match[0], 1, strlen($match[0]) - 2);
        if (substr($match, 0, 8) === '&#x200f;') {
            $match = substr($match, 8, strlen($match) - 16);
        }
        $tlds[] = $punycode->encode($match);
    }
    return $tlds;
}

?>