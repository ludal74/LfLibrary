<?php

namespace LfLibrary;

use Zend\Session\Container;

class PreviousUrl
{
	protected $session = NULL;
	
	public function __construct()
	{
		$this->session = new Container('previous');
	}
	
	/**
	 * Example use:
	 * App_Helpers_LastVisited::saveThis($this->_request->getRequestUri());
	 */
	public function saveThis($url) 
	{		
        $this->session->offsetSet('last', $url);
       // PreviousUrl::$session->offsetSet('last', $url);
	}

	/**
	 * I typically use redirect:
	 * $this->_redirect(App_Helpers_LastVisited::getLastVisited());
	 */
	public function getLastVisited() 
	{		
		//print_r( $this->session );
		
		if( $this->session->offsetExists( 'last') ) 
		{
			$path = $this->session->offsetGet('last');
			return $path;
		}

		return ''; // Go back to index/index by default;
	}
}