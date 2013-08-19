<?php

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
        $status = false;

        $articleOfTheDay = $em->getRepository('Newscoop\ArticlesCalendarBundle\Entity\ArticleOfTheDay')
            ->createQueryBuilder('a')
            ->where('a.articleNumber = :articleNumber')
            ->andWhere('a.articleLanguageId = :articleLanguageId')
            ->andWhere('a.publicationId = :publicationId')
            ->setParameters(array(
                'articleNumber' => $article->getNumber(),
                'articleLanguageId' => $article->getLanguageId(),
                'publicationId' => $article->getPublicationId()
            ))
            ->getQuery()
            ->getOneOrNullResult();

        if ($articleOfTheDay) {
            $status = true;
            $articleOfTheDayDate = $articleOfTheDay->getDate();
        }

        $form = $this->container->get('form.factory')->create(new ArticleOfTheDayType(), array(
            'custom_date' => $articleOfTheDayDate->format('Y-m-d'),
            'articleId' => $article->getNumber(),
            'articleLanguageId' => $article->getLanguageId(),
            'publicationId' => $article->getPublicationId()
        ), array());

        $response = $this->container->get('templating')->renderResponse(
            'NewscoopArticlesCalendarBundle:Hooks:sidebar.html.twig',
            array(
                'article' => $article,
                'form' => $form->createView(),
                'status' => $status,
                'error' => array('exists' => false, 'error' => false), 
                'articleOfTheDay' => $articleOfTheDay
            )
        );

        $event->addHookResponse($response);
    }
}