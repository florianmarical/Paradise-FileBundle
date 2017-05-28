<?php

namespace Paradise\FileBundle\Service;

use Gedmo\Uploadable\MimeType\MimeTypesExtensionsMap;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceFile extends MimeTypesExtensionsMap
{
    
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    
    public function getFileTypeByMime($mime){
        if(isset(self::$map[$mime])){
            return self::$map[$mime];
        }else{
            return '';
        }
    }
    public function getUploadFileByUrl($url, $name){
        if($url && $name){
            $image = file_get_contents($url);
            $image_name = __DIR__ . '/../../../../web/tmp/'.'img-'.uniqid().'.tmp';
            file_put_contents($image_name, $image);
            $type = mime_content_type($image_name);
            $size = filesize($image_name);
            $ext = $this->getFileTypeByMime($type);
            return new UploadedFile($image_name, $name.'.'.$ext, $type, $size);
        }
        return null;
    }
    
    
    
    public function uploadFile($Image, $path){
        $uploadableManager = $this->container->get('stof_doctrine_extensions.uploadable.manager');
        $listener = $uploadableManager->getUploadableListener();
        $listener->setDefaultPath($path);

        $uploadableManager->markEntityToUpload($Image, $Image->getFile());
    }
}

