<?php
/**
 * @package Newscoop\ArticlesCalendarBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesCalendarBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Newscoop\EventDispatcher\Events\PluginHooksEvent;
use Newscoop\ArticlesCalendarBundle\Form\Type\ArticleOfTheDayType;

class HooksListener
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function sidebar(PluginHooksEvent $event)
    {   
        $em = $this->container->get('em');
        $eventArticle = $event->getArgument('article');
        $article = $em->getRepository('Newscoop\Entity\Article')->findOneBy(array(
            'language' => $eventArticle->getLanguageId(), 
            'number' => $eventArticle->getArticleNumber()
        ));

        $articleOfTheDayDate = $article->getPublishDate();
        $publicationsArray = array();
        $status = false;

        $articleOfTheDay = $em->getRepository('Newscoop\ArticlesCalendarBundle\Entity\ArticleOfTheDay')
            ->createQueryBuilder('a')
            ->where('a.articleNumber = :articleNumber')
            ->andWhere('a.articleLanguageId = :articleLanguageId')
            ->andWhere('a.publicationId = :publicationId')
            ->andWhere('a.is_active = :is_active')
            ->setParameters(array(
                'articleNumber' => $article->getNumber(),
                'articleLanguageId' => $article->getLanguageId(),
                'publicationId' => $article->getPublicationId(),
                'is_active' => true
            ))
            ->getQuery()
            ->getOneOrNullResult();

        if ($articleOfTheDay) {
            $status = true;
            $articleOfTheDayDate = $articleOfTheDay->getDate(); 

            $query = "";
            $publicationNumbers = explode(',', $articleOfTheDay->getPublicationNumbers());
            foreach($publicationNumbers as $value) {
                $query .= 'p.id = '. $value .' OR ';
            }

            $publications = $em->getRepository('Newscoop\Entity\Publication')
                ->createQueryBuilder('p')
                ->where(substr($query, 0, -4))
                ->getQuery()
                ->getResult();

            foreach ($publications as $publication) {
                $publicationsArray[] = $publication->getName();
            }
        }

        $form = $this->container->get('form.factory')->create(new ArticleOfTheDayType(), array(
            'custom_date' => $articleOfTheDayDate->format('Y-m-d'),
            'articleId' => $article->getNumber(),
            'articleLanguageId' => $article->getLanguageId(),
            'publicationId' => $article->getPublicationId(),
            'publicationNumbers' => !$articleOfTheDay ? explode(',', $article->getPublicationId()) : $publicationNumbers
        ), array());

        $response = $this->container->get('templating')->renderResponse(
            'NewscoopArticlesCalendarBundle:Hooks:sidebar.html.twig',
            array(
                'article' => $article,
                'form' => $form->createView(),
                'status' => $status,
                'success' => false,
                'articleOfTheDay' => $articleOfTheDay,
                'publicationsNames' => $publicationsArray
            )
        );

        $event->addHookResponse($response);
    }
}