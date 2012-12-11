<?php

namespace Smartkill\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="event_found")
 */
class EventFound extends Event {
	
    /**
     * @ORM\ManyToOne(targetEntity="Smartkill\WebBundle\Entity\User")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     */
    private $player;
    
    /**
     * @ORM\ManyToOne(targetEntity="Smartkill\WebBundle\Entity\Package")
     * @ORM\JoinColumn(name="package_id", referencedColumnName="id")
     */
    private $package;
    

    /**
     * Set player
     *
     * @param \Smartkill\WebBundle\Entity\User $player
     * @return EventFound
     */
    public function setPlayer(\Smartkill\WebBundle\Entity\User $player = null)
    {
        $this->player = $player;
    
        return $this;
    }

    /**
     * Get player
     *
     * @return \Smartkill\WebBundle\Entity\User 
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set package
     *
     * @param \Smartkill\WebBundle\Entity\Package $package
     * @return EventFound
     */
    public function setPackage(\Smartkill\WebBundle\Entity\Package $package = null)
    {
        $this->package = $package;
    
        return $this;
    }

    /**
     * Get package
     *
     * @return \Smartkill\WebBundle\Entity\Package 
     */
    public function getPackage()
    {
        return $this->package;
    }
}
