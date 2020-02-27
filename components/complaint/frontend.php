<?php

/* * *************************************************************************** */
//                       soft-solution.ru team                                   //
/* * *************************************************************************** */
if (!defined('VALID_CMS')) {
    die('ACCESS DENIED');
}

function complaint() {

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inUser = cmsUser::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inConf = cmsConfig::getInstance();

    $inCore->loadModel('complaint');
    $model = new cms_model_complaint();

    $cfg = $inCore->loadComponentConfig('complaint');

    //Проверяем включени ли компонент
    if(!$cfg['component_enabled']) { cmsCore::error404(); }

    $user_id = $inUser->id;

    //config
    $do = $inCore->request('do', 'str', 'view');

// ===================================================================================================== //
// ============ главная страница компонента ============================================================ //
// ===================================================================================================== //
    if ($do == 'view') {
        
        $page_url = $inCore->request('page_url');
        $is_submit = $inCore->inRequest('page_url');

      
        if ($is_submit) {
            
            $errors = false;
            $error_mess = '';
            $item = array();
            $fields = array();
            
            
            
            // get fields
            if(isset($cfg['forms'])){
                if (is_array($cfg['forms'])){
                    foreach($cfg['forms'] as $num=>$form_id){
                        $fields = $model->getFields($form_id, $fields);
                    }
                }
            }
            
            if ($inCore->inRequest('field')){
                foreach($_POST['field'] as $k=>$val){
                    $item['fields'][$k] = $inCore->strClear($_POST['field'][$k]);

                    //check mustbe
                    if (array_key_exists($k, $fields)){
                        if(!$item['fields'][$k] && $fields[$k]['mustbe']==1){
                            $item['validation'][$k] = 1;
                            $errors = true;
                            $error_mess = 'Не заполнены обязательные поля!';
                        }
                    }
                }
            }
            
            $validation = array();
            
            //поля не входящие в форму
            $violation     = $inCore->request('violation', 'int');
            $comment       = $inCore->request('comment', 'str');
            $name          = $inCore->request('name');
            $email         = $inCore->request('email');
            $phone         = $inCore->request('phone');
            
            //проверяем обязательные поля
            if(!$violation || $violation>11 || $violation<0){ $errors = true; $error_mess = 'Не заполнены обязательные поля!'; $validation['violation']=1;}
            
            if(!$comment){$errors = true; $error_mess = 'Не заполнены обязательные поля!'; $validation['comment']=1; }
            
            if(!$name){$errors = true; $error_mess = 'Не заполнены обязательные поля!'; $validation['name']=1; }
            if (!$email || !preg_match('/^([a-z0-9\._-]+)@([a-z0-9\._-]+)\.([a-z]{2,4})$/i', $email)) { $validation['email']=1; $errors = true; }
            
            if(!$user_id){
                if (!$inCore->checkCaptchaCode($inCore->request('code', 'str'))) {
                    $errors = true;
                    $error_mess .= ' Неверно указан код с картинки!';
                }
            }

            //if there are errors
            if ($errors) {
                $is_submit = false;
            }

            //if there are no errors
            if (!$errors) {
                
                foreach($fields as $k=>$field){
                    $forma .= $field['title'] .' - '. $_POST['field'][$k]."\n\n";
                }
                
                //получаем дополнительную информацию о пользователе
                if(!$user_id){
                    $user_info = "Гость";
                } else {
                    $user_info = $inDB->get_fields('cms_users', "id='{$user_id}' AND is_locked = 0 AND is_deleted = 0", 'login, nickname, email');
                    $user_info = "Логин - ".$user_info['login'].", Никнейм - ".$user_info['nickname']." Профиль - ".HOST."/users/".$user_info['login'];
                }
                
                $phone = $phone ? $phone : 'Не указан';
                
                switch ($violation) {
                    case 1: $violation = "Информация, нарушающая авторские права"; break;
                    case 2: $violation = "Информация о товарах и услугах, не соответствующих законодательству"; break;
                    case 3: $violation = "Незаконно полученная частная и конфиденциальная информация"; break;
                    case 4: $violation = "Информация с множеством грамматических ошибок"; break;
                    case 5: $violation = "Информация непристойного содержания"; break;
                    case 6: $violation = "Содержание, связанное с насилием"; break;
                    case 7: $violation = "Спам, вредоносные программы и вирусы (в том числе ссылки)"; break;
                    case 8: $violation = "Информация о заработке в интернете"; break;
                    case 9: $violation = "Информация не носящая деловой характер"; break;
                    case 10: $violation = "Информация оскорбляющая честь и достоинство третьих лиц"; break;
                    case 11: $violation = "Другие нарушения правил размещения информации"; break;
                }
                
                //send mail
                //load mail template
                $letter_path = PATH.'/components/complaint/mail.txt';
                $letter      = file_get_contents($letter_path);

                //Replace tags in the template mail to the text
                $letter = str_replace('{sitename}', $inConf->sitename, $letter);
                $letter = str_replace('{page_url}', $page_url, $letter);
                
                $letter = str_replace('{violation}', $violation, $letter);
                $letter = str_replace('{comment}', $comment, $letter);
                
                $letter = str_replace('{user_info}', $user_info, $letter);
                
                $letter = str_replace('{email}', $email, $letter);
                $letter = str_replace('{phone}', $phone, $letter);
                $ip = $inUser->ip;
                $letter = str_replace('{ip}', $ip, $letter);
                
                $letter = str_replace('{forma}', $forma, $letter);
                
                if ($cfg['sendto']=='mail' || $cfg['sendto']=='both'){
                    
                    $inDB   = cmsDatabase::getInstance();
                    $email = $inDB->get_field('cms_users', "id='1'", 'email');
                    
                    if($email) {
                        $inCore->mailText($email, 'Новая жалоба на сайте '. $inConf->sitename, $letter);
                    }
                }
                
                if ($cfg['sendto']=='user' || $cfg['sendto']=='both'){
                    $letter = nl2br($letter);
                    $letter = str_replace('<br /><br /><br /><br />', '<br/>', $letter);
                    cmsUser::sendMessage(-2, 1, $letter);
                }
                
                $inPage->setTitle('Пожаловаться на страницу');
                $inPage->addPathway('Пожаловаться на страницу');

                $smarty = $inCore->initSmarty('components', 'com_complaint_success.tpl');
                $smarty->assign('page_url', $page_url);
                $smarty->display('com_complaint_success.tpl');

            }
        }
        
        if (!$is_submit) {

            $forms = '';
            if(isset($cfg['forms'])){
                if (is_array($cfg['forms'])){
                    foreach($cfg['forms'] as $num=>$form_id){
                        $forms .= $model->getForm($form_id, $item);
                    }
                }
            }
            
            if($user_id){
                $user_info = $inDB->get_fields('cms_users', "id='{$user_id}' AND is_locked = 0 AND is_deleted = 0", 'nickname, email');
                $email = $email ? $email : $user_info['email'];
                $name = $name ? $name : $user_info['nickname'];
            }

            $page_url = $page_url ?  $page_url : $_SERVER['HTTP_REFERER'];
            
            $inPage->setTitle('Пожаловаться на страницу');
            $inPage->addPathway('Пожаловаться на страницу');

            $smarty = $inCore->initSmarty('components', 'com_complaint.tpl');
            $smarty->assign('errors', $errors);
            $smarty->assign('error_mess', $error_mess);
            $smarty->assign('forms', $forms);
            $smarty->assign('page_url', $page_url);
            $smarty->assign('user_id', $user_id);
            $smarty->assign('violation', $violation);
            $smarty->assign('comment', $comment);
            $smarty->assign('name', $name);
            $smarty->assign('email', $email);
            $smarty->assign('phone', $phone);
            $smarty->assign('validation', $validation);
            $smarty->display('com_complaint.tpl');
        }
        
    }
}
?>