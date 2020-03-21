<?php

namespace Application\Modules;

use Application\Repositories\FrontPage\FrontPageMMFTCRepository;
use Application\Repositories\FrontPage\FrontPageRealmRepository;
use Application\Repositories\FrontPage\FrontPageWorldRepository;
use Application\ViewModels\DatabaseCredentials;
use Application\ViewModels\ServerConfiguration;

class FrontPageModule extends AbstractModule
{

    /**
     * @var FrontPageRealmRepository
     */
    private $realmRepository;
    /**
     * @var
     */
    private $online;
    /**
     * @var FrontPageWorldRepository
     */
    private $worldRepository;
    /**
     * @var FrontPageMMFTCRepository
     */
    private $mmftcRepository;

    public function __construct(int $realmID, ?DatabaseCredentials $mmftcCredentails, ?DatabaseCredentials $realmCredentails, ?DatabaseCredentials $characterCredentials, ?DatabaseCredentials $wordCredentails)
    {
        parent::__construct(
            $realmID,
            $mmftcCredentails,
            $realmCredentails,
            $characterCredentials,
            $wordCredentails
        );
        $this->mmftcRepository = new FrontPageMMFTCRepository($this->mmftcConnection, $realmID);
        $this->realmRepository = new FrontPageRealmRepository($this->realmConnection, $realmID);
        $this->worldRepository = new FrontPageWorldRepository($this->worldConnection);
    }

    public function renderTop(ServerConfiguration $server, array $lang_index)
    {
        $output = '
                <div class="top">';

        if ($this->testPort($server->getAddres(),$server->getGamePort()))
        {
            $stats = $this->realmRepository->getUptime();
            $uptimetime = time() - $stats['starttime'];

            $staticUptime = $lang_index['realm'].' <em>'.htmlentities($this->realmRepository->getRealmName()).'</em> '.$lang_index['online'].' for '.$this->formatUptime($uptimetime);
            unset($uptimetime);
            $output .= '
                    <div id="uptime">
                        <h1>
                            <font color="#55aa55">'.$staticUptime.'<br />'.$lang_index['maxplayers'].': '.$stats['maxplayers'].'</font>
                        </h1>
                    </div>';
            unset($staticUptime, $stats);
            $this->online = true;
        }
        else
        {
            $output .= '
                    <h1>
                        <font class="error">'.$lang_index['realm'].' <em>'.htmlentities($this->realmRepository->getRealmName()).'</em> '.$lang_index['offline_or_let_high'].'</font>
                    </h1>';
            $this->online = false;
        }

        //  This retrieves the actual database version from the database itself, instead of hardcoding it into a string
        $version = $this->worldRepository->getVersion();
        $output .= '
                    '.$lang_index['trinity_rev'].' '.$version['core_revision'].' '.$lang_index['using_db'].' '.$version['db_version'].'
                </div>';
        unset($version);
        return $output;
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

    private function testPort($server,$port)
    {
        $sock = @fsockopen($server, $port, $ERROR_NO, $ERROR_STR, (float)0.5);
        if($sock)
        {
            @fclose($sock);
            return true;
        }
        return false;
    }

    public function renderMOTD($user_lvl, $action_permission, $showPoster, $lang_global, $lang_index){
        $output = '';
        if ($user_lvl >= $action_permission['delete']){
            $output .= '
                <script type="text/javascript">
                    // <![CDATA[
                        answerbox.btn_ok="'.$lang_global['yes_low'].'";
                        answerbox.btn_cancel="'.$lang_global['no'].'";
                        var del_motd = "motd.php?action=delete_motd&amp;id=";
                    // ]]>
                </script>';
        }

        $output .= '<table class="lined">
                        <tr>
                            <th align="right">';
        if ($user_lvl >= $action_permission['insert'])
            $output .= '
                                <a href="motd.php?action=add_motd">'.$lang_index['add_motd'].'</a>';
        $output .= '
                            </th>
                        </tr>';
        $motdCount = $this->mmftcRepository->getMOTDCount();
        if($motdCount > 0)
        {
            $start_m = $_GET['start_m'] ?? 0;
            if (!is_numeric($start_m)){
                $start_m = 0;
            }

            $posts = $this->mmftcRepository->getMOTDItems($start_m);
            foreach($posts as $post)
            {
                $output .= '
                        <tr>
                            <td align="left" class="large">
                                <blockquote>'.bbcode_bbc2html($post['content']).'</blockquote>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">';
                ($showPoster) ? $output .= $post['type'] : '';

                if ($user_lvl >= $action_permission['delete'])
                    $output .= '
                                <img src="img/cross.png" width="12" height="12" onclick="answerBox(\''.$lang_global['delete'].': &lt;font color=white&gt;'.$post['id'].'&lt;/font&gt;&lt;br /&gt;'.$lang_global['are_you_sure'].'\', del_motd + '.$post['id'].');" style="cursor:pointer;" alt="" />';
                if ($user_lvl >= $action_permission['update'])
                    $output .= '
                                <a href="motd.php?action=edit_motd&amp;error=3&amp;id='.$post['id'].'">
                                    <img src="img/edit.png" width="14" height="14" alt="" />
                                </a>';
                $output .= '
                            </td>
                        </tr>
                        <tr>
                            <td class="hidden"></td>
                        </tr>';
            }
            if ($this->online){
                $output .= '%%REPLACE_TAG%%';
            } else {
                $output .= '
                        <tr>
                            <td align="right" class="hidden">'.generate_pagination('index.php?start=0', $motdCount, 3, $start_m, 'start_m').'</td>
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
}
