<?php

/**
 * Autoupdate install script
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
// Ajout du module dans la table des mod de OGSpy
$is_ok = false;
$mod_folder = "autoupdate";
$is_ok = install_mod($mod_folder);
if ($is_ok) {
    //si besoin de créer des tables, a faire ici
    //Options par défaut.
    mod_set_option("CYCLEMAJ", "24");
    mod_set_option("MAJ_ALPHA", "0");
    mod_set_option("MAJ_BETA", "0");
    mod_set_option("LAST_REPO_LIST", "0");
    mod_set_option("GITHUBTOKEN", "");
} else {
    echo "<script>alert(\"Désolé, un problème a eu lieu pendant l'installation: corrigez les problèmes survenus et réessayez.\");</script>";
}
