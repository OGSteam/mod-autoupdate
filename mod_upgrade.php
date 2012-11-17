<?php
/**
* mod_upgrade.php Met � jour les mods depuis le serveur
* @package [MOD] AutoUpdate
* @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
* @version 1.0a
* created	: 27/10/2006
* modified	: 18/01/2007
* $Id: MaJ.php 7668 2012-07-15 22:16:03Z darknoon $
*/

if (!defined('IN_SPYOGAME')) die("Hacking attempt");
/**
*R�cup�re les fonctions zip
*/
$zip = new ZipArchive;

require_once("views/page_header.php");

if(!isset($pub_confirmed)) $pub_confirmed = "no";

if($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) {
    
    $modroot = mysql_real_escape_string($pub_mod);
    
    if(isset($pub_tag))
    {
        //Si une version est sp�cifi�e...
        $version = mysql_real_escape_string($pub_tag);
    }else{
        //Sinon on prends la derni�re
        $version= getRepositoryVersion($modroot);
        if($version == '-1') die("Pas de version officielle disponible, Merci de signaler le probl�me � l'OGSteam");
    }
    
	if ($pub_sub == "mod_upgrade" && $pub_confirmed == "yes") {
        
        //R�cup�ration des infos du mod :
        $repoDetails = getRepositoryDetails($modroot);

		if( $version == 'trunk'){
            $modzip = "https://bitbucket.org/".$repoDetails['owner']."/mod-".$modroot."/get/tip.zip";
        }else{
            $modzip = "https://bitbucket.org/".$repoDetails['owner']."/mod-".$modroot."/get/".$version.".zip";
        }
        
		if (!is_writable("./mod/autoupdate/tmp/")) {
			die("Erreur: Le repertoire /mod/autoupdate/tmp/ doit etre accessible en �criture (777) ".__FILE__. "(Ligne: ".__LINE__.")");
		}
        
		if(copy($modzip , './mod/autoupdate/tmp/'.$modroot.'.zip')) {
                echo '<table align="center" style="width : 400px">'."\n";
			if ($zip->open('./mod/autoupdate/tmp/'.$modroot.'.zip') === TRUE) {
				echo "\t".'<tr>'."\n";
                echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_downok'].'</td>'."\n";
                echo "\t".'</tr>'."\n";
                
                $zip->extractTo("./mod/autoupdate/tmp/".$modroot."/"); //On extrait le mod dans le r�pertoire temporaire d'autoupdate
                $zip->close();
                unlink("./mod/autoupdate/tmp/".$modroot.".zip");
                $nom_r�pertoire = glob("./mod/autoupdate/tmp/".$modroot."/*-".$modroot."*",GLOB_ONLYDIR);//On r�cup�re le nom du r�pertoire
                $folder = explode('/', $nom_r�pertoire[0]);
                rcopy("./mod/autoupdate/tmp/".$modroot."/".$folder[5],"./mod/".$modroot); //Copie du r�pertoire dans le dossier des mods
                rrmdir("./mod/autoupdate/tmp/".$modroot);
                echo "\t".'<tr>'."\n";
                echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_unzipok'].'</td>'."\n";
                echo "\t".'</tr>'."\n";
                echo "\t".'<tr>'."\n";
                echo "\t\t".'<td class="c">'.upgrade_ogspy_mod($modroot).'</td>'."\n";
                echo "\t".'</tr>'."\n";
                echo "\t".'<tr>'."\n";
                echo "\t\t".'<td class="c">'.$lang['autoupdate_tableau_back'].'</td>'."\n";
                echo "\t".'</tr>'."\n";
				echo '</table>'."\n";
				echo '<br />'."\n";
			}
		}
    }else{
            echo '<table>'."\n";
            echo "\t".'<tr>'."\n";
            echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_wantupdate'].$modroot.' '.$version.' ?</td>'."\n";
            echo "\t".'</tr>'."\n";
            echo "\t".'<tr>'."\n";
            echo "\t\t".'<th><a href="index.php?action=autoupdate&sub=mod_upgrade&confirmed=yes&mod='.$modroot.'&tag='.$version.'">'.$lang['autoupdate_MaJ_linkupdate'].'</a></th>'."\n";
            echo "\t".'</tr>'."\n";
            echo "\t".'<tr>'."\n";
            echo "\t".'</tr>'."\n";
            echo "\t".'<tr>'."\n";            
            echo "\t\t".'<td class="c">'.$lang['autoupdate_tableau_back'].'</td>'."\n";
            echo "\t".'</tr>'."\n";
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
