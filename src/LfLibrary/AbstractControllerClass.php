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


class AbstractControllerClass extends AbstractActionController
{ 
    protected $view = NULL;
    protected $session = NULL;
    protected $uri = NULL;
    protected $em;
    protected $translate;


    /**
     * 
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
    
    public function getTranslator()
    {
    	$translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
    	return $translate;
    }
    
    public function getTranslate( $string )
    {
        $translate = $this->getServiceLocator()->get('viewhelpermanager')->get('translate');
        return $translate( $string );
    }
    
    public function setEntityManager(EntityManager $em)
    {
    	$this->em = $em;
    }
    
    public function getEntityManager()
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
    
    
    /*******************************************************************/
    // IMAGE GENERATION METHOD
    /******************************************************************/
    
    /**
     * Resize logo to fit screen size
     * @param unknown $source_image_path
     * @param unknown $thumbnail_image_path
     * @return boolean
     */
    protected function generate_image_thumbnail($source_image_path, $thumbnail_image_path)
    {
    	list( $source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
    
    	switch ($source_image_type)
    	{
    		case IMAGETYPE_GIF:
    			$source_gd_image = imagecreatefromgif($source_image_path);
    			break;
    		case IMAGETYPE_JPEG:
    			$source_gd_image = imagecreatefromjpeg($source_image_path);
    			break;
    		case IMAGETYPE_PNG:
    			$source_gd_image = imagecreatefrompng($source_image_path);
    			break;
    	}
    
    	if ($source_gd_image === false) {
    		return false;
    	}
    
    	$source_aspect_ratio = $source_image_width / $source_image_height;
    	$thumbnail_aspect_ratio = THUMBNAIL_IMAGE_MAX_WIDTH / THUMBNAIL_IMAGE_MAX_HEIGHT;
    
    	if ($source_image_width <= THUMBNAIL_IMAGE_MAX_WIDTH && $source_image_height <= THUMBNAIL_IMAGE_MAX_HEIGHT)
    	{
    		$thumbnail_image_width = $source_image_width;
    		$thumbnail_image_height = $source_image_height;
    	}
    	elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
    		$thumbnail_image_width = (int) (THUMBNAIL_IMAGE_MAX_HEIGHT * $source_aspect_ratio);
    		$thumbnail_image_height = THUMBNAIL_IMAGE_MAX_HEIGHT;
    	}
    	else {
    		$thumbnail_image_width = THUMBNAIL_IMAGE_MAX_WIDTH;
    		$thumbnail_image_height = (int) (THUMBNAIL_IMAGE_MAX_WIDTH / $source_aspect_ratio);
    	}
    
    	$thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
    	imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
    	
    	switch ($source_image_type)
    	{
    		case IMAGETYPE_GIF:
    			$newImage = imagegif($thumbnail_gd_image, $thumbnail_image_path);
    			break;
    		case IMAGETYPE_JPEG:
    			$newImage = imagejpeg($thumbnail_gd_image, $thumbnail_image_path, 90);
    			break;
    		case IMAGETYPE_PNG:
    			$newImage = imagepng($thumbnail_gd_image, $thumbnail_image_path, 9);
    			break;
    	}
    	
    	//$newImage = imagejpeg($thumbnail_gd_image, $thumbnail_image_path, 90);
    	
    	imagedestroy($source_gd_image);
    	imagedestroy($thumbnail_gd_image);
    
    	return $newImage;
    	//return true;
    }
    
    
    
    /*******************************************************************/
    // FILES MANAGEMENT METHODS METHODS
    /******************************************************************/
    
    
    /**
     * Remove folder and subfolders
     * @param unknown $dir
     */
    protected function removeFolder($dir)
    {
    	if (is_dir($dir)) {
    		$objects = scandir($dir);
    		foreach ($objects as $object) {
    			if ($object != "." && $object != "..") {
    				if (filetype($dir."/".$object) == "dir")
    					$this->removeFolder($dir."/".$object);
    				else unlink   ($dir."/".$object);
    			}
    		}
    		reset($objects);
    		rmdir($dir);
    	}
    }
    
	/**
     * Remove files in specific folder
     * @param unknown $folderName
     */
    protected function clearFolder( $folderName )
    {
    	$handle=opendir( $folderName );
    	
    	while (false !== ($fichier = readdir($handle))) 
    	{
    		if (($fichier != ".") && ($fichier != "..")) 
    		{
    			unlink( $folderName.$fichier);
    		}
    	}
    }
    
    /**
     * Send an email
     * @param unknown $htmlBody
     * @param unknown $textBody
     * @param unknown $subject
     * @param unknown $from
     * @param unknown $to
     */
    protected function sendMail( $htmlBody, $textBody, $subject, $from, $to )
    {
    	$htmlPart = new MimePart($htmlBody);
    	$htmlPart->type = "text/html";
    
    	$textPart = new MimePart($textBody);
    	$textPart->type = "text/plain";
    
    	$body = new MimeMessage();
    	$body->setParts(array($textPart, $htmlPart));

    
    	$message = new Mail\Message();
    	$message->setFrom($from);
    	$message->addTo($to);
    	$message->setSubject($subject);
    
    	$message->setEncoding("UTF-8");
    	$message->setBody($body);
    	$message->getHeaders()->get('content-type')->setType('multipart/alternative');
    
    	$transport = new Mail\Transport\Sendmail();
    	$transport->send($message);
    }
    
    
    
}
