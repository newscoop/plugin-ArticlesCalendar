<?php
/**
 * @package Newscoop\PaywallBundle
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
            'attr' => array('min'=>'1'),
            'error_bubbling' => true,
            'required' => true
        ))
        ->add('view', 'choice', array(
            'label' => 'View',
            'choices'   => array(
                'month'   => 'Month',
                'widget'   => 'Widget',
            ),
            'error_bubbling' => true,
            'required' => true
        ));
    }

    public function getName()
    {
        return 'settingsform';
    }
}