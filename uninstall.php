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

namespace Ogsteam\Ogspy;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

mod_del_all_option();
