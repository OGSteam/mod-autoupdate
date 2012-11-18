<?php
/**
* tool_upgrade.php Met � jour OGSpy depuis le serveur
* @package [MOD] AutoUpdate
* @author DarkNoon
* @version 2.0
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

if($user_data['user_admin'] == 1) {
    
    $toolroot = mysql_real_escape_string($pub_tool);
    $version = mysql_real_escape_string($pub_tag);

	if ($pub_sub == "tool_upgrade" && $pub_confirmed == "yes") {
        
        //echo substr(sprintf('%o', fileperms('./install')), -4);
        
		if (!is_writable(".")) {
			die("Erreur: Le r�pertoire OGSpy doit etre accessible en �criture (755) ".__FILE__. "(Ligne: ".__LINE__.")");
		}
      
        if( $version == 'trunk'){
            $toolzip = "https://bitbucket.org/ogsteam/".$toolroot."/get/tip.zip";
        }else{
            $toolzip = "https://bitbucket.org/ogsteam/".$toolroot."/get/".$version.".zip";
        }
       
		if(copy($toolzip , './mod/autoupdate/tmp/'.$toolroot.'.zip')) {
                echo '<table align="center" style="width : 400px">'."\n";
                
			if ($zip->open('./mod/autoupdate/tmp/'.$toolroot.'.zip') === TRUE) {
				echo "\t".'<tr>'."\n";
                echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_downok'].'</td>'."\n";
                echo "\t".'</tr>'."\n";
                
                $zip->extractTo("./mod/autoupdate/tmp/".$toolroot."/"); //On extrait le mod dans le r�pertoire temporaire d'autoupdate
                $zip->close();
                
                unlink("./mod/autoupdate/tmp/".$toolroot.".zip");

                $nom_r�pertoire = glob("./mod/autoupdate/tmp/".$toolroot."/*-".$toolroot."*",GLOB_ONLYDIR);//On r�cup�re le nom du r�pertoire
                $folder = explode('/', $nom_r�pertoire[0]);
                rcopy("./mod/autoupdate/tmp/".$toolroot."/".$folder[5],".");
                rrmdir("./mod/autoupdate/tmp/".$toolroot);
                
                //On passe au script de mise � jour.
                if (!is_writable("./install")) {
                    die("Erreur: Le r�pertoire install OGSpy doit etre accessible en �criture (755) ".__FILE__. "(Ligne: ".__LINE__.")");
                }
                
                chdir('./install'); //Passage dans le r�pertoire d'installation
                $pub_verbose = false; //Param�trage de la mise � jour
                echo "\t".'<tr>'."\n";
				require_once("upgrade_to_latest.php"); // Mise � jour...
				echo "\t".'</tr>'."\n";
                chdir('..');// Retour au r�pertoire par d�faut.
				
				if(!rrmdir("./install")){
					die("Impossible de supprimer le r�pertoire d'installation");
				}

               
                echo "\t".'<tr>'."\n";
                echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJ_unzipok'].'</td>'."\n";
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
            echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJtool_wantbackup'].'</td>'."\n";
            echo "\t\t".'<td class="c">'.$lang['autoupdate_MaJtool_wantupdate'].$toolroot.' '.$version.' ?</td>'."\n";
            echo "\t\t".'<th><a href="index.php?action=autoupdate&sub=tool_upgrade&confirmed=yes&tool='.$toolroot.'&tag='.$version.'">'.$lang['autoupdate_MaJ_linkupdate'].'</a></th>'."\n";
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
