<?php

namespace Tresepic\BoprBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TestimonialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('testimony', 'text', array(
        		'required' => true,
        		'label'    => 'Testimonio'
        ))
        ->add('author', 'text', array(
        		'required' => true,
        		'label'    => 'Autor'
        ));
    }

    public function getName()
    {
        return 'bopr_testimonial';
    }
}
