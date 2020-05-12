<?php

if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

$plugins->add_hook('global_start', 'customcolor_headerinclude');
$plugins->add_hook("usercp_start", "customcolor_start");
$plugins->add_hook("usercp_menu", "customcolor_usercp_menu");

function customcolor_info()
{
    global $lang;
    $lang->load("customcolor");
    return array(
        "name"          => $lang->title_plugin,
        "description"   => $lang->desc_plugin,
        "website"       => "https://developement.design/",
        "author"        => "AmazOuz and fixed by Vintagedaddyo",
        "authorsite"    => "https://developement.design/",
        "version"       => "1.1",
        "guid"          => "",
        "codename"      => "customcolor",
        "compatibility" => "*"
    );
}

function customcolor_install()
{
    customcolor_add_table();
}

function customcolor_uninstall()
{
    customcolor_remove_table();
}

function customcolor_is_installed()
{
    global $db;
    $table = TABLE_PREFIX.'users';
    $column = 'customcolor';
    $query = "SHOW COLUMNS FROM $table LIKE '$column'";
    $result = $db->query($query) or die(mysql_error());
    if($num_rows = $result->num_rows > 0)
    {
	   return true;
    }
    else
    {
	   return false;
    }
}

function customcolor_activate()
{
    customcolor_create_template();
}

function customcolor_deactivate()
{
    customcolor_delete_template();
}

include 'customcolor/functions.php';