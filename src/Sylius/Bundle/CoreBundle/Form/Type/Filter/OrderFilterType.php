<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class OrderFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.order_filter.number',
                'attr'     => array(
                    'placeholder' => 'sylius.form.order_filter.number'
                )
            ))
            ->add('totalFrom', 'money', array(
                'required' => false,
                'label'    => 'sylius.form.order_filter.total_from',
                'attr'     => array(
                    'placeholder' => 'sylius.form.order_filter.total_from'
                )
            ))
            ->add('totalTo', 'money', array(
                'required' => false,
                'label'    => 'sylius.form.order_filter.total_to',
                'attr'     => array(
                    'placeholder' => 'sylius.form.order_filter.total_to'
                )
            ))
            ->add('createdAtFrom', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.order_filter.created_at_from',
                'attr'     => array(
                    'placeholder' => 'sylius.form.order_filter.created_at_from'
                )
            ))
            ->add('createdAtTo', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.order_filter.created_at_to',
                'attr'     => array(
                    'placeholder' => 'sylius.form.order_filter.created_at_to'
                )            
            ))
            ->add('month', 'choice', array(
				    'choices'   => array(
				        '01' => '01',
				        '02' => '02',
				        '03' => '03',
				        '04' => '04',
				        '05' => '05',
				        '06' => '06',
				        '07' => '07',
				        '08' => '08',
				        '09' => '09',
				        '10' => '10',
				        '11' => '11',
				        '12' => '12',
				    ),
            		'required' => false,
            		'label'    => 'sylius.form.order_filter.month',
            		'empty_value' => 'Mes'
            ))
            ->add('year', 'choice', array(
				    'choices'   => array(
				        '2014' => '2014',
				        '2015' => '2015',
				    ),
            		'required' => false,
            		'label'    => 'sylius.form.order_filter.year',
            		'empty_value' => 'Año'
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_order_filter';
    }
}
