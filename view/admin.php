<?php
global $lang, $pub_cycle, $pub_majbeta, $pub_githubtoken;
/**
 * Autoupdate Admin view File
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
<form action="index.php?action=autoupdate&sub=admin" method="post">

<table class='og-table og-full-table'>
    <thead>
    <tr>
        <th><?php echo $lang['autoupdate_admin_option']; ?></th>
        <th><?php echo $lang['autoupdate_admin_value']; ?>
            <br><?php echo $lang['autoupdate_admin_value1']; ?>
        </th>
    </tr>
    </thead>
    <tbody>

        <tr>
            <td><?php echo $lang['autoupdate_admin_beta']; ?><br /><?php echo $lang['autoupdate_admin_beta1']; ?></td>
            <td>
                <label>
                    <input type="radio" name="majbeta" <?php echo (mod_get_option("MAJ_BETA") == 1) ? 'checked' : ''; ?> value="1" />
                </label>
                <span>|</span>
                <label>
                    <input type="radio" name="majbeta" <?php echo (mod_get_option("MAJ_BETA") == 0) ? 'checked' : ''; ?> value="0" />
                </label>
            </td>
        </tr>
        <tr>
            <td><?php echo $lang['autoupdate_admin_frequency']; ?></td>
            <td>
                <label>
                    <input name="cycle" type="text" size="3" maxlength="2" value="<?php echo mod_get_option("CYCLEMAJ"); ?>">
                </label>
            </td>
        </tr>
        <tr>
            <td><?php echo $lang['autoupdate_admin_githubtoken']; ?></td>
            <td>
                <label>
                    <input name="githubtoken" type="text" size="40" maxlength="40" value="<?php echo mod_get_option("GITHUBTOKEN"); ?>">
                </label>
            </td>
        </tr>
        <tr>
            <td colspan="2"><input class="og-button og-button-little" type="submit" name="valid" value="<?php echo $lang['autoupdate_admin_valid']; ?>" /></td>
        </tr>

    </tbody>
</table>
</form>

    <div style="text-align: center">
        AutoUpdate <?= $lang['autoupdate_version'] . ' ' . versionmod(); ?><br>
        <?= $lang['autoupdate_createdby'] . ' Jibus ' . $lang['autoupdate_and'] . ' Bartheleway' ?><br>
        <a href="https://github.com/ogsteam/mod-autoupdate" target="_blank" rel="noopener"><img src="./mod/autoupdate/img/GitHub-Mark-Light-32px.png"  alt="Github"/></a>
    </div>


<?php
require_once("views/page_tail.php");
