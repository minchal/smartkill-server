<?php

namespace Smartkill\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Smartkill\WebBundle\Entity\UserRepository")
 */
class User implements UserInterface {
	
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $admin;
    
    /**
     * @ORM\Column(type="string", unique=true, length=100)
     * @Assert\NotBlank()
     */
    protected $username;
    
    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\NotBlank()
     */
    protected $pass;
    
    /**
     * @ORM\Column(type="string", unique=true, length=100)
     * @Assert\NotBlank()
     * @Assert\Email(message = "Podany adres e-mail jest nieprawidłowy.")
     */
    protected $email;
    
    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $avatar;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $registeredAt;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $points;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $matchesPrey;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $matchesHunter;

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
     * Set admin
     *
     * @param boolean $admin
     * @return User
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
    
        return $this;
    }

    /**
     * Get admin
     *
     * @return boolean 
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set login
     *
     * @param string $login
     * @return User
     */
    public function setLogin($login)
    {
        $this->login = $login;
    
        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set pass
     *
     * @param string $pass
     * @return User
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
    
        return $this;
    }

    /**
     * Get pass
     *
     * @return string 
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    
        return $this;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set registeredAt
     *
     * @param \DateTime $registeredAt
     * @return User
     */
    public function setRegisteredAt($registeredAt)
    {
        $this->registeredAt = $registeredAt;
    
        return $this;
    }

    /**
     * Get registeredAt
     *
     * @return \DateTime 
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * Set points
     *
     * @param integer $points
     * @return User
     */
    public function setPoints($points)
    {
        $this->points = $points;
    
        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set matchesPrey
     *
     * @param integer $matchesPrey
     * @return User
     */
    public function setMatchesPrey($matchesPrey)
    {
        $this->matchesPrey = $matchesPrey;
    
        return $this;
    }

    /**
     * Get matchesPrey
     *
     * @return integer 
     */
    public function getMatchesPrey()
    {
        return $this->matchesPrey;
    }

    /**
     * Set matchesHunter
     *
     * @param integer $matchesHunter
     * @return User
     */
    public function setMatchesHunter($matchesHunter)
    {
        $this->matchesHunter = $matchesHunter;
    
        return $this;
    }

    /**
     * Get matchesHunter
     *
     * @return integer 
     */
    public function getMatchesHunter()
    {
        return $this->matchesHunter;
    }
}
