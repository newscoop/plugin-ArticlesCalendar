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
            'label' => 'plugin.label.firstday',
            'attr' => array('min'=>'1', 'max' => '31'),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('showDayNames', 'checkbox', array(
            'label' => 'plugin.label.showdays',
            'required' => false
        ))
        ->add('navigation', 'checkbox', array(
            'label' => 'plugin.label.shownav',
            'required' => false
        ))
        ->add('imageWidth', 'integer', array(
            'label' => 'plugin.label.width',
            'attr' => array('min'=>'0'),
            'required' => false
        ))
        ->add('imageHeight', 'integer', array(
            'label' => 'plugin.label.height',
            'attr' => array('min'=>'0'),
            'required' => false
        ))
        ->add('latestMonth', 'integer', array(
            'label' => 'plugin.label.latest',
            'attr' => array('min'=>'1', 'max' => '12'),
            'required' => true
        ))
        ->add('earliestMonth', 'integer', array(
            'label' => 'plugin.label.earliest',
            'attr' => array('min'=>'1', 'max' => '12'),
            'required' => true
        ))
        ->add('rendition', 'choice', array(
            'choices' => $renditionsArray,
            'label' => 'plugin.label.rendition',
            'required' => true
        ))
        ->add('publicationNumbers', 'choice', array(
            'choices' => $publicationsArray,
            'label' => 'plugin.label.showfrom',
            'multiple' => true,
            'required' => true
        ));
    }

    public function getName()
    {
        return 'settingsform';
    }
}