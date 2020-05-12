<?php

function customcolor_add_table()
{
    global $db, $mybb;
    $query = "ALTER TABLE `".TABLE_PREFIX."users` ADD `customcolor` VARCHAR( 6 ) DEFAULT ''";
    $db->write_query($query);
    
    $setting_group = array(  
		'name'			=> 'customcolor',
		'title'			=>  'Color Changer',
		'description'	=>  'Settings for color changer plugin',
		'disporder'		=> '1'
	);
    $db->insert_query('settinggroups', $setting_group);
	$gid = $db->insert_id();
    
    $setting1 = array(
		'name'			=> 'customcolor_default',
		'title'			=> 'Default Color',
		'description'	=> 'Write here the hex of the default theme color. Example: for #003b75 write 003b75',
		'optionscode'	=> 'text', 
		'value'			=> '008dd4', 
		'disporder'		=> '1', 
		'gid'			=> intval($gid)
	);
	$db->insert_query('settings', $setting1);
    
    $setting2 = array(
		'name'			=> 'customcolor_texts',
		'title'			=> 'Text Elements',
		'description'	=> 'Write here Classes & Ids (separated by comma) of elements for which you want a changing text color',
		'optionscode'	=> 'textarea', 
		'value'			=> 'table a:link, .top_links a:link, a:link, a:hover, a:focus, a:visited', 
		'disporder'		=> '2', 
		'gid'			=> intval($gid)
	);
	$db->insert_query('settings', $setting2);
    
    $setting3 = array(
		'name'			=> 'customcolor_borders',
		'title'			=> 'Border Elements',
		'description'	=> 'Write here Classes & Ids (separated by comma) of elements for which you want a changing border color',
		'optionscode'	=> 'textarea', 
		'value'			=> '', 
		'disporder'		=> '3', 
		'gid'			=> intval($gid)
	);
	$db->insert_query('settings', $setting3);
    
    $setting4 = array(
		'name'			=> 'customcolor_backgrounds',
		'title'			=> 'Backgrounds',
		'description'	=> 'Write here Classes & Ids (separated by comma) of elements for which you want a changing background color',
		'optionscode'	=> 'textarea', 
		'value'			=> '.thead, #search input.button', 
		'disporder'		=> '4', 
		'gid'			=> intval($gid)
	);
	$db->insert_query('settings', $setting4);
    rebuild_settings();
}

function customcolor_remove_table()
{
    global $db, $mybb;
    $query = "ALTER TABLE `".TABLE_PREFIX."users` DROP `customcolor`";
    $db->query($query);
    $db->write_query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN ('customcolor_default','customcolor_backgrounds','customcolor_borders','customcolor_texts')");
	$db->write_query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name = 'customcolor'");
	rebuild_settings();
}

function customcolor_create_template()
{   
    global $mybb, $db;
    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
    find_replace_templatesets('headerinclude', '#'.preg_quote('{$stylesheets}').'#i', '{$stylesheets}{$customcolor_headerinclude}');
    $template = '<link href="{$mybb->settings[\'bburl\']}/css/skin.css" rel=\'stylesheet\' type=\'text/css\'>
<link href="{$mybb->settings[\'bburl\']}/css/colorpicker.css" rel=\'stylesheet\' type=\'text/css\'>
<script type="text/javascript" src="{$mybb->settings[\'bburl\']}/js/colorpicker.js"></script>
<script type="text/javascript" src="{$mybb->settings[\'bburl\']}/js/skin.js"></script>
<script type="text/javascript" src="{$mybb->settings[\'bburl\']}/js/cookie.js"></script>
<link rel=\'stylesheet\' type="text/css" href="{$mybb->settings[\'bburl\']}/skin.css.php" />
<style>
.custom_theme
{
    margin-left:5px;
}

.custom_theme #colorpicker{
    height: 30px;
    width: 30px;
    padding: 0;
    margin: 0;
    cursor: pointer;
    color: transparent;
    border: 3px solid black;
    border-radius: 100%;
}

