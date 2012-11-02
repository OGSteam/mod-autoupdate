<?php
/** $Id: functions.php 7668 2012-07-15 22:16:03Z darknoon $ **/
/**
* functions.php Défini les fonctions du mod
* @package [MOD] AutoUpdate
* @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
* @version 1.0
*	created		: 20/11/2006
*	modified	: 17/01/2007
*/
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

require_once("mod/autoupdate/ban_list.php");

/**
*Récupère la version du mod
*/
function versionmod() {
	global $db, $pub_action;
	$sql = "SELECT version FROM ".TABLE_MOD." WHERE action = '".$pub_action."' LIMIT 1";
	$query = $db->sql_query($sql);
	$fetch = $db->sql_fetch_assoc($query);
	return $fetch['version'];
}

/**
*Copie le fichier modupdate.json dans mod/modupdate.json
*/
function copymodupdate() {
global $lang;

    if(time() > (mod_get_option('LAST_MODLIST_UPDATE') + mod_get_option('CYCLEMAJ') * 3600)){
        if (!copy("http://update.ogsteam.fr/mods/latest.php", "parameters/modupdate.json")) {
            $affiche = "<br />\n".$lang['autoupdate_tableau_error2'];
        } else {
            $affiche = "<br />\n".$lang['autoupdate_tableau_ok'];
            mod_set_option('LAST_MODLIST_UPDATE', time());
        }

        return $affiche;
    }   
}
function io_mkdir_p($target) {
	if (@is_dir($target)||empty($target)) return 1;
	if (@file_exists($target) && !is_dir($target)) return 0;
	if (io_mkdir_p(substr($target,0,strrpos($target,'/')))) {
		$ret=false;
		if (! file_exists($target)) $ret = @mkdir($target,0777);
		if (is_dir($target)) chmod($target, 0777);
		return $ret;
	}
	return 0;
}	

function ap_mkdir($d) {
	$ok = io_mkdir_p($d);
	return $ok;
}

/**
* Affiche sous forme de tableau table à 2 colonne les fichiers du zip et son état.
*/
function tableau($tableau, $type = "maj") {
	global $lang;
	while(list($key,$valeur) = each($tableau)) {
		$fichier = explode("/", $key);
		$nom = "";
		for($i = 1; $i < count($fichier); $i++) {
			if (count($fichier) >= 3 AND count($fichier) != $i AND $i > 1) {
				$slash = "/";
			} else {
				$slash = "";
			}
			$nom .= $slash.$fichier[$i];
		}
		$explode = explode(".", $key);
		if ($nom != "" AND $explode[0] != $key) {
			if ($type == "maj") {
				$etat = $lang['autoupdate_MaJ_uptodateok'];
			} else if ($type == "down") {
				$etat = $lang['autoupdate_MaJ_downok'];
			}
			echo "\t".'<tr>'."\n";
			echo "\t\t".'<td class="a">'.$nom.'</td>'."\n";
			echo "\t\t".'<td class="a">'.$etat.'</td>'."\n";
			echo "\t".'</tr>'."\n";
		}
	}
}

if (! function_exists("is__writable") ) {
/**
* Verifie les droits en écriture d'ogspy sur un fichier ou repertoire 
* @param string $path le fichier ou repertoire à tester
* @return boolean True si accés en écriture
* @comment http://fr.php.net/manual/fr/function.is-writable.php#68598
*/
	function is__writable($path)
	{
	
	    if ($path{strlen($path)-1}=='/')
	       
	        return is__writable($path.uniqid(mt_rand()).'.tmp');
	   
	    elseif (ereg('.tmp', $path))
	    {
	       
	        if (!($f = @fopen($path, 'w+')))
	            return false;
	        fclose($f);
	        unlink($path);
	        return true;
	
	    }
	    else
	       
	        return 0; // Or return error - invalid path...
	
	}
}
function getmodlist(){
	global $ban_mod;
	// Récupérer la liste des dernières versions dans le fichier JSON
    if(!file_exists("parameters/modupdate.json")) {
	//Retry once to not overload the server.
		if (!copy("http://update.ogsteam.fr/mods/latest.php", "parameters/modupdate.json")){
			die ("Fichier JSON Introuvable !");
		}
	}
	$contents = file_get_contents("parameters/modupdate.json");	
	$results = utf8_encode($contents);
	$data = json_decode($results, true);
	//Suppresion des Mods interdits
	if( mod_get_option("BAN_MODS") == 1){
		foreach( $ban_mod as $to_ban)
		{
			unset($data[$to_ban]);
		}
	}
	return $data;	
}	
?>
