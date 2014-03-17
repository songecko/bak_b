<?php

namespace Tresepic\BoprBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ManufacturerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', 'text', array(
        		'required' => true,
        		'label'    => 'Nombre'
        ))
        ->add('email', 'text', array(
        		'required' => true,
        		'label'    => 'Email'
        ))
        ->add('paypal_id', 'text', array(
        		'required' => true,
        		'label'    => 'Paypal'
        ));
    }

    public function getName()
    {
        return 'bopr_manufacturer';
    }
}
