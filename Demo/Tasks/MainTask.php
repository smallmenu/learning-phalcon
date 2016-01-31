<?php
namespace Demo\Tasks;

class MainTask extends \Phalcon\CLI\Task
{
    public function mainAction()
    {
        //exit;
        /** @var \Extend\Db\Hbase\HbaseIf $client */
        $client = $this->hbase->client();
        $this->hbase->open();
        $table = 'test:content_billion';

        for ($i = 1000000; $i < 10000000; $i++) {
            echo "NO:". $i."||";

            $startTime = microtime(true);
            $client->deleteAllRow($table, $i, array());
            $endTime = microtime(true);
            $usedtime = number_format($endTime - $startTime, 6);

            echo "Used:".$usedtime; echo PHP_EOL;
        }
        $this->hbase->close();
    }
}