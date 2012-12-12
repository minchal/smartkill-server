<?php

namespace Smartkill\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\SerializerBundle\Annotation\Accessor;

/**
 * Uwaga: ta tabela wyjątkowo nazywa się "matches" bo "match" to słowo kluczowe MYSQL!!!
 * 
 * @ORM\Entity
 * @ORM\Table(name="matches")
 * @ORM\Entity(repositoryClass="Smartkill\WebBundle\Entity\MatchRepository")
 */
class Match {
	
	const PLANED   = 'planed';
	const GOINGON  = 'goingon';
	const FINISHED = 'finished';
	
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('planed', 'goingon', 'finished')")
     */
    private $status;
    
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
     * @ORM\Column(type="datetime", name="start_date", nullable=true)
     */
    private $startDate;
    
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
    
    /**
     * @ORM\OneToMany(targetEntity="Smartkill\WebBundle\Entity\Event", mappedBy="match", cascade="remove")
     */
    private $events;
    
    /**
     * @ORM\OneToMany(targetEntity="Smartkill\WebBundle\Entity\Package", mappedBy="match", cascade="remove")
     */
    private $packages;
    
    /**
     * Ilość paczek na km2
     * 
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $density = 10;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $pkgTime = true;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $pkgShield = true;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $pkgSnipe = true;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $pkgSwitch = true;
    
    
	public function __construct() {
        $this->dueDate = new \DateTime();
    }
    
    /**
     * Powierzchnia obszaru gry w km2.
     * 
     * @return number
     */
    public function getArea() {
		return pow($this->getSize()/1000,2)*pi();
	}
	
    public function getLengthDesc()
    {
		$h = floor($this->length / 60);
		$m = $this->length % 60;
        return $h.':'.($m<10?'0':'').$m;
    }
    
    public function isFull() {
		return $this->getPlayers()->count() >= $this->getMaxPlayers();
	}
    
    public function isStatus($status) {
		return $this->getStatus() == $status;
	}
    
    public function getCreatedById()
    {
    	return $this->getCreatedBy() ? $this->getCreatedBy()->getId() : null;
    }
    
    /**
     * Rozpoczęcie mecz:
     *  - zablokowanie edycji (zmiana statusu)
     *  - ustawienie czasu rozpoczęcia
     *  - wylosowanie paczek
     */
    public function start(EntityManager $em) {
		if ($this->status != self::PLANED) {
			throw new \LogicException('Pylko planowany mecz może zostać rozpoczęty!');
		}
		
		$this->status    = self::GOINGON;
		$this->startDate = new \DateTime();
		$em -> persist($this);
		
		$pkgs = $this->getPackagesTypes();
		$count = round($this->getArea() * $this->getDensity());
		
		if ($pkgs && $count) {
			for ($i=0; $i<$count; $i++) {
				$pkg = Package::createRandom($this->getLat(), $this->getLng(), $this->getSize(), $pkgs);
				$pkg -> setMatch($this);
				$em -> persist($pkg);
			}
		}
		
		//$em -> flush();
	}
	
	private function getPackagesTypes() {
		$types = array();
		
		foreach (Package::getTypes() as $type) {
			$m = 'getPkg'.ucfirst($type);
			if ($this->$m()) {
				$types[] = $type;
			}
		}
		
		return $types;
	}
	
	/**
	 * Zakończenie meczu:
	 *  - zmiana statusu
	 *  - dodanie użytkownikom zdobytych punktów
	 */
	public function finish() {
		if ($this->status != self::GOINGON) {
			throw new \LogicException('Pylko trwający mecz może zostać zakończony!');
		}
		
		$this->status = self::FINISHED;
		$em -> persist($this);
		
		
		
		
		$em -> flush();
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
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
    	return $this->status;
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

    /**
     * Add events
     *
     * @param \Smartkill\WebBundle\Entity\Event $events
     * @return Match
     */
    public function addEvent(\Smartkill\WebBundle\Entity\Event $events)
    {
        $this->events[] = $events;
    
        return $this;
    }

    /**
     * Remove events
     *
     * @param \Smartkill\WebBundle\Entity\Event $events
     */
    public function removeEvent(\Smartkill\WebBundle\Entity\Event $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add packages
     *
     * @param \Smartkill\WebBundle\Entity\Package $packages
     * @return Match
     */
    public function addPackage(\Smartkill\WebBundle\Entity\Package $packages)
    {
        $this->packages[] = $packages;
    
        return $this;
    }

    /**
     * Remove packages
     *
     * @param \Smartkill\WebBundle\Entity\Package $packages
     */
    public function removePackage(\Smartkill\WebBundle\Entity\Package $packages)
    {
        $this->packages->removeElement($packages);
    }

    /**
     * Get packages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * Set density
     *
     * @param float $density
     * @return Match
     */
    public function setDensity($density)
    {
        $this->density = $density;
    
        return $this;
    }

    /**
     * Get density
     *
     * @return float 
     */
    public function getDensity()
    {
        return $this->density;
    }

    /**
     * Set pkgTime
     *
     * @param boolean $pkgTime
     * @return Match
     */
    public function setPkgTime($pkgTime)
    {
        $this->pkgTime = $pkgTime;
    
        return $this;
    }

    /**
     * Get pkgTime
     *
     * @return boolean 
     */
    public function getPkgTime()
    {
        return $this->pkgTime;
    }

    /**
     * Set pkgShield
     *
     * @param boolean $pkgShield
     * @return Match
     */
    public function setPkgShield($pkgShield)
    {
        $this->pkgShield = $pkgShield;
    
        return $this;
    }

    /**
     * Get pkgShield
     *
     * @return boolean 
     */
    public function getPkgShield()
    {
        return $this->pkgShield;
    }

    /**
     * Set pkgSnipe
     *
     * @param boolean $pkgSnipe
     * @return Match
     */
    public function setPkgSnipe($pkgSnipe)
    {
        $this->pkgSnipe = $pkgSnipe;
    
        return $this;
    }

    /**
     * Get pkgSnipe
     *
     * @return boolean 
     */
    public function getPkgSnipe()
    {
        return $this->pkgSnipe;
    }

    /**
     * Set pkgSwitch
     *
     * @param boolean $pkgSwitch
     * @return Match
     */
    public function setPkgSwitch($pkgSwitch)
    {
        $this->pkgSwitch = $pkgSwitch;
    
        return $this;
    }

    /**
     * Get pkgSwitch
     *
     * @return boolean 
     */
    public function getPkgSwitch()
    {
        return $this->pkgSwitch;
    }
}
