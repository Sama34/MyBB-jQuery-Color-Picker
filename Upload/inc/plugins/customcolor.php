<?php

/***************************************************************************
 *
 *	MyBB jQuery Color Picker plugin (/inc/plugins/customcolor.php)
 *	Author: Omar Gonzalez
 *	Copyright: © 2020 Omar Gonzalez
 *
 *	Website: https://ougc.network
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
    $plugins->add_hook('global_intermediate', 'customcolor_headerinclude');
    $plugins->add_hook("usercp_options_end", "customcolor_usercp_options_end");
    $plugins->add_hook("misc_start", "customcolor_misc_start");
    $plugins->add_hook("xmlhttp", "customcolor_xmlhttp");
    
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

// PLUGINLIBRARY
defined('PLUGINLIBRARY') or define('PLUGINLIBRARY', MYBB_ROOT.'inc/plugins/pluginlibrary.php');

function customcolor_info()
{
    global $lang;

    customcolor_lang_load();

    $lang->desc_plugin .= ' This work is isnpired off the work by <strong><a href="https://iandrew.org/">iAndrew</a></strong>, <a href="https://iandrew.org/">AmazOuz</a>, and <a href="https://community.mybb.com/thread-158934-post-1343419.html#pid1343419">vintagedaddyo</a>.';

    return array(
        "name"          => $lang->title_plugin,
        "description"   => $lang->desc_plugin,
		'website'		=> 'https://ougc.network',
		'author'		=> 'Omar G.',
		'authorsite'	=> 'https://ougc.network',
        "version"       => "1.8",
		'versioncode'	=> 1800,
        "codename"      => "ougc_customcolor",
        "compatibility" => "18*"
    );
}

// Load language file
function customcolor_lang_load()
{
	global $lang;

	isset($lang->title_plugin) or $lang->load('customcolor');
}

// PluginLibrary requirement check
function customcolor_pluginlibrary()
{
	global $lang;

	$info = customcolor_info();

	if($file_exists = file_exists(PLUGINLIBRARY))
	{
        global $PL;
    
        $PL or require_once PLUGINLIBRARY;
	}

	if(!$file_exists || $PL->version < $info['pl']['version'])
	{
		flash_message($lang->sprintf($lang->customcolor_pluginlibrary, $info['pl']['ulr'], $info['pl']['version']), 'error');
		admin_redirect('index.php?module=config-plugins');
	}
}

function customcolor_install()
{
    global $db;

    customcolor_is_installed() || $db->add_column('users', 'customcolor', "VARCHAR(6) DEFAULT ''");
}

function customcolor_uninstall()
{
    global $db, $PL;

	customcolor_pluginlibrary();

    !customcolor_is_installed() || $db->drop_column('users', 'customcolor');

	// Delete stylesheet
	$PL->stylesheet_delete('customcolor');

	// Delete settings
	$PL->settings_delete('customcolor');

	// Delete template/group
	$PL->templates_delete('customcolor');

	global $cache;

	// Remove version code from cache
	$plugins = (array)$cache->read('ougc_plugins');

	if(isset($plugins['customcolor']))
	{
		unset($plugins['customcolor']);
	}

	if($plugins)
	{
		$cache->update('ougc_plugins', $plugins);
	}
	else
	{
		$PL->cache_delete('ougc_plugins');
	}
}

function customcolor_is_installed()
{
    global $db;

    return $db->field_exists('customcolor', 'users');
}

function customcolor_activate()
{
    global $lang, $PL;

    customcolor_pluginlibrary();

	$PL->stylesheet('customcolor', '/***************************************************************************
*
*	MyBB jQuery Color Picker plugin (CSS FILE)
*	Author: Omar Gonzalez
*	Copyright: © 2020 Omar Gonzalez
*
*	Website: https://ougc.network
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
}');

	// Add our settings
	$PL->settings('customcolor', 'Color Changer', 'Settings for color changer plugin', array(
		'default'	=> array(
			'title'		=> 'Default Color',
			'description'	=> 'Write here the hex of the default theme color. Example: for #003b75 write 003b75',
			'optionscode'	=> 'text',
			'value'			=> '008dd4',
		),
		'backgrounds'	=> array(
			'title'		=> 'Backgrounds',
			'description'	=> 'Write here Classes & Ids (separated by comma) of elements for which you want a changing background color',
			'optionscode'	=> 'textarea',
			'value'			=> '.thead, #search input.button',
		),
		'borders'	=> array(
			'title'		=> 'Border Elements',
			'description'	=> 'Write here Classes & Ids (separated by comma) of elements for which you want a changing border color',
			'optionscode'	=> 'textarea',
			'value'			=> '',
        ),
		'fills'	=> array(
			'title'		=> 'Fill Elements',
			'description'	=> 'Write here Classes & Ids (separated by comma) of elements for which you want a changing fill color',
			'optionscode'	=> 'textarea',
			'value'			=> '#logo-svg',
		),
		'texts'	=> array(
			'title'		=> 'Text Elements',
			'description'	=> 'Write here Classes & Ids (separated by comma) of elements for which you want a changing text color',
			'optionscode'	=> 'textarea',
			'value'			=> 'table a:link, .top_links a:link, a:link, a:hover, a:focus, a:visited',
		)
    ));

	// Insert template/group
	$PL->templates('customcolor', 'Custom Color', array(
		'headerinclude' => '{$ucp}
<link rel="stylesheet" type="text/css" href="{$mybb->settings[\'bburl\']}/xmlhttp.php?action=customcolor" />',
        'footer'  => '<script type="text/javascript" src="{$mybb->settings[\'bburl\']}/jscripts/customcolor/colorpicker.js"></script>
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
</script>',
		//'input'    => '<span class="custom_theme" title="{$lang->colorucp_input}"><input type="text" id="colorpicker" value="{$mybb->settings[\'customcolor_default\']}" /></span>',
		'usercp'    => '<tr>
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
</tr>',
        'css_backgrounds' => '{$mybb->settings[\'customcolor_backgrounds\']}
{ 
    background: #{$color}; 
}',
        'css_borders' => '{$mybb->settings[\'customcolor_borders\']}
{ 
    border-color: #{$color}; 
}',
        'css_fills' => '{$mybb->settings[\'customcolor_fills\']}
{ 
    fill: #{$color}; 
}',
        'css_texts' => '{$mybb->settings[\'customcolor_texts\']}
{ 
    color: #{$color}; 
}'
	));

	// Modify some templates.
	require_once MYBB_ROOT.'inc/adminfunctions_templates.php';
	find_replace_templatesets('headerinclude', '#'.preg_quote('{$stylesheets}').'#i', '{$stylesheets}{$customcolor_headerinclude}');
	find_replace_templatesets('footer', '#'.preg_quote('{$auto_dst_detection}').'#i', '{$auto_dst_detection}{$customcolor_footer}');
	find_replace_templatesets('usercp_options', '#'.preg_quote('{$board_style}').'#i', '{$board_style}{$customcolor}');
	//find_replace_templatesets('header_welcomeblock_member', '#'.preg_quote('{$buddylink}').'#i', '{$customcolor_input}{$buddylink}');

	global $cache;

	// Insert version code into cache
    $plugins = $cache->read('ougc_plugins');

	if(!$plugins)
	{
		$plugins = array();
	}

	$info = customcolor_info();

    $plugins['customcolor'] = $info['versioncode'];

	$cache->update('ougc_plugins', $plugins);
}

function customcolor_deactivate()
{
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
