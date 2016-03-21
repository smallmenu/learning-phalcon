#!/bin/php
<?php
set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', 'on');

// 配置
$config = array(
    'url' => 'http://localhost:8900/solr/',
    'auth' => 'lewell@Apache-Solr:797c0f0b6eab39783265f0e043a03583',
);
$solrDelta = array('command' => 'delta-import', 'commit' => true, 'optimize' => false, 'wt'=> 'json');
$solrStatus = array('command' => 'status', 'wt'=> 'json');

// 命令行参数
$options = getopt("c:Hl:", array('help::', 'path:'));
if (isset($options['H']) || isset($options['help'])) {
    show_help();
}
if (!isset($options['c'])) {
    show_help('-c       require solr core name ');
} else {
    $core = $options['c'];
    $path = $core. '/dataimport';
    $url = $config['url'] . $path;

    // 检测Core状态，
    $params = http_build_query($solrStatus);
    $statusUrl = $url . '?'. $params;
    $status = solr_curl($statusUrl, $config['auth']);
    if ($status['httpcode'] == 200) {
        $data = json_decode($status['data'], true);

        // 空闲状态
        if ($data['status'] == 'idle') {

            // 执行增量
            $params = http_build_query($solrDelta);
            $deltaUrl = $url . '?'. $params;
            $delta = solr_curl($deltaUrl, $config['auth']);
            if ($delta['httpcode'] == 200) {

                record("SUCESS: solr core($core) delta-import");

            } else {
                record("ERROR: query delta-import failed! httpcode: ". $delta['httpcode']);
            }
        } else {
            record("ERROR: solr core($core) is ". $data['status']);
        }
    } else {
        record("ERROR: query status failed! httpcode: ". $status['httpcode']);
    }
}

function solr_curl($url, $auth, $timeout = 10)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $auth);

    $data = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return array('httpcode' => $httpcode, 'data' => $data);
}

function show_help($msg = '')
{
    if ($msg) {
        echo "ERROR:". PHP_EOL;
        echo "  ".wordwrap($msg, 72, "\n  "). PHP_EOL;
        exit;
    }
    echo "Solr delta-import script:". PHP_EOL. PHP_EOL;
    echo "USAGE:". PHP_EOL;
    echo "php solr-delta.php -c abc". PHP_EOL. PHP_EOL;
    echo "OPTIONS:". PHP_EOL;
    echo "  -c      the solr core name will delta-import ". PHP_EOL;
    echo "  -H      show this help". PHP_EOL;
    echo PHP_EOL;
    exit;
}

function record($msg)
{
    $time = date('[Y-m-d H:i:s] ');
    echo $time . $msg;
    echo PHP_EOL;
    exit;
}