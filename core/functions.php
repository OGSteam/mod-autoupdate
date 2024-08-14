<?php

/**
 * Autoupdate common functions
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

//require_once("mod/autoupdate/mod_list.php");

/*On récupère la liste des mods installés*/

/**
 * @return mixed
 */
function get_installed_mod_list()
{

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
    } elseif (file_exists($src)) {
        copy($src, $dst);
    }
}

/**
 * Affiche sous forme de tableau table à 2 colonnes les fichiers du zip et son état.
 * @param $tableau
 * @param string $type
 */
function tableau($tableau, string $type = "maj")
{
    global $lang;

    foreach ($tableau as $key => $value) {
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
            } elseif ($type == "down") {
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

    //Version Minimale OGSpy (Ligne 3)
    /** @var string $mod_required_ogspy */
    if (isset($file[3])) {
        $mod_required_ogspy = trim($file[3]);
        if (version_compare($mod_required_ogspy, $server_config["version"]) > 0) {
            log_("mod_erreur_txt_version", $mod_folder);
            return false;
        }
    } else {
        log_("mod_erreur_txt_warning", $mod_folder);
        return false;
    }
    return true;
}
