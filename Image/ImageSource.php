<?php

namespace h4kuna\ImageManager\Image;

use h4kuna\Pathnizer;
use Nette\Image;
use Nette\Utils\Html;
use h4kuna\ImageManager;
use h4kuna\ImageManagerException;

/**
 *
 * @author Milan Matějček
 */
class ImageSource extends \SplFileInfo {

    /** @var ImageManager */
    protected $parent;

    /** @var Pathnizer */
    private $pathnizer;

    /** @var ImageSource */
    private $original;

    /** @var array */
    private $imageInfo;

    /**
     *
     * @param ImageManager $imageManager
     * @param Pathnizer $pathnizer
     * @throws ImageManagerException
     */
    public function __construct(ImageManager $imageManager, Pathnizer $pathnizer) {
        $this->parent = $imageManager;
        $this->pathnizer = $pathnizer;
        $file = $pathnizer->getPathname();
        if (!file_exists($file)) {
            throw new ImageManagerException('File does not exists: ' . $file);
        }
        parent::__construct($file);
    }

    /**
     *
     * @param bool $absolute
     * @return string
     */
    public function getUrl($absolute = FALSE) {
        return $this->pathnizer->getUrlname($absolute);
    }

    /**
     *
     * @return int
     */
    public function getHeight() {
        $this->getImageInfo(); // 100x faster than $this->getImage()->getHeight()
        return $this->imageInfo[1];
    }

    /**
     *
     * @return array
     */
    public function getImageInfo() {
        if ($this->imageInfo === NULL) {
            $this->imageInfo = @getimagesize($this->getPathname()); // @ - files smaller than 12 bytes causes read error
        }
        return $this->imageInfo;
    }

    /**
     *
     * @return int
     */
    public function getWidth() {
        $this->getImageInfo();
        return $this->imageInfo[0];
    }

    /**
     *
     * @param string $alt
     * @return Html
     */
    public function render($alt = NULL) {
        $img = Html::el('img');
        $img->addAttributes(array(
            'src' => $this->getUrl(),
            'alt' => $alt,
            'width' => $this->getWidth(),
            'height' => $this->getHeight()
        ));
        return $img;
    }

    /**
     * @return Image
     */
    public function getImage() {
        return Image::fromFile($this->getPathname());
    }

    /**
     *
     * @param int $width
     * @param int $height
     * @param Pathnizer|string $temp
     * @param int $method
     * @param int $quality
     * @return ImageSource
     */
    public function create($width, $height, $namespace = NULL, $method = Image::SHRINK_ONLY, $quality = NULL) {
        $temp = !($namespace instanceof Pathnizer) ? $this->getTempPath($namespace) : $namespace;
        $file = $temp->getPathname();
        if (!file_exists($file)) {
            $temp->mkdirMe();
            $this->getOriginal()->getImage()
                    ->resize($width, $height, $method)
                    ->save((string) $file, $quality);
        }

        $image = new static($this->parent, $temp);
        $image->setOriginal($this->getOriginal());
        return $image;
    }

    /**
     *
     * @return string
     */
    public function getFilename() {
        return $this->pathnizer->getFilename();
    }

    /**
     *
     * @param string $namespace
     * @return ImageSource
     */
    public function getTempPath($namespace) {
        $temp = $this->parent->getTemp()->append($namespace);
        $temp->setFilename($this->getFilename());
        return $temp;
    }

    /**
     *
     * @return string
     */
    public function __toString() {
        return (string) $this->getUrl();
    }

    /**
     *
     * @param ImageSource $image
     * @return ImageSource
     */
    protected function setOriginal(ImageSource $image) {
        $this->original = $image;
        return $this;
    }

    /**
     *
     * @return ImageSource
     */
    private function getOriginal() {
        return $this->original ? $this->original : $this;
    }

    public function __clone() {
        $this->pathnizer = clone $this->pathnizer;
    }

}
