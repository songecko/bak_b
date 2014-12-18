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
		$uspsPrice = $this->shippingCalculator->calculateUspsAddress($protocol, array());
		
		//ldd($uspsPrice);
		if($uspsPrice && !($uspsPrice > 0))
		{
			$this->context->addViolationAt(
				'postcode',
				$constraint->message,
				array('%string%' => $protocol->getPostcode()),
				null
			);
		}
	}
}