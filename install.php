<?php
/**
 * Autoupdate install script
 * @package [Mod] Autoupdate
 * @subpackage main
 * @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 2.1.9
 */

namespace Ogsteam\Ogspy;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

// Ajout du module dans la table des mod de OGSpy
$is_ok = false;
$mod_folder = "autoupdate";

mod_set_option("CYCLEMAJ", "24");
mod_set_option("MAJ_TRUNK", "0");
mod_set_option("LAST_REPO_LIST", "0");

