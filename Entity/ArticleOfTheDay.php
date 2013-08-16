<?php
/**
 * @package Newscoop\ArticlesCalendarBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesCalendarBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Article of the day entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="plugin_articles_calendar_article_of_the_day")
 */
class ArticleOfTheDay 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", name="id")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Article")
     * @ORM\JoinColumns({
     *      @ORM\JoinColumn(name="article_number", referencedColumnName="Number"),
     *      @ORM\JoinColumn(name="article_language_id", referencedColumnName="IdLanguage")
     *  })
     */
    private $article;

    /**
     * @ORM\Column(type="integer", name="publication_id")
     */
    private $publicationId;

    /**
     * @ORM\Column(type="integer", name="article_number")
     */
    private $articleNumber;

    /**
     * @ORM\Column(type="integer", name="article_language_id")
     * @var int
     */
    private $articleLanguageId;

    /**
     * @ORM\Column(type="datetime", name="date")
     * @var int
     */
    private $date;

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
     * Get publicationId
     *
     * @return integer
     */
    public function getPublicationId()
    {
        return $this->publicationId;
    }

    /**
     * Set publicationId
     *
     * @param integer $publicationId
     *
     */
    public function setPublicationId($publicationId)
    {
        $this->publicationId = $publicationId;

        return $this;
    }

    /**
     * Get article
     *
     * @return string
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set article
     *
     * @param integer $article
     */
    public function setArticle($article)
    {
        $this->article = $article;
        
        return $this;
    }

    /**
     * Get date
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        
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