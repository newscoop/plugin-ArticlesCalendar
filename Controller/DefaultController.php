<?php
/**
 * @package Newscoop\ArticlesCalendarBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesCalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
    * @Route("/plugin/articlescalendar/articlesoftheday/get")
    */
    public function getArticlesOfTheDayAction(Request $request)
    {   
        $em = $this->getDoctrine()->getManager();
        $publication = $request->get('publication');

        return new Response('');
    }

    /**
    * @Route("/plugin/articlescalendar/articlesoftheday/set")
    */
    public function setArticlesOfTheDayAction(Request $request)
    {   
        $em = $this->getDoctrine()->getManager();
        $date = $request->get('date');
        $articleNumber = $request->get('article_number');
        $articleLanguage = $request->get('article_language');

        // set article to date

        return new Response('');
    }
}