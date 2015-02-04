<?php

namespace Tresepic\BoprBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class USPSAddress extends Constraint
{
	public $message = 'The address does not exist.';
	
	public function validatedBy()
	{
		return 'usps_address_validator';
	}
	
	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}
}