<?php

function isDomain($domain) {
    preg_match('/^(?=^.{3,255}$)[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+$/', $domain, $match);
    return (count($match) != 0);
}

function isVoice($str) {
    $regs = array(
        '/^\+[0-9]{1,3} [0-9 -]+$/',
        '/^\+ [0-9]{1,3} [0-9 -]+$/',
        '/^\+[0-9]{1,3}-[0-9 -]+$/',
        '/^[0-9]{1,3}.[0-9.]+$/',
        '/^[0-9]{1,3}-[0-9-]+$/',
        '/^\([0-9]{1,3}\) [0-9 -]+$/',
        '/^[0-9]{1,3} [0-9 -]+$/',
        '/^[0-9]{1}-[0-9-]+$/',
        '/^[0-9.]+ x1$/',
        '/^\+[0-9]{1,3}[0-9 -]+$/',
        '/^\+[0-9]{1,3}[0-9.]+$/',
        '/^\+\+[0-9]+$/',
        '/^[0-9 ]+$/',
        '/^[0-9.-]+$/',
        '/^[0-9-]+ [0-9]+$/',
        '/^\(\+\) [0-9-]+$/',
        '/^[0-9.]+ [0-9 ]+$/',
        '/^\+[0-9]+.[0-9]+x[0-9]+$/',
        '/^\+[0-9]+\([0-9]\)[0-9]+$/',
        '/^[0-9]+ \([0-9]{3}\) [0-9 ]+$/',
        '/^\+[0-9]{2} \([0-9]{3}\) [0-9 ]+$/',
        '/^\+ \([0-9]{3}\) [0-9]+$/',
        '/^\+\([0-9]{3}\) [0-9 ]+$/',
        '/^\(\+[0-9]{3}\) [0-9]+$/',
        '/^\+[0-9.]+-[0-9]{2}$/',
        '/^\+[0-9\/]+ [0-9 ]+$/',
        '/^\+[0-9 ]+–[0-9]$/',
        '/^[0-9 ]+ x114$/',
        '/^\+ [0-9-]+$/',
        '/^\+[0-9].[0-9-]+$/',
        '/^[0-9 -]+,[0-9 -]+$/',
        '/^\(\+[0-9]\) [0-9-]+$/',
        '/^\+[0-9] [0-9]+ x204$/',
        '/^\+[0-9]{1,3}.[0-9 ]+$/',
        '/^\+[0-9]{1,3} [0-9.]+$/',
        '/^\+[0-9].[0-9]+x[0-9]+$/',
        '/^\+[0-9] [0-9.]+ [0-9.]+$/',
        '/^\+[0-9]+\([0-9]\)[0-9 ]+$/',
        '/^\+[0-9]+ \([0-9]\)[0-9 ]+$/',
        '/^\+[0-9]\([0-9]{3}\)[0-9]+$/',
        '/^\+[0-9]{3} [0-9]{3} [0-9\/]+$/',
        '/^\+[0-9] \([0-9]{3}\) [0-9 ]+$/',
        '/^\+[0-9]{1,3}\([0-9]\) [0-9 ]+$/',
        '/^\+[0-9] \([0-9]{4}\) [0-9- ]+$/',
        '/^\+[0-9 ]+\/[0-9]+ or \+[0-9 ]+$/',
        '/^\+[0-9]{2} [0-9]{4} \/ [0-9]{6}$/',
        '/^\+[0-9]{1,3} \([0-9]\) [0-9 -]+$/',
        '/^\+[0-9 ]+, \+[0-9 ]+, \+[0-9]+,$/',
        '/^\+[0-9]{2} \([0-9]{2}\) [0-9 ]+$/',
        '/^\+[0-9]{3} \([0-9]\) [0-9 ]+ or \+[0-9]{3} \([0-9]\) [0-9 ]+$/',
        '/^[0-9]+ [0-9-]+, [0-9]+ [0-9-]+, [0-9]+ [0-9-]+$/',
        '/^\+[0-9] \([0-9]{3}\) [0-9-]+, ext [0-9]{4}$/',
        '/^\+[0-9]{5} [0-9 ]+ \/ \+[0-9]{5} [0-9 ]+$/',
        '/^\+[0-9]{1,3} \([0-9]{1,3}\) [0-9-]+$/',
        '/^\+[0-9]{1,3} \([0-9]{1}\) [0-9 ]+$/',
        '/^\+[0-9]{2}.[0-9]+ ext\: [0-9]{4}$/',
        '/^\+[0-9-]+, \+[0-9-]+, \+[0-9-]+$/',
        '/^[0-9]{1,3} [0-9-]+ EXT. [0-9]+$/',
        '/^\+[0-9] [0-9 -]+ Ext. [0-9]+$/',
        '/^\+[0-9] [0-9 -]+ ext [0-9]+$/',
        '/^\+[0-9]{2}\([0-9]\)[0-9-]+$/',
        '/^\+[0-9] [0-9 ]+ xt. [0-9]+$/',
        '/^\+[0-9 ]+ ext [0-9]{3}$/',
        '/^\+[0-9 ]+ ext. [0-9]+$/',
        '/\+[0-9 ]+ Ext. [0-9]+$/',
        '/^[0-9 ]+ ext. [0-9]+$/',
        '/^\+[0-9]-[0-9-]+ x1$/',
        '/^\+[0-9-]+ x 102$/',
        '/^[0-9 ]+ Ext. 1$/',
        '/^\+[0-9 ]+[0-9\/]+$/',
        '/^\+[0-9]{2}.[0-9 ]+$/',
        '/^\+[0-9 ]+ ; \+[0-9 ]+$/',
        '/^\+ \([0-9]{3}\) [0-9 ]+$/',
        '/^\+[0-9- ]+ ext. [0-9]{3}$/',
        '/^[0-9]{2} \([0-9]\)[0-9 ]+$/',
        '/^\+[0-9]{3} [0-9]{4}\+[0-9]{4}$/',
        '/^\+[0-9 ]+, \+[0-9 ]+, \+[0-9 ]+$/',
        '/^\+[0-9] \([0-9]{4}\) [0-9-]+, [0-9-]+$/',
        '/^\+[0-9] \([0-9]{3}\)[0-9]+., \+\([0-9]{3}\) [0-9]+$/',
        '/^\+[0-9]{3} \([0-9]\) [0-9 ]+, \+[0-9]{3} \([0-9]\) [0-9 ]+$/'
    );
    foreach ($regs as $reg) {
        preg_match($reg, $str, $match);
        if (count($match) === 1) {
            return true;
        }
    }
    return false;
}

