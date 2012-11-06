<?php

namespace Smartkill\APIBundle\Entity;

use Smartkill\WebBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="api_session")
 */
class Session {
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", unique=true, length=40)
	 */
    private $id;
    
    /**
    * @ORM\ManyToOne(targetEntity="Smartkill\WebBundle\Entity\User", inversedBy="sessions")
    * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
    */
    private $user;
    
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
	public function __construct()
    {
        $this->id = md5(uniqid(null, true));
        $this->createdAt = new \DateTime();
    }
    
    /**
    * Get id
    *
    * @return string
    */
    public function getId() {
    	return $this->id;
    }
    
    /**
     * Set user
     *
     * @param User $user
     * @return Session
     */
    public function setUser($user) {
    	$this->user = $user;
    
    	return $this;
    }
    
    /**
     * Get user
     *
     * @return User
     */
    public function getUser() {
    	return $this->user;
    }
    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Session
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
}
