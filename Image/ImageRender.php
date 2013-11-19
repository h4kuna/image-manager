<?php

namespace h4kuna\ImageManager\Image;

use Nette\Utils\Html;

/**
 * Description of Image
 *
 * @author Milan Matějček
 */
abstract class ImageRender extends \SplFileInfo implements IImageSource {

    /** @var array */
    protected $imageInfo;

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
     *
     * @return int
     */
    public function getHeight() {
        $this->getImageInfo(); // 100x faster than $this->getImage()->getHeight()
        return $this->imageInfo[1];
    }

    /**
     *
     * @return int
     */
    public function getWidth() {
        $this->getImageInfo();
        return $this->imageInfo[0];
    }

}
