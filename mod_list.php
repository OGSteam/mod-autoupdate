<?php
/**
* mod_list.php List des Fichiers à ne pas télécharger
* @package [MOD] AutoUpdate
* @author DarkNoon <darknoon@darkcity.fr>
* @version 1.0
* created	: 16/01/2012
* modified	: 16/01/2012
*/

function getRepositorylist(){
    
    $mod_list = array();
    
    $repo_link ='https://api.bitbucket.org/1.0/users/ogsteam';
    if(time() > (mod_get_option('LAST_REPO_LIST') + mod_get_option('CYCLEMAJ') * 3600)){
        @copy($repo_link, './mod/autoupdate/tmp/repo_list.json');
        mod_set_option('LAST_REPO_LIST', time());
    }
    $mod_file = file_get_contents('./mod/autoupdate/tmp/repo_list.json');	

    //$result = utf8_encode($mod_file);
    $data = json_decode($mod_file, true);
	//print_r($data);
    //On récupère ce que l'on a besoin dans la structure JSON
    foreach($data['repositories'] as $id)
    {
        $mods_tmp[] = array('nom' => $id["slug"],
        'description' => $id["description"],
        'resource_uri' => $id["resource_uri"],
        'owner' =>$id["owner"],
        'is_fork' =>$id["is_fork"],
        'fork_resource_uri' => $id["fork_of"]["resource_uri"],
        'fork_owner' =>$id["fork_of"]["owner"]);
    }
    //Mise en Forme pour le mod (Fait en 2 partie pour plus de lisibilité)
    foreach($mods_tmp as $mod)
    {
        if(preg_match("/mod-/",$mod["nom"])){
            $mod_name = explode('-',$mod["nom"]);
            $mod["nom"] = $mod_name[1];
			if($mod["is_fork"] == false){            
				$mod_list[] = array('nom' => $mod["nom"],
				'description' => utf8_decode($mod["description"]),
				'resource_uri' => $mod["resource_uri"],
				'owner' => $mod["owner"]);
			}else{
				$mod_list[] = array('nom' =>$mod["nom"],
				'description' => utf8_decode($mod["description"]),
				'resource_uri' => $mod["fork_resource_uri"],
				'owner' => $mod["fork_owner"]);            
			}
		}else if(preg_match("/ogspy/",$mod["nom"])){
        		$mod_list[] = array('nom' => $mod["nom"],
				'description' => utf8_decode($mod["description"]),
				'resource_uri' => $mod["resource_uri"],
				'owner' => $mod["owner"]);
			}
     }
    return ($mod_list);
}

function getRepositoryDetails($repoName){
    
    $liste_mods = getRepositorylist();
    //print_r($liste_mods);
    foreach($liste_mods as $mod){

        if($mod['nom'] == $repoName){
            
            return array('nom' =>$mod["nom"],
                'description' => $mod["description"],
                'resource_uri' => $mod["resource_uri"],
                'owner' => $mod["owner"]);  
        }
    }
return false;
}


function getRepositoryVersion($Reponame, $isMod = true ){

    $repo_details = getRepositoryDetails($Reponame);
    if($repo_details == false) return "-1";
    
    $repo_link = 'https://api.bitbucket.org'.$repo_details['resource_uri'].'/tags';
   
    if( !ini_get('safe_mode') ) @set_time_limit(30);
    
    if(time() > (mod_get_option('LAST_MOD_UPDATE-'.$Reponame) + mod_get_option('CYCLEMAJ') * 3600)){
        @copy($repo_link, './mod/autoupdate/tmp/'.$Reponame.'.json');
        mod_set_option('LAST_MOD_UPDATE-'.$Reponame, time());
    }
     if(file_exists('./mod/autoupdate/tmp/'.$Reponame.'.json')){
        $api_list = file_get_contents('./mod/autoupdate/tmp/'.$Reponame.'.json');	

        $result = utf8_encode($api_list);
        $data = json_decode($result, true);
        $version_list = array_keys($data);
        // Supression de l'étiquette tip
        $tip_id = array_search('tip', $version_list);
        unset($version_list[$tip_id]);

        //tri de la liste de versions pour obtenir la dernière :
        rsort($version_list);

        if(count($version_list) > 0)
        {
            return $version_list[0];
        }else
        {
            log_('mod', 'Pas de version de Production pour le mod '.$Reponame);
            return "-1";
        }
     }else{
         log_('mod', 'Impossible de récupérer le fichier de version du mod '.$Reponame);
         mod_del_option('LAST_MOD_UPDATE-'.$Reponame);
         return "-1";
         
     }
}

?>