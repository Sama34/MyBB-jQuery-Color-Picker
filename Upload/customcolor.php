<?php

define('IN_MYBB', 1); require "global.php";
if (isset($mybb->input["color"]) AND $mybb->user["usergroup"] != 1 and strlen($mybb->input['color']) == 6)
{
    $uid = $mybb->user["uid"];
    $color = $mybb->input["color"];
    $update_array = array(
     "customcolor" => $color,
    );
    $db->update_query("users", $update_array, "uid = '".intval($uid)."'"); 
}