function splitHtml($htmlFile) {
    // Get core part
    $html = file_get_contents($htmlFile);
    $html = explode('main_right">', $html)[1];
    $html = explode('<div id="sidebar_left', $html)[0];
    $html = explode('<script>', $html)[0];

    // Get title
    preg_match('/<h1>[\s\S]+<\/h1>/', $html, $match); // First <h1>...</h1>
    if (count($match) !== 1) {
        die('error -> title');
    }
    $match = trim($match[0]);
    $result['title'] = substr($match, 4, strlen($match) - 9);

    // Get domain type
    preg_match('/<p>[\s\S]+?<\/p>/', $html, $match); // First <p>...</p>
    if (count($match) !== 1) {
        die('error -> domain');
    }
    $match = trim($match[0]);
    $result['type'] = substr($match, 3, strlen($match) - 7);

    $html = explode('<h2>', $html);
    if (count($html) !== 6 && count($html) !== 7) {
        die('error -> html');
    }

    // Get manager
    $manager = trim($html[1]);
    if (strpos($manager, 'Sponsoring Organisation') !== 0 && strpos($manager, 'ccTLD Manager') !== 0) {
        die('error -> manager');
    }
    $result['manager'] = trim(explode('</h2>', $manager)[1]);

    // Get admin contact
    $admin = trim($html[2]);
    if (strpos($admin, 'Administrative Contact') !== 0) {
        die('error -> admin contact');
    }
    $result['admin'] = trim(explode('</h2>', $admin)[1]);

    // Get tech contact
    $tech = trim($html[3]);
    if (strpos($tech, 'Technical Contact') !== 0) {
        die('error -> tech contact');
    }
    $result['tech'] = trim(explode('</h2>', $tech)[1]);

    // Get nameserver
    $ns = trim($html[4]);
    if (strpos($ns, 'Name Servers') !== 0) {
        die('error -> name server');
    }
    preg_match('/This domain is not present in the root zone at this time./', $ns, $match);
    if (count($match) !== 0) {
        $result['ns'] = '';
    } else {
        $ns = explode('<tbody>', $ns)[1];
        $ns = explode('</tbody>', $ns)[0];
        $result['ns'] = trim($ns);
    }
    
    // Get registry info
    $info = trim($html[5]);
    if (strpos($info, 'Registry Information') !== 0) {
        die('error -> registry info');
    }
    $info = trim(explode('</h2>', $info)[1]);
    if (strpos($info, '<i>')) {
        if (count($html) !== 6) {
            die('error -> registry info');
        }
        $date = trim(explode('<i>', $info)[1]);
        $info = trim(explode('<i>', $info)[0]);
        if (substr($info, -3) !== '<p>') {
            die('error -> registry info');
        }
        $result['info'] = trim(substr($info, 0, strlen($info) - 3));
    } else {
        if (count($html) !== 7) {
            die('error -> registry info');
        }
        if (substr($info, -4) !== '</p>') {
            die('error -> registry info');
        }
        $result['info'] = $info;

        // Get report
        $report = trim($html[6]);
        if (strpos($report, 'IANA Reports') !== 0) {
            die('error -> report');
        }
        $date = trim(explode('<i>', $report)[1]);
        preg_match('/<ul>[\s\S]+<\/ul>/', $report, $match);
        if (count($match) !== 1) {
            die('error -> report');
        }
        $report = trim($match[0]);
        if (substr($report, 0, 4) !== '<ul>') {
            die('error -> report');
        }
        $report = substr($report, 4 - strlen($report));
        if (substr($report, -5) !== '</ul>') {
            die('error -> report');
        }
        $result['report'] = trim(substr($report, 0, strlen($report) - 5));
    }

    // Get date
    if (substr($date, -8) !== '</i></p>') {
        die('error -> date');
    }
    $result['date'] = trim(substr($date, 0, strlen($date) - 8));
    if (count($result) !== 8 && count($result) !== 9) {
        die('error -> result');
    }
    if (!isset($result['title']) || !isset($result['type']) || !isset($result['manager']) || !isset($result['admin'])) {
        die('error -> result');
    }
    if (!isset($result['tech']) || !isset($result['ns']) || !isset($result['info']) || !isset($result['date'])) {
        die('error -> result');
    }
    if (count($result) === 9 && !isset($result['report'])) {
        die('error -> result');
    }
    foreach ($result as &$row) {
        $row = trim($row);
    }
    return $result;
}

