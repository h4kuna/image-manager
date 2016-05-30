<?php

namespace h4kuna\ImageManager\Path;

use Nette\Object;

interface IPath {

    /** @return string */
    public function getSeparator();
}

/**
 * @author Milan Matějček
 */
abstract class Path extends Object implements IPath {

    /** @var string */
    protected $path;

    /**
     *
     * @param string $path
     */
    public function __construct($path) {
        $this->setPath($path);
    }

    /** @return string */
    public function getPath() {
        return $this->path;
    }

    /**
     *
     * @param string $path
     */
    public function setPath($path) {
        if ($this->path) {
            $path = $this->getSeparator() . $path;
        }
        $this->path .= rtrim(preg_replace('~(\\\|/)+~', $this->getSeparator(), $path), '\/');
        return $this;
    }

    /** @return string */
    public function __toString() {
        return $this->getPath();
    }

}
