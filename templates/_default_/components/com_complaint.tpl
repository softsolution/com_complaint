{* ================================================================================ *}
{* =========== Пожаловаться на страницу  ========================================== *}
{* ================================================================================ *}

{add_css file="templates/_default_/css/complaint.css"}

<h1 class="con_heading">Пожаловаться на страницу</h1>
<div class=clear></div>

{if $errors}<p style="color:red">{$error_mess}</p>{/if}

<div id="complaint">
<form action="" method="POST" name="complaint">
    
    <table width="100%" border="0" cellspacing="0" class="complainttbl">
        <tr>
            <td><strong>Адрес страницы с нарушением</strong></td>
        </tr>
        <tr>
            <td><span id="page_url">{if $page_url}{$page_url}{else}<span style="color:red">Страница не определена!</span>{/if}</span>
                <input name="page_url" type="hidden" value="{$page_url}" />
            </td>
        </tr>
    </table>
            
    {$forms}
    
    <table width="100%" border="0" cellspacing="0" class="complainttbl">
        <tr>
            <td><strong>Нарушение</strong><span class="star">*</span></td>
        </tr>
        <tr>
            <td>
                <select name="violation" size="11" class="violation">
                    <option value="1" {if $violation ==1} selected{/if}>Информация, нарушающая авторские права</option>
                    <option value="2" {if $violation ==2} selected{/if}>Информация о товарах и услугах, не соответствующих законодательству</option>
                    <option value="3" {if $violation ==3} selected{/if}>Незаконно полученная частная и конфиденциальная информация</option>
                    <option value="4" {if $violation ==4} selected{/if}>Информация с множеством грамматических ошибок</option>
                    <option value="5" {if $violation ==5} selected{/if}>Информация непристойного содержания</option>
                    <option value="6" {if $violation ==6} selected{/if}>Содержание, связанное с насилием</option>
                    <option value="7" {if $violation ==7} selected{/if}>Спам, вредоносные программы и вирусы (в том числе ссылки)</option>
                    <option value="8" {if $violation ==8} selected{/if}>Информация о заработке в интернете</option>
                    <option value="9" {if $violation ==9} selected{/if}>Информация не носящая деловой характер</option>
                    <option value="10" {if $violation ==10} selected{/if}>Информация оскорбляющая честь и достоинство третьих лиц</option>
                    <option value="11" {if $violation ==11} selected{/if}>Другие нарушения правил размещения информации</option>
                </select>
                {if $validation.violation}<br /><span class="required">Это поле обязательно для заполнения</span>{/if}
            </td>
        </tr>
        <tr>
            <td><strong>Комментарий</strong><span class="star">*</span></td>
        </tr>
        <tr>
            <td>
                <textarea rows="5" name="comment" class="complaint_textarea">{$comment}</textarea>
                <p class="description">Опишите причину нарушения.</p>
                {if $validation.comment}<br /><span class="required">Это поле обязательно для заполнения</span>{/if}
            </td>
        </tr>
        <tr>
            <td><strong>Ваше имя</strong><span class="star">*</span></td>
        </tr>
        <tr>
            <td><input name="name" type="text" value="{$name}" class="complaint_itext" />
                {if $validation.name}<br /><span class="required">Это поле обязательно для заполнения</span>{/if}
            </td>
        </tr>
        <tr>
            <td><strong>Email для обратной связи</strong><span class="star">*</span></td>
        </tr>
        <tr>
            <td><input name="email" type="text" value="{$email}" class="complaint_itext" />
                {if $validation.email}<br /><span class="required">Поле не заполнено или не корректный адрес</span>{/if}
            </td>
        </tr>
        <tr>
            <td><strong>Контактный телефон</strong></td>
        </tr>
        <tr>
            <td><input name="phone" type="text" value="{$phone}" class="complaint_itext" />
            </td>
        </tr>
    </table>

    {if !$user_id}
    <div class="captcha">
        {php}echo cmsPage::getCaptcha();{/php}
    </div>
    {/if}
    
    <div style="margin:15px 0 0 0;">
        <input name="submit" type="submit" id="submit" onclick="sendComplaint()" value="Отправить"/>
        <input type="button" name="cancel" onclick="window.history.go(-1)" value="{$LANG.CANCEL}"/>
    </div>
    
</form>
</div>