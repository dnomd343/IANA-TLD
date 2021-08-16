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

?>
