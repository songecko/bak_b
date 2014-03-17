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
    
    public function setManufacturer(\Tresepic\BoprBundle\Entity\Manufacturer $manufacturer = null)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getManufacturer()
    {
        return $this->manufacturer;
    }
}
