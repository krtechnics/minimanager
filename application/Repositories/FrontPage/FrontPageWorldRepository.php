<?php


namespace Application\Repositories\FrontPage;


use Application\Repositories\AbstractWorldRepository;

final class FrontPageWorldRepository extends AbstractWorldRepository
{
    public function getVersion(){
        $query = 'SELECT core_revision, db_version FROM version';
        return $this->queryAndFetch($query);
    }
}
