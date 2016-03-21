<?php
namespace Demo\Tasks;

class CliTask extends \Phalcon\CLI\Task
{
    /**
     * @debug php cli.php cli
     */
    public function mainAction()
    {
        echo "\nThis is the CLI task and the default action \n";
    }

    /**
     * @debug php cli.php cli test
     */
    public function testAction()
    {
        echo "\nThis is the CLI task and the test action \n";
    }
}