.colorpicker {
    width: 356px;
    height: 176px;
    overflow: hidden;
    position: absolute;
    background: url(images/cp/colorpicker_background.png);
    font-family: Arial, Helvetica, sans-serif;
    display: none;
    z-index: 9000;
    margin-left: 36px;
}
.colour_instructions{
    width: 134px;
    height: 92px;
    position: absolute;
    left: 211px;
    top: 47px;
    text-align: left;
    font-size: 10px;
    color: #898989;
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
    background: url(images/cp/colorpicker_indic.gif) left top;
    margin: -4px 0 0 0;
    left: 0px;
}
.colorpicker_new_color {
    position: absolute;
    width: 130px;
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
    display: none;
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
    background: url(images/cp/colorpicker_hex.png) top;
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
    display: none; /* Hide colour boxes */
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
    background-image: url(images/cp/colorpicker_rgb_r.png);
    top: 52px;
    left: 212px;
}
.colorpicker_rgb_g {
    background-image: url(images/cp/colorpicker_rgb_g.png);
    top: 82px;
    left: 212px;
}
.colorpicker_rgb_b {
    background-image: url(images/cp/colorpicker_rgb_b.png);
    top: 112px;
    left: 212px;
}
.colorpicker_hsb_h {
    background-image: url(images/cp/colorpicker_hsb_h.png);
    top: 52px;
    left: 282px;
}
.colorpicker_hsb_s {
    background-image: url(images/cp/colorpicker_hsb_s.png);
    top: 82px;
    left: 282px;
}
.colorpicker_hsb_b {
    background-image: url(images/cp/colorpicker_hsb_b.png);
    top: 112px;
    left: 282px;
}
.colorpicker_submit {
    position: absolute;
    width: 56px;
    height: 22px;
    background: url(images/cp/colorpicker_submit.png) top;
    left: 288px;
    top: 142px;
    cursor: pointer;
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
}
</style>
    ';
    $insert_array = array(
        'title' => 'customcolor_headerinclude',
        'template' => $db->escape_string($template),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);
    
    $template = '<html>
<head>
<title>{$mybb->settings[\'bbname\']} - Theme Settings</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
	{$usercpnav}
	<td valign="top">
		<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
			<tr>
				<td class="thead" colspan="2"><strong>Theme Settings</strong></td>
			</tr>
			<tr>
				<td class="trow1" colspan="2">
					<table cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td>
								Choose your favourite color from the color picker below, then click "save" button :
								<span class="custom_theme"><input type="text" id="colorpicker" /></span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
{$footer}
</body>
</html>';
    $insert_array = array(
        'title' => 'customcolor_usercp',
        'template' => $db->escape_string($template),
        'sid' => '-1',
        'version' => '',
        'dateline' => time()
    );
    $db->insert_query('templates', $insert_array);
    
}

function customcolor_delete_template()
{
    global $mybb, $db;
    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
    find_replace_templatesets('headerinclude', '#'.preg_quote('{$customcolor_headerinclude}').'#i', '');
    $db->delete_query("templates", "title = 'customcolor_headerinclude'");
    $db->delete_query("templates", "title = 'customcolor_usercp'");
}

function customcolor_headerinclude()
{
    global $mybb, $db, $templates, $customcolor_headerinclude;
    require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
    eval("\$customcolor_headerinclude = \"".$templates->get("customcolor_headerinclude")."\";");
}

function customcolor_start()
{
	global $db, $footer, $header, $navigation, $headerinclude, $themes, $mybb, $templates, $usercpnav, $theme;

	if($mybb->input['action'] != "customcolor")
	{
		return false;
	}
	
	eval("\$output = \"".$templates->get("customcolor_usercp")."\";");
    output_page($output);
	
}

function customcolor_usercp_menu()
{
	global $templates;
	$template = "\n\t<tr><td class=\"trow1 smalltext\"><a href=\"usercp.php?action=customcolor\" class=\"usercp_nav_item usercp_nav_options\">Theme Settings</a></td></tr>";
	$templates->cache["usercp_nav_misc"] = str_replace("<tbody style=\"{\$collapsed['usercpmisc_e']}\" id=\"usercpmisc_e\">", "<tbody style=\"{\$collapsed['usercpmisc_e']}\" id=\"usercpmisc_e\">{$template}", $templates->cache["usercp_nav_misc"]);
}