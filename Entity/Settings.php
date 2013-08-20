<?php
/**
 * @package Newscoop\PaywallBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesCalendarBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Article of the day settings entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="plugin_articles_calendar_settings")
 */
class Settings 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="id")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="firstDay")
     * @var int
     */
    private $firstDay;

    /**
     * @ORM\Column(type="boolean", name="showDayNames")
     * @var bool
     */
    private $showDayNames;

    /**
     * @ORM\Column(type="boolean", name="navigation")
     * @var bool
     */
    private $navigation;

    /**
     * @ORM\Column(type="integer", name="imageWidth", nullable=true)
     * @var int
     */
    private $imageWidth;

    /**
     * @ORM\Column(type="integer", name="imageHeight", nullable=true)
     * @var int
     */
    private $imageHeight;

    /**
     * @ORM\Column(type="string", name="rendition")
     * @var string
     */
    private $rendition;

    /**
     * @ORM\Column(type="string", name="view")
     * @var string
     */
    private $view;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     * @var string
     */
    private $created_at;

    /**
     * @ORM\Column(type="boolean", name="is_active")
     * @var boolean
     */
    private $is_active;

    public function __construct() {
        $this->setCreatedAt(new \DateTime());
        $this->setIsActive(true);
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
     * Get firstDay
     *
     * @return int
     */
    public function getFirstDay()
    {
        return $this->firstDay;
    }

    /**
     * Set firstDay
     *
     * @param  int $firstDay
     * @return int
     */
    public function setFirstDay($firstDay)
    {
        $this->firstDay = $firstDay;
        
        return $firstDay;
    }

    /**
     * Get showDayNames
     *
     * @return bool
     */
    public function getShowDayNames()
    {
        return $this->showDayNames;
    }

    /**
     * Set showDayNames
     *
     * @param  bool $showDayNames
     * @return bool
     */
    public function setShowDayNames($showDayNames)
    {
        $this->showDayNames = $showDayNames;
        
        return $this;
    }

    /**
     * Get navigation
     *
     * @return bool
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * Set navigation
     *
     * @param  bool $navigation
     * @return bool
     */
    public function setNavigation($navigation)
    {
        $this->navigation = $navigation;
        
        return $this;
    }

    /**
     * Get imageWidth
     *
     * @return int
     */
    public function getImageWidth()
    {
        return $this->imageWidth;
    }

    /**
     * Set imageWidth
     *
     * @param  int $imageWidth
     * @return int
     */
    public function setImageWidth($imageWidth)
    {
        $this->imageWidth = $imageWidth;
        
        return $this;
    }

    /**
     * Get imageHeight
     *
     * @return int
     */
    public function getImageHeight()
    {
        return $this->imageHeight;
    }

    /**
     * Set imageHeight
     *
     * @param  int $imageHeight
     * @return int
     */
    public function setImageHeight($imageHeight)
    {
        $this->imageHeight = $imageHeight;
        
        return $this;
    }

    /**
     * Get rendition
     *
     * @return string
     */
    public function getRendition()
    {
        return $this->rendition;
    }

    /**
     * Set rendition
     *
     * @param  string $rendition
     * @return string
     */
    public function setRendition($rendition)
    {
        $this->rendition = $rendition;
        
        return $this;
    }

    /**
     * Get view
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Set view
     *
     * @param  string $view
     * @return string
     */
    public function setView($view)
    {
        $this->view = $view;
        
        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Set status
     *
     * @param boolean $is_active
     * @return boolean
     */
    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;
        
        return $this;
    }

    /**
     * Get create date
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set create date
     *
     * @param datetime $created_at
     * @return datetime
     */
    public function setCreatedAt(\DateTime $created_at)
    {
        $this->created_at = $created_at;
        
        return $this;
    }
}