function getHtmlTitle($str) { // 提取标题TLD字段
    $str = str_replace('<span class="force-rtl">', '', $str);
    $str = str_replace('</span>', '', $str);
    if (strpos($str, 'Delegation Record for .') !== 0) {
        die('error analyse -> title');
    }
    $str = substr($str, 22 - strlen($str));
    $str = (new Punycode)->encode($str);
    if ($str === '.xn--l4fe') { // 特殊差错
        return '.xn--node';
    }
    return $str;
}

function getHtmlType($str) { // 提取TLD类型
    preg_match('/^\(Country-code top-level domain designated for two-letter country code [A-Z]{2}\)/', $str, $match);
    if (count($match) !== 0) {
        return 'ccTLD for ' . substr(substr($str, -3), 0, 2);
    }
    switch ($str) {
        case '(Generic top-level domain)':
            return 'gTLD';
        case '(Country-code top-level domain)':
            return 'ccTLD';
        case '(Sponsored top-level domain)':
            return 'sTLD';
        case '(Infrastructure top-level domain)':
            return 'Infrastructure TLD';
        case '(Restricted generic top-level domain)':
            return 'Restricted TLD';
        case '(Test top-level domain)':
            return 'TLD for test';
        default:
            die('error analyse -> type');
    }
}

function getHtmlManager($str) { // 提取TLD所有者信息
    if ($str == '') {
        return array(
            'manager' => '',
            'manager_info' => ''
        );
    }
    $temp = explode('</b><br/>', $str);
    if (count($temp) !== 2) {
        die('error analyse -> manager');
    }
    $manager = trim($temp[0]);
    $manager = substr($manager, 3 - strlen($manager));
    if ($manager === 'Not assigned') {
        return array(
            'manager' => '',
            'manager_info' => ''
        );
    }
    if ($temp[1] == '') {
        return array(
            'manager' => $manager,
            'manager_info' => ''
        );
    }
    $temp = str_replace('<br>', '<br/>', trim($temp[1]));
    $temp = explode('<br/>', $temp);
    foreach ($temp as $line) {
        $line = trim($line);
        if ($line != '') {
            $manager_addr[] = $line;
        }
    }
    if (!isset($manager_addr)) {
        die('error analyse -> manager');
    }
    return array(
        'manager' => $manager,
        'manager_addr' => $manager_addr
    );
}

