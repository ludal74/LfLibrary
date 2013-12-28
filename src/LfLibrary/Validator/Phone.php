<?php

namespace LfLibrary\Validator;

use Zend\Validator\AbstractValidator as AbstractValidator;

class Phone extends AbstractValidator
{
    const MSG_TOO_SHORT = 'short';
	const MSG_NUMERIC = 'msgNumeric';
	const MSG_MINIMUM = 'msgMinimum';
	const MSG_MAXIMUM = 'msgMaximum';

	public $minimum = 10;
	public $maximum = 10;
	public $digitsNumber = 10;

	protected $messageVariables = array(
			'min' => 'minimum',
			'max' => 'maximum'
	);

	protected $messageTemplates = array(
	           self::MSG_TOO_SHORT => "'%value%' is not a correct phone number",
			   self::MSG_NUMERIC => "'%value%' is not numeric",
			//self::MSG_MINIMUM => "'%value%' must be at least '%min%'",
			//self::MSG_MAXIMUM => "'%value%' must be no more than '%max%'"
	);

	public function isValid($value)
	{
		$this->setValue($value);

		
		if (!is_numeric($value)) {
			$this->error(self::MSG_NUMERIC);
			return false;
		}
        
		/*
		if ($value < $this->minimum) {
			$this->error(self::MSG_MINIMUM);
			return false;
		}

		if ($value > $this->maximum) {
			$this->error(self::MSG_MAXIMUM);
			return false;
		}
		*/
		
		// 10 numbers requiered for phone
		if( strlen( $value ) < $this->digitsNumber )
		{
		    $this->error(self::MSG_TOO_SHORT);
		    return false;
		}
		
		//check phone first two numbers
		if(          strpos( $value, "01" ) === false 
		          && strpos( $value, "02" ) === false 
		          && strpos( $value, "03" ) === false 
		          && strpos( $value, "04" ) === false 
		          && strpos( $value, "05" ) === false
		          && strpos( $value, "06" ) === false 
		          && strpos( $value, "07" ) === false 
		          && strpos( $value, "09" ) === false 
		)
		{
		    $this->error(self::MSG_TOO_SHORT);
		    return false;
		}

		return true;
	}
}