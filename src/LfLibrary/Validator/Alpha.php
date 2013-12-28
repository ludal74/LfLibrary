<?php

namespace LfLibrary\Validator;

use Zend\Validator\AbstractValidator as AbstractValidator;

class Alpha extends AbstractValidator
{
	const MSG_NUMERIC = 'msgNumeric';

	protected $messageVariables = array(

	);

	protected $messageTemplates = array(
			   self::MSG_NUMERIC => "'%value%' is not numeric",

	);

	public function isValid($value)
	{
		$this->setValue($value);

		$regEx="#[^a-zA-ZÃƒï¿½Ãƒâ‚¬Ãƒâ€šÃƒâ€žÃƒâ€°ÃƒË†ÃƒÅ Ãƒâ€¹Ãƒï¿½ÃƒÅ’ÃƒÅ½Ãƒï¿½Ãƒâ€œÃƒâ€™Ãƒâ€�Ãƒâ€“ÃƒÅ¡Ãƒâ„¢Ãƒâ€ºÃƒÅ“ÃƒÂ¡ÃƒÂ ÃƒÂ¢ÃƒÂ¤ÃƒÂ©ÃƒÂ¨ÃƒÂªÃƒÂ«ÃƒÂ­ÃƒÂ¬ÃƒÂ®ÃƒÂ¯ÃƒÂ³ÃƒÂ²ÃƒÂ´ÃƒÂ¶ÃƒÂºÃƒÂ¹ÃƒÂ»ÃƒÂ¼Ãƒâ€¡ÃƒÂ§\'-\s]#";
		
	 
		if( preg_match($regEx, $value ))
		{
		    $this->error(self::MSG_NUMERIC);
		    return false;
		}
		
		return true;
	}
}