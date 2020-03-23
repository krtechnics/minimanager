<?php


use Application\Modules\FrontPageModule;
use Application\Repositories\AbstractRepository;
use Application\ViewModels\ServerConfiguration;
use Application\ViewModels\User;

require_once 'header.php';
require_once 'libs/bbcode_lib.php';
require_once 'libs/char_lib.php';
require_once 'libs/map_zone_lib.php';
require_once 'vendor/autoload.php';

if (isset($action_permission['read'])){
    valid_login($action_permission['read']);
}



//#############################################################################
// MAIN
//#############################################################################

$lang_index = lang_index();

$user = new User();
$user->setUserID($user_id);
$user->setUserLevel($user_lvl);

$module = new FrontPageModule(
    $realm_id,
    $user,
    AbstractRepository::connectMySQL(AbstractRepository::convertArrayToCredentails($mmfpm_db)),
    AbstractRepository::connectMySQL(AbstractRepository::convertArrayToCredentails($realm_db)),
    AbstractRepository::connectMySQL(AbstractRepository::convertArrayToCredentails($characters_db[$realm_id])),
    AbstractRepository::connectMySQL(AbstractRepository::convertArrayToCredentails($world_db[$realm_id]))
);
$serverConfiguration = ServerConfiguration::convertArrayToConfiguration($server[$realm_id]);
$module->setServerConfiguration($serverConfiguration);

$output .= $module->renderTop($lang_index);

$output .= '<center>';
$output .= $module->renderMOTD(
    $action_permission,
    $motd_display_poster,
    $lang_global,
    $lang_index
);

$output = $module->renderOnlineCharacters($output, $itemperpage, $gm_online, $gm_online_count,$showcountryflag, $lang_global, $lang_index);
$output .= '</center>';

unset($action_permission);
unset($lang_index);

require_once 'footer.php';


?>
