<?php

namespace Tresepic\BoprBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\ProductType as BaseProductType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductType extends BaseProductType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	parent::buildForm($builder, $options);
    	
        $builder
        ->add('position', 'integer', array(
        		'required' => true,
        		'label'    => 'Orden'
        ))
        ->add('manufacturer', 'entity', array(
        		'class'    => 'TresepicBoprBundle:Manufacturer',
        		'required' => true,
        		'empty_value' => '--- Elegir marca ---',
        		'label'    => 'Marca'
        ));
    }
}
