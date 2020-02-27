<?php
/******************************************************************************/
//                       soft-solution.ru team                                //
/******************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_complaint{

    function __construct(){
        $this->inDB        = cmsDatabase::getInstance();
        $this->inCore      = cmsCore::getInstance();
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function install(){

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getForm($form_id, $item){
        
        $inPage = cmsPage::getInstance();

	$html   = '';
        
	global $_LANG;
        
	//GET FORM DATA
	$sql = "SELECT * FROM cms_forms	WHERE id = $form_id";
	$result = $this->inDB->query($sql);
	
	if (!$this->inDB->num_rows($result)) { return false; }

        $form = $this->inDB->fetch_assoc($result);
    
        //BUILD FORM
        if($form['description']) { $html .= '<div id="formdesc">'.$form['description'].'</div>'; }

        //GET FIELDS DATA
        $sql    = "SELECT * FROM cms_form_fields WHERE form_id = $form_id ORDER BY ordering ASC";
        $result = $this->inDB->query($sql);
        
        if ($this->inDB->num_rows($result)){
            //BUILD FORM FIELDS
            $html .= '<table class="coplaint_forma">';
            while ($field = $this->inDB->fetch_assoc($result)) {
                $html .= '<tr><td class="fieldtitle"><strong>'.$field['title'].'</strong>';
                
                if($field['mustbe']==1){
                    $html .= '<span class="star">*</span>';
                }
                
                if(is_array($item['fields'])){
                    if (array_key_exists($field['id'], $item['fields'])){
                        $default = $item['fields'][$field['id']];
                        $default = str_replace('&quot;', '"', $default);
                    } else {
                        $default = '';
                    }
                }
                
                $html .= '</td></tr><tr><td class="fieldvalue">'.$inPage->buildFormField(1, $field, $default);
                if(is_array($item['fields'])){
                    if (array_key_exists($field['id'], $item['validation'])){
                        $html .= '<br /><span class="required">Это поле обязательно для заполнения</span>';
                    }
                }
                $html .= '</td></tr>';
            }
            $html .= '</table>';
        } else { $html .= '<p>'.$_LANG['IN_FORM_NOT_FIELDS'].'</p>'; }

	return $html;

    }
    
/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getFields($form_id, $fields){

        //GET FIELDS DATA
        $sql    = "SELECT * FROM cms_form_fields WHERE form_id = $form_id ORDER BY ordering ASC";
        $result = $this->inDB->query($sql);
        
        if ($this->inDB->num_rows($result)){
            while ($field = $this->inDB->fetch_assoc($result)) {
                    $fields[$field['id']] = $field;
            }
        } else { return false;}

	return $fields;

    }

}
?>