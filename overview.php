<?php
/** $Id: tableau.php 7672 2012-08-05 21:33:46Z darknoon $ **/
/**
 * autoupdate.php Met à jour les mods depuis le serveur
 * @package [MOD] AutoUpdate
 * @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
 * @version 1.0
 * created    : 27/10/2006
 * modified    : 18/01/2007
 */

if (!defined('IN_SPYOGAME')) die("Hacking attempt");

require_once("mod/autoupdate/mod_list.php");
require_once("views/page_header.php");

$query = "SELECT `active` FROM `" . TABLE_MOD . "` WHERE `action`='autoupdate' AND `active`='1' LIMIT 1";
if (!$db->sql_numrows($db->sql_query($query))) die("Hacking attempt");

$installed_mods = get_installed_mod_list();

// Recupération des Mods disponible sur l'ogsteam
//$data = getmodlist();
//$mod_names = array_keys($data); // Récupération des clés


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
        <?php if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) echo '<td class=\'c\' width = "100">' . $lang['autoupdate_tableau_action'] . '</td>'; ?>
        <?php if (mod_get_option("MAJ_TRUNK") == 1) {
            echo "<td class='c' width = '50'>";
            echo $lang['autoupdate_tableau_versionTrunk'] . "</td>";
        } ?>
        <td class='c' width="80"><?php echo $lang['autoupdate_tableau_bug']; ?></td>
    </tr>
    <tr>
        <th>OGSpy</th>
        <th>OGSteam</th>
        <th>
            <?php
            echo $server_config["version"] . "</th>";
            $cur_version = getRepositoryVersion('ogspy', false);
            if ($cur_version == '-1') {
                echo "\t\t<th>" . $lang['autoupdate_tableau_norefered'] . "</th>\n";
                $cur_version = 0;
            } else {
                echo "\t\t<th>" . $cur_version . "</th>\n";
            }
            echo "<th>";
            if (version_compare($cur_version, $server_config["version"], ">")) {
                $ziplink = "<a href='index.php?action=autoupdate&sub=tool_upgrade&tool=ogspy&tag=" . $cur_version . "'>" . $lang['autoupdate_tableau_uptodate'] . "</a>";
                echo "<font color='lime'>" . $ziplink . "</font>";
            } else {
                echo "Aucune";
            }
            echo "</th>";
            if (mod_get_option("MAJ_TRUNK") == 1) {
                echo "<th>";
                $ziplink = "<a href='index.php?action=autoupdate&sub=tool_upgrade&tool=ogspy&tag=trunk'>Télécharger</a>";
                echo "<font color='lime'>" . $ziplink . "</font>";
                echo "</th>";
            }
            echo "<th>";
            $trackerlink = "<a href='https://bitbucket.org/ogsteam/ogspy/issues?status=new&status=open' target='_blank'>" . $lang['autoupdate_tableau_buglink'] . "</a>";
            echo "<font color='lime'>" . $trackerlink . "</font>";
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
        <?php if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) echo '<td class=\'c\' width = "100">' . $lang['autoupdate_tableau_action'] . '</td>'; ?>
        <?php if (mod_get_option("MAJ_TRUNK") == 1) {
            echo "<td class='c' width = '50'>";
            echo $lang['autoupdate_tableau_versionTrunk'] . "</td>";
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
            if (isset($repo_details['owner'])) echo "\t\t<th>" . $repo_details['owner'] . "</th>\n";
            else echo "\t\t<th>Non OGSteam</th>\n";

            echo "\t\t<th>" . $installed_mods[$i]['version'] . "</th>\n";

            $cur_modroot = $installed_mods[$i]['root'];
            $cur_version = getRepositoryVersion($cur_modroot);

            if ($cur_version == '-1') {
                echo "\t\t<th>" . $lang['autoupdate_tableau_norefered'] . "</th>\n";
                $cur_version = 0;
            } else {
                echo "\t\t<th>" . $cur_version . "</th>\n";
            }

            if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) {
                echo "\t\t<th>";
                if (!is_writable("./mod/" . $installed_mods[$i]['root'] . "/")) echo "<a title='Pas de droit en écriture sur:./mod/" . $installed_mods[$i]['root'] . "'><font color=red>(RO)</font></a>";
                else {
                    if (version_compare($cur_version, $installed_mods[$i]['version'], ">")) {
                        $ziplink = "<a href='index.php?action=autoupdate&sub=mod_upgrade&mod=" . $cur_modroot . "&tag=" . $cur_version . "'>" . $lang['autoupdate_tableau_uptodate'] . "</a>";
                        echo "<font color='lime'>" . $ziplink . "</font>";
                    } else {
                        echo "Aucune";
                    }
                }
                echo "</th>\n";
                if (mod_get_option("MAJ_TRUNK") == 1) {
                    echo "\t\t<th>";
                    $ziplink = "<a href='index.php?action=autoupdate&sub=mod_upgrade&mod=" . $cur_modroot . "&tag=trunk'>Télécharger</a>";
                    echo "<font color='lime'>" . $ziplink . "</font>";
                    echo "</th>\n";
                }
                echo "\t\t<th>";
                if (isset($repo_details['owner'])) {
                    $trackerlink = "<a href='https://bitbucket.org/" . $repo_details['owner'] . "/mod-" . $cur_modroot . "/issues?status=new&status=open' target='_blank'>" . $lang['autoupdate_tableau_buglink'] . "</a>";
                    echo "<font color='lime'>" . $trackerlink . "</font>";
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
            <th colspan="100"><a href="http://www.ogsteam.fr">OGSteam.fr</a></th>
        </tr>
        <?php
    }
    ?>
</table>
<?php
echo '<br />' . "\n";
echo 'AutoUpdate ' . $lang['autoupdate_version'] . ' ' . versionmod();
echo '<br />' . "\n";
echo $lang['autoupdate_createdby'] . ' Jibus ' . $lang['autoupdate_and'] . ' Bartheleway.</div>';
require_once("views/page_tail.php");
?>
