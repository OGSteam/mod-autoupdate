<?php

/**
 * Autoupdate Mod list
 * @package [Mod] Autoupdate
 * @subpackage main
 * @author DarkNoon <darknoon@darkcity.fr>
 * @copyright Copyright &copy; 2016, http://ogsteam.eu/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 2.1.9
 */

function getRepositorylist()
{

    $mod_list = array();
    $repoListFilename = './mod/autoupdate/tmp/repo_list.json';


    if (time() > (mod_get_option('LAST_REPO_LIST') + mod_get_option('CYCLEMAJ') * 3600)) {
        $mod_data = github_Request("https://api.github.com/orgs/ogsteam/repos?per_page=100");
        file_put_contents($repoListFilename, $mod_data);
        mod_set_option('LAST_REPO_LIST', time());
    }

    if (is_readable($repoListFilename)) {
        $mod_file = file_get_contents($repoListFilename);
    }else {
        mod_set_option('LAST_REPO_LIST', 0);
        return false;
    }

    $data = json_decode($mod_file, true);
    //print_r($data);
    //On récupère ce que l'on a besoin dans la structure JSON
    foreach ($data as $id) {
        $mods_tmp[] = array(
            'nom' => $id["name"],
            'description' => $id["description"],
            'resource_uri' => $id["url"],
            'owner' => "OGSteam",
            'is_fork' => $id["fork"]
        );
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
                'owner' => $mod["owner"]
            );
        } elseif (preg_match("/^ogspy$/", $mod["nom"])) {
            $mod_list[] = array(
                'nom' => $mod["nom"],
                'description' => $mod["description"],
                'resource_uri' => $mod["resource_uri"],
                'owner' => $mod["owner"]
            );
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
    if (is_array($liste_mods)) {
        foreach ($liste_mods as $mod) {

            if ($mod['nom'] == $repoName) {

                return array(
                    'nom' => $mod["nom"],
                    'description' => $mod["description"],
                    'resource_uri' => $mod["resource_uri"],
                    'owner' => $mod["owner"]
                );
            }
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

    $version = array('release' => '' , 'beta' => '');

    $repo_details = getRepositoryDetails($Reponame);
    if (!$repo_details) {
        return "-1";
    }

    $repo_link = $repo_details['resource_uri'] . '/releases'; // Get Tags or Release

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
            foreach ($data as $tagged_release) {

                if ($tagged_release['prerelease']) {
                    if (version_compare($tagged_release['tag_name'], $version['beta'], ">")) {
                        $version['beta'] = $tagged_release['tag_name'];
                    }
                } else {
                    if (version_compare($tagged_release['tag_name'], $version['release'], ">")) {
                        $version['release'] = $tagged_release['tag_name'];
                    }
                }
            }

            if (!empty($version['beta'])) {
                if (version_compare($version['release'], $version['beta'], ">")) {
                    $version['beta'] = null;
                }
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
function github_Request($request)
{

    global $lang;
    $userdefinedtoken = '';
    $tokensource = '';

    $userdefinedtoken = mod_get_option('GITHUBTOKEN');
    $tokensource = 'mod_get_option';
    if (is_array($userdefinedtoken)) {
        if (count($userdefinedtoken) == 0) $userdefinedtoken = '';
    }
    if (substr($userdefinedtoken, 0, 4) != 'ghp_') $userdefinedtoken = ''; // Nouveau format de Token avec prefixe requis

    if (strlen($userdefinedtoken) !== 40) {
        $userdefinedtoken = github_RequestToken();
        $tokensource = 'external';
    }

    if (strlen($userdefinedtoken) !== 40) {
        log_('mod', $lang['autoupdate_tableau_errortoken'] . ' ' . $request);
        $userdefinedtoken = '';
        $tokensource = 'none';
    }

    //log_('debug', "Autoupdate Token source $tokensource");

    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: OGSpy',
                'Authorization: token '
            ]
        ]
    ];

    if ($userdefinedtoken ===  '') {
        // On Efface le header d'Authorization
        unset($opts['http']['header'][1]);
    } else {
        $opts['http']['header'][1] .= $userdefinedtoken;
    }

    $context = stream_context_create($opts);

    $data = @file_get_contents($request, false, $context);

    if ($data === false) {
        log_('mod', "[ERROR_github_Request] Unable to get: " . $request);
        mod_set_option('GITHUBTOKEN', 'UnauthorizedToken', 'autoupdate');
        echo ('Error Data, Please Retry with a new Token (See Settings)');
        exit();
    }

    mod_set_option('GITHUBTOKEN', $userdefinedtoken);

    return $data;
}

function github_RequestToken()
{

    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: OGSpy'
            ]
        ]
    ];

    $context = stream_context_create($opts);

    $data = file_get_contents('https://darkcity.fr/statistiques/collector/repolistkey', false, $context);
    return $data;
}
