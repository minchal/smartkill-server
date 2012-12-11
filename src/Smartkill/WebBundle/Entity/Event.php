<?php

namespace Smartkill\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="event")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"catch" = "EventCatch", "found" = "EventFound"})
 */
class Event {
	
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Smartkill\WebBundle\Entity\Match", inversedBy="events")
     * @ORM\JoinColumn(name="match_id", referencedColumnName="id")
     */
    protected $match;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $date;
    
    /**
     * @ORM\Column(type="decimal", scale=6)
     */
    protected $lat;
    
    /**
     * @ORM\Column(type="decimal", scale=6)
     */
    protected $lng;
    
	public function __construct() {
        $this->date = new \DateTime();
    }
    
    /**
     * Tylko dla Twiga :/
     */
    public function getType() {
		return strtolower(substr(get_class($this), strlen(__CLASS__)));
	}
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Event
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set lat
     *
     * @param float $lat
     * @return Event
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    
        return $this;
    }

    /**
     * Get lat
     *
     * @return float 
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param float $lng
     * @return Event
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    
        return $this;
    }

    /**
     * Get lng
     *
     * @return float 
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set match
     *
     * @param \Smartkill\WebBundle\Entity\Match $match
     * @return Event
     */
    public function setMatch(\Smartkill\WebBundle\Entity\Match $match = null)
    {
        $this->match = $match;
    
        return $this;
    }

    /**
     * Get match
     *
     * @return \Smartkill\WebBundle\Entity\Match 
     */
    public function getMatch()
    {
        return $this->match;
    }
}
