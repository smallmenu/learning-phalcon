<?php
/**
 * Hbase
 * @author
 * @copyright
 */
namespace Demo\Controllers;

use Demo\Models\Company51job;
use Demo\Models\ContentM;
use Demo\Models\Demo;
use Phalcon\Mvc\Controller;
use Extend\Db\Hbase\Mutation;
use Extend\Db\Hbase\BatchMutation;
use Extend\Db\Hbase\ColumnDescriptor;
use Extend\Db\Hbase\AlreadyExists;
use Extend\Db\Hbase\IOError;
use Extend\Db\Hbase\TScan;

class HbaseTestController extends Controller
{
    public function onConstruct()
    {
    }

    public function initialize()
    {
    }

    /**
     *
     * @debug http://demo.phalcon.loc/hbasetest/put
     */
    public function putAction()
    {
        /** @var \Extend\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $table = "test:content_m";

        try {
            $rowkey = '#'.uniqid();
            $mutations = array(
                new Mutation(array('column' => 'd:url', 'value' => 'http://www.baidu.com')),
                new Mutation(array('column' => 'd:html', 'value' => $this->getTxt()))
            );
            $startTime = microtime(true);
            $client->mutateRow($table, $rowkey, $mutations, array());
            $endTime = microtime(true);
            $usedtime = $endTime - $startTime;
            $usedtime = number_format($usedtime, 6);

            echo $usedtime;
        } catch (IOError $e) {
            echo($e->message);
        }

        $this->hbase->close();
    }

    /**
     *
     * @debug http://demo.phalcon.loc/hbasetest/putmysql
     */
    public function putmysqlAction()
    {
        $content  = new ContentM();

        $content->title = '1';
        $content->catid = 1;
        $content->modelid = 1;
        $content->created = 1;
        $content->createdby = 1;
        $content->status = 1;
        $content->weight = 1;
        $content->topicid = 1;
        $content->text = $this->getTxt();


        $startTime = microtime(true);
        if ($content->create() == false) {
            //foreach ($demo->getMessages() as $message) {
                //echo $message, "\n";
            //}
        } else {
            $endTime = microtime(true);
            $usedtime = $endTime - $startTime;
            $usedtime = number_format($usedtime, 6);
            echo $usedtime;
            //echo "Great, a new robot was created successfully!";
        }
    }



    /**
     *
     * @debug http://demo.phalcon.loc/hbasetest/puts
     */
    public function putsAction()
    {
        /** @var \Extend\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $table = "test:raw";

        $bitches = array();

        $rows = Company51job::find(array('limit'=>10))->toArray();

        foreach ($rows as $k => $d) {
            $data = array();
            $data['row'] = md5($d['id']);
            $data['mutations'] = array(
                new Mutation(array('column' => 'd:url', 'value' => $d['Url'])),
                new Mutation(array('column' => 'd:name', 'value' => $d['CompanyName'])),
                new Mutation(array('column' => 'd:html', 'value' => $d['Html'])),
            );
            array_push($bitches, new BatchMutation($data));
        }

        try {
            $client->mutateRows($table, $bitches, array());
        } catch (IOError $e) {
            echo $e->message;
        }

        echo 'done';

        $this->hbase->close();
    }

    /**
     *
     * @debug http://demo.phalcon.loc/hbasetest/get
     */
    public function getAction()
    {

        /** @var \Extend\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $table = "test:demo";

        $rows = $client->get($table, pack('N', 5), 'd:v', array());

        foreach ($rows as $key => $row) {
            print_r($row->value);
        }

        $this->hbase->close();
    }

    /**
     *
     * @debug http://demo.phalcon.loc/hbasetest/hash
     */
    public function hashAction()
    {
        /** @var \Extend\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $table = "test:demo";

        $bitches = array();

        $inserts = array(
            strtotime('2016-01-10 23:00:00'),
            strtotime('2016-01-10 22:00:00'),
            strtotime('2016-01-11 23:00:00'),
            strtotime('2016-01-11 22:00:00'),
            strtotime('2016-01-11 21:00:00'),
            strtotime('2016-01-11 20:00:00'),
            strtotime('2016-01-11 19:00:00'),
            strtotime('2016-01-11 18:00:00'),
            strtotime('2016-01-09 23:00:00'),
        );

        foreach ($inserts as $k => $i) {
            $data = array();
            $data['row'] = (md5($i) % 5).'#'.$i;
            //$data['row'] = PHP_INT_MAX - $i;
            //$data['row'] = md5($i);
            $data['mutations'] = array(
                new Mutation(array('column' => 'd:v', 'value' => date('Y-m-d H:i:s', $i))),
            );
            array_push($bitches, new BatchMutation($data));
        }

        try {
            $client->mutateRows($table, $bitches, array());
        } catch (IOError $e) {
            echo $e->message;
        }

        echo 'done';

        $this->hbase->close();
    }

    /**
     *
     * @debug http://demo.phalcon.loc/hbasetest/saltscan
     */
    public function saltScanAction()
    {
        /** @var \Extend\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $table = "test:demo";

        for ($region = 0; $region < 10; $region++) {
            $prefix = $region.'#';
            $filterString = "RowFilter(=,'binaryprefix:". $region. "')";
            $scanFilter = new TScan();
            $scanFilter->filterString = $filterString;
            $scanFilter->startRow = $prefix;

            $scan = $client->scannerOpenWithScan($table, $scanFilter, array());

            $result = $client->scannerGetList($scan, 10);
            print_r($result);
        }


        $this->hbase->close();
    }

}