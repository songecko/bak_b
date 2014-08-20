<?php

namespace Tresepic\BoprBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Sylius\Bundle\CoreBundle\Model\Product as BaseProduct;

/**
 * Product
 */
class Product extends BaseProduct
{
	use ORMBehaviors\Translatable\Translatable;
	
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
    
    public function getDescriptionTranslation($locale)
    {
    	if($localizedDescription = $this->translate($locale)->getDescription())
    	{
    		return $localizedDescription;
    	}else {
    		return $this->getDescription();
    	} 
    }
}
