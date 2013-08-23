<?php
/**
 * @package Newscoop\ArticlesCalendarBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesCalendarBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SettingsType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = \Zend_Registry::get('container')->getService('em');
        $renditions = \Zend_Registry::get('container')->getService('image.rendition')->getRenditions();

        $renditionsArray = array();
        foreach ($renditions as $rendition) {
            $renditionsArray[$rendition->getName()] = $rendition->getName().' ('.$rendition->getWidth().'x'.$rendition->getHeight().')';
        }

        $publications = $em->getRepository('Newscoop\Entity\Publication')
            ->createQueryBuilder('p')
            ->getQuery()
            ->getResult();

        $publicationsArray = array();
        foreach ($publications as $publication) {
            $publicationsArray[$publication->getId()] = $publication->getName();
        }

        $builder
        ->add('firstDay', 'integer', array(
            'label' => 'First day',
            'attr' => array('min'=>'1'),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('showDayNames', 'checkbox', array(
            'label' => 'Show day names?',
            'required' => false
        ))
        ->add('navigation', 'checkbox', array(
            'label' => 'Show navigation?',
            'required' => false
        ))
        ->add('imageWidth', 'integer', array(
            'label' => 'Image width (in pixels)',
            'attr' => array('min'=>'0'),
            'required' => false
        ))
        ->add('imageHeight', 'integer', array(
            'label' => 'Image height (in pixels)',
            'attr' => array('min'=>'0'),
            'required' => false
        ))
        ->add('rendition', 'choice', array(
            'choices' => $renditionsArray,
            'label' => 'Rendition type',
            'required' => true
        ))
        ->add('publicationNumbers', 'choice', array(
            'choices'   => $publicationsArray,
            'label' => 'Show articles of the day from:',
            'multiple'  => true,
            'required' => false
        ));
    }

    public function getName()
    {
        return 'settingsform';
    }
}