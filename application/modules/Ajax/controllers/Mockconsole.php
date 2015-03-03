<?php
/**
 * Mock Server Manager Ajax Controller
 * @author Lancer He <lancer.he@gmail.com>
 * @since  2015-03-03
 */
class Controller_MockConsole extends \Core\Controller\Ajax {

    public function StartAction() {
        ( new \Service\Mock\Console() )->start();
        $this->getView()->displayAjax("Start Service Successfully.");
    }

    public function RestartAction() {
        ( new \Service\Mock\Console() )->restart();
        $this->getView()->displayAjax("Restart Service Successfully.");
    }
}