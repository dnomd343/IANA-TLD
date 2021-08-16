<?php

class ICP { // 获取ICP备案TLD信息
    function isDomain($domain) {
        preg_match('/^(?=^.{3,255}$)[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+$/', $domain, $match);
        return (count($match) != 0);
    }

    function checkUrl($url) {
        $url = (new Punycode)->encode($url);
        if ($this->isDomain($url)) {
            return true;
        }
        if (filter_var('https://' . $url, FILTER_VALIDATE_URL)) {
            return true;
        }
        return false;
    }

    function getIcpTld($csv_content) {
        $rawData = explode(PHP_EOL, $csv_content);
        foreach ($rawData as $index => &$row) {
            $row = trim($row);
            if ($row == '') {
                unset($rawData[$index]);
            }
        }
        $punycode = new Punycode;
        foreach ($rawData as $row) {
            $row = explode(',', $row);
            if (count($row) !== 3) {
                die('error -> icp csv format');
            }
            $tld = $row[0];
            $row['org'] = $row[1];
            unset($row[0]);
            unset($row[1]);
            $temp = preg_replace('/[\s]+/', ' ', $row[2]);
            if ($temp === '空' || $temp === '-' || $temp === '--') {
                $row['site'] = array();
            } else {
                $temp = explode(' ', $temp);
                foreach ($temp as $index => &$url) {
                    $url = trim($url);
                    if (!$this->checkUrl($url)) {
                        die('error' . $url);
                    }
                }
                $row['site'] = $temp;
            }
            unset($row[2]);
            $data[$punycode->encode($tld)] = $row;
        }
        return $data;
    }
}

?>
