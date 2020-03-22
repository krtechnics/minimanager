<?php

namespace Application\Modules;

use Application\Repositories\FrontPage\FrontPageCharacterRepository;
use Application\Repositories\FrontPage\FrontPageMMFTCRepository;
use Application\Repositories\FrontPage\FrontPageRealmRepository;
use Application\Repositories\FrontPage\FrontPageWorldRepository;
use Application\ViewModels\User;
use PDO;

class FrontPageModule extends AbstractModule
{
    /**
     * @var FrontPageRealmRepository
     */
    private $realmRepository;
    /**
     * @var FrontPageWorldRepository
     */
    private $worldRepository;
    /**
     * @var FrontPageMMFTCRepository
     */
    private $mmftcRepository;
    /**
     * @var FrontPageCharacterRepository
     */
    private $characterRepository;

    /**
     * @var bool
     */
    private $online;


    private $motdCount = 0;
    private $motdStart = 0;


    public function __construct(int $realmID, User $user, ?PDO $mmftcCredentails, ?PDO $realmCredentails, ?PDO $characterCredentials, ?PDO $wordCredentails)
    {
        parent::__construct(
            $realmID,
            $user,
            $mmftcCredentails,
            $realmCredentails,
            $characterCredentials,
            $wordCredentails
        );
        $this->mmftcRepository = new FrontPageMMFTCRepository(
            $this->mmftcConnection,
            $realmID
        );
        $this->realmRepository = new FrontPageRealmRepository(
            $this->realmConnection,
            $realmID
        );
        $this->characterRepository = new FrontPageCharacterRepository($this->characterConnection);
        $this->characterRepository->setUser($user);
        $this->worldRepository = new FrontPageWorldRepository($this->worldConnection);
    }

    public function renderTop(array $lang_index)
    {
        $output = '
                <div class="top">';

        if ($this->testPort(
            $this->serverConfiguration->getAddres(),
            $this->serverConfiguration->getGamePort()
        )) {
            $stats = $this->realmRepository->getUptime();
            $uptimetime = time() - $stats['starttime'];

            $staticUptime = $lang_index['realm'] . ' <em>' . htmlentities(
                    $this->realmRepository->getRealmName()
                ) . '</em> ' . $lang_index['online'] . ' for ' . $this->formatUptime($uptimetime);
            unset($uptimetime);
            $output .= '
                    <div id="uptime">
                        <h1>
                            <font color="#55aa55">' . $staticUptime . '<br />' . $lang_index['maxplayers'] . ': ' . $stats['maxplayers'] . '</font>
                        </h1>
                    </div>';
            unset($staticUptime, $stats);
            $this->online = true;
        } else {
            $output .= '
                    <h1>
                        <font class="error">' . $lang_index['realm'] . ' <em>' . htmlentities(
                    $this->realmRepository->getRealmName()
                ) . '</em> ' . $lang_index['offline_or_let_high'] . '</font>
                    </h1>';
            $this->online = false;
        }

