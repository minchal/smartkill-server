<?php

namespace Smartkill\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="package")
 */
class Package {
	
	const AVAILABLE = 'available';
	const FOUND     = 'found';
	
	const T_TIME   = 'time';
	const T_SHIELD = 'shield';
	const T_SNIPE  = 'snipe';
	const T_SWITCH = 'switch';
	
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Smartkill\WebBundle\Entity\Match", inversedBy="packages")
     * @ORM\JoinColumn(name="match_id", referencedColumnName="id")
     */
    private $match;
    
    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('available', 'found')")
     */
    private $status;
    
    /**
     * @ORM\Column(type="decimal", scale=6)
     */
    private $lat;
    
    /**
     * @ORM\Column(type="decimal", scale=6)
     */
    private $lng;
    
    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('time','shield','snipe','switch')")
     */
    private $type;
    

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
     * Set status
     *
     * @param string $status
     * @return Package
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set lat
     *
     * @param float $lat
     * @return Package
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
     * @return Package
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
     * @return Package
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

    /**
     * Set type
     *
     * @param string $type
     * @return Package
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
}