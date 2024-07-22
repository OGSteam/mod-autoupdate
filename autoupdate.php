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

?>

<div class="og-msg ">
    <h3 class="og-title">Autoupdate</h3>
    <p class="og-content">Autoupdate permet d'installer ou de mettre à jour vos modules OGSpy</p>
    <p class="og-content">Il permet aussi d'obtenir les préversions des modules ainsi que de soumettre des tickets à l'équipe de développement.</p>
</div>

<div class="nav-page-menu">

    <div class="nav-page-menu-item">
        <a class="nav-page-menu-link" href='index.php?action=autoupdate&sub=overview'>
            <?= $lang['autoupdate_autoupdate_table'] ?>
        </a>
    </div>
    <div class="nav-page-menu-item">
        <a class="nav-page-menu-link" href='index.php?action=autoupdate&sub=down'>
            <?= $lang['autoupdate_autoupdate_down'] ?>
        </a>
    </div>
    <?php if ($user_data["user_admin"] == 1) { ?>
        <div class="nav-page-menu-item">
            <a class="nav-page-menu-link" href='index.php?action=autoupdate&sub=admin'>
                <?= $lang['autoupdate_autoupdate_admin'] ?>
            </a>
        </div>
    <?php } ?>
</div>


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
