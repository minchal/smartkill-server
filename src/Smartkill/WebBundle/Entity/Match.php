<?php

namespace Smartkill\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\SerializerBundle\Annotation\Accessor;

/**
 * @ORM\Entity
 * @ORM\Table(name="matches")
 * @ORM\Entity(repositoryClass="Smartkill\WebBundle\Entity\MatchRepository")
 */
class Match {
	
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank()
     * @Assert\Length(max=150)
     */
    private $name;
    
    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     * @Assert\Length(max=1000)
     */
    private $descr;
    
    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Assert\Length(max=30)
     */
    private $password;
    
    /**
     * @ORM\Column(type="decimal", scale=6)
     * @Assert\NotBlank()
     */
    private $lat;
    
    /**
     * @ORM\Column(type="decimal", scale=6)
     * @Assert\NotBlank()
     */
    private $lng;
    
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(min=500, max=5000)
     */
    private $size;
    
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(min=30, max=180)
     */
    private $length;
    
    /**
     * @ORM\Column(type="datetime", name="due_date")
     */
    private $dueDate;
    
    /**
     * @ORM\Column(type="integer", name="max_players")
     * @Assert\NotBlank()
     * @Assert\Range(min=2, max=20)
     */
    private $maxPlayers = 2;
    
    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;
    
    /**
     * @ORM\ManyToOne(targetEntity="Smartkill\WebBundle\Entity\User", inversedBy="createdMatches")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @Accessor(getter="getCreatedById")
     */
    private $createdBy;
	
    /**
     * @ORM\OneToMany(targetEntity="Smartkill\WebBundle\Entity\MatchUser", mappedBy="match", cascade="remove")
     */
    private $players;
    
    
	public function __construct() {
        $this->dueDate = new \DateTime();
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
     * Set name
     *
     * @param string $name
     * @return Match
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Match
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }
	
	public function hasPassword() {
		return (boolean) $this->password;
	}
	
    /**
     * Set lat
     *
     * @param double $lat
     * @return Match
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    
        return $this;
    }

    /**
     * Get lat
     *
     * @return double 
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param double $lng
     * @return Match
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    
        return $this;
    }

    /**
     * Get lng
     *
     * @return double 
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set size
     *
     * @param double $size
     * @return Match
     */
    public function setSize($size)
    {
        $this->size = $size;
    
        return $this;
    }

    /**
     * Get size
     *
     * @return double 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set length
     *
     * @param integer $length
     * @return Match
     */
    public function setLength($length)
    {
        $this->length = $length;
    
        return $this;
    }

    /**
     * Get length
     *
     * @return integer 
     */
    public function getLength()
    {
        return $this->length;
    }
    
    public function getLengthDesc()
    {
		$h = floor($this->length / 60);
		$m = $this->length % 60;
        return $h.':'.($m<10?'0':'').$m;
    }

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate
     * @return Match
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;
    
        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime 
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set maxPlayers
     *
     * @param integer $maxPlayers
     * @return Match
     */
    public function setMaxPlayers($maxPlayers)
    {
        $this->maxPlayers = $maxPlayers;
    
        return $this;
    }

    /**
     * Get maxPlayers
     *
     * @return integer 
     */
    public function getMaxPlayers()
    {
        return $this->maxPlayers;
    }
    
    public function isFull() {
		return $this->getPlayers()->count() >= $this->getMaxPlayers();
	}

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Match
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param \Smartkill\WebBundle\Entity\User $createdBy
     * @return Match
     */
    public function setCreatedBy(\Smartkill\WebBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;
    
        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Smartkill\WebBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
    
    public function getCreatedById()
    {
    	return $this->getCreatedBy() ? $this->getCreatedBy()->getId() : null;
    }

    /**
     * Add players
     *
     * @param \Smartkill\WebBundle\Entity\MatchUser $players
     * @return Match
     */
    public function addPlayer(\Smartkill\WebBundle\Entity\MatchUser $players)
    {
        $this->players[] = $players;
    
        return $this;
    }

    /**
     * Remove players
     *
     * @param \Smartkill\WebBundle\Entity\MatchUser $players
     */
    public function removePlayer(\Smartkill\WebBundle\Entity\MatchUser $players)
    {
        $this->players->removeElement($players);
    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Set desc
     *
     * @param string $desc
     * @return Match
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    
        return $this;
    }

    /**
     * Get desc
     *
     * @return string 
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * Set descr
     *
     * @param string $descr
     * @return Match
     */
    public function setDescr($descr)
    {
        $this->descr = $descr;
    
        return $this;
    }

    /**
     * Get descr
     *
     * @return string 
     */
    public function getDescr()
    {
        return $this->descr;
    }
}
