<?php
/**
* down.php T�l�charge de nouveaux mods sur le serveur
* @package [MOD] AutoUpdate
* @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
* @version 1.0
* created	: 27/10/2006
* modified	: 18/01/2007
* $Id: down.php 7668 2012-07-15 22:16:03Z darknoon $
*/

if (!defined('IN_SPYOGAME')) die("Hacking attempt");
require_once("views/page_header.php");

if($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) {
	
	// R�cup�rer la liste des modules install�s
	$sql = "SELECT title,root,version from ".TABLE_MOD;
	$res = $db->sql_query($sql,false,true);
	
	$a = 0;
	while (list($modname,$modroot,$modversion) = $db->sql_fetch_row($res)) {
		$installed_mods[$a]['name'] = $modname;	
		$installed_mods[$a]['root'] = $modroot;
		$installed_mods[$a++]['version'] = $modversion;
	}
    //R�cup�rer la liste des mods disponibles sur le d�pot
    $download_mod_list = getRepositorylist();

?>
<table width='600'>
<?php
	if (!is_writable("./mod/")) {
	echo "<tr><td class='c' colspan='100'><font color='red'>Attention le mod autoupdate n'a pas acc�s en �criture au repertoire '<b>mod</b>'.<br /> Les installations de nouveaux modules ne sont pas possible.<br>Donnez les droits 777 au r�pertoire <b>'[OGSPY]/mod'</b></font></td></tr>";
	}

?>
	<tr>
		<td class='c' colspan='4'><?php echo $lang['autoupdate_tableau_modnoinstall']; ?></td>
	</tr>
	<tr>
		<td class='c'><?php echo $lang['autoupdate_tableau_namemod']; ?></td>
		<?php if($user_data['user_admin'] == 1 || $user_data['user_coadmin'] == 1) echo '<td class=\'c\' width = "100">'.$lang['autoupdate_tableau_action'].'</td>'; ?>
	</tr>
	<?php	
	//
	foreach($download_mod_list as $downloadmod) {

		$cur_modname = $downloadmod['nom'];
		$cur_description = $downloadmod['description'];
		
                
		$install = false;
		for ($j = 0 ; $j < $a ; $j++) {
			if ($installed_mods[$j]['root'] == $cur_modname || $cur_modname == 'ogspy') {
				$install = true;
			}
		}
		if ($install == false) {
			$link = "<a href=\"?action=autoupdate&sub=mod_upgrade&type=down&mod=".$cur_modname."\">T�l�charger</a>";
			echo "\t<tr>\n";
			echo "\t\t<th>".$cur_modname."</th>\n";
			echo "\t\t<th><font color='lime'>".$link."</font></th>\n";
			echo "\t</tr>\n";
		}
	}
	?>
	<tr>
		<td class="c" colspan="100"><?php echo $lang['autoupdate_tableau_link']; ?></td>
	</tr>
	<tr>
		<th colspan="100"><a href="index.php?action=administration&subaction=mod"><?php echo $lang['autoupdate_tableau_pageadmin']; ?></a></th>
	</tr>
	<tr>
		<th colspan="100"><a href="http://www.ogsteam.fr">OGSteam.fr</a></th>
	</tr>
</table><?php
} else die($lang['autoupdate_MaJ_rights']);

echo '<br />'."\n";
echo 'AutoUpdate '.$lang['autoupdate_version'].' '.versionmod();
echo '<br />'."\n";
echo $lang['autoupdate_createdby'].' Jibus '.$lang['autoupdate_and'].' Bartheleway.</div>';

require_once("views/page_tail.php");
?>
