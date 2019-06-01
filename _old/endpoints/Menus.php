<?php namespace NozCore\Endpoints;

use NozCore\Endpoint;

class Menus extends Endpoint {

    public function get() {
        $menu = new \NozCore\Objects\Menu\Menu();
        if($_REQUEST['endpoint'] == 'menus') {
            $this->result = $menu->getAll();
        } else if($_REQUEST['endpoint'] == 'menu' && isset($_REQUEST['id'])) {
            $this->result = $menu->get($_REQUEST['id']);
        }
    }
}