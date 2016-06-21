<?php
/**
 * tool_upgrade.php Met à jour OGSpy depuis le serveur
 * @package [MOD] AutoUpdate
 * @author DarkNoon
 * @version 2.0
 * created    : 27/10/2006
 * modified    : 18/01/2007
 * $Id: MaJ.php 7668 2012-07-15 22:16:03Z darknoon $
 */

if (!defined('IN_SPYOGAME')) die("Hacking attempt");
/**
 *Récupère les fonctions zip
 */
$zip = new ZipArchive;

require_once("views/page_header.php");

if (!isset($pub_confirmed)) $pub_confirmed = "no";

if ($user_data['user_admin'] == 1) {

    $toolroot = filter_var($pub_tool, FILTER_SANITIZE_STRING);
    $version = filter_var($pub_tag, FILTER_SANITIZE_STRING);

    if ($pub_sub == "tool_upgrade" && $pub_confirmed == "yes") {

        if (!is_writeable(".")) {
            die("Error: OGSpy folder must be writeable (755) " . __FILE__ . "(Ligne: " . __LINE__ . ")");
        }

        if ($version == 'trunk') {
            $toolzip = "https://bitbucket.org/ogsteam/" . $toolroot . "/get/default.zip";
        } else {
            $toolzip = "https://bitbucket.org/ogsteam/" . $toolroot . "/get/" . $version . ".zip";
        }

        if (copy($toolzip, './mod/autoupdate/tmp/' . $toolroot . '.zip')) {
            echo '<table align="center" style="width : 400px">' . "\n";

            if ($zip->open('./mod/autoupdate/tmp/' . $toolroot . '.zip') === TRUE) {
                echo "\t" . '<tr>' . "\n";
                echo "\t\t" . '<td class="c">' . $lang['autoupdate_MaJ_downok'] . '</td>' . "\n";
                echo "\t" . '</tr>' . "\n";

                $zip->extractTo("./mod/autoupdate/tmp/" . $toolroot . "/"); //On extrait le mod dans le répertoire temporaire d'autoupdate
                $zip->close();

                unlink("./mod/autoupdate/tmp/" . $toolroot . ".zip");

                $nom_répertoire = glob("./mod/autoupdate/tmp/" . $toolroot . "/*-" . $toolroot . "*", GLOB_ONLYDIR);//On récupère le nom du répertoire
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
                chdir('..');// Retour au répertoire par défaut.
                //Supression du répertoire Install
                rrmdir("./install");



                echo "\t" . '<tr>' . "\n";
                echo "\t\t" . '<td class="c">' . $lang['autoupdate_MaJ_unzipok'] . '</td>' . "\n";
                echo "\t" . '</tr>' . "\n";
                echo "\t" . '<tr>' . "\n";
                echo "\t\t" . '<td class="c">' . $lang['autoupdate_tableau_back'] . '</td>' . "\n";
                echo "\t" . '</tr>' . "\n";
                echo '</table>' . "\n";
                echo '<br>' . "\n";
            }
        }
    } else {
        echo '<table>' . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<td class="c">' . $lang['autoupdate_MaJtool_wantbackup'] . '</td>' . "\n";
        echo "\t" . '</tr>' . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<td class="c">' . $lang['autoupdate_MaJtool_wantupdate'] . $toolroot . ' ' . $version . ' ?</td>' . "\n";
        echo "\t" . '</tr>' . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<th><a href="index.php?action=autoupdate&sub=tool_upgrade&confirmed=yes&tool=' . $toolroot . '&tag=' . $version . '">' . $lang['autoupdate_MaJ_linkupdate'] . '</a></th>' . "\n";
        echo "\t" . '</tr>' . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<td class="c">' . $lang['autoupdate_tableau_back'] . '</td>' . "\n";
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

