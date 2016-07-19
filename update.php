<?php
/**
 * Autoupdate update script
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

$mod_folder = "autoupdate";
$mod_name = "autoupdate";

update_mod($mod_folder, $mod_name);
mod_set_option("CYCLEMAJ", "24");
mod_set_option("MAJ_TRUNK", "0");

