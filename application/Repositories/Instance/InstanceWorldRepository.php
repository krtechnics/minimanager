<?php


namespace Application\Repositories\Instance;


use Application\Repositories\AbstractWorldRepository;

class InstanceWorldRepository extends AbstractWorldRepository
{
    public function getInstanceCount(){
        $query = 'SELECT COUNT(*) FROM instance_template it JOIN access_requirement ar ON it.map = ar.mapId';
        return $this->queryAndFetchCell($query);
    }

    public function getInstances(string $orderByColumn, string $orderByDirection, int $start, int $itemsPerPage)
    {
        $query = 'SELECT mapid, level_min, level_max, item_level, quest_done_A AS quest_a, quest_done_H AS quest_h, comment AS map, parent FROM instance_template it JOIN access_requirement ar ON it.map = ar.mapId
                            ORDER BY ' . $orderByColumn . ' ' . $orderByDirection . ' LIMIT ' . $start . ', ' . $itemsPerPage;
        return $this->queryAndFetchAll($query);
    }
}
