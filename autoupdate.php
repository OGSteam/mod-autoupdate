<?php
/**
* autoupdate.php Page maitresse du mod (fait les mises à jours des mods et affiche les pages demandées)
* @package [MOD] AutoUpdate
* @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
* @version 1.0
* created	: 27/10/2006
* modified	: 18/01/2007
* $Id: autoupdate.php 7668 2012-07-15 22:16:03Z darknoon $
*/

if (!defined('IN_SPYOGAME')) die("Hacking attempt");

require_once("views/page_header.php");
if ( !function_exists('json_decode')) die("Autoupdate ne peut fonctionner correctement sans la librairie JSON, Merci de mettre à jour PHP(>= 5.2)");
require_once("mod/autoupdate/functions.php");
require_once("mod/autoupdate/lang_main.php");

/**
* Défini où se trouve le fichier qui contient les dernières versions des mods.
* Différent suivant si allow_url_fopen est activé ou non. S'il n'est pas activé, on va chercher le fichier en local après téléchargement.
*/
if(mod_get_option("DOWNJSON")) {
	DEFINE("JSON_FILE","http://update.ogsteam.fr/update.json");
} else {
	DEFINE("JSON_FILE","parameters/modupdate.json");
}

if (!isset($pub_sub)) {
	$sub = "tableau";
	$pub_sub = "tableau";
} else $sub = $pub_sub;

if ($user_data["user_admin"] == 1 OR $user_data["user_coadmin"] == 1) {
	if ($sub != "tableau") {
		$bouton1 = "\t\t"."<td class='c' align='center' width='150' style='cursor:pointer' onclick=\"window.location = 'index.php?action=autoupdate&sub=tableau';\">";

		$bouton1 .= "<font color='lime'>".$lang['autoupdate_autoupdate_table']."</font>";
		$bouton1 .= "</td>\n";
	} else {
		$bouton1 = "\t\t"."<th width='150'>";
		$bouton1 .= "<font color=\"#5CCCE8\">".$lang['autoupdate_autoupdate_table']."</font>";
		$bouton1 .= "</th>\n";
	}
	if ($sub != "down") {
		$bouton2 = "\t\t"."<td class='c' align='center' width='150' style='cursor:pointer' onclick=\"window.location = 'index.php?action=autoupdate&sub=down';\">";
		$bouton2 .= "<font color='lime'>".$lang['autoupdate_autoupdate_down']."</font>";
		$bouton2 .= "</td>\n";
	} else {
		$bouton2 = "\t\t"."<th width='150'>";
		$bouton2 .= "<font color=\"#5CCCE8\">".$lang['autoupdate_autoupdate_down']."</font>";
		$bouton2 .= "</th>\n";
	}
} else {
	$bouton1 = "";
	$bouton2 = "";
}
if ($user_data["user_admin"] == 1) {
	if ($sub != "admin") {
		$bouton3 = "\t\t"."<td class='c' align='center' width='150' style='cursor:pointer' onclick=\"window.location = 'index.php?action=autoupdate&sub=admin';\">";
		$bouton3 .= "<font color='lime'>".$lang['autoupdate_autoupdate_admin']."</font>";
		$bouton3 .= "</td>\n";
	} else {
		$bouton3 = "\t\t"."<th width='150'>";
		$bouton3 .= "<font color=\"#5CCCE8\">".$lang['autoupdate_autoupdate_admin']."</font>";
		$bouton3 .= "</th>\n";
	}
} else {
	$bouton3 = "";
}

/**
*Si le chargement de la page contient la variable GET['maj'] == yes on fait une MaJ du mod et on envoie le résultat
*/
if(!empty($pub_maj) AND $pub_maj == 'yes') {
	$request1 = "select id, title, root, link, version, active from ".TABLE_MOD." WHERE root='".$pub_modroot."' order by position, title";
	$result1 = $db->sql_query($request1);
 	list($pub_mod_id, $title, $root, $link, $version, $active) = $db->sql_fetch_row($result1);
	if (file_exists("mod/".$pub_modroot."/update.php")) {
		require_once("mod/".$pub_modroot."/update.php");
		
		$request = "SELECT title FROM ".TABLE_MOD." WHERE root = '".$pub_modroot."' LIMIT 1";
		$result = $db->sql_query($request);
		list($title) = $db->sql_fetch_row($result);
		log_("mod_update", $title);
		$maj = $lang['autoupdate_tableau_uptodateok']."<br />\n<br />\n";
	} else {
		$maj = $lang['autoupdate_tableau_uptodateoff']."<br />\n<br />\n";
	}
} else $maj = "";

echo $maj;
echo "\n<table>\n";
echo "\t<tr>\n";
	echo $bouton1.$bouton2.$bouton3;
echo "\t</tr><br />\n";
echo "</table>\n<br />\n";

if (!isset($pub_sub)) $sub = 'tableau'; else $sub = htmlentities($pub_sub);
 switch($sub)
{
case 'tableau': include ('tableau.php');break;
case 'admin': include ('admin.php');break;
case 'maj': include ('MaJ.php');break;
case 'down': include ('down.php');break;
}
?>
