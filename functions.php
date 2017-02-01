<?php
/**
 * Autoupdate common functions
 * @package [Mod] Autoupdate
 * @subpackage main
 * @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 2.1.9
 */

namespace Ogsteam\Ogspy;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

require_once("mod/autoupdate/mod_list.php");

/*On récupère la liste des mods installés*/

/**
 * @return mixed
 */
function get_installed_mod_list() {

    global $db;
    $sql = "SELECT title,root,version from " . TABLE_MOD;
    $res = $db->sql_query($sql, false, true);

    $i = 0;
    while (list($modname, $modroot, $modversion) = $db->sql_fetch_row($res)) {
        $installed_mods[$i]['name'] = $modname;
        $installed_mods[$i]['root'] = $modroot;
        $installed_mods[$i++]['version'] = $modversion;
    }
    return $installed_mods;
}
/**
 *Récupère la version du mod
 */
function versionmod()
{
    global $db, $pub_action;
    $sql = "SELECT version FROM " . TABLE_MOD . " WHERE action = '" . $pub_action . "' LIMIT 1";
    $query = $db->sql_query($sql);
    $fetch = $db->sql_fetch_assoc($query);
    return $fetch['version'];
}

/**
 * @param $mod
 * @return string
 */
function upgrade_ogspy_mod($mod)
{
    global $db, $lang;
    // On vérifie si le mod est déjà installé
    $check = "SELECT title FROM " . TABLE_MOD . " WHERE root='" . $mod . "'";
    $query_check = $db->sql_query($check);
    $result_check = $db->sql_numrows($query_check);

    if ($result_check != 0) {
        // Si le mod existe, on fait une mise à jour
        if (file_exists("mod/" . $mod . "/update.php")) {
            require_once("mod/" . $mod . "/update.php");
            generate_mod_cache();
            log_("mod_update", $mod);
            $maj = $lang['autoupdate_tableau_uptodateok'] . "<br>\n<br>\n";
        } else {
            $maj = $lang['autoupdate_tableau_uptodateoff'] . "<br>\n<br>\n";
        }
        return $maj;

    } else {
        // Si le mod n'existe pas, on fait une installation
        if (file_exists("mod/" . $mod . "/install.php")) {
            require_once("mod/" . $mod . "/install.php");
            generate_all_cache();
            log_("mod_install", $mod);
            $maj = $lang['autoupdate_tableau_installok'] . "<br><br>";
        } else {
            $maj = $lang['autoupdate_tableau_installoff'] . "<br><br>";
        }
        return $maj;

    }
}

/**
 * @param $dir
 */
function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") {
                    rrmdir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

// copies files and non-empty directories
/**
 * @param $src
 * @param $dst
 */
function rcopy($src, $dst)
{
    if (is_dir($src)) {
        if (!is_dir($dst)) {
            mkdir($dst);
        }
        $files = scandir($src);
        foreach ($files as $file) {
                    if ($file != "." && $file != "..") {
                        rcopy("$src/$file", "$dst/$file");
                    }
        }
    } else if (file_exists($src)) {
        copy($src, $dst);
    }
    }

/**
 * Affiche sous forme de tableau table à 2 colonne les fichiers du zip et son état.
 * @param $tableau
 * @param string $type
 */
function tableau($tableau, $type = "maj")
{
    global $lang;
    while (list($key, $valeur) = each($tableau)) {
        $fichier = explode("/", $key);
        $nom = "";
        for ($i = 1; $i < count($fichier); $i++) {
            if (count($fichier) >= 3 && count($fichier) != $i && $i > 1) {
                $slash = "/";
            } else {
                $slash = "";
            }
            $nom .= $slash . $fichier[$i];
        }
        $explode = explode(".", $key);
        if ($nom != "" && $explode[0] != $key) {
            if ($type == "maj") {
                $etat = $lang['autoupdate_MaJ_uptodateok'];
            } else if ($type == "down") {
                $etat = $lang['autoupdate_MaJ_downok'];
            }
            echo "\t" . '<tr>' . "\n";
            echo "\t\t" . '<td class="a">' . $nom . '</td>' . "\n";
            echo "\t\t" . '<td class="a">' . $etat . '</td>' . "\n";
            echo "\t" . '</tr>' . "\n";
        }
    }
}

/**
 * Vérifie la version d'ogspy avant installation
 *
 * @param $mod_folder
 * @return bool
 */
function check_ogspy_version_bcopy($mod_folder)
{
    global $server_config;
    // verification sur le fichier .txt
    $filename = 'mod/autoupdate/tmp/' . $mod_folder . '/version.txt';

    // On récupère les données du fichier version.txt

    if (!file_exists($filename)) {
        return false;
    }
    $file = file($filename);

    //Version Minimale OGSpy
    /** @var string $mod_required_ogspy */
    $mod_required_ogspy = trim($file[3]);
    if (isset($mod_required_ogspy)) {
        if (version_compare($mod_required_ogspy, $server_config["version"]) > 0) {
            log_("mod_erreur_txt_version", $mod_folder);
            return false;
        }
    }
    return true;
}

function send_stats()
{
    global $server_config, $serveur_key, $serveur_date, $db;

    $mod_tools = new Mod_DevTools('autoupdate');

    if (time() > (intval($mod_tools->mod_get_option('LAST_REPO_LIST')) + intval($mod_tools->mod_get_option('CYCLEMAJ')) * 3600)) {
        // recuperation du pays et de l univers du serveur
        $og_pays = "NA";
        $og_uni = "NA";
        if (isset($server_config["xtense_universe"])) {
            //pattern de recherche
            $pattern = "#https:\/\/s([0-9]{1,3})-([a-z]{2,3})\.ogame\.gameforge.com#";
            if (preg_match($pattern, $server_config["xtense_universe"], $retour)) {
                $og_pays = $retour[2]; // seconde capture
                $og_uni = $retour[1]; // premiere capture
            }
        }
        //Liste des modules installés

        $installed_mod_list = get_installed_mod_list();
        foreach ($installed_mod_list as $mod_details) {

            $data_mod[] = $mod_details['root'];
        }
            $data_mode_to_send = json_encode($data_mod);

        //Statistiques concernant les membres
        $users_info = sizeof(user_statistic());
        //
        $db_size_info = $db->db_size_info();

            $link = "/statistiques/getstats/";
            $link .= "?version=" . $server_config["version"];
            $link .= "&nb_users=" . $users_info;
            //Paramètres Serveur
            $link .= "&db_size=" . urlencode($db_size_info["Server"]);
            $link .= "&php_version=" . PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;
            // clef unique
            $link .= "&server_since=" . $serveur_date;
            $link .= "&server_key=" . $serveur_key;
            // recuperation pays et univers du serveur
            $link .= "&og_uni=" . $og_uni;
            $link .= "&og_pays=" . $og_pays;
            $link .= "&mod_list=" . $data_mode_to_send;
        
            $repo_link = 'http://darkcity.fr' . $link;
            @copy($repo_link, './mod/autoupdate/tmp/stats.answer'); //Will be used later
    }

    //log_('debug',"Sending Statistics done");
}

