<?php
namespace Test\Tasks;

use Test\Models\Industry;

class MainTask extends \Phalcon\CLI\Task
{
    private $prefix = 'http://www.stats.gov.cn/zjtj/tjbz/tjypflml/2010/';

    private $keywords = array();

    /**
     * @debug php cli.php main
     */
    public function mainAction()
    {
        $industrys = Industry::find()->toArray();

        foreach ($industrys as $key => $industry) {
            if ($industry['SubIndustryCode'] == 37) {
                $this->keywords = array();
                $this->_list($industry['SubIndustryCode'].'.html', $this->prefix);
                $insert = Industry::findFirst($industry['id']);
                $insert->Keywords = implode(',', $this->keywords);

                if ($insert->save() == false) {
                    foreach ($insert->getMessages() as $error) {
                        echo $error, "\n";
                    }
                } else {
                    echo PHP_EOL;
                    echo $industry['SubIndustryCode']. '/' .$industry['SubIndustry']. '/Done';
                    echo PHP_EOL;
                }
            }
        }
    }

    /**
     * 列表
     *
     * @param $path
     * @param $prefix
     */
    public function _list($path, $prefix)
    {
        $url = $prefix. $path;
        $response = $this->_request($url);
        if ($response['httpcode'] == 200) {
            $content = iconv('GBK', 'UTF-8', $response['content']);

            $m = preg_match_all('#<td><a href=\'((.*)\.html)\'>(.*)</a></td>#U', $content, $matches);
            $end = preg_match_all('#<tr class=\'villagetr\'><td>(.*)</td><td>(.*)</td><td>(.*)</td></tr>#U', $content, $endmatches);

            // 最终页无连接
            if ($end) {
                $endlists = isset($endmatches[2]) ? $endmatches[2] : null;
                if ($endlists) {
                    foreach ($endlists as $key => $list) {
                        $list = trim($list);
                        if (array_search($list, $this->keywords) === false) {
                            array_push($this->keywords, $list);
                        }
                    }
                }
            }

            // 列表页
            $lists = isset($matches[3]) ? $matches[3] : null;
            $urls = isset($matches[2]) ? $matches[2] : null;
            if ($lists) {
                foreach ($lists as $key => $list) {
                    if (!preg_match('#^\d+$#', $list, $m)) {
                        $list = trim($list);
                        if (array_search($list, $this->keywords) === false) {
                            array_push($this->keywords, $list);
                        }
                        $split = preg_split('#(\d+\.html)#', $url);
                        $this->_list($urls[$key].'.html', $split[0]);
                    }
                }
            }
        }
    }

    /**
     * @param $url
     * @param null $post
     * @param int $timeout
     * @param bool $sendcookie
     * @param array $options
     * @return array
     */
    private function _request($url, $post = null, $timeout = 10, $sendcookie = true, $options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'cmstopinternalloginuseragent');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 35);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout ? $timeout : 40);
        if ($sendcookie) {
            $cookie = '';
            foreach ($_COOKIE as $key => $val) {
                $cookie .= rawurlencode($key) . '=' . rawurlencode($val) . ';';
            }
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($post) ? http_build_query($post) : $post);
        }

        if (!ini_get('safe_mode') && ini_get('open_basedir') == '') {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        foreach ($options as $key => $value) {
            curl_setopt($ch, $key, $value);
        }

        $ret = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_length = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        if (!$content_length) $content_length = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        return array(
            'httpcode'       => $httpcode,
            'content_length' => $content_length,
            'content_type'   => $content_type,
            'content'        => $ret
        );
    }
}