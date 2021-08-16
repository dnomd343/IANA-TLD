<?php

class TldList { // 获取TLD-List 包括次级域名
    public function getTlds($html_content) {
        $match = explode('center-block col-xs-4', $html_content);
        $match = explode('</div>', $match[1]);
        $html = $match[0];
        preg_match_all('/<a[\s\S]+?a>/', $html, $match);
        $punycode = new Punycode();
        $html = $match[0];
        foreach ($html as $row) {
            $row = strtr($row, array("><" => ''));
            preg_match('/>[\S]+?</', $row, $match);
            $tld = substr($match[0], 1, strlen($match[0]) - 2);
            $tlds[] = $punycode->encode($tld);
        }
        return $tlds;
    }
}

?>
