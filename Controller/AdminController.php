<?php
/**
 * @package Newscoop\ArticlesCalendarBundle
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
use Newscoop\ArticlesCalendarBundle\Entity\Settings;
use Newscoop\ArticlesCalendarBundle\Form\Type\SettingsType;

class AdminController extends Controller
{
    /**
    * @Route("/admin/articles-calendar")
    * @Template()
    */
    public function adminAction(Request $request)
    {  
        $em = $this->container->get('em');
        $insert = false;
        $settings = $em->getRepository('Newscoop\ArticlesCalendarBundle\Entity\Settings')->findOneBy(array(
            'is_active' => true
        ));

        if (!$settings) {
            $insert = true;
            $settings = new Settings();
        }

        $form = $this->container->get('form.factory')->create(new SettingsType(), array(
            'firstDay' => $settings->getFirstDay(),
            'showDayNames' => $settings->getShowDayNames(),
            'navigation' => $settings->getNavigation(),
            'imageWidth' => $settings->getImageWidth(),
            'imageHeight' => $settings->getImageHeight(),
            'rendition' => $settings->getRendition(),
        ), array());

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if ($insert) {
                    $em->persist($settings);
                }
                
                $settings->setFirstDay($data['firstDay']);
                $settings->setShowDayNames($data['showDayNames']);
                $settings->setNavigation($data['navigation']);
                $settings->setImageWidth($data['imageWidth']);
                $settings->setImageHeight($data['imageHeight']);
                $settings->setRendition($data['rendition']);

                $em->flush();
            }
        }

        return array(
            'form' => $form->createView(),
            'styles' => $settings->getStyles(),
            'lastModified' => $settings->getCreatedAt(),
        );
    }

    /**
    * @Route("/admin/articles-calendar/save-css")
    */
    public function savecssAction(Request $request)
    {    
        try {
            $em = $this->container->get('em');
            $settings = $em->getRepository('Newscoop\ArticlesCalendarBundle\Entity\Settings')->findOneBy(array(
                'is_active' => true
            ));

            $settings->setStyles($request->get('styles'));
            $settings->setCreatedAt(new \Datetime($request->get('lastModified')));
            $em->flush();
        } catch (\Exception $e) {
            return new Response(json_encode(array('status' => false)));
        }

        return new Response(json_encode(array('status' => true)));
    }
}