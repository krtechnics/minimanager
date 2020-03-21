<?php


namespace Application\ViewModels;


class ServerConfiguration
{
    /**
     * @var string
     */
    private $addres;
    /**
     * @var string
     */
    private $addresWan;
    /**
     * @var integer
     */
    private $gamePort;
    /**
     * @var string
     */
    private $terminalType;
    /**
     * @var integer
     */
    private $terminalPort;
    private $telnetPort;
    private $telnetUser;
    private $telnetPassword;
    private $bothFactions;
    private $talentRate;

    public static function convertArrayToConfiguration($serverConfigurationArray): self
    {
        $serverConfiguration = new self();
        $serverConfiguration->setAddres($serverConfigurationArray['addr']);
        $serverConfiguration->setAddresWan($serverConfigurationArray['addr_wan']);
        $serverConfiguration->setGamePort($serverConfigurationArray['game_port']);
        $serverConfiguration->setTerminalType($serverConfigurationArray['term_type']);
        $serverConfiguration->setTelnetPort($serverConfigurationArray['telnet_port']);
        $serverConfiguration->setTelnetUser($serverConfigurationArray['telnet_user']);
        $serverConfiguration->setTelnetPassword($serverConfigurationArray['telnet_pass']);
        $serverConfiguration->setBothFactions($serverConfigurationArray['both_factions']);
        $serverConfiguration->setTalentRate($serverConfigurationArray['talent_rate']);
        return $serverConfiguration;
    }

    /**
     * @return mixed
     */
    public function getAddres()
    {
        return $this->addres;
    }

    /**
     * @param mixed $addres
     */
    public function setAddres($addres): void
    {
        $this->addres = $addres;
    }

    /**
     * @return mixed
     */
    public function getAddresWan()
    {
        return $this->addresWan;
    }

    /**
     * @param mixed $addresWan
     */
    public function setAddresWan($addresWan): void
    {
        $this->addresWan = $addresWan;
    }

    /**
     * @return mixed
     */
    public function getGamePort()
    {
        return $this->gamePort;
    }

    /**
     * @param mixed $gamePort
     */
    public function setGamePort($gamePort): void
    {
        $this->gamePort = $gamePort;
    }

    /**
     * @return mixed
     */
    public function getTerminalType()
    {
        return $this->terminalType;
    }

    /**
     * @param mixed $terminalType
     */
    public function setTerminalType($terminalType): void
    {
        $this->terminalType = $terminalType;
    }

    /**
     * @return mixed
     */
    public function getTerminalPort()
    {
        return $this->terminalPort;
    }

    /**
     * @param mixed $terminalPort
     */
    public function setTerminalPort($terminalPort): void
    {
        $this->terminalPort = $terminalPort;
    }

    /**
     * @return mixed
     */
    public function getTelnetPort()
    {
        return $this->telnetPort;
    }

    /**
     * @param mixed $telnetPort
     */
    public function setTelnetPort($telnetPort): void
    {
        $this->telnetPort = $telnetPort;
    }

    /**
     * @return mixed
     */
    public function getTelnetUser()
    {
        return $this->telnetUser;
    }

    /**
     * @param mixed $telnetUser
     */
    public function setTelnetUser($telnetUser): void
    {
        $this->telnetUser = $telnetUser;
    }

    /**
     * @return mixed
     */
    public function getTelnetPassword()
    {
        return $this->telnetPassword;
    }

    /**
     * @param mixed $telnetPassword
     */
    public function setTelnetPassword($telnetPassword): void
    {
        $this->telnetPassword = $telnetPassword;
    }

    /**
     * @return mixed
     */
    public function getBothFactions()
    {
        return $this->bothFactions;
    }

    /**
     * @param mixed $bothFactions
     */
    public function setBothFactions($bothFactions): void
    {
        $this->bothFactions = $bothFactions;
    }

    /**
     * @return mixed
     */
    public function getTalentRate()
    {
        return $this->talentRate;
    }

    /**
     * @param mixed $talentRate
     */
    public function setTalentRate($talentRate): void
    {
        $this->talentRate = $talentRate;
    }
}
