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
     */
    private $name;
    
    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Assert\MinLength(limit=5)
     */
    private $password;
    
    /**
     * @ORM\Column(type="decimal", scale=2)
     * @Assert\NotBlank()
     */
    private $lat;
    
    /**
     * @ORM\Column(type="decimal", scale=2)
     * @Assert\NotBlank()
     */
    private $lng;
    
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $size;
    
    /**
    * @ORM\Column(type="integer")
    * @Assert\NotBlank()
    */
    private $length;
    
    /**
     * @ORM\Column(type="datetime", name="due_date")
     */
    private $dueDate;
    
    /**
     * @ORM\Column(type="integer", name="max_players")
     */
    private $maxPlayers = 2;
    
    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;
    
    /**
     * @ORM\ManyToOne(targetEntity="Smartkill\WebBundle\Entity\User", inversedBy="matches")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @Accessor(getter="getCreatedById")
     */
    private $createdBy;
	
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
}
