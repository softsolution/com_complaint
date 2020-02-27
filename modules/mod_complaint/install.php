<?php
function info_module_mod_complaint(){
        $_module['title']         = 'Пожаловаться на страницу';
        $_module['name']          = 'Пожаловаться на страницу';
        $_module['description']   = 'Модуль пожаловаться на страницу';
        $_module['link']          = 'mod_complaint';
        $_module['position']      = 'sidebar';
        $_module['author']        = 'soft-solution.ru';
        $_module['version']       = '1.0';

        $_module['config'] = array();

        return $_module;

    }

    function install_module_mod_complaint(){

        return true;

    }

    function upgrade_module_mod_complaint(){

        return true;

    }

?>