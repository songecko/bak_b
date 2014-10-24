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
class LoadManufacturerData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 5; $i++) 
        {
	        $manufacturer = $this->getManufacturerRepository()->createNew();

	        $manufacturer->setName('Brand '.$i);
	        $manufacturer->setEmail('info@brand'.$i.'.com');

            $manager->persist($manufacturer);

            $this->setReference('Sylius.Manufacturer-'.$i, $manufacturer);
        }

        $manager->flush();
    }
    
	public function getManufacturerRepository()
	{	
		return $this->get('bopr.repository.manufacturer');
	}
	
    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
