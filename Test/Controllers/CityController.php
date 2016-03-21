<?php
/**
 * City
 *
 * @author
 * @copyright
 */

namespace Test\Controllers;

use Phalcon\Mvc\Controller;
use Test\Models\City;
use Test\Models\TextCityData;

class CityController extends Controller
{
    /**
     * __construct alias
     */
    public function onConstruct()
    {
    }

    /**
     * 初始化控制器
     */
    public function initialize()
    {
        $this->view->disable();
    }

    /**
     *
     * @debug http://test.phalcon.loc/city
     */
    public function indexAction()
    {
        $provinces = array (
            'BJ' => '北京',
            'SH' => '上海',
            'TJ' => '天津',
            'CQ' => '重庆',
            'HB' => '河北',
            'SX' => '山西',
            'HEN' => '河南',
            'LN' => '辽宁',
            'JL' => '吉林',
            'HLJ' => '黑龙江',
            'NMG' => '内蒙古',
            'JS' => '江苏',
            'SD' => '山东',
            'AH' => '安徽',
            'ZJ' => '浙江',
            'HUB' => '湖北',
            'FJ' => '福建',
            'GD' => '广东',
            'HUN' => '湖南',
            'JX' => '江西',
            'GX' => '广西',
            'HAIN' => '海南',
            'SC' => '四川',
            'YN' => '云南',
            'GZ' => '贵州',
            'SAX' => '陕西',
            'XZ' => '西藏',
            'QH' => '青海',
            'GS' => '甘肃',
            'XJ' => '新疆',
            'NX' => '宁夏',
        );

        foreach ($provinces as $key => $province) {
            $citys = TextCityData::find(
                array(
                    'conditions' => "province = '{$province}'",
                    'group' => 'city_code',
                )
            )->toArray();

            if ($citys) {
                foreach ($citys as $k => $city) {
                    $areas = TextCityData::find(
                        array(
                            'conditions' => "province = '{$province}' AND city_code = '{$city['city_code']}' ",
                            'group' => 'area',
                        )
                    )->toArray();
                    if ($areas) {
                        $keywords = array();
                        foreach ($areas as $j => $area) {

                            $areavalue = trim($area['area']);

                            // 过滤
                            if (strpos($areavalue, '已合并') || strpos($areavalue, '属于') || strpos($areavalue, '更名')) {
                                continue;
                            }

                            // 带括号以前面为准
                            $matches = array();
                            if (preg_match('#(.*)\((.*)\)#', $areavalue, $matches)) {
                                if (isset($matches[1])) {
                                    $oldarea = trim($matches[1]);
                                    $oldarea = preg_replace('#(.*)区$#', '$1', $oldarea);
                                    $areavalue = trim($oldarea);
                                }
                            }

                            if (array_search($areavalue, $keywords) === false) {
                                $keywords[] = $areavalue;
                            }
                        }


                        $insert = new City();
                        $insert->ProvinceCode = $key;
                        $insert->Province = $province;
                        $insert->CityCode = $city['city_code'];
                        $insert->City = $city['city'];
                        $insert->CityKeywords = implode('|', $keywords);

                        if ($insert->create() == false) {
                            foreach ($insert->getMessages() as $error) {
                                echo $error, "\n";
                            }
                        } else {
                            echo 'OK';
                            echo PHP_EOL;
                        }
                    }
                }
            }

        }
    }


}