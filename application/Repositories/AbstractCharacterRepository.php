<?php


namespace Application\Repositories;


use Application\ViewModels\User;
use PDO;

abstract class AbstractCharacterRepository extends AbstractRepository
{
    /**
     * @var User
     */
    protected $user;

    public function setUser(User $user){
        $this->user = $user;
    }

}
