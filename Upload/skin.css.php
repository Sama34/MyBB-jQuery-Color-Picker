
<?php
header('Content-Type: text/css');
define('IN_MYBB', true);
require_once 'global.php';

$uid = $mybb->user["uid"];
$color_req = $db->query("SELECT customcolor AS color FROM ".TABLE_PREFIX."users WHERE uid = $uid");
while ($color_fetch = $db->fetch_array($color_req))
{
    $color = $color_fetch["color"];
}

if ($color == "")
{
    $color = $mybb->settings['customcolor_default']; // Write your default color here
}

$color = "#$color";

if (!empty($mybb->settings['customcolor_borders']))
{
    echo $mybb->settings['customcolor_borders']."
{ 
    border-color: $color; 
}
";
}
if (!empty($mybb->settings['customcolor_texts']))
{
    echo $mybb->settings['customcolor_texts']."
{ 
    color: $color; 
}
";
}
if (!empty($mybb->settings['customcolor_backgrounds']))
{
    echo $mybb->settings['customcolor_backgrounds']."
{ 
    background: $color; 
}
";
}

echo "#colorpicker
{ 
    background: $color; 
}";

?>

