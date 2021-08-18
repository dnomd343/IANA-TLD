<?php

class DNSSEC {
    public function getDnssecStatus($html_contant) {
        $html = explode('<table', $html_contant);
        if (count($html) !== 4) {
            die('error -> dnssec page');
        }
        $html = explode('</table>', $html[3]);
        if (count($html) !== 2) {
            die('error -> dnssec page');
        }
        $html = explode('</tr>' . PHP_EOL . '<tr', $html[0]);
        unset($html[0]);

        foreach ($html as $row) {
            $row = trim($row);
            preg_match('/^bgcolor=#[0-9A-F]{6}/', $row, $match);
            if (count($match) !== 1) {
                die('error -> dnssec list');
            }
            $color = substr($match[0], -6);
            preg_match('/href=http:\/\/www.iana.org\/domains\/root\/db\/[a-z0-9-]+.html>/', $row, $match);
            if (count($match) !== 1) {
                die('error -> dnssec list');
            }
            $tld = str_replace('.html>', '', $match[0]);
            $tld = '.' . str_replace('href=http://www.iana.org/domains/root/db/', '', $tld);

            $row = str_replace('<td align=left>YES</td>', '|YES|', $row);
            $row = str_replace('<td>NO</td>', '|NO|', $row);
            preg_match('/\|(YES|NO)\|\|(YES|NO)\|\|(YES|NO)\|/', $row, $match);
            if (count($match) !== 4) {
                die('error -> dnssec list');
            }
            if ($match[0] === '|YES||YES||NO|') {
                $type = 1; // 正常DNSSEC
                if ($color !== '00CC00') {
                    die('error -> dnssec list');
                }
            } else if ($match[0] === '|NO||NO||NO|') {
                $type = 2; // 未启用DNSSEC
                if ($color !== 'F2F2F2') {
                    die('error -> dnssec list');
                }
            } else if ($match[0] === '|YES||NO||NO|') {
                $type = 3; // 启用DNSSEC 但未部署DS记录
                if ($color !== '3399CC') {
                    die('error -> dnssec list');
                }
            } else {
                die('error -> dnssec list');
            }
            $dnssec[$tld] = array(
                'type' => $type
            );
        }
        return $dnssec;
    }

    public function getDsRecord($tld) { // 获取DS记录
        $tld = substr($tld, 1, strlen($tld) - 1); // 去除前面的.
        $raw = shell_exec('dig DS +short @' . chr(mt_rand(97, 109)) . '.root-servers.net ' . $tld . '.');
        if ($raw == '') {
            return array();
        }
        $dsRecords = explode(PHP_EOL, $raw);
        foreach ($dsRecords as $dsRecord) {
            if ($dsRecord == '') { continue; }
            $ds = explode(' ', $dsRecord);
            if (count($ds) !== 4 && count($ds) !== 5) {
                return 'error';
            }
            $temp['tag'] = $ds[0];
            $temp['algorithm'] = $ds[1];
            $temp['digest'] = $ds[2];
            $temp['hash'] = $ds[3];
            if (count($ds) === 5) {
                $temp['hash'] .= $ds[4];
            }
            $dnssec[] = $temp;
        }
        foreach ($dnssec as $row) {
            if ($row['digest'] === '1') { // SHA-1
                preg_match('/^[0-9A-Z]{40}$/', $row['hash'], $match);
                if (count($match) !== 1) {
                    return 'error';
                }
            } else if ($row['digest'] === '2') { // SHA-256
                preg_match('/^[0-9A-Z]{64}$/', $row['hash'], $match);
                if (count($match) !== 1) {
                    return 'error';
                }
            } else { // 3 -> GOST R 34.11-94 / 4 -> SHA-384
                return 'error';
            }
        }
        foreach ($dnssec as &$row) { // 计算权重
            $row['weight'] = $row['tag'] * 1000 + $row['algorithm'] * 10 + $row['digest'];
        }
        $temp = array(); // 排序算法
        foreach ($dnssec as $val){
            $temp[] = $val['weight'];
        }
        sort($temp);
        $temp = array_flip($temp);
        $sort = array();
        foreach ($dnssec as $val) {
            $temp_1 = $val['weight'];
            $temp_2 = $temp[$temp_1];
            $sort[$temp_2] = $val;
        }
        asort($sort);
        $dnssec = $sort;
        foreach ($dnssec as &$record) {
            unset($record['weight']);
        }
        return $dnssec;
    }
}

?>
