<?php
/**
 * Hbase
 *
 * @author
 * @copyright
 */
namespace Demo\Controllers;


use Phalcon\Mvc\Controller;

use SDK\Library\Db\Hbase\TIncrement;
use SDK\Library\Db\Hbase\Mutation;
use SDK\Library\Db\Hbase\BatchMutation;
use SDK\Library\Db\Hbase\ColumnDescriptor;
use SDK\Library\Db\Hbase\AlreadyExists;
use SDK\Library\Db\Hbase\IOError;
use SDK\Library\Db\Hbase\TScan;

class HbaseController extends Controller
{
    protected $table = "raw:test";

    public function onConstruct()
    {
    }

    public function initialize()
    {
    }

    /**
     * @debug http://demo.phalcon.loc/hbase
     */
    public function indexAction()
    {
        if ($this->request->isPost()) {
            $result['status'] = true;

            $table = $this->request->getPost('table');
            $column = $this->request->getPost('column');
            $rowkey = $this->request->getPost('rowkey');

            /** @var \SDK\Library\Db\Hbase\HbaseIf $client */
            $client = $this->hbase->client();
            $this->hbase->open();

            $startTime = microtime(true);
            $row = $client->get($table, $rowkey, $column, array());
            $endTime = microtime(true);
            $usedtime = $endTime - $startTime;
            $usedtime = number_format($usedtime, 6);

            $this->hbase->close();
            $data = array();
            foreach ($row as $k => $v) {
                $data[] = $v->value;
            }
            $result['value'] = var_export($data, true);

            $result['ms'] = $usedtime * 1000;
            echo json_encode($result);
            exit;
        }
    }

