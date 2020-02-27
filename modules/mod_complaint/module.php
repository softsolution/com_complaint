<?php
/* soft-solution.ru created by AlexG */

function mod_complaint($module_id){
        $inCore = cmsCore::getInstance();

        $cfg = $inCore->loadModuleConfig($module_id);

        $smarty = $inCore->initSmarty('modules', 'mod_complaint.tpl');
        $smarty->display('mod_complaint.tpl');

        return true;

}
?>