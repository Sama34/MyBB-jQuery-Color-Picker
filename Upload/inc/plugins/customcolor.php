<?php

/***************************************************************************
 *
 *	MyBB jQuery Color Picker plugin (/inc/plugins/customcolor.php)
 *  Authors: Omar Gonzalez & Vintagedaddyo & iAndrew & AmazOuz
 *  Copyright: © 2022
 *
 *  Websites: https://github.com/Sama34 https://github.com/Vintagedaddyo
 *
 *  Based off: https://community.mybb.com/thread-158934-post-1343419.html#pid1343419
 *
 *	Allow your users to select custom colors to adapt the theme display.
 *
 ***************************************************************************
 
****************************************************************************
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
****************************************************************************/

if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

if(!defined('IN_ADMINCP'))
{
    // Add hooks

    $plugins->add_hook('global_intermediate', 'customcolor_headerinclude');

    $plugins->add_hook("usercp_options_end", "customcolor_usercp_options_end");

    $plugins->add_hook("misc_start", "customcolor_misc_start");

    $plugins->add_hook("xmlhttp", "customcolor_xmlhttp");

    // Templatelist

    global $templatelist;
    
    if(isset($templatelist))
    {
        $templatelist .= ',';
    }

    else
    {
        $templatelist = '';
    }
    
    $templatelist .= 'customcolor_headerinclude, customcolor_footer, customcolor_input';

    if(defined('THIS_SCRIPT') && THIS_SCRIPT == 'usercp.php')
    {
        $templatelist .= ', customcolor_usercp';
    }
}

function customcolor_info()
{
    // Info

    global $lang;

    customcolor_lang_load();

    // Desc

    $lang->desc_plugin .= '<br />This work is inspired off the work by: <strong><a href="https://iandrew.org/">iAndrew</a></strong>, <a href="https://iandrew.org/">AmazOuz</a>, and <strong><a href="https://community.mybb.com/thread-158934-post-1343419.html#pid1343419">Vintagedaddyo</a></strong>.';

    return array(
        "name"          => $lang->title_plugin,
        "description"   => $lang->desc_plugin,
		'website'		=> 'https://github.com/vintagedaddyo/MyBB-jQuery-Color-Picker',
		'author'		=> 'Omar G. & Vintagedaddyo',
		'authorsite'	=> 'https://github.com/vintagedaddyo/MyBB-jQuery-Color-Picker',
        "version"       => "1.2",
        "codename"      => "customcolor",
        "compatibility" => "18*"
    );
}

function customcolor_lang_load()
{
    // Lang load

	global $lang;

	isset($lang->title_plugin) or $lang->load('customcolor');

}

function customcolor_install()
{
    // Install

    global $db;

    customcolor_is_installed() || $db->add_column('users', 'customcolor', "VARCHAR(6) DEFAULT ''");
}

function customcolor_uninstall()
{
    // Uninstall

    global $db;

    !customcolor_is_installed() || $db->drop_column('users', 'customcolor');

	// Delete stylesheet

    $db->delete_query('themestylesheets', "name='customcolor.css'");

    $query = $db->simple_select('themes', 'tid');

    while($theme = $db->fetch_array($query))
    {
        require_once MYBB_ADMIN_DIR.'inc/functions_themes.php';

        update_theme_stylesheet_list($theme['tid']);
    }

	// Delete settings

    $db->write_query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN ('customcolor_default','customcolor_backgrounds','customcolor_borders','customcolor_fills','customcolor_texts')");

    $db->write_query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name = 'customcolor'");

    // Rebuild settings

    rebuild_settings();

	// Delete template group

    $db->delete_query("templategroups", "prefix = 'customcolor'");

    $update_array = array(
        'sid' => '-1'
    );

    $db->update_query("templates", $update_array, "title like '%customcolor_%'");

}

function customcolor_is_installed()
{
    // Is installed

    global $db;

    return $db->field_exists('customcolor', 'users');
}

