<?php

namespace Paradise\FileBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * File
 *
 * @ORM\Table(name="files")
 * @ORM\Entity
 * @Gedmo\Uploadable(callback="myCallbackMethod", filenameGenerator="SHA1", allowOverwrite=true, appendNumber=true)
 * @ORM\HasLifecycleCallbacks()
 */
class File {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="path", type="string")
     * @Gedmo\UploadableFilePath
     */
    private $path;

    /**
     * @ORM\Column(name="name", type="string")
     * @Gedmo\UploadableFileName
     */
    private $name;

    /**
     * @ORM\Column(name="mime_type", type="string")
     * @Gedmo\UploadableFileMimeType
     */
    private $mimeType;

    /**
     * @ORM\Column(name="size", type="decimal")
     * @Gedmo\UploadableFileSize
     */
    private $size;

    /**
     * @var string
     *
     * @Assert\File(
     *     maxSize = "8M",
     *     notFoundMessage = "The file was not found on the disk",
     *     uploadErrorMessage = "Error in upload file"
     * )
     */
    private $file;

    /**
     * @ORM\Column(name="width", type="string", nullable=true)
     */
    private $width;

    /**
     * @ORM\Column(name="height", type="string", nullable=true)
     */
    private $height;

    public function myCallbackMethod(array $info) {
        // Do some stuff with the file..
    }

    /**
     * Set file
     *
     * @param string $file
     *
     * @return Poi
     */
    public function setFile($file) {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile() {
        return $this->file;
    }

    public function __toString() {
        if (null !== $this->getFile() && null !== $this->getFile()->getRealPath()) {
            return $this->getFile()->getRealPath();
        } else {
            return $this->getPath();
        }
        
        return $this->getPath();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return File
     */
    public function setPath($path) {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return File
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     *
     * @return File
     */
    public function setMimeType($mimeType) {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string
     */
    public function getMimeType() {
        return $this->mimeType;
    }

    /**
     * Set size
     *
     * @param string $size
     *
     * @return File
     */
    public function setSize($size) {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize() {
        return $this->size;
    }

    public function copyFile($source, $dest) {
        $this->source = $source;
        $this->dest = $dest;

        $dest = $this->getNewDest($dest);
        if (copy($source, $dest)) {
            $this->setPath($dest);
        }
    }

    public function getNewDest($dest) {
        $pathInfo = pathinfo($this->source);
        $dest = $dest;
        $dest.= (substr($dest, -1) !== '/') ? '/' : '';
        $dest.= $pathInfo['basename'];
        $dest = str_replace($pathInfo['basename'], '', $dest);

        if (file_exists($dest)) {
            $limit = 0;
            while ($limit < 1000) {
                $file = $dest . $pathInfo['filename'] . '-' . $limit . '.' . $pathInfo['extension'];
                if (!file_exists($file)) {
                    $dest = $file;
                    break;
                }
                $limit++;
            }
        } else {
            $this->checkAndCreateDirectory($dest);
            $dest.= $pathInfo['basename'];
        }

        return $dest;
    }

    public function checkAndCreateDirectory($path, $right = '775') {
        $PATH = explode('/', $path);

        $completePath = '';
        foreach ($PATH as $dir) {
            $completePath.= $dir . '/';

            if (!file_exists($completePath)) {
                mkdir($completePath, $right);
            }
        }
    }

    /**
     * Set width
     *
     * @param string $width
     * @return File
     */
    public function setWidth($width) {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return string 
     */
    public function getWidth() {
        if ($this->width === null) {
            $this->getDim();
        }
        return $this->width;
    }

    /**
     * Set height
     *
     * @param string $height
     * @return File
     */
    public function setHeight($height) {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return string 
     */
    public function getHeight() {
        if ($this->width === null) {
            $this->getDim();
        }
        return $this->height;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate 
     */
    public function getDim() {
        try {
            if ($this->getPath() !== null) {
                $dimensions = getimagesize($this->getPath());
                if ($dimensions[0]) {
                    $this->setWidth($dimensions[0]);
                }
                if ($dimensions[1]) {
                    $this->setHeight($dimensions[1]);
                }
            }
        } catch (Exception $ex) {
            unset($ex);
        }
    }
}
