<?php


require_once 'header.php';
require_once 'libs/char_lib.php';
valid_login($action_permission['read']);

function top100($realmid, &$sqlr, &$sqlc)
{
    global $output, $lang_top, $realm_db, $characters_db, $server, $itemperpage, $developer_test_mode, $multi_realm_mode;

    $realm_id = $realmid;
    $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    //==========================$_GET and SECURE========================
    $type = (isset($_GET['type'])) ? $sqlc->quote_smart($_GET['type']) : 'level';
    if (preg_match('/^[_[:lower:]]{1,10}$/', $type));
    else
        $type = 'level';

    $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
    if (is_numeric($start));
    else
        $start=0;

    $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 'level';
    if (preg_match('/^[_[:lower:]]{1,14}$/', $order_by));
    else
        $order_by = 'level';

    $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
    if (preg_match('/^[01]{1}$/', $dir));
    else
        $dir=1;

    $order_dir = ($dir) ? 'DESC' : 'DESC';
    $dir = ($dir) ? 0 : 1;
    //==========================$_GET and SECURE end========================

    $type_list = array('level', 'stat', 'defense', 'attack', 'resist', 'crit_hit', 'pvp');
    if (in_array($type, $type_list));
    else
        $type = 'level';

    $result = $sqlc->query('SELECT count(*) FROM characters WHERE characters.account NOT IN (SELECT AccountID FROM '.$realm_db['name'].'.account_access WHERE SecurityLevel > 1)');
    $all_record = $sqlc->result($result, 0);
    $all_record = (($all_record < 100) ? $all_record : 100);

    $result = $sqlc->query('SELECT characters.guid, BINARY characters.name AS name, characters.race, characters.class, characters.gender, characters.level, characters.totaltime, characters.online, characters.money, COALESCE(guild_member.guildid,0) as gname, character_stats.maxhealth as health, characters.power1 AS mana, character_stats.strength AS str, character_stats.agility AS agi, character_stats.stamina AS sta,
                            character_stats.intellect AS intel, character_stats.spirit AS spi, character_stats.armor, character_stats.blockPct AS block, character_stats.dodgePct AS dodge, character_stats.parryPct AS parry, character_stats.attackPower AS ap, character_stats.rangedAttackPower AS ranged_ap, character_stats.resHoly AS holy, character_stats.resFire AS fire,
                            character_stats.resNature AS nature, character_stats.resFrost AS frost, character_stats.resShadow AS shadow, character_stats.resArcane AS arcane, character_stats.critPct AS melee_crit, character_stats.rangedCritPct AS range_crit, characters.totalHonorPoints AS honor, characters.totalKills AS kills, characters.arenaPoints AS arena
                            FROM characters LEFT JOIN character_stats ON character_stats.guid = characters.guid LEFT JOIN guild_member ON guild_member.guid = characters.guid WHERE characters.account NOT IN (SELECT AccountID FROM '.$realm_db['name'].'.account_access WHERE SecurityLevel > 1) ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'');
    //mindmg,maxdmg,minrangeddmg,maxrangeddmg,expertise,off_expertise,meleehit,rangehit,spellhit missing

    //==========================top tage navigaion starts here========================
    $output .= '
                <center>
                    <div id="tab">
                        <ul>
                            <li'.(($type === 'level') ? ' id="selected"' : '' ).'>
                                <a href="top100.php?start='.$start.'">
                                    '.$lang_top['general'].'
                                </a>
                            </li>
                            <li'.(($type === 'stat') ? ' id="selected"' : '' ).'>
                                <a href="top100.php?start='.$start.'&amp;type=stat&amp;order_by=health">
                                    '.$lang_top['stats'].'
                                </a>
                            </li>
                            <li'.(($type === 'defense') ? ' id="selected"' : '' ).'>
                                <a href="top100.php?start='.$start.'&amp;type=defense&amp;order_by=armor">
                                    '.$lang_top['defense'].'
                                </a>
                            </li>
                            <li'.(($type === 'resist') ? ' id="selected"' : '' ).'>
                                <a href="top100.php?start='.$start.'&amp;type=resist&amp;order_by=holy">
                                    '.$lang_top['resist'].'
                                </a>
                            </li>
                            <li'.(($type === 'pvp') ? ' id="selected"' : '' ).'>
                                <a href="top100.php?start='.$start.'&amp;type=pvp&amp;order_by=honor">
                                    '.$lang_top['pvp'].'
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div id="tab_content">
                        <table class="top_hidden" style="width: 720px">';
    if($developer_test_mode && $multi_realm_mode)
    {
        $realms = $sqlr->query('SELECT count(*) FROM realmlist');
        $tot_realms = $sqlr->result($realms, 0);
        if (1 < $tot_realms && 1 < count($server))
        {
            $output .= '
                            <tr>
                                <td colspan="2" align="left">';

            makebutton('View', 'javascript:do_submit(\'form'.$realm_id.'\',0)', 130);

            $output .= '
                                    <form action="top100.php?type='.$type.'" method="post" name="form'.$realm_id.'">
                                        Number of Realms :
                                        <input type="hidden" name="action" value="realms" />
                                        <select name="n_realms">';
            for($i=1;$i<=$tot_realms;++$i)
                $output .= '
                                            <option value="'.$i.'">'.htmlentities($i).'</option>';
            $output .= '
                                        </select>
                                    </form>
                                </td>
                            </tr>';
        }
    }
    $output .= '
                            <tr>
                                <td align="right">Total: '.$all_record.'</td>
                                <td align="right" width="25%">';
    $output .= generate_pagination('top100.php?type='.$type.'&amp;order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).'', $all_record, $itemperpage, $start);

    $output .= '
                                </td>
                            </tr>
                        </table>';

    //==========================top tage navigaion ENDS here ========================
    $output .= '
                        <table class="lined" style="width: 720px">
                            <tr>
                                <th width="5%">#</th>
                                <th width="14%">'.$lang_top['name'].'</th>
                                <th width="11%">'.$lang_top['race'].' '.$lang_top['class'].'</th>
                                <th width="8%"><a href="top100.php?type='.$type.'&amp;order_by=level&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='level' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['level'].'</a></th>';
    if ($type === 'level')
    {
        $output .= '
                                <th width="22%">'.$lang_top['guild'].'</th>
                                <th width="20%"><a href="top100.php?type='.$type.'&amp;order_by=money&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='money' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['money'].'</a></th>
                                <th width="20%"><a href="top100.php?type='.$type.'&amp;order_by=totaltime&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='totaltime' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['time_played'].'</a></th>';
    }
    elseif ($type === 'stat')
    {
        $output .= '
                                <th width="11%"><a href="top100.php?type='.$type.'&amp;order_by=health&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='health' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['health'].'</a></th>
                                <th width="10%"><a href="top100.php?type='.$type.'&amp;order_by=mana&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='mana' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['mana'].'</a></th>
                                <th width="9%"><a href="top100.php?type='.$type.'&amp;order_by=str&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='str' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['str'].'</a></th>
                                <th width="8%"><a href="top100.php?type='.$type.'&amp;order_by=agi&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='agi' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['agi'].'</a></th>
                                <th width="8%"><a href="top100.php?type='.$type.'&amp;order_by=sta&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='sta' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['sta'].'</a></th>
                                <th width="8%"><a href="top100.php?type='.$type.'&amp;order_by=intel&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='intel' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['intel'].'</a></th>
                                <th width="8%"><a href="top100.php?type='.$type.'&amp;order_by=spi&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='spi' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['spi'].'</a></th>';
    }
    elseif ($type === 'defense')
    {
        $output .= '
                                <th width="16%"><a href="top100.php?type='.$type.'&amp;order_by=armor&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='armor' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['armor'].'</a></th>
                                <th width="16%"><a href="top100.php?type='.$type.'&amp;order_by=block&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='block' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['block'].'</a></th>
                                <th width="15%"><a href="top100.php?type='.$type.'&amp;order_by=dodge&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='dodge' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['dodge'].'</a></th>
                                <th width="15%"><a href="top100.php?type='.$type.'&amp;order_by=parry&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='parry' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['parry'].'</a></th>';
    }
    elseif ($type === 'resist')
    {
        $output .= '
                                <th width="10%"><a href="top100.php?type='.$type.'&amp;order_by=holy&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='holy' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['holy'].'</a></th>
                                <th width="10%"><a href="top100.php?type='.$type.'&amp;order_by=fire&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='fire' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['fire'].'</a></th>
                                <th width="10%"><a href="top100.php?type='.$type.'&amp;order_by=nature&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='nature' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['nature'].'</a></th>
                                <th width="10%"><a href="top100.php?type='.$type.'&amp;order_by=frost&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='frost' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['frost'].'</a></th>
                                <th width="11%"><a href="top100.php?type='.$type.'&amp;order_by=shadow&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='shadow' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['shadow'].'</a></th>
                                <th width="11%"><a href="top100.php?type='.$type.'&amp;order_by=arcane&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='arcane' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['arcane'].'</a></th>';
    }
    elseif ($type === 'pvp')
    {
        $output .= '
                                <th width="20%"><a href="top100.php?type='.$type.'&amp;order_by=honor&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='honor' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['rank'].'</a></th>
                                <th width="14%">'.$lang_top['honor_points'].'</th>
                                <th width="14%"><a href="top100.php?type='.$type.'&amp;order_by=kills&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='kills' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['kills'].'</a></th>
                                <th width="14%"><a href="top100.php?type='.$type.'&amp;order_by=arena&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='arena' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['arena_points'].'</a></th>';
    }
    $output .= '
                            </tr>';
    $i=0;
    while($char = $sqlc->fetch_assoc($result))
    {
        $output .= '
                            <tr valign="top">
                                <td>'.(++$i+$start).'</td>
                                <td><a href="char.php?id='.$char['guid'].'&amp;realm='.$realm_id.'">'.htmlentities($char['name']).'</a></td>
                                <td>
                                    <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" alt="'.char_get_race_name($char['race']).'" onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" />
                                    <img src="img/c_icons/'.$char['class'].'.gif" alt="'.char_get_class_name($char['class']).'" onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" />
                                </td>
                                <td>'.char_get_level_color($char['level']).'</td>';
        if ($type === 'level')
        {
            $guild_name = $sqlc->result($sqlc->query('SELECT BINARY name AS name FROM guild WHERE guildid = '.$char['gname'].''), 0);
            $days  = floor(round($char['totaltime'] / 3600)/24);
            $hours = round($char['totaltime'] / 3600) - ($days * 24);
            $time = '';

            if ($days)
                $time .= $days.' days ';

            if ($hours)
                $time .= $hours.' hours';

            $top_money = $char['money'];
            $money_gold = (int)($top_money/10000);
            $total_money = $top_money - ($money_gold*10000);
            $money_silver = (int)($total_money/100);
            $money_cooper = $total_money - ($money_silver*100);

            $output .= '
                                <td><a href="guild.php?action=view_guild&amp;realm='.$realm_id.'&amp;error=3&amp;id='.$char['gname'].'">'.htmlentities($guild_name).'</a></td>
                                <td align="right">
                                    '.$money_gold.'<img src="img/gold.gif" alt="" align="middle" />
                                    '.$money_silver.'<img src="img/silver.gif" alt="" align="middle" />
                                    '.$money_cooper.'<img src="img/copper.gif" alt="" align="middle" />
                                </td>
                                <td align="right">'.$time.'</td>';
        }
        elseif ($type === 'stat')
        {
            $output .= '
                                <td>'.$char['health'].'</td>
                                <td>'.$char['mana'].'</td>
                                <td>'.$char['str'].'</td>
                                <td>'.$char['agi'].'</td>
                                <td>'.$char['sta'].'</td>
                                <td>'.$char['intel'].'</td>
                                <td>'.$char['spi'].'</td>';
        }
        elseif ($type === 'defense')
        {
            $block = round($char['block'],2);
            $dodge = round($char['dodge'],2);
            $parry = round($char['parry'],2);

            $output .= '
                                <td>'.$char['armor'].'</td>
                                <td>'.$block.'%</td>
                                <td>'.$dodge.'%</td>
                                <td>'.$parry.'%</td>';
        }
        elseif ($type === 'resist')
        {
            $output .= '
                                <td>'.$char['holy'].'</td>
                                <td>'.$char['fire'].'</td>
                                <td>'.$char['nature'].'</td>
                                <td>'.$char['frost'].'</td>
                                <td>'.$char['shadow'].'</td>
                                <td>'.$char['arcane'].'</td>';
        }
        elseif ($type === 'pvp')
        {
            $output .= '
                                <td align="left"><img src="img/ranks/rank'.char_get_pvp_rank_id($char['honor'], char_get_side_id($char['race'])).'.gif" alt=""></img> '.char_get_pvp_rank_name($char['honor'], char_get_side_id($char['race'])).'</td>
                                <td>'.$char['honor'].'</td>
                                <td>'.$char['kills'].'</td>
                                <td>'.$char['arena'].'</td>';
        }
        $output .= '
                            </tr>';
    }
    $output .= '
                        </table>
                        <table class="top_hidden" style="width: 720px">
                            <tr>
                                <td align="right">Total: '.$all_record.'</td>
                                <td align="right" width="25%">';

    $output .= generate_pagination('top100.php?type='.$type.'&amp;order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).'', $all_record, $itemperpage, $start);
    unset($all_record);

    $output .= '
                                </td>
                            </tr>
                        </table>
                    </div>
                    <br />
                </center>';
}


//#############################################################################
// MAIN
//#############################################################################

//$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

//$output .= '
//          <div class="top">';

$lang_top = lang_top();

//if(1 == $err);
//else
//  $output .= "
//            <h1>'.$lang_top['top100'].'</h1>;

//unset($err);

//$output .= '
//          </div>';

$action = (isset($_POST['action'])) ? $_POST['action'] : NULL;

if ('realms' === $action)
{
    if (isset($_POST['n_realms']))
    {
        $n_realms = $_POST['n_realms'];

        $realms = $sqlr->query('SELECT id, name FROM realmlist LIMIT 10');

        if (1 < $sqlr->num_rows($realms) && 1 < (count($server)))
        {
            for($i=1;$i<=$n_realms;++$i)
            {
                $realm = $sqlr->fetch_assoc($realms);
                if(isset($server[$realm['id']]))
                {
                    $output .= '
                        <div class="top"><h1>Top 100 of '.$realm['name'].'</h1></div>';
                    top100($realm['id'], $sqlr, $sqlc);
                }
            }
        }
        else
        {
            $output .= '
                        <div class="top"><h1>'.$lang_top['top100'].'</h1></div>';
            top100($realm_id, $sqlr, $sqlc);
        }
    }
    else
    {
        $output .= '
                        <div class="top"><h1>'.$lang_top['top100'].'</h1></div>';
        top100($realm_id, $sqlr, $sqlc);
    }
}
else
{
    $output .= '
                        <div class="top"><h1>'.$lang_top['top100'].'</h1></div>';
    top100($realm_id, $sqlr, $sqlc);
}

unset($action);
unset($action_permission);
unset($lang_top);

require_once 'footer.php';


?>
