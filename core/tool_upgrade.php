<?php
global $user_data, $lang,$pub_confirmed,$pub_tool,$pub_tag,$pub_sub;

/**
 * Autoupdate Tool upgrade File
 * @package [Mod] Autoupdate
 * @subpackage main
 * @author DarkNoon <darknoon@darkcity.fr>
 * @copyright Copyright &copy; 2016, https://ogsteam.eu/
 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 2.1.9
 */

 use Ogsteam\Ogspy\Model\Config_Model;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}
/**
 *Récupère les fonctions zip
 */
$zip = new ZipArchive;

require("views/page_header.php");

if (!isset($pub_confirmed)) {
    $pub_confirmed = "no";
}

if ($user_data['admin'] == 1) {

    $toolroot = filter_var($pub_tool);
    $tool_tag = filter_var($pub_tag);

    $version = getRepositoryVersion('ogspy');

    $target_version = $version[$tool_tag];


    if ($pub_sub == "tool_upgrade" && $pub_confirmed == "yes") {

        echo "<table class='og-table og-full-table'>" . "\n";
        echo "<thead>" . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<th>' . $lang['autoupdate_MaJ_rightscheck'] . '</th>' . "\n";
        echo "\t" . '</tr>' . "\n";
        echo "</thead>" . "\n";
        if (!is_writeable(".")) {
            die("Error: OGSpy folder must be writeable (755) " . __FILE__ . "(Ligne: " . __LINE__ . ")");
        }

        if ($tool_tag == 'beta') {
            $toolzip = "https://github.com/OGSteam/" . $toolroot . "/releases/download/" . $version['beta'] . "/$toolroot-" . $version['beta'] . ".zip";
        } else {
            $toolzip = "https://github.com/OGSteam/" . $toolroot . "/releases/download/" . $version['release'] . "/$toolroot-" . $version['release'] . ".zip";
        }
        echo "<tbody>" . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<td>' . $lang['autoupdate_MaJ_startdownload'] . '</td>' . "\n";
        echo "\t" . '</tr>' . "\n";

        $tool_file = github_Request($toolzip);
        file_put_contents('./mod/autoupdate/tmp/' . $toolroot . '.zip', $tool_file);

        if (file_exists('./mod/autoupdate/tmp/' . $toolroot . '.zip')) {

            echo "\t" . '<tr>' . "\n";
            if ($zip->open('./mod/autoupdate/tmp/' . $toolroot . '.zip')) {
                echo "\t\t" . '<td>' . $lang['autoupdate_MaJ_downok'] . '</td>' . "\n";
                echo "\t" . '</tr>' . "\n";

                $zip->extractTo("./mod/autoupdate/tmp/" . $toolroot . "/"); //On extrait le mod dans le répertoire temporaire d'autoupdate
                $zip->close();

                unlink("./mod/autoupdate/tmp/" . $toolroot . ".zip");

                $nom_répertoire = glob("./mod/autoupdate/tmp/" . $toolroot, GLOB_ONLYDIR); //On récupère le nom du répertoire
                $folder = explode('/', $nom_répertoire[0]);
                rcopy("./mod/autoupdate/tmp/" . $toolroot, ".");
                rrmdir("./mod/autoupdate/tmp/" . $toolroot);

                //On passe au script de mise à jour.
                if (!is_writable("./install")) {
                    die("Error: OGSpy install folder must be writeable (755) " . __FILE__ . "(Ligne: " . __LINE__ . ")");
                }

                //Disable Logs (Will not work due to chdir)
                $Config_Model = new Config_Model();
                $Config_Model->update_one(0, "debug_log");
                $Config_Model->update_one(0, "log_phperror");
                generate_config_cache();

                // Utilisation du système moderne de mise à jour d'OGSpy
                echo "\t" . '<tr>' . "\n";
                echo "\t\t" . '<td>Démarrage de la mise à jour automatique...</td>' . "\n";
                echo "\t" . '</tr>' . "\n";
                
                try {
                    // Chargement des classes modernes d'OGSpy
                    require_once('./install/MigrationManager.php');
                    require_once('./install/AutoUpgradeManager.php');
                    require_once('./install/version.php');
                    
                    // Utilisation du logger global d'OGSpy
                    global $log, $db, $table_prefix;
                    
                    // Création du gestionnaire de mise à jour automatique
                    $autoUpgradeManager = new AutoUpgradeManager($db, $log, $table_prefix);
                    
                    // Exécution de la mise à jour
                    $result = $autoUpgradeManager->checkAndUpgrade();
                    
                    echo "\t" . '<tr>' . "\n";
                    if ($result['status'] === 'success') {
                        echo "\t\t" . '<td style="color: green;">✓ ' . $lang['autoupdate_MaJ_uptodateok'] . '</td>' . "\n";
                        if (!empty($result['migrations_count'])) {
                            echo "\t" . '</tr>' . "\n";
                            echo "\t" . '<tr>' . "\n";
                            echo "\t\t" . '<td>Migrations appliquées: ' . (int)$result['migrations_count'] . '</td>' . "\n";
                        }
                    } else {
                        echo "\t\t" . '<td style="color: red;">❌ Erreur: ' . htmlspecialchars($result['message'] ?? 'Erreur inconnue') . '</td>' . "\n";
                    }
                    echo "\t" . '</tr>' . "\n";
                    
                } catch (Exception $e) {
                    echo "\t" . '<tr>' . "\n";
                    echo "\t\t" . '<td style="color: red;">❌ Erreur lors de la mise à jour: ' . htmlspecialchars($e->getMessage()) . '</td>' . "\n";
                    echo "\t" . '</tr>' . "\n";
                }
                
                // Note: Le répertoire install/ n'est plus supprimé car il contient
                // les classes modernes de gestion des migrations (MigrationManager, AutoUpgradeManager)

                echo "\t" . '<tr>' . "\n";
                echo "\t\t" . '<td>' . $lang['autoupdate_MaJ_unzipok'] . '</td>' . "\n";
                echo "\t" . '</tr>' . "\n";
                echo "\t" . '<tr>' . "\n";
                echo "\t\t" . '<td><a href=index.php?action=autoupdate>' . $lang['autoupdate_tableau_back'] . '</a></td>' . "\n";
                echo "\t" . '</tr>' . "\n";
                echo "</tbody>" . "\n";
                echo '</table>' . "\n";
                echo '<br>' . "\n";
                // Rechargement de la page
                redirection("index.php");
            } else {
                echo "\t\t" . '<td><span style="color:red">' . $lang['autoupdate_MaJ_unzipnotok'] . '</span></td>' . "\n";
                echo "\t" . '</tr>' . "\n";

            }
        }
    } else {
        echo "<table class='og-table og-full-table'>" . "\n";
        echo "<thead>" . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<th><span style="color:red">' . $lang['autoupdate_MaJtool_wantbackup'] . '</span></th>' . "\n";
        echo "\t" . '</tr>' . "\n";
        echo "</thead>" . "\n";
        echo "<tbody>" . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<td>' . $lang['autoupdate_MaJtool_wantupdate'] . $toolroot . ' ' . $target_version . ' ?</td>' . "\n";
        echo "\t" . '</tr>' . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<th><a href="index.php?action=autoupdate&sub=tool_upgrade&confirmed=yes&tool=' . $toolroot . '&tag=' . $tool_tag . '">' . $lang['autoupdate_MaJ_linkupdate'] . '</a></th>' . "\n";
        echo "\t" . '</tr>' . "\n";
        echo "\t" . '<tr>' . "\n";
        echo "\t\t" . '<td><a href=index.php?action=autoupdate>' . $lang['autoupdate_tableau_back'] . '</a></td>' . "\n";
        echo "\t" . '</tr>' . "\n";
        echo "</tbody>" . "\n";
        echo '</table>' . "\n";
    }
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
