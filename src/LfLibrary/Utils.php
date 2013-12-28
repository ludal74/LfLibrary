<?php
namespace LfLibrary;

use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

use \Exception;

class Utils
{
    
    
    static public function _translate($str)
    {
    	return $str;
    }
    
    
    
    /*******************************************************************/
    // FILES MANAGEMENT METHODS METHODS
    /******************************************************************/
    
    
    
    /**
     * Remove folder and subfolders
     * @param unknown $dir
     */
    static public function removeFolder($dir)
    {
        if (is_dir($dir)) 
        {
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
        else
        {
            throw new \Exception('folder "'.$dir.'" not found and can\'t be removed !!', "0", NULL);
        }
    }
    
    /**
     * Remove files in specific folder
     * @param unknown $folderName
     */
    static public function clearFolder( $folderName )
    {
        if( file_exists( $folderName ) )
        {
            $handle = opendir( $folderName );
             
            while (false !== ($fichier = readdir($handle)))
            {
                if (($fichier != ".") && ($fichier != ".."))
                {
                    unlink( $folderName.$fichier);
                }
            }   
        }
        else
        {
            throw new \Exception('folder "'.$folderName.'" not found and can\'t be cleared !!', "0", NULL);
        }
    }
    
    
    
    /*******************************************************************/
    // EMAIL SENDING
    /******************************************************************/

    
    /**
     * Send an email
     * @param unknown $htmlBody
     * @param unknown $textBody
     * @param unknown $subject
     * @param unknown $from
     * @param unknown $to
     */
    static public function sendMail( $htmlBody, $textBody, $subject, $from, $to )
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
    
    
    
    
    
    /*******************************************************************/
    // IMAGE GENERATION METHOD
    /******************************************************************/
    
    
    
    /**
     * Resize logo to fit screen size
     * @param unknown $source_image_path
     * @param unknown $thumbnail_image_path
     * @return boolean
     */
    protected function generate_image_thumbnail($source_image_path, $thumbnail_image_path, $maxWidth, $maxHeight)
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
        $thumbnail_aspect_ratio = $maxWidth / $maxHeight;
    
        if ($source_image_width <= $maxWidth && $source_image_height <= $maxHeight)
        {
            $thumbnail_image_width = $source_image_width;
            $thumbnail_image_height = $source_image_height;
        }
        elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
            $thumbnail_image_width = (int) ($maxHeight * $source_aspect_ratio);
            $thumbnail_image_height = $maxHeight;
        }
        else {
            $thumbnail_image_width = $maxWidth;
            $thumbnail_image_height = (int) ($maxWidth / $source_aspect_ratio);
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
}
