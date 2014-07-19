<?php

namespace Tresepic\BoprBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Bundle\CoreBundle\Model\Product as BaseProduct;

/**
 * Product
 */
class Product extends BaseProduct
{
    private $manufacturer;
    private $position;
    
    public function setManufacturer(\Tresepic\BoprBundle\Entity\Manufacturer $manufacturer = null)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getManufacturer()
    {
        return $this->manufacturer;
    }
    
    public function setPosition($position)
    {
    	$this->position = $position;
    
    	return $this;
    }
    
    public function getPosition()
    {
    	return $this->position;
    }
}
