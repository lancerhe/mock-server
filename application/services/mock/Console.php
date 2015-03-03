<?php
/**
 * Mock console for start, stop mock server.
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-02-20
 */
namespace Service\Mock;

class Console {

    protected $_status = false; // 0 stop, 1 runing

    public function status() {
        $processes = shell_exec("cd .. && ps -ef | grep 'node service.js' | grep -v grep | wc -l");
        return $processes > 0 ? true : false;
    }

    public function start() {
        if ( $this->status() ) 
            throw new \Core\Exception("Mock service is running.");

        popen("cd ".ROOT_PATH." && nohup node service.js & echo $!", 'r');
        if ( ! $this->status() ) 
            throw new \Core\Exception("Mock service start failure.");
    }

    public function stop() {
        if ( ! $this->status() )
            throw new \Core\Exception("Mock service is not running.");

        shell_exec("kill -9 `cd .. && ps -ef | grep 'node service.js' | grep -v grep | awk '{print $2}'`");
        if ( $this->status() ) 
            throw new \Core\Exception("Mock service stop failure.");
    }

    public function restart() {
        $this->stop();
        $this->start();
    }
}