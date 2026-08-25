<?php

class Controller{
    public function view($vista, $datos = []){
        extract($datos);

        require_once __DIR__ . "/../views/" . $vista . ".php";
    }
}

?>