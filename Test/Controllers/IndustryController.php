<?php
/**
 * Range
 *
 * @author
 * @copyright
 */

namespace Test\Controllers;

use Test\Models\Industry;
use Phalcon\Mvc\Controller;

class IndustryController extends Controller
{

    private $prefix = 'http://www.stats.gov.cn/zjtj/tjbz/tjypflml/2010/';

    private $keywords = array();

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
     * @debug http://test.phalcon.loc/industry/find
     */
    public function findAction()
    {
        $industrys = Industry::find(
            array(
                'conditions'=>'',
                'columns'=>'id, SubIndustry, SortKeywords',
            )
        )->toArray();

        foreach ($industrys as $key => $industry) {
            print_r($industry);
        }

    }

    /**
     *
     * @debug http://test.phalcon.loc/industry/sort
     */
    public function sortAction()
    {
        $lensort = function($a, $b) {
            $la = mb_strlen($a, 'UTF-8');
            $lb = mb_strlen($b, 'UTF-8');
            if ($la == $lb) {
                return 0;
            }
            return ($la > $lb) ? 1 : -1;
        };

        $industrys = Industry::find()->toArray();
        foreach ($industrys as $key => $industry) {

            $sortKeywords = explode(',', $industry['Keywords']);
            usort($sortKeywords, $lensort);
            $sortKeywords = implode(',', $sortKeywords);

            $update = Industry::findFirst($industry['id']);
            $update->ExtendSortKeywords = $sortKeywords;
            if ($update->save() == false) {
                foreach ($update->getMessages() as $error) {
                    echo $error, "\n";
                }
            } else {
                echo 'OK';
            }
        }
        exit;
    }

    /**
     *
     * @debug http://test.phalcon.loc/industry/extend
     */
    public function entendAction()
    {
        $lensort = function($a, $b) {
            $la = mb_strlen($a, 'UTF-8');
            $lb = mb_strlen($b, 'UTF-8');
            if ($la == $lb) {
                return 0;
            }
            return ($la > $lb) ? 1 : -1;
        };

        $industrys = Industry::find()->toArray();
        foreach ($industrys as $key => $industry) {

            $keywords = explode(',', $industry['Keywords']);
            $sortKeywords = array();
            foreach ($keywords as $k => $keyword) {
                if (strpos($keyword, '、') !== false) {
                    $cells = explode('、', $keyword);
                    foreach ($cells as $kk => $cell) {
                        if (array_search($cell, $sortKeywords) === false) {
                            array_push($sortKeywords, $cell);
                        }
                    }
                } else {
                    array_push($sortKeywords, $keyword);
                }
            }

            usort($sortKeywords, $lensort);
            $sortKeywords = implode(',', $sortKeywords);

            $update = Industry::findFirst($industry['id']);
            $update->ExtendSortKeywords = $sortKeywords;
            if ($update->save() == false) {
                foreach ($update->getMessages() as $error) {
                    echo $error, "\n";
                }
            } else {
                echo 'OK';
            }
        }
        exit;
    }
}