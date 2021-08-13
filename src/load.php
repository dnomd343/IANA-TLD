<?php

function getTldsInfo($tldList, $htmlDir) { // 抓取各个TLD数据
    foreach ($tldList as $tld) {
        $html = splitHtml($htmlDir . substr($tld, 1 - strlen($tld)) . '.html');
        unset($html['report']);
        if (getHtmlTitle($html['title']) !== $tld) {
            die('error analyse -> title');
        }
        $info['type'] = getHtmlType($html['type']);
        $info += getHtmlManager($html['manager']);
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

function getIanaTlds($htmlFile) { // 获取IANA上所有TLD
    $html = file_get_contents($htmlFile);
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