<?php
/**
 * Autoupdate Table view
 * @package [Mod] Autoupdate
 * @subpackage main
 * @author DarkNoon <darknoon@darkcity.fr>
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
<div align="center"><?php echo $lang['autoupdate_tableau_info']; ?></div>
<br/>
<table width='60%'>
    <tr>
        <td class='c' colspan='100'><?php echo $lang['autoupdate_tableau_toolinstall']; ?></td>
    </tr>
    <tr>
        <td class='c'><?php echo $lang['autoupdate_tableau_nametool']; ?></td>
        <td class='c'><?php echo $lang['autoupdate_tableau_authtool']; ?></td>
        <td class='c' width="50"><?php echo $lang['autoupdate_tableau_version']; ?></td>
        <td class='c' width="50"><?php echo $lang['autoupdate_tableau_versionSVN']; ?></td>
        <?php if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) {
    echo '<td class=\'c\' width = "100">' . $lang['autoupdate_tableau_action'] . '</td>';
}
         if (mod_get_option("MAJ_BETA") == 1) {
            echo "<td class='c' width = '50'>";
            echo $lang['autoupdate_tableau_versionBeta'] . "</td>";
        }
        if (mod_get_option("MAJ_ALPHA") == 1) {
            echo "<td class='c' width = '50'>";
            echo $lang['autoupdate_tableau_versionAlpha'] . "</td>";
        }
        if (mod_get_option("MAJ_DEV") == 1) {
            echo "<td class='c' width = '50'>";
            echo $lang['autoupdate_tableau_versionDev'] . "</td>";
        }
        ?>
        <td class='c' width="80"><?php echo $lang['autoupdate_tableau_bug']; ?></td>
    </tr>
    <tr>
        <th>OGSpy</th>
        <th>OGSteam</th>
        <th>
            <?php
            echo $server_config["version"] . "</th>";
            $cur_version = getRepositoryVersion('ogspy', false);
            if (!is_array($cur_version) || $cur_version == '-1') {
                echo "\t\t<th>" . $lang['autoupdate_tableau_norefered'] . "</th>\n";
                $cur_version = 0;
            } else {
                echo "\t\t<th>" . $cur_version['release'] . "</th>\n";
            }
            echo "<th>";
            if (version_compare($cur_version['release'], $server_config["version"], ">")) {
                $ziplink = "<a href='index.php?action=autoupdate&sub=tool_upgrade&tool=ogspy&tag=" . $cur_version['release'] . "'>" . $lang['autoupdate_tableau_uptodate'] . "</a>";
                echo "<span style=\"color: lime; \">" . $ziplink . "</span>";
            } else {
                echo "Aucune";
            }
            echo "</th>";
            if (mod_get_option("MAJ_BETA") == 1) {
                echo "<th>";
            if(isset($cur_version['alpha'])) {
                $ziplink = "<a href='index.php?action=autoupdate&sub=tool_upgrade&tool=ogspy&tag=beta'>".$cur_version['beta']."</a>";
            }else{
                $ziplink = "-";
            }
                echo "<span style=\"color: lime; \">" . $ziplink . "</span>";
                echo "</th>";
            }
            if (mod_get_option("MAJ_ALPHA") == 1) {
                echo "<th>";
            if(isset($cur_version['alpha'])) {
                $ziplink = "<a href='index.php?action=autoupdate&sub=tool_upgrade&tool=ogspy&tag=alpha'>".$cur_version['alpha']."</a>";
            }else{
                $ziplink = "-";
            }
                echo "<span style=\"color: lime; \">" . $ziplink . "</span>";
                echo "</th>";
            }
            if (mod_get_option("MAJ_DEV") == 1) {
                echo "<th>";
            if(isset($cur_version['alpha'])) {
                $ziplink = "<a href='index.php?action=autoupdate&sub=tool_upgrade&tool=ogspy&tag=trunk'>".$cur_version['dev']."</a>";
            }else{
                $ziplink = "-";
            }
                echo "<span style=\"color: lime; \">" . $ziplink . "</span>";
                echo "</th>";
            }
            echo "<th>";
            $trackerlink = "<a href='https://github.com/OGSteam/ogspy/issues' target='_blank'>" . $lang['autoupdate_tableau_buglink'] . "</a>";
            echo "<span style=\"color: lime; \">" . $trackerlink . "</span>";
            echo "</th>";
            ?>
    </tr>
    <tr>
        <td class='c' colspan='100'></td>
    </tr>
    <tr>
        <td class='c' colspan='100'><?php echo $lang['autoupdate_tableau_modinstall']; ?></td>
    </tr>
    <tr>
        <td class='c'><?php echo $lang['autoupdate_tableau_namemod']; ?></td>
        <td class='c' width="50"><?php echo $lang['autoupdate_tableau_authormod']; ?></td>
        <td class='c' width="50"><?php echo $lang['autoupdate_tableau_version']; ?></td>
        <td class='c' width="50"><?php echo $lang['autoupdate_tableau_versionSVN']; ?></td>
        <?php if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) {
    echo '<td class=\'c\' width = "100">' . $lang['autoupdate_tableau_action'] . '</td>';
}
?>
        <?php if (mod_get_option("MAJ_BETA") == 1) {
            echo "<td class='c' width = '50'>";
            echo $lang['autoupdate_tableau_versionBeta'] . "</td>";
        } ?>
        <?php if (mod_get_option("MAJ_ALPHA") == 1) {
            echo "<td class='c' width = '50'>";
            echo $lang['autoupdate_tableau_versionAlpha'] . "</td>";
        } ?>
        <?php if (mod_get_option("MAJ_DEV") == 1) {
            echo "<td class='c' width = '50'>";
            echo $lang['autoupdate_tableau_versionDev'] . "</td>";
        } ?>
        <td class='c' width="80"><?php echo $lang['autoupdate_tableau_bug']; ?></td>
    </tr>
    <?php

    //
    for ($i = 0; $i < count($installed_mods); $i++) {
        if (substr($installed_mods[$i]['name'], 0, 5) != "Group") {
            $repo_details = getRepositoryDetails($installed_mods[$i]['root']);
            echo "\t<tr>\n";
            echo "\t\t<th>" . $installed_mods[$i]['name'] . "</th>\n";
            if (isset($repo_details['owner'])) {
                echo "\t\t<th>" . $repo_details['owner'] . "</th>\n";
            } else {
                echo "\t\t<th>Non OGSteam</th>\n";
            }

            echo "\t\t<th>" . $installed_mods[$i]['version'] . "</th>\n";

            $cur_modroot = $installed_mods[$i]['root'];
            $cur_version = getRepositoryVersion($cur_modroot);

            if (!is_array($cur_version) || $cur_version == '-1') {
                echo "\t\t<th>" . $lang['autoupdate_tableau_norefered'] . "</th>\n";
                $cur_version = 0;
            } else {
                echo "\t\t<th>" . $cur_version['release'] . "</th>\n";
            }

            if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) {
                echo "\t\t<th>";
                if (!is_writeable("./mod/" . $installed_mods[$i]['root'] . "/")) {
                    echo "<a title='Pas de droit en écriture sur:./mod/" . $installed_mods[$i]['root'] . "'><span style=\"color: red; \">(RO)</span></a>";
                } else {
                    if (version_compare($cur_version['release'], $installed_mods[$i]['version'], ">")) {
                        $ziplink = "<a href='index.php?action=autoupdate&sub=mod_upgrade&mod=" . $cur_modroot . "&tag=" . $cur_version['release'] . "'>" . $lang['autoupdate_tableau_uptodate'] . "</a>";
                        echo "<span style=\"color: lime; \">" . $ziplink . "</span>";
                    } else {
                        echo "Aucune";
                    }
                }
                echo "</th>\n";
                if (mod_get_option("MAJ_BETA") == 1) {
                    echo "\t\t<th>";
                    if(isset($cur_version['beta'])) {
                        $ziplink = "<a href='index.php?action=autoupdate&sub=mod_upgrade&mod=" . $cur_modroot . "&tag=beta'>".$cur_version['beta']."</a>";
                    }else
                    {
                        $ziplink = "-";
                    }
                    echo "<span style=\"color: lime; \">" . $ziplink . "</span>";
                    echo "</th>\n";
                }
                if (mod_get_option("MAJ_ALPHA") == 1) {
                    echo "\t\t<th>";
                    if(isset($cur_version['alpha'])) {
                    $ziplink = "<a href='index.php?action=autoupdate&sub=mod_upgrade&mod=" . $cur_modroot . "&tag=alpha'>".$cur_version['alpha']."</a>";
                    }else
                    {
                        $ziplink = "-";
                    }
                    echo "<span style=\"color: lime; \">" . $ziplink . "</span>";
                    echo "</th>\n";
                }
                if (mod_get_option("MAJ_DEV") == 1) {
                    echo "\t\t<th>";
                    if(isset($cur_version['dev'])) {
                    $ziplink = "<a href='index.php?action=autoupdate&sub=mod_upgrade&mod=" . $cur_modroot . "&tag=trunk'>".$cur_version['dev']."</a>";
                    }else
                    {
                        $ziplink = "-";
                    }
                    echo "<span style=\"color: lime; \">" . $ziplink . "</span>";
                    echo "</th>\n";
                }
                echo "\t\t<th>";
                if (isset($repo_details['owner'])) {
                    $trackerlink = "<a href='https://github.com/" . $repo_details['owner'] . "/mod-" . $cur_modroot . "/issues' target='_blank'>" . $lang['autoupdate_tableau_buglink'] . "</a>";
                    echo "<span style=\"color: lime; \">" . $trackerlink . "</span>";
                }
                echo "</th>\n";

            }

        }
        echo "\t</tr>\n";
    }


    if ($user_data["user_admin"] == 1 || $user_data['user_coadmin'] == 1) {
        // Proposer le lien vers le panneau d'administration des modules

        ?>
        <tr>
            <td class="c" colspan="100"><?php echo $lang['autoupdate_tableau_link']; ?></td>
        </tr>
        <tr>
            <th colspan="100"><a
                    href="index.php?action=administration&subaction=mod"><?php echo $lang['autoupdate_tableau_pageadmin']; ?></a>
            </th>
        </tr>
        <tr>
            <th colspan="100"><a href="https://www.ogsteam.fr">OGSteam.fr</a></th>
        </tr>
        <?php
    }
    ?>
</table>
<?php
echo '<br>' . "\n";
echo 'AutoUpdate ' . $lang['autoupdate_version'] . ' ' . versionmod();
echo '<br>' . "\n";
echo $lang['autoupdate_createdby'] . ' Jibus ' . $lang['autoupdate_and'] . ' Bartheleway.</div>';
require_once("views/page_tail.php");
?>
