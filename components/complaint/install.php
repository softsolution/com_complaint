<?php

// ========================================================================== //

    function info_component_complaint(){

        //Описание компонента

        $_component['title']        = 'Пожаловаться на страницу';               //название
        $_component['description']  = 'Пожаловаться на страницу';               //описание
        $_component['link']         = 'complaint';                              //ссылка (идентификатор)
        $_component['author']       = 'soft-solution.ru';                       //автор
        $_component['internal']     = '0';                                      //внутренний (только для админки)? 1-Да, 0-Нет
        $_component['version']      = '1.0';                                    //текущая версия

        //Настройки по-умолчанию
        $_component['config'] = array();
        $_component['config']['sendto']='both';

        return $_component;

    }

// ========================================================================== //

    function install_component_complaint(){
        
        $inCore     = cmsCore::getInstance();       //подключаем ядро
        $inDB       = cmsDatabase::getInstance();   //подключаем базу данных
        $inConf     = cmsConfig::getInstance();

        include($_SERVER['DOCUMENT_ROOT'].'/includes/dbimport.inc.php');

        dbRunSQL($_SERVER['DOCUMENT_ROOT'].'/components/complaint/install.sql', $inConf->db_prefix);
        
        $module_id    = $inDB->get_last_id('cms_modules');

        $inDB->query("INSERT INTO cms_modules_bind (module_id, menu_id, position) VALUES ($module_id, 0, 'sidebar')");
        
        return true;

    }

// ========================================================================== //
    function upgrade_component_complaint(){

        return true;

    }

// ========================================================================== //

?>