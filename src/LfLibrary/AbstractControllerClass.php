<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace LfLibrary;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use Zend\Session\Container;

use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;


/**
 * @copyright LF Developpement
 * @author ludo
 *
 */
class AbstractControllerClass extends AbstractActionController
{ 
    public $view = NULL;
    protected $session = NULL;
    protected $uri = NULL; 
    protected $em ;
    protected $translate;


   /**
    * @method __construct
    */
    public function __construct()
    { 
        if( $this->view == NULL )
        {  
            $this->view = new ViewModel();
        } 
        
        if( $this->session == NULL )
        {
            $this->session = new Container('locale');
        }
        
        $this->view->setVariable( 'lang',  $this->session->offsetGet( 'lang' ) );
        $this->uri = $_SERVER["REQUEST_URI"];
    }
    
    protected function getTranslator()
    {
    	$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
    	return $translate;
    }
    
    protected function getTranslate( $string )
    {
        $translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
        return $translate( $string );
    }
    
    
    protected function getServiceManager()
    {
    	$serviceManager = $this->getServiceLocator();
    	return $serviceManager;
    }
    
    
    protected function getPlugin( $pluginName )
    {
    	$plugin = $this->getServiceLocator()->get('ControllerPluginManager')->get( $pluginName );
    	return $plugin;
    }
    
    protected function setEntityManager(EntityManager $em)
    {
    	$this->em = $em;
    }
    
    protected function getEntityManager()
    {
    	if (null === $this->em) {
    		$this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    	}
    	return $this->em;
    }
    
    
    
    /*******************************************************************/
    // MAIN REDIRECTION METHODS 
    /******************************************************************/
    
    
    /**
     * Redirection if not connected ADMINISTRATION CONNECTION
     * If not connecteed access -> redirection to loggin page
     */
    protected function redirectIfNotConnected()
    {
    	if( !$this->session->offsetExists( 'adminUser') )
    	{
    		$this->redirect()->toRoute('administration');
    	}
    }
    
    /**
     * Redirection if not connected As USER
     * If not connecteed access -> redirection to loggin page
     */
    protected function redirectIfNotConnectedAsUser()
    {
    	$isConnected = true;
    	
    	if( !$this->session->offsetExists( 'user') )
    	{
    		$isConnected = false;
    		$this->redirect()->toRoute('home');	
    	}
    	
    	return $isConnected;
    }
    
    /**
     * Redirection if not connected As USER
     * If not connecteed access -> redirection to loggin page
     */
    
    protected function redirectIfNotConnectedAsAdmin()
    {	
    	if( $this->session->offsetExists('user') )
    	{
    		$userArray = $this->session->offsetGet( 'user');
    		
	    	if( $userArray[0]->accountTypeId != 1 )
	    	{
	    		$this->redirect()->toRoute('home');
	    	}
    	}
    	else 
    	{
    		$this->redirect()->toRoute('home');
    	}
    }
    
    
    /**
     * Redirection to error template page
     * @param unknown $message
     * @return \Zend\View\Model\ViewModel
     */
    protected function redirectError( $message )
    {
    	$this->view->setTemplate( 'layout/error/formError' );
    	$this->view->setVariable( 'errorMessage', $message );
    	return $this->view;
    }

}
