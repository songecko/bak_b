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
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

class Banner
{
    /**
     * Id
     *
     * @var integer
     */
    protected $id;
	
    protected $name;
    protected $priority;
    protected $link;
 	protected $imageFile;
    protected $imageName;
    protected $isSuscription;
    protected $isEnabled;

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
    public function setImageFile(File $image = null)
    {
    	$this->imageFile = $image;
    
    	if ($image) {
    		// It is required that at least one field changes if you are using doctrine
    		// otherwise the event listeners won't be called and the file is lost
    		$this->updatedAt = new \DateTime('now');
    	}
    }
    
    /**
     * @return File
     */
    public function getImageFile()
    {
    	return $this->imageFile;
    }
    
    /**
     * @param string $imageName
     */
    public function setImageName($imageName)
    {
    	$this->imageName = $imageName;
    }
    
    /**
     * @return string
     */
    public function getImageName()
    {
    	return $this->imageName;
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
    
    public function getName()
    {
    	return $this->name;
    }
    
    public function setName($name)
    {
    	$this->name = $name;
    
    	return $this;
    }
    
    public function getPriority()
    {
    	return $this->priority;
    }
    
    public function setPriority($priority)
    {
    	$this->priority = $priority;
    
    	return $this;
    }
    
    public function getLink()
    {
    	return $this->link;
    }
    
    public function setLink($link)
    {
    	$this->link = $link;
    
    	return $this;
    }
    
    public function getIsSuscription()
    {
    	return $this->isSuscription;
    }
    
    public function setIsSuscription($isSuscription)
    {
    	$this->isSuscription = $isSuscription;
    
    	return $this;
    }
    
    public function getIsEnabled()
    {
    	return $this->isEnabled;
    }
    
    public function setIsEnabled($isEnabled)
    {
    	$this->isEnabled = $isEnabled;
    
    	return $this;
    }
}
