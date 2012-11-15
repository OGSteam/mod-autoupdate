<?php
/**
* admin.php Partie admin du mod
* @package [MOD] AutoUpdate
* @author Bartheleway <contactbarthe@g.q-le-site.webou.net>
* @version 1.0
* created	: 13/11/2006
* modified	: 18/01/2007
* $Id: admin.php 7668 2012-07-15 22:16:03Z darknoon $
*/
if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

$error = "";
if (isset($pub_valid)) {
	
    if(empty($pub_cycle)) $pub_cycle = 0;

     mod_set_option ( "CYCLEMAJ", $pub_cycle);
     mod_set_option ( "MAJ_TRUNK", $pub_majtrunk);
  
}

?>
<table>
	<tr>
		<td class="c"><?php echo $lang['autoupdate_admin_option']; ?></td>
		<td class="c" align="center"><?php echo $lang['autoupdate_admin_value']; ?><br /><?php echo $lang['autoupdate_admin_value1']; ?></td>
	</tr>
	<form action="index.php?action=autoupdate&sub=admin" method="post">
	<tr>
		<th><?php echo $lang['autoupdate_admin_trunk']; ?><br /><?php echo $lang['autoupdate_admin_trunk1']; ?></th>
		<th><input type="radio" name="majtrunk" <?php echo (mod_get_option("MAJ_TRUNK") == 1) ? 'checked' : ''; ?> value="1"/> <font size="5">|</font> <input type="radio" name="majtrunk" <?php echo (mod_get_option("MAJ_TRUNK") == 0) ? 'checked' : ''; ?> value="0"/></th>
	</tr>
    <tr>
		<th><?php echo $lang['autoupdate_admin_frequency']; ?></th>
		<th><input name="cycle" type="text" size="3" maxlength="2" value="<?php echo mod_get_option("CYCLEMAJ");?>">
		</th>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" name="valid" value="<?php echo $lang['autoupdate_admin_valid']; ?>"/></td>
	</tr>
	</form>
</table>
<?php

echo "<br />\n";
echo 'AutoUpdate '.$lang['autoupdate_version'].' '.versionmod();
echo '<br />'."\n";
echo $lang['autoupdate_createdby'].' Jibus '.$lang['autoupdate_and'].' Bartheleway.</div>';
require_once("views/page_tail.php");
?>
