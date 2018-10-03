<?php
/**
 * Autoupdate Mod list
 * @package [Mod] Autoupdate
 * @subpackage main
 * @author DarkNoon <darknoon@darkcity.fr>
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 2.1.9
 */

function getRepositorylist()
{

    $mod_list = array();


    if (time() > (mod_get_option('LAST_REPO_LIST') + mod_get_option('CYCLEMAJ') * 3600)) {
        $mod_data = github_Request("https://api.github.com/orgs/ogsteam/repos?per_page=100");
        file_put_contents('./mod/autoupdate/tmp/repo_list.json', $mod_data);
        mod_set_option('LAST_REPO_LIST', time());
    }
    $mod_file = file_get_contents('./mod/autoupdate/tmp/repo_list.json');

    $data = json_decode($mod_file, true);
    //print_r($data);
    //On récupère ce que l'on a besoin dans la structure JSON
    foreach ($data as $id) {
        $mods_tmp[] = array(
            'nom' => $id["name"],
            'description' => $id["description"],
            'resource_uri' => $id["url"],
            'owner' => "OGSteam",
            'is_fork' => $id["fork"]);
    }
    //Mise en Forme pour le mod (Fait en 2 partie pour plus de lisibilité)
    foreach ($mods_tmp as $mod) {
        if (preg_match("/mod-/", $mod["nom"])) {
            $mod_name = explode('-', $mod["nom"]);
            $mod["nom"] = $mod_name[1];
            $mod_list[] = array(
                'nom' => $mod["nom"],
                'description' => $mod["description"],
                'resource_uri' => $mod["resource_uri"],
                'owner' => $mod["owner"]);

        } else if (preg_match("/^ogspy$/", $mod["nom"])) {
            $mod_list[] = array('nom' => $mod["nom"],
                'description' => $mod["description"],
                'resource_uri' => $mod["resource_uri"],
                'owner' => $mod["owner"]);
        }
    }

    return ($mod_list);
}

/**
 * @param $repoName
 * @return array|bool
 */
function getRepositoryDetails($repoName)
{

    $liste_mods = getRepositorylist();
    //print_r($liste_mods);
    foreach ($liste_mods as $mod) {

        if ($mod['nom'] == $repoName) {

            return array('nom' => $mod["nom"],
                'description' => $mod["description"],
                'resource_uri' => $mod["resource_uri"],
                'owner' => $mod["owner"]);
        }
    }
    return false;
}


/**
 * @param      $Reponame
 * @return array|string
 */
function getRepositoryVersion($Reponame)
{
    global $lang;

    $version = array('release' => null , 'beta' => null , 'alpha' => null );

    $repo_details = getRepositoryDetails($Reponame);
    if ($repo_details == false) {
        return "-1";
    }

    $repo_link = $repo_details['resource_uri'] . '/tags';

    if (time() > (intval(mod_get_option('LAST_MOD_UPDATE-' . $Reponame)) + intval(mod_get_option('CYCLEMAJ')) * 3600)) {
        $mod_data = github_Request($repo_link);
        file_put_contents('./mod/autoupdate/tmp/' . $Reponame . '.json', $mod_data);
        mod_set_option('LAST_MOD_UPDATE-' . $Reponame, time());
    }
    if (file_exists('./mod/autoupdate/tmp/' . $Reponame . '.json')) {
        $api_list = file_get_contents('./mod/autoupdate/tmp/' . $Reponame . '.json');

        $data = json_decode($api_list, true);
        arsort($data);

        if (count($data) > 0) {
            foreach ($data as $tagged_release)
            {
                $tagged_release = $tagged_release['name'];

                if( preg_match("/alpha/i",$tagged_release) ){
                   if(!isset($version['alpha']))  $version['alpha'] = $tagged_release;
                } elseif (preg_match("/beta/i",$tagged_release) ) {
                    if (!isset($version['beta'])) $version['beta'] = $tagged_release;
                } else{
                    if (!isset($version['release'])) $version['release'] = $tagged_release;
                }
            }

            if(version_compare($version['release'], $version['alpha'], ">")){
                $version['alpha'] = null;
            }
            if (version_compare($version['release'], $version['beta'], ">")) {
                $version['beta'] = null;
            }
            return $version; // Récupération du Tag de version
        } else {
            log_('mod', $lang['autoupdate_tableau_error4'] . ' ' . $Reponame);
            return "-1";
        }
    } else {
        log_('mod', $lang['autoupdate_tableau_error1'] . $Reponame);
        mod_del_option('LAST_MOD_UPDATE-' . $Reponame);
        return "-1";

    }
}


/**
 * @param string $request
 * @return string
 */
function github_Request($request) {

    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: OGSpy',
                'Authorization: token d08499607a0f2469405465cf29e3aeb9d4b1265f'
            ]
        ]
    ];

    $context = stream_context_create($opts);

    try {
        $data = file_get_contents($request, false, $context);

        if ($data === false) {
            log_('mod', "[ERROR_github_Request] Unable to get: " . $request);
        }
    } catch (Exception $e) {
        log_('mod', "[ERROR_github_Request] Exception: " . $e->getMessage());
    }

    return $data;
}