function getHtmlContact($str) { // 提取联系人信息
    if ($str === '') {
        return array();
    }
    preg_match_all('/<b>[\s\S]+?<\/b>/', $str, $match);
    if (count($match) !== 1) {
        die('error analyse -> contact');
    }
    $match = $match[0];
    if (count($match) === 1 && $match[0] === '<b>Not assigned</b>') {
        return array();
    }
    if (count($match) !== 4 && count($match) !== 3 && count($match) !== 2) {
        die('error analyse -> contact');
    }
    if ($match[1] !== '<b>Email:</b>') {
        die('error analyse -> contact');
    }
    $email = 1;
    $voice = -1;
    $fax = -1;
    if (isset($match[2])) {
        if ($match[2] === '<b>Voice:</b>') {
            $voice = 2;
        } else if ($match[2] === '<b>Fax:</b>') {
            $fax = 2;
        } else {
            die('error analyse -> contact');
        }
    }
    if (isset($match[3])) {
        if ($match[3] === '<b>Fax:</b>') {
            $fax = 3;
        } else {
            die('error analyse -> contact');
        }
    }
    $name = substr($match[0], 0, strlen($match[0]) - 4);
    $result['name'] = trim(substr($name, 3 - strlen($name)));
    $str = preg_replace('/<b>[\s\S]+?<\/b>/', '|_|-|_|', $str);
    $contact = explode('|_|-|_|', $str);
    if ($contact[0] !== '') {
        die('error analyse -> contact');
    }
    if (count($contact) !== 5 && count($contact) !== 4 && count($contact) !== 3) {
        die('error analyse -> contact');
    }
    unset($contact[0]);
    $temp = str_replace('<br>', '<br/>', trim($contact[1]));
    $temp = explode('<br/>', $temp);
    foreach ($temp as $index => &$line) {
        $line = trim($line);
        if ($line == '') {
            unset($temp[$index]);
        }
    }
    if (count($temp) < 2) {
        die('error analyse -> contact');
    }
    $flag = false;
    foreach ($temp as $line) {
        if (!$flag) {
            $result['org'] = $line;
            $flag = true;
            continue;
        }
        $addr[] = $line;
    }
    $result['addr'] = $addr;
    $result['email'] = '';
    $result['voice'] = '';
    $result['fax'] = '';
    foreach ($contact as $index => &$row) {
        $row = trim($row);
        if (substr($row, -5) !== '<br/>') {
            die('error analyse -> contact');
        }
        if ($index - 1 === $email) {
            $result['email'] = $row;
        }
        if ($index - 1 === $voice) {
            $result['voice'] = $row;
        }
        if ($index - 1 === $fax) {
            $result['fax'] = $row;
        }
    }
    if ($result['email'] != '' && substr($result['email'], -5) !== '<br/>') {
        die('error analyse -> contact');
    }
    $result['email'] = substr($result['email'], 0, strlen($result['email']) - 5);
    if ($result['voice'] != '' && substr($result['voice'], -5) !== '<br/>') {
        die('error analyse -> contact');
    }
    $result['voice'] = substr($result['voice'], 0, strlen($result['voice']) - 5);
    if ($result['fax'] != '' && substr($result['fax'], -5) !== '<br/>') {
        die('error analyse -> contact');
    }
    $result['fax'] = substr($result['fax'], 0, strlen($result['fax']) - 5);
    if ($result['fax'] === 'n/a' || $result['fax'] === 'NA' || $result['fax'] === 'N/A' || $result['fax'] === '-') {
        $result['fax'] = '';
    }
    if (substr($result['voice'], 0, 1) === ']') {
        $result['voice'] = trim(substr($result['voice'], 1 - strlen($result['voice'])));
    }
    if($result['email'] != '' && !filter_var($result['email'], FILTER_VALIDATE_EMAIL)) {
        die('error analyse -> contact');
    }
    if($result['voice'] != '' && !isVoice($result['voice'])) {
        die('error analyse -> contact');
    }
    if($result['fax'] != '' && !isVoice($result['fax'])) {
        die('error analyse -> contact');
    }
    return $result;
}

