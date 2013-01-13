<?php

namespace Smartkill\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\SerializerBundle\Annotation;

/**
 * @ORM\Entity
 * @ORM\Table(name="match_user")
 * @ORM\Entity(repositoryClass="Smartkill\WebBundle\Entity\MatchUserRepository")
 */
class MatchUser {
	
	const TYPE_PREY = 'prey';
	const TYPE_HUNTER = 'hunter';
	
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Smartkill\WebBundle\Entity\User", inversedBy="playedMatches")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Annotation\Accessor(getter="getUserId")
     */
    protected $user;
    
     /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Smartkill\WebBundle\Entity\Match", inversedBy="players")
     * @ORM\JoinColumn(name="match_id", referencedColumnName="id")
     * @Annotation\Accessor(getter="getMatchId")
     */
    private $match;
    
    /**
     * @ORM\Column(type="string", columnDefinition="ENUM('prey', 'hunter')")
     */
    private $type;
    
    /**
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    private $lat;
    
    /**
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    private $lng;
    
    /**
     * @ORM\Column(type="integer")
     */
    private $offense = 0;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $disqualification = false;
    
    /**
     * @ORM\Column(type="datetime", name="updated_at", nullable=true)
     */
    private $updatedAt;

    /**
    * @ORM\Column(name="points_prey", type="integer")
    */
    private $pointsPrey = 0;
    
    /**
     * @ORM\Column(name="points_hunter", type="integer")
     */
    private $pointsHunter = 0;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $alive = true;
	
    public function getUserId()
    {
    	return $this->getUser() ? $this->getUser()->getId() : null;
    }
    
    public function getMatchId()
    {
    	return $this->getMatch() ? $this->getMatch()->getId() : null;
    }
    
    /**
     * Set type
     *
     * @param string $type
     * @return MatchUser
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

    /**
     * Set lat
     *
     * @param float $lat
     * @return MatchUser
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
     * @return MatchUser
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
     * Set offense
     *
     * @param integer $offense
     * @return MatchUser
     */
    public function setOffense($offense)
    {
        $this->offense = $offense;
    
        return $this;
    }

    /**
     * Get offense
     *
     * @return integer 
     */
    public function getOffense()
    {
        return $this->offense;
    }

    /**
     * Set disqualification
     *
     * @param boolean $disqualification
     * @return MatchUser
     */
    public function setDisqualification($disqualification)
    {
        $this->disqualification = $disqualification;
    
        return $this;
    }

    /**
     * Get disqualification
     *
     * @return boolean 
     */
    public function getDisqualification()
    {
        return $this->disqualification;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return MatchUser
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set user
     *
     * @param integer $user
     * @return MatchUser
     */
    public function setUser($user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set match
     *
     * @param integer $match
     * @return MatchUser
     */
    public function setMatch($match)
    {
        $this->match = $match;
    
        return $this;
    }

    /**
     * Get match
     *
     * @return integer 
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * Set pointsPrey
     *
     * @param integer $pointsPrey
     * @return MatchUser
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
     * @return MatchUser
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
     * Set alive
     *
     * @param boolean $alive
     * @return MatchUser
     */
    public function setAlive($alive)
    {
        $this->alive = $alive;
    
        return $this;
    }

    /**
     * Get alive
     *
     * @return boolean 
     */
    public function getAlive()
    {
        return $this->alive;
    }
}