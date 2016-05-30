<?php

namespace h4kuna\ImageManager\Image;

/**
 *
 * @author Milan Matějček
 */
class ImagePlahold extends ImageRender {

    /** @var string */
    private $url;

    /** @var string */
    private $size;

    /** @var array */
    private $info;

    public function __construct($url) {
        parent::__construct('');
        $this->url = $url;
    }

    public function setSize($size) {
        $this->size = $size;
        return $this;
    }

    public function getUrl() {
        return str_replace('$size', $this->size, $this->url);
    }

    public function getImageInfo() {
        if ($this->imageInfo === NULL) {
            $this->imageInfo = explode('x', $this->size);
        }
        return $this->imageInfo;
    }

    public function __toString() {
        return $this->getUrl();
    }

}
