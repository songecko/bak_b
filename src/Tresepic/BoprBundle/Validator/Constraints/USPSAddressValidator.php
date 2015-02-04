<?php

namespace Tresepic\BoprBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Tresepic\BoprBundle\Shipping\Calculator\USPSCalculator;

class USPSAddressValidator extends ConstraintValidator
{
	private $shippingCalculator;
	
	public function __construct(USPSCalculator $shippingCalculator)
	{
		$this->shippingCalculator = $shippingCalculator;	
	}
	
	public function validate($protocol, Constraint $constraint)
	{
		// If you're using the new 2.5 validation API (you probably are!)
		/*$this->context->buildViolation("no funca")
			->setParameter('%string%', $value)
			->addViolation();
*/
		// If you're using the old 2.4 validation API
		$propertyPath = $this->context->getPropertyPath();
		
		if($propertyPath == 'children[shippingAddress].data')
		{
			$uspsPrice = $this->shippingCalculator->calculateUspsAddress($protocol, array());
			
			if($uspsPrice !== null && !($uspsPrice > 0))
			{
				$this->context->addViolation($constraint->message);
			}
		}
	}
}