<?php

namespace Tresepic\BoprBundle\Shipping\Calculator;

use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;
use Sylius\Bundle\ShippingBundle\Calculator\Calculator;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sylius\Bundle\CoreBundle\Model\Shipment;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sylius\Bundle\AddressingBundle\Model\Address;

require_once dirname(__FILE__).'/../../usps/PriceCalculator.php';

/**
 * Usps Calculator
 */
class USPSCalculator extends Calculator
{
    /**
     * {@inheritdoc}
     */
    public function calculate(ShippingSubjectInterface $subject, array $configuration)
    {
    	if(!$subject instanceof Shipment)
    	{
    		throw new NotFoundHttpException();
    	}
    	
    	$order = $subject->getOrder();
    	
    	return $this->calculateUspsAddress($order->getShippingAddress(), $order->getItems());
    }
    
    public function calculateUspsAddress(Address $shippingAddress = null, $items = array())
    {
    	$priceCalculator = 0;
    	
    	if($shippingAddress)
    	{
    		$country = $shippingAddress->getCountry()->getName();
    		$postCode = $shippingAddress->getPostcode();
    	
    		$totalPounds = 0;
    		$totalWidth = 0;
    		$totalLength = 0;
    		$totalHeight = 0;
    	
    		foreach($items as $item)
    		{
    			$totalPounds += ($item->getQuantity()) * ($item->getProduct()->getMasterVariant()->getWeight());
    			$totalWidth += ($item->getQuantity()) * ($item->getProduct()->getMasterVariant()->getWidth());
    			$totalLength += ($item->getQuantity()) * ($item->getProduct()->getMasterVariant()->getDepth());
    			$totalHeight += ($item->getQuantity()) * ($item->getProduct()->getMasterVariant()->getHeight());
    		}
    	
    		$totalOunces = $totalPounds*16;
    	
    		$priceCalculator = USPSParcelRate($totalPounds, $totalOunces, 'REGULAR', $postCode, $country, $totalWidth, $totalLength, $totalHeight);
    	}
    	
    	return $priceCalculator*100;
    }

    /**
     * {@inheritdoc}
     */
    public function isConfigurable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        //return 'sylius_shipping_calculator_usps_configuration'; //Not neccesary yet
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(OptionsResolverInterface $resolver)
    {
    }
}
