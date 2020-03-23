<?php

namespace Application\Modules;

use Application\Repositories\Instance\InstanceMMFTCRepository;
use Application\Repositories\Instance\InstanceWorldRepository;
use PDO;

final class InstanceModule extends AbstractModule
{
    /**
     * @var InstanceMMFTCRepository
     */
    private $mmftcRepository;
    /**
     * @var InstanceWorldRepository
     */
    private $worldRepository;

    public function __construct(int $realmID, ?PDO $mmftcCredentails, ?PDO $wordCredentails)
    {
        parent::__construct(
            $realmID,
            null,
            $mmftcCredentails,
            null,
            null,
            $wordCredentails
        );
        $this->mmftcRepository = new InstanceMMFTCRepository(
            $this->mmftcConnection,
            $realmID
        );
        $this->worldRepository = new InstanceWorldRepository($this->worldConnection);
    }

    public function renderTop($lang_instances)
    {
        return '<div class="top">
            <h1>' . $lang_instances['instances'] . '</h1>
        </div>';
    }

    public function renderContent(array $lang_instances, int $itemsPerPage)
    {
        $start = $_GET['start'] ?? 0;
        if (!is_numeric($start)) {
            $start = 0;
        }


        $orderByColumn = $_GET['order_by'] ?? 'level_min';
        if (!preg_match(
            '/^[_[:lower:]]{1,11}$/',
            $orderByColumn
        )) {
            $orderByColumn = 'level_min';
        }

        $dir = $_GET['dir'] ?? 1;
        if (!preg_match(
            '/^[01]{1}$/',
            $dir
        )) {
            $dir = 1;
        }


        $orderByDirection = ($dir) ? 'ASC' : 'DESC';
        $dir = ($dir) ? 0 : 1;

        $instanceCount = $this->worldRepository->getInstanceCount();

        $output = '
                <center>
                    <table class="top_hidden">
                        <tr>
                            <td width="25%" align="right">';

        // multi page links
        $output .=
            $lang_instances['total'] . ' : ' . $instanceCount . '<br /><br />' .
            generate_pagination(
                'instances.php?order_by=' . $orderByColumn . '&amp;dir=' . (($dir) ? 0 : 1),
                $instanceCount,
                $itemsPerPage,
                $start
            );

        // column headers, with links for sorting
        $output .= '
                            </td>
                        </tr>
                    </table>
                    <table class="lined">
                        <tr>
                            <th width="30%"><a href="instances.php?order_by=map&amp;start=' . $start . '&amp;dir=' . $dir . '"' . ($orderByColumn === 'map' ? ' class="' . $orderByDirection . '"' : '') . '>' . $lang_instances['map'] . '</a></th>
                            <th width="10%"><a href="instances.php?order_by=parent&amp;start=' . $start . '&amp;dir=' . $dir . '"' . ($orderByColumn === 'parent' ? ' class="' . $orderByDirection . '"' : '') . '>' . $lang_instances['parent'] . '</a></th>
							<th width="10%"><a href="instances.php?order_by=mapid&amp;start=' . $start . '&amp;dir=' . $dir . '"' . ($orderByColumn === 'mapid' ? ' class="' . $orderByDirection . '"' : '') . '>' . $lang_instances['mapid'] . '</a></th>
                            <th width="10%"><a href="instances.php?order_by=level_min&amp;start=' . $start . '&amp;dir=' . $dir . '"' . ($orderByColumn === 'level_min' ? ' class="' . $orderByDirection . '"' : '') . '>' . $lang_instances['level_min'] . '</a></th>
                            <th width="10%"><a href="instances.php?order_by=level_max&amp;start=' . $start . '&amp;dir=' . $dir . '"' . ($orderByColumn === 'level_max' ? ' class="' . $orderByDirection . '"' : '') . '>' . $lang_instances['level_max'] . '</a></th>
							<th width="10%"><a href="instances.php?order_by=item_level&amp;start=' . $start . '&amp;dir=' . $dir . '"' . ($orderByColumn === 'item_level' ? ' class="' . $orderByDirection . '"' : '') . '>' . $lang_instances['item_level'] . '</a></th>
							<th width="10%"><a href="instances.php?order_by=quest_a&amp;start=' . $start . '&amp;dir=' . $dir . '"' . ($orderByColumn === 'quest_a' ? ' class="' . $orderByDirection . '"' : '') . '>' . $lang_instances['quest_a'] . '</a></th>
							<th width="10%"><a href="instances.php?order_by=quest_h&amp;start=' . $start . '&amp;dir=' . $dir . '"' . ($orderByColumn === 'quest_h' ? ' class="' . $orderByDirection . '"' : '') . '>' . $lang_instances['quest_h'] . '</a></th>
                        </tr>';

        $instances = $this->worldRepository->getInstances($orderByColumn, $orderByDirection, $start, $itemsPerPage);
        foreach ($instances as $instance) {

            $output .= '
                        <tr valign="top">
                            <td>' . $instance['map'] . '</td>
							<td>' . $this->mmftcRepository->getMapName($instance['parent']) . '</td>
							<td>' . $instance['mapid'] . '</td>
                            <td>' . $instance['level_min'] . '</td>
                            <td>' . $instance['level_max'] . '</td>
							<td>' . $instance['item_level'] . '</td>
							<td>' . $instance['quest_a'] . '</td>
							<td>' . $instance['quest_h'] . '</td>
                        </tr>';
        }
        $output .= '
                        <tr>
                            <td colspan="8" class="hidden" align="right" width="25%">';

        // multi page links
        $output .= generate_pagination(
            'instances.php?order_by=' . $orderByColumn . '&amp;dir=' . (($dir) ? 0 : 1),
            $instanceCount,
            $itemsPerPage,
            $start
        );

        $output .= '
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8" class="hidden" align="right">' . $lang_instances['total'] . ' : ' . $instanceCount . '</td>
                        </tr>
                    </table>
                </center>';
        return $output;
    }
}
