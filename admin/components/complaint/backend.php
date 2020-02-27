<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

    cpAddPathway('Пожаловаться на страницу', '?view=components&do=config&id='.$_REQUEST['id']);
	
    echo '<h3>Пожаловаться на страницу</h3>';
	
    if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }
	
    $toolmenu = array();

    $toolmenu[0]['icon'] = 'save.gif';
    $toolmenu[0]['title'] = 'Сохранить';
    $toolmenu[0]['link'] = 'javascript:document.optform.submit();';

    $toolmenu[1]['icon'] = 'cancel.gif';
    $toolmenu[1]['title'] = 'Отмена';
    $toolmenu[1]['link'] = '?view=components';

    cpToolMenu($toolmenu);

    //LOAD CURRENT CONFIG
    $cfg = $inCore->loadComponentConfig('complaint');

    if($opt=='saveconfig'){	
        $cfg = array();

        $cfg['sendto'] = $_REQUEST['sendto'];		
        $cfg['forms']   = $_REQUEST['forms'];

        $inCore->saveComponentConfig('complaint', $cfg);

        $msg = 'Настройки сохранены.';
    }

    global $_CFG;

    if(!isset($cfg['sendto'])) { $cfg['sendto'] = 'mail'; }
    
    $GLOBALS['cp_page_head'][] = '<link href="/includes/jquery/tabs/tabs.css" rel="stylesheet" type="text/css" />';
    $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/tabs/jquery.ui.min.js"></script>';

	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>
<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
<div id="config_tabs" style="margin-top:12px;">

    <ul id="tabs">
        <li><a href="#basic"><span>Общие настройки</span></a></li>
        <li><a href="#form"><span>Форма</span></a></li>
    </ul>
    
    <div id="basic">
    <table width="605" border="0" cellpadding="10" cellspacing="0" class="proptable">
        
            <tr>
                <td><strong>Шаблон уведомления о новой жалобе:</strong></td>
                <td><a href="/components/complaint/mail.txt">/components/complaint/mail.txt</a></td>
            </tr>
          <tr>
            <td><strong>Как отправлять письмо: </strong></td>
            <td>
                <select name="sendto" id="sendto" style="width:250px;">
                <option value="mail" <?php if(@$cfg['sendto']=='mail' || !isset($cfg['sendto'])) { echo 'selected'; } ?>>На адрес e-mail</option>
                <option value="user" <?php if(@$cfg['sendto']=='user') { echo 'selected'; } ?>>Личным сообщением на сайте</option>
                <option value="both" <?php if(@$cfg['sendto']=='both') { echo 'selected'; } ?>>E-mail + личное сообщение</option>
                </select>
            </td>
          </tr>
        </table>
    </div>
    
    <div id="form">
        <table width="605" border="0" cellspacing="0" cellpadding="10" class="proptable">
                <tr>
                    <td valign="top">
                        <p>Выберите, какие формы должны присутствовать при жалобе на страницу: </p>
                        <p>
                            <select name="forms[]" size="10" style="width:100%; border:solid 1px silver;" multiple="multiple">
                                <?php
                                if (!isset($cfg['forms'])) { $cfg['forms']=array(); }

                                $sql = "SELECT * FROM cms_forms";
                                $rs = dbQuery($sql);

                                if (mysql_num_rows($rs)){
                                    while($f = mysql_fetch_assoc($rs)){
                                        if (in_array($f['id'], $cfg['forms'])) { $selected='selected="selected"'; } else { $selected = ''; }
                                        echo '<option value="'.$f['id'].'" '.$selected.'>'.$f['title'].'</option>';
                                    }
                                }

                                ?>
                            </select>
                        </p>
                        <p>Можно выбрать несколько форм, удерживая CTRL.</p>
                        <p>Формы можно редактировать в настройках компонента <a href="index.php?view=components&do=config&id=<?php echo $inDB->get_field('cms_components', "link='forms'", 'id');?>">Конструктор форм</a>.</p>
                    </td>
                </tr>
            </table>
        </div>
    
    </div>
    <p>
        <input name="opt" type="hidden" value="saveconfig" />
        <input name="save" type="submit" id="save" value="Сохранить" />
        <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
    </p>
</form>
<script type="text/javascript">
$('#config_tabs > ul#tabs').tabs();
</script>