<?php
/**
* MaJ.php Met à jour les mods depuis le serveur
* @package [MOD] AutoUpdate
* @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
* @version 1.0a
* created	: 27/10/2006
* modified	: 18/01/2007
* $Id: MaJ.php 7668 2012-07-15 22:16:03Z darknoon $
*/

if (!defined('IN_SPYOGAME')) die("Hacking attempt");
/**
*Récupère les fonctions zip
*/
$zip = new ZipArchive;
/**
*
*/
require_once("views/page_header.php");

if($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) {
	
	if (empty($pub_type) || $pub_type == "down") {
		$modroot = mysql_real_escape_string($pub_mod);
		$version = mysql_real_escape_string($pub_tag);
		$modzip = "http://update.ogsteam.fr/mods/download.php?download=".$modroot."-".$version;
		
		if (!is__writable("./mod/autoupdate/tmp/")) {
			die("Erreur: Le repertoire /mod/autoupdate/tmp/ doit etre accessible en écriture (777) ".__FILE__. "(Ligne: ".__LINE__.")");
		}
		if(@copy($modzip , './mod/autoupdate/tmp/'.$modroot.'.zip')) {
			
			if ($zip->open('./mod/autoupdate/tmp/'.$modroot.'.zip') === TRUE) {
				$zip->extractTo("./mod/".$modroot."/");
                $zip->close();
                //$tab = $zip->Extract("./mod/autoupdate/tmp/".$modroot.".zip", "./mod/".$modroot."/")
				echo '<table align="center" style="width : 400px">'."\n";
				echo "\t".'<tr>'."\n";
				echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_file'].'</td>'."\n";
				echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_condition'].'</td>'."\n";
				echo "\t".'</tr>'."\n";
				
				//tableau($tab);
				
				echo '</table>'."\n";
				echo '<br />'."\n";
				unlink("./mod/autoupdate/tmp/".$modroot.".zip");
			}
		}
            echo '<table>'."\n";
            echo "\t".'<tr>'."\n";
            echo "\t\t".'<td colspan="2" align="center" class="c">'.$lang['autoupdate_MaJ_statistic'].'</td>'."\n";
            echo "\t".'</tr>'."\n";
            echo "\t".'<tr>'."\n";
            echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_number'].'</td>'."\n";
            echo "\t".'</tr>'."\n";
            if ( isset ( $pub_type ) && $pub_type == "down") {
                echo "\t".'<tr>'."\n";
                echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_wantinstall'].'</td>'."\n";
                echo "\t\t".'<th><a href="index.php?action=administration&subaction=mod">'.$lang['autoupdate_MaJ_linkupdate'].'</th>'."\n";
                echo "\t".'</tr>'."\n";
            } else {
                echo "\t".'<tr>'."\n";
                echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_wantupdate'].'</td>'."\n";
                echo "\t\t".'<th><a href="index.php?action=autoupdate&maj=yes&modroot='.$modroot.'">'.$lang['autoupdate_MaJ_linkupdate'].'</a></th>'."\n";
                echo "\t".'</tr>'."\n";
            }
            echo '</table>'."\n";
	}

} else {
	echo $lang['autoupdate_MaJ_rights'];
}
echo '<br />'."\n";
echo 'AutoUpdate '.$lang['autoupdate_version'].' '.versionmod();
echo '<br />'."\n";
echo $lang['autoupdate_createdby'].' Jibus '.$lang['autoupdate_and'].' Bartheleway.</div>';
require_once("views/page_tail.php");
?>
