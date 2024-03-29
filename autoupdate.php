<?php

/**
 * Autoupdate Controller
 * @package [Mod] Autoupdate
 * @subpackage main
 * @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
 * @copyright Copyright &copy; 2016, http://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 2.1.9
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

require_once("views/page_header.php");
if (!function_exists('random_bytes')) {
    die("OGSpy cannot work anymore without Php Security Layers, please use PHP(>= 7.0)");
}
if (!function_exists('json_decode')) {
    die("Autoupdate cannot work without the JSON Library, please use PHP(>= 5.2)");
}
if (!extension_loaded('zip')) {
    die("Autoupdate cannot work without the ZIP Library, Please check your server configuration");
}
if (!ini_get('allow_url_fopen')) {
    die("Autoupdate cannot work without external connections (fopen), Please check your server configuration");
}

require_once("mod/autoupdate/core/functions.php");
require_once("mod/autoupdate/lang/" . $ui_lang . "/lang_autoupdate.php");
require_once("mod/autoupdate/core/mod_list.php");


/* Envoi des statistiques du serveur */
send_stats();

/**
 * Défini où se trouve le fichier qui contient les dernières versions des mods.
 * Différent suivant si allow_url_fopen est activé ou non. S'il n'est pas activé, on va chercher le fichier en local après téléchargement.
 */

if (!isset($pub_sub)) {
    $sub = "overview";
    $pub_sub = "overview";
} else {
    $sub = $pub_sub;
}

if ($user_data["user_admin"] == 1 || $user_data["user_coadmin"] == 1) {
    if ($sub != "overview") {
        $bouton1 = "\t\t" . "<td class='c' align='center' width='150' style='cursor:pointer' onclick=\"window.location = 'index.php?action=autoupdate&sub=overview';\">";

        $bouton1 .= "<span style=\"color: lime; \">" . $lang['autoupdate_autoupdate_table'] . "</span>";
        $bouton1 .= "</td>\n";
    } else {
        $bouton1 = "\t\t" . "<th width='150'>";
        $bouton1 .= "<span style=\"color: #5CCCE8; \">" . $lang['autoupdate_autoupdate_table'] . "</span>";
        $bouton1 .= "</th>\n";
    }
    if ($sub != "down") {
        $bouton2 = "\t\t" . "<td class='c' align='center' width='150' style='cursor:pointer' onclick=\"window.location = 'index.php?action=autoupdate&sub=down';\">";
        $bouton2 .= "<span style=\"color: lime; \">" . $lang['autoupdate_autoupdate_down'] . "</span>";
        $bouton2 .= "</td>\n";
    } else {
        $bouton2 = "\t\t" . "<th width='150'>";
        $bouton2 .= "<span style=\"color: #5CCCE8; \">" . $lang['autoupdate_autoupdate_down'] . "</span>";
        $bouton2 .= "</th>\n";
    }
} else {
    $bouton1 = "";
    $bouton2 = "";
}
if ($user_data["user_admin"] == 1) {
    if ($sub != "admin") {
        $bouton3 = "\t\t" . "<td class='c' align='center' width='150' style='cursor:pointer' onclick=\"window.location = 'index.php?action=autoupdate&sub=admin';\">";
        $bouton3 .= "<span style=\"color: lime; \">" . $lang['autoupdate_autoupdate_admin'] . "</span>";
        $bouton3 .= "</td>\n";
    } else {
        $bouton3 = "\t\t" . "<th width='150'>";
        $bouton3 .= "<span style=\"color: #5CCCE8; \">" . $lang['autoupdate_autoupdate_admin'] . "</span>";
        $bouton3 .= "</th>\n";
    }
} else {
    $bouton3 = "";
}
echo "\n<table>\n";
echo "\t<tr>\n";
echo $bouton1 . $bouton2 . $bouton3;
echo "\t</tr><br>\n";
echo "</table>\n<br>\n";

if (!isset($pub_sub)) {
    $sub = 'overview';
} else {
    $sub = htmlentities($pub_sub);
}
switch ($sub) {
    case 'overview':
        include('view/overview.php');
        break;
    case 'mod_upgrade':
        include('core/mod_upgrade.php');
        break;
    case 'tool_upgrade':
        include('core/tool_upgrade.php');
        break;
    case 'down':
        include('view/down.php');
        break;
    case 'admin':
        include('view/admin.php');
        break;
}
