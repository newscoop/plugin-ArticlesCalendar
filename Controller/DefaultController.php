<?php
/**
 * @package Newscoop\ArticlesCalendarBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesCalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Newscoop\ArticlesCalendarBundle\Entity\ArticleOfTheDay;
use Newscoop\ArticlesCalendarBundle\Form\Type\ArticleOfTheDayType;

class DefaultController extends Controller
{
    /**
    * @Route("/plugin/articlescalendar/widget")
    * @Template()
    */
    public function widgetAction(Request $request)
    {  
        $date = $request->get('date', date('Y/m/d'));
        $date = explode('/', $date);
        $today = explode('/', date('Y/m/d'));
        $view = $request->get('view', 'month');

        if (isset($date[0])) {
            $year = $date[0];
        }
        if (isset($date[1])) {
            $month = $date[1] - 1;
        }
        if (isset($date[2])) {
            $day = $date[2];
        } else if (!isset($date[2]) && $view === 'month') {
            $day = 1;
        }

        $now = new \DateTime("$today[0]-$today[1]");

        $earliestMonth = $request->get('earliestMonth');
        if (isset($earliestMonth) && $earliestMonth == 'current') {
            $earliestMonth = $today;
        } else if (isset($earliestMonth)) {
            $earliestMonth = explode('/', $earliestMonth);
            $tmp_earliest = new \DateTime("$earliestMonth[0]-$earliestMonth[1]");

            if ($tmp_earliest > $now) {
                $earliestMonth = $today;
            }
        } else {
            $earliestMonth = null;
        }

        $latestMonth = $request->get('latestMonth', null);
        if (isset($latestMonth) && $latestMonth == 'current') {
            $latestMonth = $today;
        } else if (isset($latestMonth)) {
            $latestMonth = explode('/', $latestMonth);
            $tmp_latest = new \DateTime("$latestMonth[0]-$latestMonth[1]");

            if ($now > $tmp_latest) {
                $latestMonth = $today;
            }
        } else {
            $latestMonth = null;
        }

        return array(
            'randomInt' => md5(uniqid('', true)),
            'today' => explode('/', date('Y/m/d')),
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'defaultView' => $view,
            'firstDay' => $request->get('firstDay', 1),
            'nav' => $request->get('navigation', 'true'),
            'dayNames' => $request->get('showDayNames'),
            'earliestMonth' => $earliestMonth,
            'latestMonth' => $latestMonth,
        );
    }

    /**
    * @Route("/plugin/articlescalendar/articlesoftheday/get")
    */
    public function getArticlesOfTheDayAction(Request $request)
    {   
        $response = new Response();
        $datetime = new \DateTime(date('Y-m-d h').':00:00');
        $response->setLastModified($datetime);
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        $em = $this->container->get('em');
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $imageWidth = $request->get('image_width', 140);

        $articlesOfTheDay = $em->getRepository('Newscoop\ArticlesCalendarBundle\Entity\ArticleOfTheDay')
            ->createQueryBuilder('a')
            ->where('a.is_active = true')
            ->getQuery()
            ->getResult();

        $results = array();

        foreach ($articlesOfTheDay as $dayArticle) {
            $element = array();
            $articleNumber = $dayArticle->getArticle()->getNumber();
            $image = $this->container->get('image.rendition')->getArticleRenditionImage($articleNumber, 'issuethumb', 140, 94);

            $element['title'] = $dayArticle->getArticle()->getTitle();
            if (isset($image)) {
                $element['image'] = $this->container->get('zend_router')->assemble(array('src' => $image['src']), 'image', true, false);
            } else {
                $element['image'] = null;
            }

            $date = $dayArticle->getDate()->format('Y-m-d');
            $date = explode(" ", $date);
            $YMD = explode("-", $date[0]);

            $element['date'] = array(
                "year" => intval($YMD[0]),
                "month" => intval($YMD[1]),
                "day" => intval($YMD[2])
            );
            $element['url'] = \ShortURL::GetURL($dayArticle->getArticle()->getPublicationId(), $dayArticle->getArticle()->getLanguageId(), null, null, $articleNumber);

            $results[] = $element;
        }

        $response->setContent(json_encode(array('articles' => $results)));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    /**
    * @Route("/plugin/articlescalendar/articlesoftheday/set")
    * @Route("/plugin/articlescalendar/articlesoftheday/unmark", name="newscoop_articlescalendar_default_unmark")
    */
    public function setArticlesOfTheDayAction(Request $request)
    {   
        $em = $this->container->get('em');
        $form = $this->container->get('form.factory')->create(new ArticleOfTheDayType(), array(), array());
        $status = false;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                $status = true;
                $articleOfTheDay = $em->getRepository('Newscoop\ArticlesCalendarBundle\Entity\ArticleOfTheDay')
                    ->createQueryBuilder('a')
                    ->where('a.articleNumber = :articleNumber')
                    ->andWhere('a.articleLanguageId = :articleLanguageId')
                    ->andWhere('a.publicationId = :publicationId')
                    ->setParameters(array(
                        'articleNumber' => $data['articleId'],
                        'articleLanguageId' => $data['articleLanguageId'],
                        'publicationId' => $data['publicationId']
                    ))
                    ->getQuery()
                    ->getOneOrNullResult();

                $article = $em->getRepository('Newscoop\Entity\Article')->findOneBy(array(
                    'language' => $data['articleLanguageId'], 
                    'number' => $data['articleId']
                ));

                $date = $em->getRepository('Newscoop\ArticlesCalendarBundle\Entity\ArticleOfTheDay')->findOneBy(array(
                    'date' => new \DateTime($data['custom_date'])
                ));

                if ($request->get('_route') === "newscoop_articlescalendar_default_unmark") {

                    $articleOfTheDay->setIsActive(false);
                    $em->flush();

                    return $this->container->get('templating')->renderResponse(
                        'NewscoopArticlesCalendarBundle:Hooks:hook_content.html.twig',
                        array(
                            'article' => $article,
                            'form' => $form->createView(),
                            'status' => false,
                            'error' => array('exists' => false, 'error' => false),
                            'articleOfTheDay' => $articleOfTheDay
                        )
                    );
                }

                if ($articleOfTheDay) {

                    if ($articleOfTheDay->getIsActive() == false) {
                        
                        $articleOfTheDay->setIsActive(true);
                        $em->flush();

                        return $this->container->get('templating')->renderResponse(
                            'NewscoopArticlesCalendarBundle:Hooks:hook_content.html.twig',
                            array(
                                'article' => $article,
                                'form' => $form->createView(),
                                'status' => $status,
                                'error' => array('exists' => false, 'error' => false),
                                'articleOfTheDay' => $articleOfTheDay
                            )
                        );
                    }

                    if ($articleOfTheDay->getDate() == new \DateTime($data['custom_date'])) {
                        return $this->container->get('templating')->renderResponse(
                            'NewscoopArticlesCalendarBundle:Hooks:hook_content.html.twig',
                            array(
                                'article' => $article,
                                'form' => $form->createView(),
                                'status' => $status,
                                'error' => array('exists' => true, 'error' => false),
                                'articleOfTheDay' => $articleOfTheDay
                            )
                        );
                    }
            
                    if ($date) {

                        return $this->container->get('templating')->renderResponse(
                            'NewscoopArticlesCalendarBundle:Hooks:hook_content.html.twig',
                            array(
                                'article' => $article,
                                'form' => $form->createView(),
                                'status' => $status,
                                'error' => array('exists' => false, 'error' => true),
                                'articleOfTheDay' => $articleOfTheDay
                            )
                        );
                    }

                    $articleOfTheDay->setDate(new \DateTime($data['custom_date']));
                } else {
                    if ($date) {
                        return $this->container->get('templating')->renderResponse(
                            'NewscoopArticlesCalendarBundle:Hooks:hook_content.html.twig',
                            array(
                                'article' => $article,
                                'form' => $form->createView(),
                                'status' => false,
                                'error' => array('exists' => false, 'error' => true),
                            )
                        );
                    }

                    $articleOfTheDay = new ArticleOfTheDay();
                    $articleOfTheDay->setDate(new \DateTime($data['custom_date']));
                    $articleOfTheDay->setArticle($article);
                    $articleOfTheDay->setPublicationId($data['publicationId']);
                    $em->persist($articleOfTheDay);
                }

                $em->flush();
            }
        }

        return $this->container->get('templating')->renderResponse(
            'NewscoopArticlesCalendarBundle:Hooks:hook_content.html.twig',
            array(
                'article' => $article,
                'form' => $form->createView(),
                'status' => $status,
                'error' => array('exists' => false, 'error' => false),
                'articleOfTheDay' => $articleOfTheDay
            )
        );
    }
}