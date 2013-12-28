<?php

namespace LfLibrary;

use Zend\Session\Container;

class PreviousUrl
{
	static protected $session = NULL;
	
	public static function createSession()
	{
		PreviousUrl::$session = new Container('previous_url');
	}
	
	/**
	 * Example use:
	 * App_Helpers_LastVisited::saveThis($this->_request->getRequestUri());
	 */
	public static function saveThis($url) 
	{	
	    if(  PreviousUrl::$session == NULL )
	    {
	        PreviousUrl::createSession();
	    }	
	
	    PreviousUrl::$session->offsetSet('last', $url);
        
       //PreviousUrl::$session->offsetSet('last', $url);
	}

	/**
	 * I typically use redirect:
	 * $this->_redirect(App_Helpers_LastVisited::getLastVisited());
	 */
	public static function getLastVisited() 
	{		
	    if( PreviousUrl::$session == NULL )
	    {
	        PreviousUrl::createSession();
	    }	
		
		if( PreviousUrl::$session->offsetExists( 'last') ) 
		{
			$path = PreviousUrl::$session->offsetGet('last');
			return $path;
		}

		return ''; // Go back to index/index by default;
	}
}