<?php


namespace Application\Repositories\FrontPage;


use Application\Repositories\AbstractMMFTCRepository;

final class FrontPageMMFTCRepository extends AbstractMMFTCRepository
{
    public function getMOTDCount()
    {
        $query = 'SELECT count(*) FROM mm_motd WHERE realmid = :realmID';
        return $this->queryAndFetchCell(
            $query,
            ['realmID' => $this->realmID]
        );
    }

    public function getMOTDItems($start)
    {
        $query = 'SELECT id, realmid, type, content FROM mm_motd WHERE realmid = :realmID ORDER BY id DESC LIMIT ' . $start . ', 3';
        return $this->queryAndFetchAll(
            $query,
            ['realmID' => $this->realmID]
        );
    }
}
