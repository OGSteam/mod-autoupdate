<?php

/**
 * Autoupdate Downloader
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

if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) {

    // Récupérer la liste des modules installés
    $sql = "SELECT title,root,version from " . TABLE_MOD;
    $res = $db->sql_query($sql, false, true);

    $a = 0;
    while (list($modname, $modroot, $modversion) = $db->sql_fetch_row($res)) {
        $installed_mods[$a]['name'] = $modname;
        $installed_mods[$a]['root'] = $modroot;
        $installed_mods[$a++]['version'] = $modversion;
    }
    //Récupérer la liste des mods disponibles sur le dépot
    $download_mod_list = getRepositorylist();

?>
    <table width='60%'>
        <?php
        if (!is_writeable("./mod/")) {
            echo "<tr><td class='c' colspan='100'><span style=\"color: red; \">" . $lang['autoupdate_tableau_error3'] . "</span></td></tr>";
        }

        ?>
        <tr>
            <td class='c' colspan='4'><?php echo $lang['autoupdate_tableau_modnoinstall']; ?></td>
        </tr>

        <tr>
            <td class='c'><?php echo $lang['autoupdate_tableau_namemod']; ?></td>
            <td class='c'><?php echo $lang['autoupdate_tableau_descmod']; ?></td>
            <?php if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) {
                echo '<td class=\'c\' width = "100">' . $lang['autoupdate_tableau_action'] . '</td>';
            }
            ?>
        </tr>
        <?php
        //
        foreach ($download_mod_list as $downloadmod) {

            $cur_modname = $downloadmod['nom'];
            $cur_description = $downloadmod['description'];

            $install = false;
            for ($j = 0; $j < $a; $j++) {
                if ($installed_mods[$j]['root'] == $cur_modname || $cur_modname == 'ogspy') {
                    $install = true;
                }
            }
            if ($install == false) {
                $link = "<a href=\"?action=autoupdate&sub=mod_upgrade&mod=" . $cur_modname . "&tag=release\">" . $lang['autoupdate_tableau_install'] . "</a>";
                echo "\t<tr>\n";
                echo "\t\t<th>" . $cur_modname . "</th>\n";
                echo "\t\t<th>" . $cur_description . "</th>\n";
                echo "\t\t<th><span style=\"color: lime; \">" . $link . "</span></th>\n";
                echo "\t</tr>\n";
            }
        }
        ?>
        <tr>
            <td class="c" colspan="100"><?php echo $lang['autoupdate_tableau_link']; ?></td>
        </tr>
        <tr>
            <th colspan="100"><a href="index.php?action=administration&subaction=mod"><?php echo $lang['autoupdate_tableau_pageadmin']; ?></a>
            </th>
        </tr>
        <tr>
            <th colspan="100"><a href="https://www.ogsteam.eu">ogsteam.eu</a></th>
        </tr>
    </table><?php
        } else {
            die($lang['autoupdate_MaJ_rights']);
        }

        echo '<br>' . "\n";
        echo 'AutoUpdate ' . $lang['autoupdate_version'] . ' ' . versionmod();
        echo '<br>' . "\n";
        echo $lang['autoupdate_createdby'] . ' Jibus ' . $lang['autoupdate_and'] . ' Bartheleway.</div>';

        require_once("views/page_tail.php");
