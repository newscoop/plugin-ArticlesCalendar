<?php
/**
 * @package Newscoop\ArticlesCalendarBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesCalendarBundle\Services;

use Doctrine\ORM\EntityManager;

/**
 * ArticleOfTheDay service
 */
class ArticleOfTheDayService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Get articles of the day by given date range and custom params
     *
     * @param \DateTime $startDate Start date
     * @param \DateTime $endDate   End date
     * @param string    $params    Query params
     *
     * @return array
     */
    public function getArticleOfTheDay(\DateTime $startDate, \DateTime $endDate, $params = null)
    {
        try {
            $qb = $this->getRepository()
                ->createQueryBuilder('a');

            $qb->select('a.articleNumber', 'a.publicationId', 'a.articleLanguageId', 'a.date', 'aa.name')
                    ->leftJoin('a.article', 'aa')
                    ->where('a.is_active = true');

            if (!is_null($params)) {
                $qb->andWhere(substr($params, 0, -4));
            }

            $qb->andWhere($qb->expr()->between(
                'a.date',
                ':start',
                ':end'
            ))
            ->setParameters(array(
                'start' => $startDate,
                'end' => $endDate
            ));

            $articlesOfTheDay = $qb->getQuery()->getArrayResult();

            return $articlesOfTheDay;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        }
    }

    /**
     * Get repository
     *
     * @return Newscoop\ArticlesCalendarBundle\Entity\ArticleOfTheDay
     */
    protected function getRepository()
    {
        return $this->em->getRepository('Newscoop\ArticlesCalendarBundle\Entity\ArticleOfTheDay');
    }
}
