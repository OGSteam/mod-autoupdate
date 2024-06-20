<?php
global $db,$lang,$user_data,$server_config;
/**
 * Autoupdate Table view
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

require_once("mod/autoupdate/core/mod_list.php");
//require_once("view/page_header.php");

$query = "SELECT `active` FROM `" . TABLE_MOD . "` WHERE `action`='autoupdate' AND `active`='1' LIMIT 1";
if (!$db->sql_numrows($db->sql_query($query))) {
    die("Mod Disabled");
}
//TODO Améliorer ce contrôle

$installed_mods = get_installed_mod_list();


?>



<div style="text-align: center"><?php echo $lang['autoupdate_tableau_info']; ?></div>
<br />
<table class='og-table og-medium-table'>
    <thead>
    <tr>
        <th colspan='100'><?php echo $lang['autoupdate_tableau_toolinstall']; ?></th>
    </tr>
    <tr>
        <th><?php echo $lang['autoupdate_tableau_nametool']; ?></th>
        <th><?php echo $lang['autoupdate_tableau_authtool']; ?></th>
        <th><?php echo $lang['autoupdate_tableau_version']; ?></th>
        <th><?php echo $lang['autoupdate_tableau_versionSVN']; ?></th>
        <?php if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) {
            echo '<th>' . $lang['autoupdate_tableau_action'] . '</th>';
        }
        if (mod_get_option("MAJ_BETA") == 1) {
            echo "<th>". $lang['autoupdate_tableau_versionBeta']. "</th>";
        }
        ?>
        <th><?php echo $lang['autoupdate_tableau_bug']; ?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="tdcontent">OGSpy</td>
        <td class="tdcontent">OGSteam</td>

            <?php
            echo "\t\t<td>" . $server_config["version"] . "</td>";
            $cur_version = getRepositoryVersion('ogspy');
            if (!is_array($cur_version) || $cur_version == '-1') {
                echo "\t\t<td>" . $lang['autoupdate_tableau_norefered'] . "</td>\n";
                $cur_version = 0;
            } else {
                echo "\t\t<td>" . $cur_version['release'] . "</td>\n";
            }
            echo "<td>";
            if (version_compare($cur_version['release'], $server_config["version"], ">")) {
                $ziplink = "<a href='index.php?action=autoupdate&sub=tool_upgrade&tool=ogspy&tag=release'>" . $lang['autoupdate_tableau_uptodate'] . "</a>";
                echo "<span style=\"color: lime; \">" . $ziplink . "</span>";
            } else {
                echo "Aucune";
            }
            echo "</td>";
            if (mod_get_option("MAJ_BETA") == 1) {
                echo "<td>";
                if (isset($cur_version['beta'])) {
                    $ziplink = "<a href='index.php?action=autoupdate&sub=tool_upgrade&tool=ogspy&tag=beta'>" . $cur_version['beta'] . "</a>";
                } else {
                    $ziplink = "-";
                }
                echo "<span style=\"color: lime; \">" . $ziplink . "</span>";
                echo "</td>";
            }
            echo "<td>";
            $trackerlink = "<a href='https://github.com/OGSteam/ogspy/issues' target='_blank' rel='noopener'>" . $lang['autoupdate_tableau_buglink'] . "</a>";
            echo "<span style=\"color: lime; \">" . $trackerlink . "</span>";
            echo "</td>";
            ?>
    </tr>
    </tbody>
</table>
<br />
<table class='og-table og-medium-table'>
    <thead>
    <tr>
        <th colspan="100"><?php echo $lang['autoupdate_tableau_modinstall']; ?></th>
    </tr>
    <tr>
        <th><?php echo $lang['autoupdate_tableau_namemod']; ?></th>
        <th><?php echo $lang['autoupdate_tableau_authormod']; ?></th>
        <th><?php echo $lang['autoupdate_tableau_version']; ?></th>
        <th><?php echo $lang['autoupdate_tableau_versionSVN']; ?></th>
        <?php if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) {
            echo '<th>' . $lang['autoupdate_tableau_action'] . '</th>';
        }
        ?>
        <?php if (mod_get_option("MAJ_BETA") == 1) {
            echo "<th> ". $lang['autoupdate_tableau_versionBeta']. "</th>";
        } ?>
        <th><?php echo $lang['autoupdate_tableau_bug']; ?></th>
    </tr>
    </thead>
    <tbody>
    <?php

    //
    for ($i = 0; $i < count($installed_mods); $i++) {
        if (!str_starts_with($installed_mods[$i]['name'], "Group")) {
            $repo_details = getRepositoryDetails($installed_mods[$i]['root']);
            echo "\t<tr>\n";
            echo "\t\t<td>" . $installed_mods[$i]['name'] . "</td>\n";
            if (isset($repo_details['owner'])) {
                echo "\t\t<td>" . $repo_details['owner'] . "</td>\n";
            } else {
                echo "\t\t<td>Non OGSteam</td>\n";
            }

            echo "\t\t<td>" . $installed_mods[$i]['version'] . "</td>\n";

            $cur_modroot = $installed_mods[$i]['root'];
            $cur_version = getRepositoryVersion($cur_modroot);

            if (!is_array($cur_version) || $cur_version == '-1') {
                echo "\t\t<td>" . $lang['autoupdate_tableau_norefered'] . "</td>\n";
                $cur_version = 0;
            } else {
                echo "\t\t<td>" . $cur_version['release'] . "</td>\n";
            }

            if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) {
                echo "\t\t<td>";
                if (!is_writeable("./mod/" . $installed_mods[$i]['root'] . "/")) {
                    echo "<a title='Pas de droit en écriture sur:./mod/" . $installed_mods[$i]['root'] . "'><span style=\"color: red; \">(RO)</span></a>";
                } else {
                    if (version_compare($cur_version['release'], $installed_mods[$i]['version'], ">")) {
                        $ziplink = "<a href='index.php?action=autoupdate&sub=mod_upgrade&mod=" . $cur_modroot . "&tag=release'>" . $lang['autoupdate_tableau_uptodate'] . "</a>";
                        echo "<span style=\"color: lime; \">" . $ziplink . "</span>";
                    } else {
                        echo "Aucune";
                    }
                }
                echo "</td>\n";
                if (mod_get_option("MAJ_BETA") == 1) {
                    echo "\t\t<td>";
                    if (isset($cur_version['beta'])) {
                        $ziplink = "<a href='index.php?action=autoupdate&sub=mod_upgrade&mod=" . $cur_modroot . "&tag=beta'>" . $cur_version['beta'] . "</a>";
                    } else {
                        $ziplink = "-";
                    }
                    echo "<span style=\"color: lime; \">" . $ziplink . "</span>";
                    echo "</td>\n";
                }

                echo "\t\t<td>";
                if (isset($repo_details['owner'])) {
                    $trackerlink = "<a href='https://github.com/" . $repo_details['owner'] . "/mod-" . $cur_modroot . "/issues' target='_blank'>" . $lang['autoupdate_tableau_buglink'] . "</a>";
                    echo "<span style=\"color: lime; \">" . $trackerlink . "</span>";
                }
                echo "</td>\n";
            }
        }
        echo "\t</tr>\n";
    }

    ?>
    </tbody>
</table>
    <?php

    if ($user_data["user_admin"] == 1 || $user_data['user_coadmin'] == 1) {
        // Proposer le lien vers le panneau d'administration des modules

    ?>
        <table class='og-table og-medium-table'>
            <thead>
        <tr>
            <th colspan="100"><?php echo $lang['autoupdate_tableau_link']; ?></th>
        </tr>
            </thead>
            <tbody>
        <tr>
            <th colspan="100"><a href="index.php?action=administration&subaction=mod"><?php echo $lang['autoupdate_tableau_pageadmin']; ?></a>
            </th>
        </tr>
        <tr>
            <th colspan="100"><a href="https://www.ogsteam.eu">ogsteam.eu</a></th>
        </tr>
            </tbody>
        </table>
    <?php
    }
    ?>
</table>
<br>
<div style="text-align: center">
    AutoUpdate <?= $lang['autoupdate_version'] . ' ' . versionmod(); ?><br>
    <?= $lang['autoupdate_createdby'] . ' Jibus ' . $lang['autoupdate_and'] . ' Bartheleway' ?>
</div>
<?php
require_once("views/page_tail.php");
?>
