<?php
/**
 * @package Newscoop\ArticlesCalendarBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesCalendarBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleOfTheDayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = \Zend_Registry::get('container')->getService('em');

        $publications = $em->getRepository('Newscoop\Entity\Publication')
            ->createQueryBuilder('p')
            ->getQuery()
            ->getResult();

        $publicationsArray = array();
        foreach ($publications as $publication) {
            $publicationsArray[$publication->getId()] = $publication->getName();
        }
       
        $builder
        ->add('articleId', 'hidden', array(
            'required' => true
        ))
        ->add('publicationId', 'hidden', array(
            'required' => true
        ))
        ->add('publicationNumbers', 'choice', array(
         'choices'   => $publicationsArray,
            'label' => 'Choose publications:',
            'error_bubbling' => true,
            'multiple'  => true,
            'required' => true
        ))
        ->add('articleLanguageId', 'hidden', array(
            'required' => true
        ))
        ->add('custom_date', 'text', array(
            'required' => true,
            'attr' => array('class' => 'datepicker'),
            'label' => ''
        ));
    }

    public function getName()
    {
        return 'articleofthedayform';
    }
}