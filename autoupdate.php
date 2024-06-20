<?php
global $lang,$ui_lang,$user_data,$server_config;

/**
 * Autoupdate Controller
 * @package [Mod] Autoupdate
 * @subpackage main
 * @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 2.1.9
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

require_once("views/page_header.php");
if (!function_exists('random_bytes')) {
    die("OGSpy cannot work anymore without Php Security Layers, please use PHP(>= 8.0)");
}
if (!function_exists('json_decode')) {
    die("Autoupdate cannot work without the PHP Module JSON Library");
}
if (!extension_loaded('zip')) {
    die("Autoupdate cannot work without the PHP Module ZIP");
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
        $bouton1 = "\t\t" . "<td class='og-button' onclick=\"window.location = 'index.php?action=autoupdate&sub=overview';\">";

        $bouton1 .= "<span style=\"color: lime; \">" . $lang['autoupdate_autoupdate_table'] . "</span>";
    } else {
        $bouton1 = "\t\t" . "<td class='og-button'";
        $bouton1 .= "<span style=\"color: #5CCCE8; \">" . $lang['autoupdate_autoupdate_table'] . "</span>";
    }
    $bouton1 .= "</td>\n";
    if ($sub != "down") {
        $bouton2 = "\t\t" . "<td class='og-button'  onclick=\"window.location = 'index.php?action=autoupdate&sub=down';\">";
        $bouton2 .= "<span style=\"color: lime; \">" . $lang['autoupdate_autoupdate_down'] . "</span>";
    } else {
        $bouton2 = "\t\t" . "<td class='og-button'";
        $bouton2 .= "<span style=\"color: #5CCCE8; \">" . $lang['autoupdate_autoupdate_down'] . "</span>";
    }
    $bouton2 .= "</td>\n";
} else {
    $bouton1 = "";
    $bouton2 = "";
}
if ($user_data["user_admin"] == 1) {
    if ($sub != "admin") {
        $bouton3 = "\t\t" . "<td class='og-button' onclick=\"window.location = 'index.php?action=autoupdate&sub=admin';\">";
        $bouton3 .= "<span style=\"color: lime; \">" . $lang['autoupdate_autoupdate_admin'] . "</span>";
    } else {
        $bouton3 = "\t\t" . "<td class='og-button'>";
        $bouton3 .= "<span style=\"color: #5CCCE8; \">" . $lang['autoupdate_autoupdate_admin'] . "</span>";
    }
    $bouton3 .= "</td>\n";
} else {
    $bouton3 = "";
}
?>
<div class="og-msg ">
    <h3 class="og-title">Autoupdate</h3>
    <p class="og-content">Autoupdate permet d'installer ou de mettre à jour vos modules OGSpy</p>
    <p class="og-content">Il permet aussi d'obtenir les préversions des modules ainsi que de soumettre des tickets à l'équipe de développement.</p>
</div>


<table class="og-table og-medium-table">
    <tr>
<?php
        echo $bouton1 . $bouton2 . $bouton3;
?>
    </tr>
</table>
<br>
<?php
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
