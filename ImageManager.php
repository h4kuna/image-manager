<?php

namespace h4kuna;

use h4kuna\Pathnizer;
use Nette\Object;
use Nette\Http\Request;
use Nette\Image;
use Nette\Http\FileUpload;
use h4kuna\Image\ImageSource;
use Nette\InvalidArgumentException;

/**
 * @todo dodelat nacitani ze servru
 *
 * @author Milan Matějček
 */
class ImageManager extends Object {

    /** @var array */
    private $maxSize;

    /** @var Pathnizer */
    private $sourcePath;

    /** @var Pathnizer */
    private $temp;

    /** @var Request */
    private $request;

    /**
     * List of namespaces
     *
     * @var array
     */
    private $options = array();

    /** @var array */
    private $imageStore = array();

    /**
     * Relative path from source
     *
     * @var string
     */
    private $noImage;

    /** @var array */
    private static $method = array(
        'exact' => Image::EXACT,
        'fill' => Image::FILL,
        'fit' => Image::FIT,
        'shrink_only' => Image::SHRINK_ONLY,
        'stretch' => Image::STRETCH
    );

    /** @var string */
    private $namespace;

    /** @var string */
    private $domain;

    /**
     *
     * @param string $wwwDir absolute path to same dir as basePath
     * @param Request $request
     * @param string $source relative from basePath
     * @param string $temp relative from basePath
     */
    public function __construct($wwwDir, Request $request, $source = 'upload/public', $temp = NULL) {
        $this->request = $request;
        $home = new Pathnizer($wwwDir, $request);
        $temporary = clone $home;

        $this->sourcePath = $home->append($source);
        $this->sourcePath->mkdirMe();

        $this->temp = ($temp === NULL) ? $temporary->append($source, 'temp') : $temporary->append($temp);
        $this->temp->mkdirMe();
    }

    /**
     * @var Pathnizer
     */
    public function getSource() {
        return clone $this->sourcePath;
    }

    /**
     * @return Pathnizer
     */
    public function getTemp() {
        return clone $this->temp;
    }

    /**
     * Check setup
     *
     * @return void
     */
    public function checkSetup() {
        $this->getSource()->checkSetup();
        $this->getTemp()->checkSetup();
        \Nette\Diagnostics\Debugger::barDump('ImageManager is set correctly, you can off test.', 'ImageManager');
    }

    /**
     *
     * @param string $size
     * @return ImageContainer
     */
    public function setMaxSize($size) {
        list($height, $width) = explode('x', $size);
        $this->maxSize = array('height' => $height, 'width' => $width);
        return $this;
    }

    /**
     * Set production domain and set alternative source for download
     *
     * @param string $http
     */
    public function setDomain($http) {
        $this->domain = rtrim($http, '/');
        return $this;
    }

    /**
     * Relative path to source
     *
     * @param string $path
     */
    public function setNoImage($path) {
        $noImage = $this->getSource()->append(dirname($path));
        $noImage->setFilename(basename($path));
        if (!file_exists($noImage->getPathname())) {
            throw new ImageManagerException('Alternative image as NoImage does not exists: ' . $noImage->getPathname());
        }
        $this->noImage = $noImage;
        return $this;
    }

    /**
     *
     * @param string|Pathnizer $name
     * @return ImageSource
     */
    public function createImage($name) {
        if (!($name instanceof Pathnizer)) {
            $name = $this->getSource()->setFilename($name);
        }
        try {
            return $this->imageStore[$name->getFilename()] = new ImageSource($this, $name);
        } catch (ImageManagerException $e) {
            if ($this->domain) {
                $source = @file_get_contents($this->domain . $name->getPath());
                try {
                    $image = @Image::fromString($source);
                    $this->saveNetteImage($image, $name->getFilename());
                    return $this->createImage($name);
                } catch (InvalidArgumentException $e) {
                    // source is not image
                }
            }
            if ($this->noImage) {
                return $this->createImage($this->noImage);
            }
            throw new ImageManagerException('Let\'s try set property domain or noImage.', NULL, $e);
        }
    }

    /**
     * Delete saved file and thumbnails
     *
     * @param string $file
     * @return void
     */
    public function unlink($file) {
        foreach ($this->options as $ns => $v) {
            @unlink($this->getTemp()->append($ns, $file)->getPathname());
        }
        @unlink($this->getSource()->append($file)->getPathname());
        unset($this->imageStore[$file]);
    }

    /**
     *
     * @param string $pathName
     * @return ImageSource
     */
    public function saveImage($pathName, $path = NULL) {
        if ($path) {
            $path .= '/';
        }
        return $this->saveNetteImage(Image::fromFile($pathName), $path . basename($pathName));
    }

    /**
     *
     * @param Image $image
     * @param string $filename
     * @return ImageSource
     */
    public function saveNetteImage(Image $image, $filename) {
        $path = $this->getSource()->setFilename($filename)->prepareToSave()->mkdirMe();
        $this->unlink($path->getFilename());

        if ($this->maxSize) {
            $image->resize($this->maxSize['width'], $this->maxSize['height'], Image::SHRINK_ONLY);
        }

        $image->save($path->getPathname());
        return $this->createImage($path);
    }

    /**
     *
     * @param FileUpload $file
     * @return ImageSource
     */
    public function saveNetteUpload(FileUpload $file, $path = NULL) {
        if ($path) {
            $path .= '/';
        }

        return $this->saveNetteImage($file->toImage(), $path . $file->getName());
    }

    /**
     * DEFINE NAMESPACE ********************************************************
     * *************************************************************************
     */

    /**
     *
     * @param string $namespace
     * @param string $size 300x300
     * @param int|string $method 4|exact
     * @param int $quality
     */
    public function appendNs($namespace, $size, $method = Image::EXACT, $quality = NULL) {
        if ($this->namespace === NULL) {
            $this->namespace = $namespace;
        }

        $this->options[$namespace] = array(
            'size' => $size,
            'method' => self::imageMethod($method),
            'image' => NULL,
            'quality' => $quality
        );
        return $this;
    }

    /**
     *
     * @param string $ns
     * @return ImageManager
     */
    public function setNamespace($ns) {
        if (isset($this->options[$ns])) {
            $this->namespace = $ns;
        } else {
            throw new ImageManagerException('Set undefined namespace: ' . $ns);
        }
        return $this;
    }

    /**
     *
     * @param string $name
     * @return ImageSource
     */
    public function request($name) {
        $data = $this->options[$this->namespace];

        if (!isset($this->imageStore[$name])) {
            $this->imageStore[$name] = $this->createImage($name);
        }

        $image = $this->imageStore[$name];

        if ($data['size']) {
            list($width, $height) = explode('x', $data['size']);
        } else {
            $width = $image->getWidth();
            $height = $image->getHeight();
        }

        $temp = $image->getTempPath($this->namespace);
        return $image->create($width, $height, $temp, $data['method'], $data['quality']);
    }

    /**
     *
     * @param string $method
     * @return int
     * @throws ImageManagerException
     */
    private static function imageMethod($method) {
        if (is_int($method)) {
            return $method;
        }

        $out = 0;
        foreach (explode('|', $method) as $m) {
            if (isset(self::$method[$m])) {
                $out |= self::$method[$m];
            } else {
                throw new ImageManagerException('Unknown method for image: ' . $m);
            }
        }
        return $out;
    }

}
