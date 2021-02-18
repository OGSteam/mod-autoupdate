<?php
/**
 * Autoupdate Tool upgrade File
 * @package [Mod] Autoupdate
 * @subpackage main
 * @author DarkNoon <darknoon@darkcity.fr>
 * @copyright Copyright &copy; 2016, http://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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

if ($user_data['user_admin'] == 1) {

    $toolroot = filter_var($pub_tool, FILTER_SANITIZE_STRING);
    $tool_tag = filter_var($pub_tag, FILTER_SANITIZE_STRING);

    $version = getRepositoryVersion('ogspy');

    $target_version = $version[ $tool_tag ];


    if ($pub_sub == "tool_upgrade" && $pub_confirmed == "yes") {

        echo '<table align="center" style="width : 400px">' . "\n";

        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<td class="c">' . $lang['autoupdate_MaJ_rightscheck'] . '</td>' . "\n";
        echo "\t" . '</tr>' . "\n";

        if (!is_writeable(".")) {
            die("Error: OGSpy folder must be writeable (755) " . __FILE__ . "(Ligne: " . __LINE__ . ")");
        }

        if ($tool_tag == 'dev') {
            $toolzip = "https://api.github.com/repos/OGSteam/" . $toolroot . "/zipball/develop";
        }elseif ($tool_tag == 'alpha') {
            $toolzip = "https://api.github.com/repos/OGSteam/" . $toolroot . "/zipball/" . $version['alpha'];
        }elseif ($tool_tag == 'beta') {
            $toolzip = "https://api.github.com/repos/OGSteam/" . $toolroot . "/zipball/" . $version['beta'];
        } else {
            $toolzip = "https://api.github.com/repos/OGSteam/" . $toolroot . "/zipball/" . $version['release'];
        }

        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<td class="c">' . $lang['autoupdate_MaJ_startdownload'] . '</td>' . "\n";
        echo "\t" . '</tr>' . "\n";

        $tool_file = github_Request($toolzip);
        file_put_contents('./mod/autoupdate/tmp/' . $toolroot . '.zip', $tool_file);

        if (file_exists('./mod/autoupdate/tmp/' . $toolroot . '.zip')) {

            if ($zip->open('./mod/autoupdate/tmp/' . $toolroot . '.zip') === TRUE) {
                echo "\t" . '<tr>' . "\n";
                echo "\t\t" . '<td class="c">' . $lang['autoupdate_MaJ_downok'] . '</td>' . "\n";
                echo "\t" . '</tr>' . "\n";

                $zip->extractTo("./mod/autoupdate/tmp/" . $toolroot . "/"); //On extrait le mod dans le répertoire temporaire d'autoupdate
                $zip->close();

                unlink("./mod/autoupdate/tmp/" . $toolroot . ".zip");

                $nom_répertoire = glob("./mod/autoupdate/tmp/" . $toolroot . "/*-" . $toolroot . "*", GLOB_ONLYDIR); //On récupère le nom du répertoire
                $folder = explode('/', $nom_répertoire[0]);
                rcopy("./mod/autoupdate/tmp/" . $toolroot . "/" . $folder[5], ".");
                rrmdir("./mod/autoupdate/tmp/" . $toolroot);

                //On passe au script de mise à jour.
                if (!is_writeable("./install")) {
                    die("Error: OGSpy install folder must be writeable (755) " . __FILE__ . "(Ligne: " . __LINE__ . ")");
                }

                chdir('./install'); //Passage dans le répertoire d'installation
                $pub_verbose = false; //Paramétrage de la mise à jour
                echo "\t" . '<tr>' . "\n";
                require_once("upgrade_to_latest.php"); // Mise à jour...
                echo "\t" . '</tr>' . "\n";
                chdir('..'); // Retour au répertoire par défaut.
                //Supression du répertoire Install
                rrmdir("./install");



                echo "\t" . '<tr>' . "\n";
                echo "\t\t" . '<td class="c">' . $lang['autoupdate_MaJ_unzipok'] . '</td>' . "\n";
                echo "\t" . '</tr>' . "\n";
                echo "\t" . '<tr>' . "\n";
                echo "\t\t" . '<td class="c"><a href=index.php?action=autoupdate>' . $lang['autoupdate_tableau_back'] . '</a></td>' . "\n";
                echo "\t" . '</tr>' . "\n";
                echo '</table>' . "\n";
                echo '<br>' . "\n";
            }else{
                echo "\t" . '<tr>' . "\n";
                echo "\t\t" . '<td class="c"><span style="color:red">' . $lang['autoupdate_MaJ_unzipnotok'] . '</span></td>' . "\n";
                echo "\t" . '</tr>' . "\n";
                echo "\t" . '<tr>' . "\n";
            }
        }
    } else {
        echo '<table>' . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<td class="c"><span style="color:red">' . $lang['autoupdate_MaJtool_wantbackup'] . '</span></td>' . "\n";
        echo "\t" . '</tr>' . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<td class="c">' . $lang['autoupdate_MaJtool_wantupdate'] . $toolroot . ' ' . $target_version . ' ?</td>' . "\n";
        echo "\t" . '</tr>' . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<th><a href="index.php?action=autoupdate&sub=tool_upgrade&confirmed=yes&tool=' . $toolroot . '&tag=' . $tool_tag . '">' . $lang['autoupdate_MaJ_linkupdate'] . '</a></th>' . "\n";
        echo "\t" . '</tr>' . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<td class="c"><a href=index.php?action=autoupdate>' . $lang['autoupdate_tableau_back'] . '</a></td>' . "\n";
        echo "\t" . '</tr>' . "\n";
        echo '</table>' . "\n";
    }

} else {
    echo $lang['autoupdate_MaJ_rights'];
}
echo '<br>' . "\n";
echo 'AutoUpdate ' . $lang['autoupdate_version'] . ' ' . versionmod();
echo '<br>' . "\n";
echo $lang['autoupdate_createdby'] . ' Jibus ' . $lang['autoupdate_and'] . ' Bartheleway.</div>';
require_once("views/page_tail.php");

