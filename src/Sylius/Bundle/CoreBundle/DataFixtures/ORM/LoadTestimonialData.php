<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * User fixtures.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadTestimonialData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
	    $testimonial = $this->getTestimonialRepository()->createNew();

	    $testimonial->setTestimony('Súper contenta con mi compra. El Café rico y el proceso para tenerlo fue accesible y rápido. Brands es una alternativa genial.');
	    $testimonial->setAuthor('Sarie Silva, San Juan');

        $manager->persist($testimonial);
        $this->setReference('Sylius.Testimonial-1', $testimonial);
       
        
        $testimonial = $this->getTestimonialRepository()->createNew();
        
        $testimonial->setTestimony('¡¡Artículo recibido en 3 días!! Muy buen servicio, así que se lo recomiendo a todos. ¡Excelente iniciativa, muchachos!');
        $testimonial->setAuthor('Ramón L. Gascot - Lozada de Maryland, USA');
        
        $manager->persist($testimonial);
                
        $this->setReference('Sylius.Testimonial-2', $testimonial);        
        $manager->flush();
    }
    
	public function getTestimonialRepository()
	{	
		return $this->get('bopr.repository.testimonial');
	}
	
    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
    }
}
