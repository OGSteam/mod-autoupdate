<?php

/**
 * Autoupdate Admin view File
 * @package [Mod] Autoupdate
 * @subpackage main
 * @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
 * @copyright Copyright &copy; 2016, http://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 2.1.9
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$error = "";
if (isset($pub_valid)) {

    if (empty($pub_cycle)) {
        $pub_cycle = 0;
    }
    mod_set_option("CYCLEMAJ", $pub_cycle);
    mod_set_option("MAJ_BETA", $pub_majbeta);
    mod_set_option("GITHUBTOKEN", $pub_githubtoken);
}

?>
<table>
    <tr>
        <td class="c"><?php echo $lang['autoupdate_admin_option']; ?></td>
        <td class="c" align="center"><?php echo $lang['autoupdate_admin_value']; ?>
            <br /><?php echo $lang['autoupdate_admin_value1']; ?>
        </td>
    </tr>
    <form action="index.php?action=autoupdate&sub=admin" method="post">
        <tr>
            <th><?php echo $lang['autoupdate_admin_beta']; ?><br /><?php echo $lang['autoupdate_admin_beta1']; ?></th>
            <th><input type="radio" name="majbeta" <?php echo (mod_get_option("MAJ_BETA") == 1) ? 'checked' : ''; ?> value="1" /> <span style="font-size: large; ">|</span> <input type="radio" name="majbeta" <?php echo (mod_get_option("MAJ_BETA") == 0) ? 'checked' : ''; ?> value="0" /></th>
        </tr>
        <tr>
            <th><?php echo $lang['autoupdate_admin_frequency']; ?></th>
            <th><input name="cycle" type="text" size="3" maxlength="2" value="<?php echo mod_get_option("CYCLEMAJ"); ?>">
            </th>
        </tr>
        <tr>
            <th><?php echo $lang['autoupdate_admin_githubtoken']; ?></th>
            <th><input name="githubtoken" type="text" size="40" maxlength="40" value="<?php echo mod_get_option("GITHUBTOKEN"); ?>">
            </th>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" name="valid" value="<?php echo $lang['autoupdate_admin_valid']; ?>" /></td>
        </tr>
    </form>
</table>
<?php

echo "<br>\n";
echo 'AutoUpdate ' . $lang['autoupdate_version'] . ' ' . versionmod();
echo '<br>' . "\n";
echo $lang['autoupdate_createdby'] . ' Jibus ' . $lang['autoupdate_and'] . ' Bartheleway.</div><br>';
?>
<a href="https://github.com/ogsteam/mod-autoupdate" target="_blank"><img src="./mod/autoupdate/img/GitHub-Mark-Light-32px.png" /></a>


<?php
require_once("views/page_tail.php");
