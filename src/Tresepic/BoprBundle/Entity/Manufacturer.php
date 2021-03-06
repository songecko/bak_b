<?php

namespace Tresepic\BoprBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Manufacturer
 */
class Manufacturer
{
    private $id;
    private $name;
    private $email;
    private $createdAt;
    private $updatedAt;
    private $products;

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function addProduct(\Tresepic\BoprBundle\Entity\Product $products)
    {
        $this->products[] = $products;

        return $this;
    }

    public function removeProduct(\Tresepic\BoprBundle\Entity\Product $products)
    {
        $this->products->removeElement($products);
    }

    public function getProducts()
    {
        return $this->products;
    }
    
    public function __toString()
    {
    	return $this->getName();
    }
}
