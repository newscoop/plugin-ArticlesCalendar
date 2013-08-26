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
        $em = $this->container->get('em');
        $settings = $em->getRepository('Newscoop\ArticlesCalendarBundle\Entity\Settings')->findOneBy(array(
            'is_active' => true
        ));

        $response = new Response();
        $response->setLastModified($settings->getCreatedAt());
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        $firstDay = $request->get('firstDay', $settings->getFirstDay());
        $navigation = $request->get('navigation', $settings->getNavigation());
        $showDayNames = $request->get('showDayNames', $settings->getShowDayNames());
        $imageWidth = $request->get('image_width', $settings->getImageWidth());
        $imageHeight = $request->get('image_height', $settings->getImageHeight());
        $publicationNumbers = $request->get('publication_numbers', $settings->getPublicationNumbers());
        $date = $request->get('date', date('Y/m/d'));
        $latestMonth = $request->get('latestMonth', $settings->getLatestMonth());
        $earliestMonth = $request->get('earliestMonth', $settings->getEarliestMonth());
        $styles = $settings->getStyles();

        $date = explode('/', $date);
        $today = explode('/', date('Y/m/d'));
        $view = 'month';

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
        $earliestMonth = date('Y/'.$earliestMonth.'/d');

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

        if (isset($latestMonth) && $latestMonth == 'current') {
            $latestMonth = $today;
        } else if (isset($latestMonth)) {
            $month = $latestMonth-1;
            $latestMonth = explode('/',date('Y/'.$latestMonth.'/d'));
            $tmp_latest = new \DateTime("$latestMonth[0]-$latestMonth[1]");

            if ($latestMonth <= $earliestMonth) {
                $earliestMonth = $latestMonth;
            }
            if ($now > $tmp_latest) {
                $latestMonth = $today;
            }
        } else {
            $latestMonth = null;
        }

        $attributes = $request->attributes->get('_newscoop_publication_metadata');
        $val = array();
        $key = 'id_default_language';

        array_walk_recursive($attributes, function($value, $index) use($key, &$val){
            if($index == $key) {
                array_push($val, $value);
            } 
        });

        $language = $em->getRepository('Newscoop\Entity\Language')
            ->createQueryBuilder('l')
            ->where('l.id = :languageId')
            ->setParameter('languageId', count($val) > 1 ? 1 : array_pop($val))
            ->getQuery()
            ->getOneOrNullResult();

        $locale = $language->getCode();

        $dateFormatter['month'] = \IntlDateFormatter::create(
            $locale,
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::NONE,
            \date_default_timezone_get(),
            \IntlDateFormatter::GREGORIAN,
            'MMM'
        );

        $dateFormatter['dayName'] = \IntlDateFormatter::create(
            $locale,
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::NONE,
            \date_default_timezone_get(),
            \IntlDateFormatter::GREGORIAN,
            'EEEE'
        );

        $months = array();
        $days = array();
        for ($i=1; $i <= 12; $i++) {
            $months[] = $dateFormatter['month']->format(new \DateTime($year."-".$i));
        }

        for ($i=0; $i <= 6; $i++) {
            $days[] = $dateFormatter['dayName']->format(strtotime("Sunday +$i days"));
        }

        $response->setContent($this->container->get('templating')->render('NewscoopArticlesCalendarBundle:Default:widget.html.twig' ,array(
            'randomInt' => md5(uniqid('', true)),
            'today' => $today,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'defaultView' => $view,
            'firstDay' => $firstDay,
            'nav' => $navigation,
            'dayNames' => $showDayNames,
            'earliestMonth' => $earliestMonth,
            'latestMonth' => $latestMonth,
            'image_width' => $imageWidth,
            'image_height' => $imageHeight,
            'styles' => $styles,
            'months' => $months,
            'days' => $days,
        )));

        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }

    /**
    * @Route("/plugin/articlescalendar/articlesoftheday/get")
    */
    public function getArticlesOfTheDayAction(Request $request)
    {   
        $em = $this->container->get('em');
        $lastArticleOfTheDay = $em->getRepository('Newscoop\ArticlesCalendarBundle\Entity\ArticleOfTheDay')
            ->createQueryBuilder('a')
            ->where('a.is_active = true')
            ->orderBy('a.created_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
        
        $response = new Response();
        $response->setLastModified($lastArticleOfTheDay->getCreatedAt());
        $response->setPublic();

        if ($response->isNotModified($request)) {
            return $response;
        }

        $publicationNumbers = $request->get('publication_numbers');

        $settings = $em->getRepository('Newscoop\ArticlesCalendarBundle\Entity\Settings')->findOneBy(array(
            'is_active' => true
        ));

        $currentPublication = $request->get('currentPublication', $settings->getPublicationNumbers());
        $renditionName = $request->get('renditionName', $settings->getRendition());
        $imageWidth = $request->get('image_width', $settings->getImageWidth());
        $imageHeight = $request->get('image_height', $settings->getImageHeight());

        $string = "";
        if (preg_match_all('/\*(.*?)\*/', $currentPublication, $match)) {
            foreach($match[1] as $value) {
                $string .= 'a.publicationNumbers LIKE \'%*'. $value .'*%\' OR ';
            }
        }

        $articlesOfTheDay = $em->getRepository('Newscoop\ArticlesCalendarBundle\Entity\ArticleOfTheDay')
            ->createQueryBuilder('a')
            ->where('a.is_active = true')
            ->andWhere(substr($string, 0, -4))
            ->getQuery()
            ->getResult();

        $results = array();
        foreach ($articlesOfTheDay as $dayArticle) {
            $element = array();
            $articleNumber = $dayArticle->getArticle()->getNumber();
            $image = $this->container->get('image.rendition')
                ->getArticleRenditionImage($articleNumber, $renditionName, $imageWidth ? $imageWidth : null, $imageHeight ? $imageHeight : null);

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
        $success = false;
        $numbers = "";

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

                $oldArticles = $em->getRepository('Newscoop\ArticlesCalendarBundle\Entity\ArticleOfTheDay')->findBy(array(
                    'date' => new \DateTime($data['custom_date'])
                ));

                if ($request->get('_route') === "newscoop_articlescalendar_default_unmark") {

                    $status = false;
                    $articleOfTheDay->setIsActive(false);
                    $em->flush();

                    return $this->returnData($article, $form, $status, $success, $articleOfTheDay);
                }

                if ($articleOfTheDay) {
        
                    foreach ($oldArticles as $old) {
                        $old->setIsActive(false);
                    }

                    $success = true;
                    $articleOfTheDay->setDate(new \DateTime($data['custom_date']));
                    $articleOfTheDay->setCreatedAt(new \DateTime());

                    foreach ($data['publicationNumbers'] as $value) {
                        $numbers .= '*'.$value.'*';
                    }

                    $articleOfTheDay->setPublicationNumbers($numbers);
                    $articleOfTheDay->setIsActive(true);

                } else {
                    $status = true;
                    $success = true;

                    if ($oldArticles) {
                        foreach ($oldArticles as $old) {
                            $old->setIsActive(false);
                        }
                    }

                    foreach ($data['publicationNumbers'] as $value) {
                        $numbers .= '*'.$value.'*';
                    }

                    $articleOfTheDay = new ArticleOfTheDay();
                    $articleOfTheDay->setDate(new \DateTime($data['custom_date']));
                    $articleOfTheDay->setArticle($article);
                    $articleOfTheDay->setPublicationId($data['publicationId']);
                    $articleOfTheDay->setPublicationNumbers($numbers);
                    $em->persist($articleOfTheDay);
                }

                $em->flush();
            }
        }
        
        return $this->returnData($article, $form, $status, $success, $articleOfTheDay);
    }

    /**
     * Returns data for given parameters
     *
     * @param entity object               $article         Article
     * @param Symfony\Component\Form\Form $form            Form
     * @param bool                        $status          Article of the day status
     * @param bool                        $success         Shows success message
     * @param entity object               $articleOfTheDay Article of the day
     *
     * @return array
     */
    private function returnData($article, $form, $status, $success, $articleOfTheDay) {
        $em = $this->container->get('em');
        $string = "";
        if (preg_match_all('/\*(.*?)\*/', $articleOfTheDay->getPublicationNumbers(), $match)) {
            foreach($match[1] as $value) {
                $string .= 'p.id = '. $value .' OR ';
            }
        }

        $publications = $em->getRepository('Newscoop\Entity\Publication')
            ->createQueryBuilder('p')
            ->where(substr($string, 0, -4))
            ->getQuery()
            ->getResult();

        foreach ($publications as $publication) {
            $publicationsArray[] = $publication->getName();
        }

        return $this->container->get('templating')->renderResponse('NewscoopArticlesCalendarBundle:Hooks:hook_content.html.twig',
            array(
                'article' => $article,
                'form' => $form->createView(),
                'status' => $status,
                'success' => $success,
                'articleOfTheDay' => $articleOfTheDay,
                'publicationsNames' => $publicationsArray
            )
        );
    }
}