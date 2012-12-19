<?php

namespace Smartkill\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="event_catch")
 */
class EventCatch extends Event {
	
    /**
     * @ORM\ManyToOne(targetEntity="Smartkill\WebBundle\Entity\User")
     * @ORM\JoinColumn(name="hunter_id", referencedColumnName="id")
     */
    private $hunter;
    
    /**
     * @ORM\ManyToOne(targetEntity="Smartkill\WebBundle\Entity\User")
     * @ORM\JoinColumn(name="prey_id", referencedColumnName="id")
     */
    private $prey;
    
    /**
     * Set hunter
     *
     * @param \Smartkill\WebBundle\Entity\User $hunter
     * @return EventCatch
     */
    public function setHunter(\Smartkill\WebBundle\Entity\User $hunter = null)
    {
        $this->hunter = $hunter;
    
        return $this;
    }

    /**
     * Get hunter
     *
     * @return \Smartkill\WebBundle\Entity\User 
     */
    public function getHunter()
    {
        return $this->hunter;
    }

    /**
     * Set prey
     *
     * @param \Smartkill\WebBundle\Entity\User $prey
     * @return EventCatch
     */
    public function setPrey(\Smartkill\WebBundle\Entity\User $prey = null)
    {
        $this->prey = $prey;
    
        return $this;
    }

    /**
     * Get prey
     *
     * @return \Smartkill\WebBundle\Entity\User 
     */
    public function getPrey()
    {
        return $this->prey;
    }
}
