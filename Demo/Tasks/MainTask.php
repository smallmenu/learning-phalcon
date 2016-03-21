<?php
namespace Demo\Tasks;

class MainTask extends \Phalcon\CLI\Task
{
    /**
     * @debug php cli.php main
     */
    public function mainAction()
    {
        echo "\nThis is the default task and the default action \n";
    }

    /**
     * @debug php cli.php main test
     */
    public function testAction()
    {
        echo "\nThis is the default task and the test action \n";
    }
}