    /**
     * @debug http://demo.phalcon.loc/hbase/get
     */
    public function getAction()
    {
        /** @var \SDK\Library\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $table = 'test:demo';

        $rows = $client->get($table, pack('N', 10), 'd:value', array());

        foreach ($rows as $key => $row) {
            print_r($row->value);
        }

        $this->hbase->close();
    }

    /**
     * @debug http://demo.phalcon.loc/hbase/getrow
     */
    public function getRowAction()
    {
        /** @var \SDK\Library\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $t = "detailedinfo";

        $rows = $client->getRow($this->table, '100000', array());

        //print_r($rows);
        foreach ($rows as $key => $row) {
            echo "rowkey:" . $row->row;
            echo "<br/>";
            foreach ($row->columns as $k => $column) {
                echo "column:" . $k;
                echo "--value:" . $column->value;
                echo "<br/>";
            }
        }
        $this->hbase->close();
    }

    /**
     * @debug http://demo.phalcon.loc/hbase/create
     */
    public function createAction()
    {
        /** @var \SDK\Library\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $table = "php_demo";

        $tables = $client->getTableNames();
        if (array_search($table, $tables)) {
            if ($client->isTableEnabled($table)) {
                echo("disabling table: {$table}\n");
                $client->disableTable($table);
            }
            echo("deleting table: {$table}\n");
            $client->deleteTable($table);
        }

        $columnFamilys = array(
            new ColumnDescriptor(array('name' => 'entry:', 'maxVersions' => 10)),
            new ColumnDescriptor(array('name' => 'unused:'))
        );
        try {
            $client->createTable($table, $columnFamilys);

            echo("created table: {$table}\n");
            $descriptors = $client->getColumnDescriptors($table);
            asort($descriptors);
            foreach ($descriptors as $col) {
                echo("  column: {$col->name}, maxVer: {$col->maxVersions}\n");
            }
        } catch (AlreadyExists $e) {
            echo($e->message);
        }
        $this->hbase->close();
    }

    /**
     * @debug http://demo.phalcon.loc/hbase/put
     */
    public function putAction()
    {
        /** @var \SDK\Library\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();

        try {
            $rowkey = '001';
            $mutations = array(
                new Mutation(array('column' => 'd:url', 'value' => 'http://www.baidu.com')),
            );
            $client->mutateRow($this->table, $rowkey, $mutations, array());
        } catch (IOError $e) {
            echo($e->message);
        }

        $this->hbase->close();
    }

    /**
     * @debug http://demo.phalcon.loc/hbase/puts
     */
    public function putsAction()
    {
        /** @var \SDK\Library\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $table = "php_demo";

        $bitches = array();
        $rows = array();

        $urls = array(
            'http://www.163.com/1.html',
            'http://www.qq.com/1.html',
            'http://www.qq.com/2.html',
            'http://www.sohu.com/1.html',
            'http://www.sohu.com/2.html',
            'http://www.sohu.com/3.html',
            'http://www.jd.com/1.html',
            'http://www.jd.com/2.html',
            'http://www.jd.com/3.html',
            'http://www.jd.com/4.html',
        );

        $startTime = microtime(true);
        foreach ($urls as $key => $url) {
            $data = array();
            $data['row'] = md5($url) . "-" . date("YmdHis");
            $data['mutations'] = array(
                new Mutation(array('column' => 'entry:url', 'value' => $url)),
            );
            $rows[] = $data;
            array_push($bitches, new BatchMutation($rows));
        }
        $endTime = microtime(true);
        $usedtime = $endTime - $startTime;
        $usedtime = number_format($usedtime, 6);

        try {
            $client->mutateRows($table, $bitches, array());
        } catch (IOError $e) {
            echo $e->message;
        }

        echo $usedtime;

        $this->hbase->close();
    }

    /**
     * @debug http://demo.phalcon.loc/hbase/pack
     */
    public function packAction()
    {
        /** @var \SDK\Library\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $table = "test:demo";

        $bitches = array();
        $rows = array();

        for ($i = 1; $i < 100; $i++) {
            $data = array();
            $data['row'] = pack('N', $i) . '-' . time();
            $data['mutations'] = array(
                new Mutation(array('column' => 'd:value', 'value' => "value" . $i)),
            );
            array_push($bitches, new BatchMutation($data));
        }

        try {
            $client->mutateRows($table, $bitches, array());
        } catch (IOError $e) {
            echo $e->message;
        }


        $this->hbase->close();
    }

    /**
     * @debug http://demo.phalcon.loc/hbase/scan
     */
    public function scanAction()
    {
        /** @var \SDK\Library\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $table = "test:content_billion";

        //$scanner1 = $client->scannerOpen($table, "1190", array("data"), array());
        //print_r($client->scannerGetList($scanner1, 10));

        $scanner2 = $client->scannerOpenWithPrefix($table, "1", array("data"), array());
        print_r($client->scannerGetList($scanner2, 5));

        //$client->scannerClose($scanner1);
        $client->scannerClose($scanner2);

        $this->hbase->close();
    }

    /**
     * @debug http://demo.phalcon.loc/hbase/scanfilter
     */
    public function scanFilterAction()
    {
        /** @var \SDK\Library\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $table = "test:content_billion";

        # 过滤器
//        $filter = array();
//        $filter[] =
//        $filterString = implode(" AND ", $filter);

        $filterString = "RowFilter(=,'binaryprefix:123')";
        $scanFilter = new TScan();
        $scanFilter->filterString = $filterString;

        // 指定返回列族
        //$scanFilter->columns = array('column' => 'author');

        $scan = $client->scannerOpenWithScan($table, $scanFilter, array());
        $result = $client->scannerGetList($scan, 10);

        print_r($result);

        $this->hbase->close();
    }

    /**
     * @debug http://demo.phalcon.loc/hbase/delete
     */
    public function deleteAction()
    {
        /** @var \SDK\Library\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $table = "test:content_billion";

        for ($i = 1; $i < 1000000; $i++) {
            $client->deleteAllRow($table, $i, array());
            echo $i;
        }
        $this->hbase->close();
    }

    /**
     * @debug http://demo.phalcon.loc/hbase/count
     */
    public function countAction()
    {
        /** @var \SDK\Library\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $table = "test:content";

        $amount = $client->atomicIncrement('test:content', 'COUNTER5', 'd:id', 1);
        print_r($amount);
        exit;

        $this->hbase->close();
    }
}