<?php

namespace Application\Repositories\FrontPage;

use Application\Repositories\AbstractRealmRepository;

class FrontPageRealmRepository extends AbstractRealmRepository
{
    public function getUptime(){
        $query = 'SELECT starttime, maxplayers FROM uptime WHERE realmid = :id ORDER BY starttime DESC LIMIT 1';
        return $this->queryAndFetch($query, ['id' => $this->realmID]);
    }
}
