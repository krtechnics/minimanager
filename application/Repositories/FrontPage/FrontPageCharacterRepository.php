<?php


namespace Application\Repositories\FrontPage;


use Application\Repositories\AbstractCharacterRepository;

class FrontPageCharacterRepository extends AbstractCharacterRepository
{
    public function getLatestRaceFromUser(){
        $quey = 'SELECT race FROM characters WHERE account = :userID1
                                    AND totaltime = (SELECT MAX(totaltime) FROM characters WHERE account = :userID2) LIMIT 1';
        return $this->queryAndFetchCell($quey, ['userID1' => $this->user->getUserID(), 'userID2' => $this->user->getUserID()]);
    }

    public function getOnlineCharacters($gmOnlineFilter, $factionFilter, $orderByColumn, $orderByDirection, $startIndex, $itemsPerPage){
        $query = 'SELECT 
                    c.guid,  
                    c.name,  
                    c.race,  
                    c.class,  
                    c.zone,  
                    c.map,  
                    c.level,  
                    c.account,  
                    c.gender,  
                    c.totalHonorPoints, 
                    gm.guildid,
                    g.name AS guildname
                FROM characters c
                    LEFT JOIN guild_member gm ON gm.guid = c.guid
                    LEFT JOIN guild g on gm.guildid = g.guildid
                WHERE c.online = 1 ' . ($gmOnlineFilter == '0' ? 'AND c.extra_flags &1 = 0 ' : '') . $factionFilter . ' 
                ORDER BY ' . $orderByColumn . ' ' . $orderByDirection . ' 
                LIMIT ' . $startIndex . ', ' . $itemsPerPage;
        return $this->queryAndFetchAll($query);
    }

    public function getOnlineCharactersCount($gmOnlineFilter, string $factionFilter)
    {
        $query = 'SELECT count(*) FROM characters c WHERE online= 1' . (($gmOnlineFilter == '0') ? ' AND extra_flags &1 = 0' : '') .$factionFilter;
        return $this->queryAndFetchCell($query);
    }
}
