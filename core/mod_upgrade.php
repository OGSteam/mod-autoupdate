<?php
global $db, $lang, $user_data, $server_config, $pub_confirmed, $pub_mod, $pub_tag, $pub_version, $pub_action, $pub_sub;
/**
 * Autoupdate Tool Mod upgrade File
 * @package [Mod] Autoupdate
 * @subpackage main
 * @author DarkNoon <darknoon@darkcity.fr>
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 2.1.9
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
/**
 *Récupère les fonctions zip
 */
$zip = new ZipArchive;

require_once("views/page_header.php");

if (!isset($pub_confirmed)) {
    $pub_confirmed = "no";
}

if ($user_data['admin'] == 1 || $user_data['coadmin'] == 1) {

    $modroot = filter_var($pub_mod);

    if (isset($pub_tag)) {
        //Si une version est spécifiée...
        $mod_tag = filter_var($pub_tag);
        $version = getRepositoryVersion($modroot);
        if (!is_array($version) || $version == '-1') {
            die("No official version available, Please contact OGSteam");
        }
        $target_version = $version[$mod_tag];
    }

    if ($pub_sub == "mod_upgrade" && $pub_confirmed == "yes") {

        //Récupération des infos du mod :
        $repoDetails = getRepositoryDetails($modroot);

        if ($mod_tag == 'beta') {
            $modzip = "https://api.github.com/repos/" . $repoDetails['owner'] . "/mod-" . $modroot . "/zipball/" . $version['beta'];
        } else {
            $modzip = "https://api.github.com/repos/" . $repoDetails['owner'] . "/mod-" . $modroot . "/zipball/" . $version['release'];
        }

        if (!is_writeable("./mod/autoupdate/tmp/")) {
            die("Error: Folder /mod/autoupdate/tmp/ must be writeable " . __FILE__ . "(Line: " . __LINE__ . ")");
        }

        $mod_file = github_Request($modzip);
        file_put_contents('./mod/autoupdate/tmp/tarball.zip', $mod_file);

        if (file_exists('./mod/autoupdate/tmp/tarball.zip')) {
            echo "<table class='og-table og-full-table'>" . "\n";
                echo "<thead>" . "\n";
            if ($zip->open('./mod/autoupdate/tmp/tarball.zip')) {
                echo "\t" . '<tr>' . "\n";
                echo "\t\t" . '<th>' . $lang['autoupdate_MaJ_downok'] . '</th>' . "\n";
                echo "\t" . '</tr>' . "\n";
                echo "</thead>" . "\n";
                $zip->extractTo("./mod/autoupdate/tmp/" . $modroot . "/"); //On extrait le mod dans le répertoire temporaire d'autoupdate
                $zip->close();
                unlink("./mod/autoupdate/tmp/tarball.zip");
                $nom_repertoire = glob("./mod/autoupdate/tmp/" . $modroot . "/*-" . $modroot . "*", GLOB_ONLYDIR); //On récupère le nom du répertoire
                $folder = explode('/', $nom_repertoire[0]);

                if (check_ogspy_version_bcopy($modroot . "/" . $folder[5])) {
                    rcopy("./mod/autoupdate/tmp/" . $modroot . "/" . $folder[5], "./mod/" . $modroot); //Copie du répertoire dans le dossier des mods
                    rrmdir("./mod/autoupdate/tmp/" . $modroot);
                    echo "<tbody>" . "\n";
                    echo "\t" . '<tr>' . "\n";
                    echo "\t\t" . '<td>' . $lang['autoupdate_MaJ_unzipok'] . '</td>' . "\n";
                    echo "\t" . '</tr>' . "\n";
                    echo "\t" . '<tr>' . "\n";
                    echo "\t\t" . '<td>' . upgrade_ogspy_mod($modroot) . '</td>' . "\n";
                } else {
                    echo "\t" . '<tr>' . "\n";
                    echo "\t\t" . '<td><span style="color:red">' . $lang['autoupdate_MaJ_errorversionogspy'] . '</span></td>' . "\n";
                    echo "\t" . '</tr>' . "\n";
                }
            }
        }
    } else {
        echo "<table class='og-table og-full-table'>" . "\n";
        echo "<thead>" . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<th>' . $lang['autoupdate_MaJ_wantupdate'] . $modroot . ' ' . $target_version . ' ?</th>' . "\n";
        echo "\t" . '</tr>' . "\n";
        echo "</thead>" . "\n";
        echo "<tbody>" . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<td><span class="og-button-small">' . ' <a href="index.php?action=autoupdate&sub=mod_upgrade&confirmed=yes&mod=' . $modroot . '&tag=' . $mod_tag . '">' . $lang['autoupdate_MaJ_linkupdate'] . '</a></span></td>' . "\n";
        echo "\t" . '</tr>' . "\n";
    }
    echo "\t" . '<tr>' . "\n";
    echo "\t\t" . '<td><a href=index.php?action=autoupdate>' . $lang['autoupdate_tableau_back'] . '</a></td>' . "\n";
    echo "\t" . '</tr>' . "\n";
    echo "</tbody>" . "\n";
    echo '</table>' . "\n";
    echo '<br>' . "\n";
} else {
    echo $lang['autoupdate_MaJ_rights'];
}
?>
<div style="text-align: center">
    AutoUpdate<br>
    <?= $lang['autoupdate_createdby'] . ' Jibus ' . $lang['autoupdate_and'] . ' Bartheleway' ?>
</div>
<?php
require_once("views/page_tail.php");
?>
