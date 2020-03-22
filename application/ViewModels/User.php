<?php


namespace Application\ViewModels;


class User
{
    private $userID;
    private $userLevel;

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @param mixed $userID
     */
    public function setUserID($userID): void
    {
        $this->userID = $userID;
    }

    /**
     * @return mixed
     */
    public function getUserLevel()
    {
        return $this->userLevel;
    }

    /**
     * @param mixed $userLevel
     */
    public function setUserLevel($userLevel): void
    {
        $this->userLevel = $userLevel;
    }


}
