<?php
/**
 * @package Newscoop\ArticlesCalendarBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesCalendarBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleOfTheDayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('articleId', 'hidden', array(
            'required' => true
        ))
        ->add('publicationId', 'hidden', array(
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