function getHtmlNS($str) { // 提取TLD名称服务器
    if ($str === '') {
        return array();
    }
    preg_match_all('/<tr>[\s\S]+?<\/tr>/', $str, $match);
    if (count($match) !== 1) {
        die('error analyse -> ns');
    }
    $match = $match[0];
    if (count($match) === 0) {
        die('error analyse -> ns');
    }
    foreach ($match as $row) {
        $row = trim(str_replace('<tr>', '', $row));
        $row = trim(str_replace('</tr>', '', $row));
        preg_match_all('/<td>[\s\S]+?<\/td>/', $row, $match);
        if (count($match) !== 1) {
            die('error analyse -> ns');
        }
        $match = $match[0];
        if (count($match) === 0) {
            die('error analyse -> ns');
        }
        $match[0] = trim(str_replace('<td>', '', $match[0]));
        $match[0] = trim(str_replace('</td>', '', $match[0]));
        $match[1] = trim(str_replace('<td>', '', $match[1]));
        $match[1] = trim(str_replace('</td>', '', $match[1]));
        $temp = explode('<br/>', $match[1]);
        $ips = array();
        foreach ($temp as $ip) {
            if ($ip == '') { continue; }
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                die('error analyse -> ns');
            }
            $ips[] = $ip;
        }
        if (count($ips) === 0) {
            die('error analyse -> ns');
        }
        if (!isDomain($match[0])) {
            die('error analyse -> ns');
        }
        $ns[$match[0]] = $ips;
    }
    if (count($ns) === 0) {
        die('error analyse -> ns');
    }
    return $ns;
}

function getHtmlInfo($str) { // 获取官网/Whois服务器信息
    // if ($str == '') {
    //     return array(
    //         'website' => '',
    //         'whois' => ''
    //     );
    // }
    preg_match_all('/<p>[\s\S]+?<\/p>/', $str, $match);
    if (count($match) !== 1) {
        die('error analyse -> info');
    }
    $match = $match[0];
    if (count($match) !== 1) {
        die('error analyse -> info');
    }
    $str = trim($match[0]);
    $str = trim(substr($str, 0, strlen($str) - 4));
    if ($str === '<p>') {
        return array(
            'website' => '',
            'whois' => ''
        );
    }
    $str = trim(substr($str, 3 - strlen($str)));
    preg_match_all('/<b>[\s\S]+?<\/b>/', $str, $match);
    if (count($match) !== 1) {
        die('error analyse -> info');
    }
    $match = $match[0];
    $website = -1;
    $whois = -1;
    if ($match[0] === '<b>URL for registration services:</b>') {
        $website = 0;
    } else if ($match[0] === '<b>WHOIS Server:</b>') {
        $whois = 0;
    } else {
        die('error analyse -> info');
    }
    if (isset($match[1])) {
        if ($match[1] !== '<b>WHOIS Server:</b>') {
            die('error analyse -> info');
        }
        $whois = 1;
    }
    $str = preg_replace('/<b>[\s\S]+?<\/b>/', '|_|-|_|', $str);
    $contact = explode('|_|-|_|', $str);
    if ($contact[0] !== '') {
        die('error analyse -> contact');
    }
    if (count($contact) !== 3 && count($contact) !== 2) {
        die('error analyse -> contact');
    }
    unset($contact[0]);
    foreach ($contact as $index => $row) {
        $row = trim($row);
        if ($index - 1 === $website) {
            preg_match('/^<a href="[\s\S]+<\/a>/', $row, $match);
            if (count($match) !== 1) {
                die('error analyse -> contact');
            }
            $row = $match[0];
            preg_match('/>[\s\S]+</', $row, $match);
            if (count($match) !== 1) {
                die('error analyse -> contact');
            }
            $row = substr($match[0], 0, strlen($match[0]) - 1);
            $result['website'] = trim(substr($row, 1 - strlen($row)));
        }
        if ($index - 1 === $whois) {
            $result['whois'] = $row;
        }
    }
    if (!isset($result['website'])) {
        $result['website'] = '';
    } else {
        if (!filter_var($result['website'], FILTER_VALIDATE_URL)) {
            die('error analyse -> contact');
        }
    }
    if (!isset($result['whois'])) {
        $result['whois'] = '';
    } else {
        if (!isDomain($result['whois'])) {
            die('error analyse -> contact');
        }
    }
    return $result;    
}

function getHtmlDate($str) { // 提取TLD注册和更新日期
    preg_match('/Record last updated [0-9]{4}-[0-9]{2}-[0-9]{2}./', $str, $match);
    if (count($match) !== 1) {
        die('error analyse -> date');
    }
    $update = substr($match[0], -11);
    $update = substr($update, 0, 10);
    preg_match('/Registration date [0-9]{4}-[0-9]{2}-[0-9]{2}./', $str, $match);
    if (count($match) !== 1) { // only eh TLD
        return array(
            'update' => $update,
            'regist' => $update
        );
    }
    $regist = substr($match[0], -11);
    $regist = substr($regist, 0, 10);
    return array(
        'update' => $update,
        'regist' => $regist
    );
}

?>