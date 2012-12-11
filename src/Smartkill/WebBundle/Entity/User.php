<?php

namespace Smartkill\WebBundle\Entity;

use Smartkill\APIBundle\Entity\Session;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Smartkill\WebBundle\Entity\UserRepository")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User implements UserInterface {
	
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", unique=true, length=100)
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[-_a-zA-Z0-9]+$/", message="Pole może zawierać tylko litery, cyfry, '_' oraz '-'!")
     */
    private $username;
    
    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;
    
    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\MinLength(limit=5)
     */
    private $password;
    
    public $oldPassword;
    
    /**
     * @ORM\Column(type="string", unique=true, length=100)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;
    
    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive = true;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $admin = false;
    
    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $avatar;
    
    /**
     * @Assert\Image(maxSize="500k")
     */
    public $avatarFile;
    
    /**
     * @ORM\Column(name="registered_at", type="datetime")
     */
    private $registeredAt;
    
    /**
    * @ORM\Column(name="points_prey", type="integer")
    */
    private $pointsPrey = 0;
    
    /**
     * @ORM\Column(name="points_hunter", type="integer")
     */
    private $pointsHunter = 0;
    
    /**
     * @ORM\Column(name="matches_prey", type="integer")
     */
    private $matchesPrey = 0;
    
    /**
     * @ORM\Column(name="matches_hunter", type="integer")
     */
    private $matchesHunter = 0;
    
    /**
     * @ORM\OneToMany(targetEntity="Smartkill\APIBundle\Entity\Session", mappedBy="user", cascade="remove")
     */
    protected $sessions;
    
    /**
     * @ORM\OneToMany(targetEntity="Smartkill\WebBundle\Entity\Match", mappedBy="createdBy", cascade="remove")
     */
    protected $createdMatches;
    
    /**
     * @ORM\OneToMany(targetEntity="Smartkill\WebBundle\Entity\MatchUser", mappedBy="user", cascade="remove")
     */
    protected $playedMatches;
	
	public function __construct()
    {
        $this->salt = md5(uniqid(null, true));
        $this->sessions = new ArrayCollection();
    }
    
    public function getAvatarUrl()  {
        return $this->getUploadDir() . ($this->avatar ? $this->avatar : 'default.png');
    }
    
    public function getAvatarPath() {
        return $this->getUploadRootDir() . ($this->avatar ? $this->avatar : 'default.png');
    }
	
    protected function getUploadRootDir() {
        return '.'.$this->getUploadDir();
    }
	
    protected function getUploadDir() {
        return '/media/avatar/';
    }
    
    public function uploadAvatar() {
		if ($this->avatarFile == null) {
			return;
		}
		
		$filename = $this->username.'.'.$this->avatarFile->guessExtension();
        
		$this->avatarFile->move(
			$this->getUploadRootDir(),
			$filename
		);
		
		$this->avatar = $filename;
		
		$this->avatarFile = null;
	}
    
    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return $this->admin ? array('ROLE_ADMIN','ROLE_USER') : array('ROLE_USER');
    }
	
    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($login)
    {
        $this->username = $login;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
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
     * Set salt
     *
     * @param string $username
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
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

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add sessions
     *
     * @param Smartkill\APIBundle\Entity\Session $sessions
     * @return User
     */
    public function addSession(\Smartkill\APIBundle\Entity\Session $sessions)
    {
        $this->sessions[] = $sessions;
    
        return $this;
    }

    /**
     * Remove sessions
     *
     * @param Smartkill\APIBundle\Entity\Session $sessions
     */
    public function removeSession(\Smartkill\APIBundle\Entity\Session $sessions)
    {
        $this->sessions->removeElement($sessions);
    }

    /**
     * Get sessions
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * Add matches
     *
     * @param \Smartkill\WebBundle\Entity\Match $matches
     * @return User
     */
    public function addMatche(\Smartkill\WebBundle\Entity\Match $matches)
    {
        $this->matches[] = $matches;
    
        return $this;
    }

    /**
     * Remove matches
     *
     * @param \Smartkill\WebBundle\Entity\Match $matches
     */
    public function removeMatche(\Smartkill\WebBundle\Entity\Match $matches)
    {
        $this->matches->removeElement($matches);
    }

    /**
     * Get matches
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * Set pointsPrey
     *
     * @param integer $pointsPrey
     * @return User
     */
    public function setPointsPrey($pointsPrey)
    {
        $this->pointsPrey = $pointsPrey;
    
        return $this;
    }

    /**
     * Get pointsPrey
     *
     * @return integer 
     */
    public function getPointsPrey()
    {
        return $this->pointsPrey;
    }

    /**
     * Set pointsHunter
     *
     * @param integer $pointsHunter
     * @return User
     */
    public function setPointsHunter($pointsHunter)
    {
        $this->pointsHunter = $pointsHunter;
    
        return $this;
    }

    /**
     * Get pointsHunter
     *
     * @return integer 
     */
    public function getPointsHunter()
    {
        return $this->pointsHunter;
    }

    /**
     * Add createdMatches
     *
     * @param \Smartkill\WebBundle\Entity\Match $createdMatches
     * @return User
     */
    public function addCreatedMatche(\Smartkill\WebBundle\Entity\Match $createdMatches)
    {
        $this->createdMatches[] = $createdMatches;
    
        return $this;
    }

    /**
     * Remove createdMatches
     *
     * @param \Smartkill\WebBundle\Entity\Match $createdMatches
     */
    public function removeCreatedMatche(\Smartkill\WebBundle\Entity\Match $createdMatches)
    {
        $this->createdMatches->removeElement($createdMatches);
    }

    /**
     * Get createdMatches
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCreatedMatches()
    {
        return $this->createdMatches;
    }

    /**
     * Add playedMatches
     *
     * @param \Smartkill\WebBundle\Entity\MatchUser $playedMatches
     * @return User
     */
    public function addPlayedMatche(\Smartkill\WebBundle\Entity\MatchUser $playedMatches)
    {
        $this->playedMatches[] = $playedMatches;
    
        return $this;
    }

    /**
     * Remove playedMatches
     *
     * @param \Smartkill\WebBundle\Entity\MatchUser $playedMatches
     */
    public function removePlayedMatche(\Smartkill\WebBundle\Entity\MatchUser $playedMatches)
    {
        $this->playedMatches->removeElement($playedMatches);
    }

    /**
     * Get playedMatches
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlayedMatches()
    {
        return $this->playedMatches;
    }
}
