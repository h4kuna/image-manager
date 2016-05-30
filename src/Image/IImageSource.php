<?php

namespace h4kuna\ImageManager\Image;

/**
 * Description of IImageSource
 *
 * @author Milan Matějček
 */
interface IImageSource {

    public function getUrl();

    public function getWidth();

    public function getHeight();

    public function getImageInfo();
}