        //  This retrieves the actual database version from the database itself, instead of hardcoding it into a string
        $version = $this->worldRepository->getVersion();
        $output .= '
                    ' . $lang_index['trinity_rev'] . ' ' . $version['core_revision'] . ' ' . $lang_index['using_db'] . ' ' . $version['db_version'] . '
                </div>';
        unset($version);
        return $output;
    }

    private function testPort($server, $port)
    {
        $sock = @fsockopen(
            $server,
            $port,
            $ERROR_NO,
            $ERROR_STR,
            (float)0.5
        );
        if ($sock) {
            @fclose($sock);
            return true;
        }
        return false;
    }

    private function formatUptime($uptime)
    {
        $seconds = $uptime % 60;
        $minutes = $uptime / 60 % 60;
        $hours = $uptime / 3600 % 24;
        $days = (int)($uptime / 86400);

        $uptimeString = '';

        if ($days) {
            $uptimeString .= $days;
            $uptimeString .= ((1 === $days) ? ' day' : ' days');
        }
        if ($hours) {
            $uptimeString .= ((0 < $days) ? ', ' : '') . $hours;
            $uptimeString .= ((1 === $hours) ? ' hour' : ' hours');
        }
        if ($minutes) {
            $uptimeString .= ((0 < $days || 0 < $hours) ? ', ' : '') . $minutes;
            $uptimeString .= ((1 === $minutes) ? ' minute' : ' minutes');
        }
        if ($seconds) {
            $uptimeString .= ((0 < $days || 0 < $hours || 0 < $minutes) ? ', ' : '') . $seconds;
            $uptimeString .= ((1 === $seconds) ? ' second' : ' seconds');
        }
        return $uptimeString;
    }

    public function renderMOTD($user_lvl, $action_permission, $showPoster, $lang_global, $lang_index)
    {
        $output = '';
        if ($user_lvl >= $action_permission['delete']) {
            $output .= '
                <script type="text/javascript">
                    // <![CDATA[
                        answerbox.btn_ok="' . $lang_global['yes_low'] . '";
                        answerbox.btn_cancel="' . $lang_global['no'] . '";
                        var del_motd = "motd.php?action=delete_motd&amp;id=";
                    // ]]>
                </script>';
        }

        $output .= '<table class="lined">
                        <tr>
                            <th align="right">';
        if ($user_lvl >= $action_permission['insert']) {
            $output .= '<a href="motd.php?action=add_motd">' . $lang_index['add_motd'] . '</a>';
        }
        $output .= '
                            </th>
                        </tr>';
        $this->motdCount = $this->mmftcRepository->getMOTDCount();
        if ($this->motdCount > 0) {
            $this->motdStart = $_GET['start_m'] ?? 0;
            if (!is_numeric($this->motdStart)) {
                $this->motdStart = 0;
            }

            $posts = $this->mmftcRepository->getMOTDItems($this->motdStart);
            foreach ($posts as $post) {
                $output .= '
                        <tr>
                            <td align="left" class="large">
                                <blockquote>' . bbcode_bbc2html($post['content']) . '</blockquote>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">';
                ($showPoster) ? $output .= $post['type'] : '';

                if ($user_lvl >= $action_permission['delete'])
                    $output .= '
                                <img src="img/cross.png" width="12" height="12" onclick="answerBox(\'' . $lang_global['delete'] . ': &lt;font color=white&gt;' . $post['id'] . '&lt;/font&gt;&lt;br /&gt;' . $lang_global['are_you_sure'] . '\', del_motd + ' . $post['id'] . ');" style="cursor:pointer;" alt="" />';
                if ($user_lvl >= $action_permission['update'])
                    $output .= '
                                <a href="motd.php?action=edit_motd&amp;error=3&amp;id=' . $post['id'] . '">
                                    <img src="img/edit.png" width="14" height="14" alt="" />
                                </a>';
                $output .= '
                            </td>
                        </tr>
                        <tr>
                            <td class="hidden"></td>
                        </tr>';
            }
            if ($this->online) {
                $output .= '%%REPLACE_TAG%%';
            } else {
                $output .= '
                        <tr>
                            <td align="right" class="hidden">' . generate_pagination(
                        'index.php?start=0',
                        $this->motdCount,
                        3,
                        $this->motdStart,
                        'start_m'
                    ) . '</td>
                        </tr>';
            }

        }
        $output .= '
                    </table>';
        return $output;
    }

    public function isOnline()
    {
        return $this->online;
    }

    public function getMotdCount()
    {
        return $this->motdCount;
    }

    public function getMotdStart()
    {
        return $this->motdStart;
    }

    public function renderOnlineCharacters($output, $itemsPerPage, $gmOnline, $gmOnlineCount, $showCountryFlag, $lang_global, $lang_index)
    {
        if ($this->online) {
            $start = $_GET['start'] ?? 0;
            if (!is_numeric($start)) {
                $start = 0;
            }


            $order_by = $_GET['order_by'] ?? 'level';
            if (!preg_match(
                '/^[_[:lower:]]{1,12}$/',
                $order_by
            )) {
                $order_by = 'level';
            }


            $dir = $_GET['dir'] ?? 1;
            if (!preg_match(
                '/^[01]{1}$/',
                $dir
            )) {
                $dir = 1;
            }


            $order_dir = ($dir) ? 'DESC' : 'ASC';
            $dir = ($dir) ? 0 : 1;
            //==========================$_GET and SECURE end=============================

            if ($order_by === 'map') {
                $order_by = 'map ' . $order_dir . ', zone';
            } elseif ($order_by === 'zone') {
                $order_by = 'zone ' . $order_dir . ', map';
            }
            $order_side = '';

            if (!($this->user->getUserID() || $this->serverConfiguration->getBothFactions())) {
                $latestRace = $this->characterRepository->getLatestRaceFromUser();
                if ($latestRace !== null) {
                    $order_side = (in_array(
                        $latestRace,
                        [
                            2,
                            5,
                            6,
                            8,
                            10,
                        ]
                    )) ? ' AND c.race IN (2,5,6,8,10) ' : ' AND c.race IN (1,3,4,7,11) ';
                }
            }
            $result = $this->characterRepository->getOnlineCharacters(
                $gmOnline,
                $order_side,
                $order_by,
                $order_dir,
                $start,
                $itemsPerPage
            );
            $totalOnline = $this->characterRepository->getOnlineCharactersCount(
                $gmOnlineCount,
                $order_side
            );
            $replace = '
              <tr>
                <td align="right" class="hidden">' . generate_pagination(
                    'index.php?start=' . $start . '&amp;order_by=' . $order_by . '&amp;dir=' . (($dir) ? 0 : 1) . '',
                    $this->motdCount,
                    3,
                    $this->motdStart,
                    'start_m'
                ) . '</td>
              </tr>';
            $output = str_replace(
                '%%REPLACE_TAG%%',
                $replace,
                $output
            );;
            $output .= '
                    <font class="bold">' . $lang_index['tot_users_online'] . ': ' . $totalOnline . '</font>
                    <table class="lined">
                        <tr>
                            <td colspan="' . (10 - $showCountryFlag) . '" align="right" class="hidden" width="25%">';
            $output .= generate_pagination(
                'index.php?start_m=' . $this->motdStart . '&amp;order_by=' . $order_by . '&amp;dir=' . (($dir) ? 0 : 1),
                $totalOnline,
                $itemsPerPage,
                $start
            );
            $output .= '
                            </td>
                        </tr>
                        <tr>
                            <th width="15%"><a href="index.php?start=' . $start . '&amp;start_m=' . $this->motdStart . '&amp;order_by=name&amp;dir=' . $dir . '"' . ($order_by === 'name' ? ' class="' . $order_dir . '"' : '') . '>' . $lang_index['name'] . '</a></th>
                            <th width="1%"><a href="index.php?start=' . $start . '&amp;start_m=' . $this->motdStart . '&amp;order_by=race&amp;dir=' . $dir . '"' . ($order_by === 'race' ? ' class="' . $order_dir . '"' : '') . '>' . $lang_index['race'] . '</a></th>
                            <th width="1%"><a href="index.php?start=' . $start . '&amp;start_m=' . $this->motdStart . '&amp;order_by=class&amp;dir=' . $dir . '"' . ($order_by === 'class' ? ' class="' . $order_dir . '"' : '') . '>' . $lang_index['class'] . '</a></th>
                            <th width="5%"><a href="index.php?start=' . $start . '&amp;start_m=' . $this->motdStart . '&amp;order_by=level&amp;dir=' . $dir . '"' . ($order_by === 'level' ? ' class="' . $order_dir . '"' : '') . '>' . $lang_index['level'] . '</a></th>
                            <th width="1%"><a href="index.php?start=' . $start . '&amp;start_m=' . $this->motdStart . '&amp;order_by=totalHonorPoints&amp;dir=' . $dir . '"' . ($order_by === 'totalHonorPoints' ? ' class="' . $order_dir . '"' : '') . '>' . $lang_index['rank'] . '</a></th>
                            <th width="15%"><a href="index.php?start=' . $start . '&amp;start_m=' . $this->motdStart . '&amp;order_by=guildname&amp;dir=' . $dir . '"' . ($order_by === 'guildname' ? ' class="' . $order_dir . '"' : '') . '>' . $lang_index['guild'] . '</a></th>
                            <th width="20%"><a href="index.php?start=' . $start . '&amp;start_m=' . $this->motdStart . '&amp;order_by=map&amp;dir=' . $dir . '"' . ($order_by === 'map ' . $order_dir . ', zone' ? ' class="' . $order_dir . '"' : '') . '>' . $lang_index['map'] . '</a></th>
                            <th width="25%"><a href="index.php?start=' . $start . '&amp;start_m=' . $this->motdStart . '&amp;order_by=zone&amp;dir=' . $dir . '"' . ($order_by === 'zone ' . $order_dir . ', map' ? ' class="' . $order_dir . '"' : '') . '>' . $lang_index['zone'] . '</a></th>';
            if ($showCountryFlag) {
                //require_once 'libs/misc_lib.php';
                $output .= '
                            <th width="1%">' . $lang_global['country'] . '</th>';
            }
            $output .= '
                        </tr>';
            foreach ($result as $char) {
                $gm = $this->realmRepository->getGMLevelForAccount($char['account']);
                if (empty($gm))
                    $gm = 0;

                $output .= '
                        <tr>
                            <td>';
                if (($this->user->getUserLevel() >= $gm)) {
                    $output .= '
                                <a href="char.php?id=' . $char['guid'] . '">
                                    <span onmousemove="toolTip(\'' . id_get_gm_level(
                            $gm
                        ) . '\', \'item_tooltip\')" onmouseout="toolTip()">' . htmlentities($char['name']) . '</span>
                                </a>';
                } else {

                    $output .= '
                                <span onmousemove="toolTip(\'' . id_get_gm_level(
                            $gm
                        ) . '\', \'item_tooltip\')" onmouseout="toolTip()">' . htmlentities($char['name']) . '</span>';
                }
                $output .= '
                            </td>
                            <td>
                                <img src="img/c_icons/' . $char['race'] . '-' . $char['gender'] . '.gif" onmousemove="toolTip(\'' . char_get_race_name(
                        $char['race']
                    ) . '\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                            </td>
                            <td>
                                <img src="img/c_icons/' . $char['class'] . '.gif" onmousemove="toolTip(\'' . char_get_class_name(
                        $char['class']
                    ) . '\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                            </td>
                            <td>' . char_get_level_color($char['level']) . '</td>
                            <td>
                                <span onmouseover="toolTip(\'' . char_get_pvp_rank_name(
                        $char['totalHonorPoints'],
                        char_get_side_id($char['race'])
                    ) . '\', \'item_tooltip\')" onmouseout="toolTip()" style="color: white;"><img src="img/ranks/rank' . char_get_pvp_rank_id(
                        $char['totalHonorPoints'],
                        char_get_side_id($char['race'])
                    ) . '.gif" alt="" /></span>
                            </td>
                            <td>
                                <a href="guild.php?action=view_guild&amp;error=3&amp;id=' . $char['guildid'] . '">' . htmlentities(
                        $char['guildname']
                    ) . '</a>
                            </td>
                            <td><span onmousemove="toolTip(\'MapID:' . $char['map'] . '\', \'item_tooltip\')" onmouseout="toolTip()">' . $this->mmftcRepository->getMapName($char['map']) . '</span></td>
                            <td><span onmousemove="toolTip(\'ZoneID:' . $char['zone'] . '\', \'item_tooltip\')" onmouseout="toolTip()">' . $this->mmftcRepository->getZoneName($char['zone']) . '</span></td>';
                if ($showCountryFlag) {
//                    $country = misc_get_country_by_account(
//                        $char['account'],
//                        $sqlr,
//                        $sqlm
//                    );
                    $country['code'] = false;
                    $output .= '
                            <td>' . (($country['code']) ? '<img src="img/flags/' . $country['code'] . '.png" onmousemove="toolTip(\'' . ($country['country']) . '\',\'item_tooltip\')" onmouseout="toolTip()" alt="" />' : '-') . '</td>';
                }
                $output .= '
                        </tr>';
            }
            $output .= '
                        <tr>';
            $output .= '
                            <td colspan="' . (10 - $showCountryFlag) . '" align="right" class="hidden" width="25%">';
            $output .= generate_pagination(
                'index.php?start_m=' . $this->motdStart . '&amp;order_by=' . $order_by . '&amp;dir=' . (($dir) ? 0 : 1),
                $totalOnline,
                $itemsPerPage,
                $start
            );
            unset($total_online);
            $output .= '
                            </td>
                        </tr>
                    </table>
                    <br />';


            return $output;
        }
    }
}