function customcolor_activate()
{
    // Activate

    global $lang, $db;

    customcolor_lang_load();

    // Add stylesheet

$stylesheet = '/***************************************************************************
*
*   MyBB jQuery Color Picker plugin (CSS FILE)
*   Authors: Omar Gonzalez & Vintagedaddyo & iAndrew & AmazOuz
*   Copyright: © 2022
*
*   Websites: https://github.com/Sama34 https://github.com/Vintagedaddyo
*
*   Based off: https://community.mybb.com/thread-158934-post-1343419.html#pid1343419
*
*   Allow your users to select custom colors to adapt the theme display.
*
***************************************************************************

****************************************************************************
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
****************************************************************************/

#colorpicker.color_select {
    height: 32px;
    width: 32px;
    border-radius: 50%;
}
#colorpicker {
    border: 0;
    text-indent: -999px;
    width: 18px;
    cursor: pointer;
}
.colorpicker * {
       -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
}
.colorpicker {
    width: 356px;
    height: 176px;
    overflow: hidden;
    position: absolute;
    background: url(images/cp/custom_background.png);
    font-family: Arial, Helvetica, sans-serif;
    display: none;
}
.colorpicker_color {
    width: 150px;
    height: 150px;
    left: 14px;
    top: 13px;
    position: absolute;
    background: #f00;
    overflow: hidden;
    cursor: crosshair;
}
.colorpicker_color div {
    position: absolute;
    top: 0;
    left: 0;
    width: 150px;
    height: 150px;
    background: url(images/cp/colorpicker_overlay.png);
}
.colorpicker_color div div {
    position: absolute;
    top: 0;
    left: 0;
    width: 11px;
    height: 11px;
    overflow: hidden;
    background: url(images/cp/colorpicker_select.gif);
    margin: -5px 0 0 -5px;
}
.colorpicker_hue {
    position: absolute;
    top: 13px;
    left: 171px;
    width: 35px;
    height: 150px;
    cursor: n-resize;
}
.colorpicker_hue div {
    position: absolute;
    width: 35px;
    height: 9px;
    overflow: hidden;
    background: url(images/cp/custom_indic.gif) left top;
    margin: -4px 0 0 0;
    left: 0px;
}
.colorpicker_new_color {
    position: absolute;
    width: 60px;
    height: 30px;
    left: 213px;
    top: 13px;
    background: #f00;
}
.colorpicker_current_color {
    position: absolute;
    width: 60px;
    height: 30px;
    left: 283px;
    top: 13px;
    background: #f00;
}
.colorpicker input {
    background-color: transparent;
    border: 1px solid transparent;
    position: absolute;
    font-size: 10px;
    font-family: Arial, Helvetica, sans-serif;
    color: #898989;
    top: 4px;
    right: 11px;
    text-align: right;
    margin: 0;
    padding: 0;
    height: 11px;
}
.colorpicker_hex {
    position: absolute;
    width: 72px;
    height: 22px;
    background: url(images/cp/custom_hex.png) top;
    left: 212px;
    top: 142px;
}
.colorpicker_hex input {
    right: 6px;
}
.colorpicker_field {
    height: 22px;
    width: 62px;
    background-position: top;
    position: absolute;
    display: none; /* hide fields */
}
.colorpicker_field span {
    position: absolute;
    width: 12px;
    height: 22px;
    overflow: hidden;
    top: 0;
    right: 0;
    cursor: n-resize;
}
.colorpicker_rgb_r {
    background-image: url(images/cp/custom_rgb_r.png);
    top: 52px;
    left: 212px;
}
.colorpicker_rgb_g {
    background-image: url(images/cp/custom_rgb_g.png);
    top: 82px;
    left: 212px;
}
.colorpicker_rgb_b {
    background-image: url(images/cp/custom_rgb_b.png);
    top: 112px;
    left: 212px;
}
.colorpicker_hsb_h {
    background-image: url(images/cp/custom_hsb_h.png);
    top: 52px;
    left: 282px;
}
.colorpicker_hsb_s {
    background-image: url(images/cp/custom_hsb_s.png);
    top: 82px;
    left: 282px;
}
.colorpicker_hsb_b {
    background-image: url(images/cp/custom_hsb_b.png);
    top: 112px;
    left: 282px;
}
.colorpicker_submit {
    position: absolute;
    width: 22px;
    height: 22px;
    background: url(images/cp/custom_submit.png) top;
    left: 322px;
    top: 142px;
    overflow: hidden;
}
.colorpicker_focus {
    background-position: center;
}
.colorpicker_hex.colorpicker_focus {
    background-position: bottom;
}
.colorpicker_submit.colorpicker_focus {
    background-position: bottom;
}
.colorpicker_slider {
    background-position: bottom;
}';

    $new_stylesheet = array(
        'name'         => 'customcolor.css',
        'tid'          => 1,
        'attachedto'   => '',
        'stylesheet'   => $stylesheet,
        'lastmodified' => TIME_NOW
    );

    $sid = $db->insert_query('themestylesheets', $new_stylesheet);

    $db->update_query('themestylesheets', array('cachefile' => "css.php?stylesheet={$sid}"), "sid='{$sid}'", 1);

    $query = $db->simple_select('themes', 'tid');

    while($theme = $db->fetch_array($query))
    {
        require_once MYBB_ADMIN_DIR.'inc/functions_themes.php';

        update_theme_stylesheet_list($theme['tid']);
    }


	// Add our settings

	// Setting group Color Changer
    
    $setting_group = array(  
        'name'          => 'customcolor',
        'title'         =>  $lang->customcolor_group_title,
        'description'   =>  $lang->customcolor_group_desc,
        'disporder'     => '1'
    );

    $db->insert_query('settinggroups', $setting_group);

    $gid = $db->insert_id();
    
    // Setting 1 Default Color

    $setting_1 = array(
        'name'          => 'customcolor_default',
        'title'         => $lang->customcolor_default_title,
        'description'   => $lang->customcolor_default_desc,
        'optionscode'   => 'text', 
        'value'         => '008dd4', 
        'disporder'     => '1', 
        'gid'           => intval($gid)
    );

    $db->insert_query('settings', $setting_1);
    
    // Setting 2 Background Elements

    $setting_2 = array(
        'name'          => 'customcolor_backgrounds',
        'title'         => $lang->customcolor_customElements_title,
        'description'   => $lang->customcolor_customElements_desc,
        'optionscode'   => 'textarea', 
        'value'         => '#search input.button, .thead', 
        'disporder'     => '2', 
        'gid'           => intval($gid)
    );

    $db->insert_query('settings', $setting_2);
    
    // Setting 3 Border Elements

    $setting_3 = array(
        'name'          => 'customcolor_borders',
        'title'         => $lang->customcolor_customBorders_title,
        'description'   => $lang->customcolor_customBorders_desc,
        'optionscode'   => 'textarea', 
        'value'         => '.postbit_buttons > a:link', 
        'disporder'     => '3', 
        'gid'           => intval($gid)
    );

    $db->insert_query('settings', $setting_3);
    
    // Setting 4 Fill Elements

    $setting_4 = array(
        'name'          => 'customcolor_fills',
        'title'         => $lang->customcolor_customFills_title,
        'description'   => $lang->customcolor_customFills_desc,
        'optionscode'   => 'textarea', 
        'value'         => '#logo-svg', 
        'disporder'     => '4', 
        'gid'           => intval($gid)
    );

    $db->insert_query('settings', $setting_4);


    // Setting 5 Text Elements

    $setting_5 = array(
        'name'          => 'customcolor_texts',
        'title'         => $lang->customcolor_customTexts_title,
        'description'   => $lang->customcolor_customTexts_desc,
        'optionscode'   => 'textarea', 
        'value'         => '.top_links a:link, .top_links a:hover, .top_links a:focus, .top_links a:visited, .navigation a:link, .navigation a:hover, .navigation a:focus, .navigation a:visited, .trow1 a:link, .trow1 a:hover, .trow1 a:focus, .trow1 a:visited,  .trow2 a:link, .trow2 a:hover, .trow2 a:focus, .trow2 a:visited, #footer .lower span#copyright  a:link', 
        'disporder'     => '5', 
        'gid'           => intval($gid)
    );

    $db->insert_query('settings', $setting_5);

    // rebuild

    rebuild_settings();    


	// Insert template group

	// Template group Custom Color
    
    $template_group = array(
        'prefix' => 'customcolor',
        'title' => 'Custom Color',
        'isdefault' => 0
    );

    $db->insert_query('templategroups', $template_group);


    // Template _headerinclude

    $insert_array_1 = array(
        'title' => 'customcolor_headerinclude',
        'template' => $db->escape_string('{$ucp}
<link rel="stylesheet" type="text/css" href="{$mybb->settings[\'bburl\']}/xmlhttp.php?action=customcolor" />'),
        'sid' => '-2',
        'version' => '',
        'dateline' => time()
        );

    $db->insert_query('templates', $insert_array_1);

    // Template _footer

    $insert_array_2 = array(
        'title' => 'customcolor_footer',
        'template' => $db->escape_string('<script type="text/javascript" src="{$mybb->settings[\'bburl\']}/jscripts/customcolor/colorpicker.js"></script>
<script type="text/javascript" src="{$mybb->settings[\'bburl\']}/jscripts/customcolor/cookie.js"></script>
<script>
$(document).ready(function($){
    $("#colorpicker").ColorPicker({
        onSubmit: function(hsb, hex, rgb, el) {
            $(el).val(hex);
            $(el).ColorPickerHide();
            $(el).css("backgroundColor", "#" + hex);
            $.ajax({
                    url : "misc.php?action=customcolor",
                    type : "POST",
                    data : "color=" + hex,
                    dataType : "html",
                    success : function(code_html, statut){
                            location.reload();
                    }
            });
        },
        onBeforeShow: function () {
            $(this).ColorPickerSetColor(this.value);
        },
    })
    .bind("keyup", function(){
        $(this).ColorPickerSetColor(this.value);
    });
});
</script>'),
        'sid' => '-2',
        'version' => '',
        'dateline' => time()
    );

    $db->insert_query('templates', $insert_array_2);

    // Template _usercp

    $insert_array_3 = array(
        'title' => 'customcolor_usercp',
        'template' => $db->escape_string('<tr>
    <td colspan="2"><span><strong>{$lang->colorucp}</strong></span></td>
</tr>
<tr>
    <td colspan="2">
        <p class="smalltext">{$lang->colorucp_input}</p>
        <span class="theme_color" title="{$lang->colorchoose}"><input class="color_select" type="text" id="colorpicker" value="{$mybb->settings[\'customcolor_default\']}" /></span>
    </td>
</tr>
<tr>
    <td colspan="2">
        <p>{$lang->colorucp_desc}</p>
    </td>
</tr>'),
        'sid' => '-2',
        'version' => '',
        'dateline' => time()
    );

    $db->insert_query('templates', $insert_array_3);

    // Template _css_backgrounds

    $insert_array_4 = array(
        'title' => 'customcolor_css_backgrounds',
        'template' => $db->escape_string('{$mybb->settings[\'customcolor_backgrounds\']}
{ 
    background: #{$color}; 
}'),
        'sid' => '-2',
        'version' => '',
        'dateline' => time()
    );

    $db->insert_query('templates', $insert_array_4);

    // Template _css_borders

    $insert_array_5 = array(
        'title' => 'customcolor_css_borders',
        'template' => $db->escape_string('{$mybb->settings[\'customcolor_borders\']}
{ 
    border-color: #{$color}; 
}'),
        'sid' => '-2',
        'version' => '',
        'dateline' => time()
    );

    $db->insert_query('templates', $insert_array_5);

    // Template _css_fills

    $insert_array_6 = array(
        'title' => 'customcolor_css_fills',
        'template' => $db->escape_string('{$mybb->settings[\'customcolor_fills\']}
{ 
    fill: #{$color}; 
}'),
        'sid' => '-2',
        'version' => '',
        'dateline' => time()
    );

    $db->insert_query('templates', $insert_array_6);

    // Template _css_texts

    $insert_array_7 = array(
        'title' => 'customcolor_css_texts',
        'template' => $db->escape_string('{$mybb->settings[\'customcolor_texts\']}
{ 
    color: #{$color}; 
}'),
        'sid' => '-2',
        'version' => '',
        'dateline' => time()
    );

    $db->insert_query('templates', $insert_array_7);


	// Modify some templates.

	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';

	find_replace_templatesets('headerinclude', '#'.preg_quote('{$stylesheets}').'#i', '{$stylesheets}{$customcolor_headerinclude}');

	find_replace_templatesets('footer', '#'.preg_quote('{$auto_dst_detection}').'#i', '{$auto_dst_detection}{$customcolor_footer}');

	find_replace_templatesets('usercp_options', '#'.preg_quote('{$board_style}').'#i', '{$board_style}{$customcolor}');

}

function customcolor_deactivate()
{
    // Deactivate

	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';

    find_replace_templatesets('headerinclude', '#'.preg_quote('{$customcolor_headerinclude}').'#i', '', 0);

    find_replace_templatesets('footer', '#'.preg_quote('{$customcolor_footer}').'#i', '', 0);

    find_replace_templatesets('usercp_options', '#'.preg_quote('{$customcolor}').'#i', '', 0);

    find_replace_templatesets('header_welcomeblock_member', '#'.preg_quote('{$customcolor_input}').'#i', '', 0);
}

function customcolor_headerinclude()
{
    global $mybb, $templates, $customcolor_headerinclude, $customcolor_footer, $customcolor_input;

    if(!$mybb->user['uid'])
    {
        return;
    }

    $customcolor_headerinclude = eval($templates->render('customcolor_headerinclude'));

    $customcolor_footer = eval($templates->render('customcolor_footer'));

    $customcolor_footer = eval($templates->render('customcolor_footer'));

    $customcolor_input = '';

    if($mybb->usergroup['canusercp'])
    {
        global $lang;

        customcolor_lang_load();

        $customcolor_input = eval($templates->render('customcolor_input'));
    }
}

function customcolor_usercp_options_end()
{
	global $themes, $mybb, $templates, $theme, $customcolor, $lang;

    customcolor_lang_load();

    $lang->colorucp_desc = $lang->sprintf($lang->colorucp_desc, $mybb->settings['bburl']);

    $customcolor = eval($templates->render('customcolor_usercp'));
}

function customcolor_misc_start()
{
	global $mybb, $db, $lang;

    if($mybb->get_input('action') != 'customcolor')
    {
        return;
    }

    if(!$mybb->usergroup['canusercp'])
    {
        error_no_permission();
    }

    $uid = (int)$mybb->user["uid"];

    if($mybb->get_input('reset', MyBB::INPUT_INT))
    {
        $db->update_query("users", array('customcolor' => ''), "uid = '{$uid}'");

        $mybb->settings['redirects'] = $mybb->user['showredirect'] = 0;

		redirect("usercp.php?action=options");
    }

    if(!isset($mybb->input["color"]) || !$mybb->user['uid'] || strlen($mybb->input['color']) != 6)
    {
        return;
    }

    $color = $mybb->get_input('color');
    
    preg_match('/([A-Fa-f0-9]{6})/', $color, $match);

    if(!empty($match[0]))
    {
        $update_array = array(
            'customcolor' => $db->escape_string($color),
        );

        $db->update_query("users", $update_array, "uid = '{$uid}'"); 
    }
}

function customcolor_xmlhttp()
{
    global $mybb, $templates, $db;

    if($mybb->get_input('action') != 'customcolor')
    {
        return;
    }

    header('Content-Type: text/css');

    if(!empty($mybb->user['customcolor']))
    {
        $color = (string)$mybb->user['customcolor'];
    }

    else
    {
        $color = (string)$mybb->settings['customcolor_default'];
    }

    preg_match('/([A-Fa-f0-9]{6})/', $color, $match);

    if(empty($match[0]))
    {
        return;
    }

    $templates->cache('customcolor_css_backgrounds, customcolor_css_borders, customcolor_css_fills, customcolor_css_texts');

    $bgs = explode(',', '#colorpicker,'.$mybb->settings['customcolor_backgrounds']);

    $mybb->settings['customcolor_backgrounds'] = implode(',', $bgs);

    $css = eval($templates->render('customcolor_css_backgrounds', true, false));

    if(!empty($mybb->settings['customcolor_borders']))
    {
        $css .= eval($templates->render('customcolor_css_borders', true, false));
    }

    if(!empty($mybb->settings['customcolor_fills']))
    {
        $css .= eval($templates->render('customcolor_css_fills', true, false));
    }

    if(!empty($mybb->settings['customcolor_texts']))
    {
        $css .= eval($templates->render('customcolor_css_texts', true, false));
    }

    echo $css;

    exit;
}

