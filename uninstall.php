<?php
/**
 * Autoupdate uninstall script
 * @package [Mod] Autoupdate
 * @subpackage main
 * @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 2.1.9
 */

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$mod_uninstall_name = "autoupdate";
uninstall_mod($mod_unistall_name, $mod_uninstall_table);

if (file_exists("mod/autoupdate/modupdate.json")) {
    unlink("mod/autoupdate/modupdate.json");
}
if (file_exists("parameters/modupdate.json")) {
    unlink("parameters/modupdate.json");
}

mod_del_all_option();

