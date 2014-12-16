<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tresepic\BoprBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Testimonial
{
    /**
     * Id
     *
     * @var integer
     */
    protected $id;
	
    protected $testimony;
    protected $author;
   
    /**
     * Creation date
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Update date
     *
     * @var \DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    
    public function getTestimony()
    {
    	return $this->testimony;
    }
    
    public function setTestimony($testimony)
    {
    	$this->testimony = $testimony;
    
    	return $this;
    }
    
    public function getAuthor()
    {
    	return $this->author;
    }
    
    public function setAuthor($author)
    {
    	$this->author = $author;
    
    	return $this;
    }
}
