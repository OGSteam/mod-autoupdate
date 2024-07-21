<?php
global $db, $lang, $user_data, $server_config, $installed_mods, $download_mod_list;

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
    <table class='og-table og-full-table'>
    <thead>
        <?php
        if (!is_writeable("./mod/")) {
            echo "<tr><td  class='og-error' colspan='100'><span style=\"color: red; \">" . $lang['autoupdate_tableau_error3'] . "</span></td></tr>";
        }

        ?>
        <tr>
            <th colspan='100'><?php echo $lang['autoupdate_tableau_modnoinstall']; ?></th>
        </tr>

        <tr>
            <th><?php echo $lang['autoupdate_tableau_namemod']; ?></th>
            <th><?php echo $lang['autoupdate_tableau_descmod']; ?></th>
            <?php if ($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) {
                echo '<th>' . $lang['autoupdate_tableau_action'] . '</th>';
            }
            ?>
        </tr>
        </thead>
        <tbody>
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
            if (!$install) {
                $link = "<a href=\"?action=autoupdate&sub=mod_upgrade&mod=" . $cur_modname . "&tag=release\">" . $lang['autoupdate_tableau_install'] . "</a>";
                echo "\t<tr>\n";
                echo "\t\t<td>" . $cur_modname . "</td>\n";
                echo "\t\t<td>" . $cur_description . "</td>\n";
                echo "\t\t<td><span style=\"color: lime; \">" . $link . "</span></td>\n";
                echo "\t</tr>\n";
            }
        }
        ?>
        </tbody>
    </table>
    <table class='og-table og-full-table'>
    <thead>
        <tr>
            <th colspan='100'><?= $lang['autoupdate_tableau_link']; ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><a href="index.php?action=administration&subaction=mod"><?= $lang['autoupdate_tableau_pageadmin']; ?></a>
            </td>
        </tr>
        <tr>
            <td><a href="https://www.ogsteam.eu">ogsteam.eu</a></td>
        </tr>
     </tbody>
    </table><?php
        } else {
            die($lang['autoupdate_MaJ_rights']);
        }
?>
<br>
<div style="text-align: center">
    AutoUpdate <?= $lang['autoupdate_version'] . ' ' . versionmod(); ?><br>
    <?= $lang['autoupdate_createdby'] . ' Jibus ' . $lang['autoupdate_and'] . ' Bartheleway' ?>
</div>
<?php
require_once("views/page_tail.php");
?>
