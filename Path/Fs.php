<?php

namespace h4kuna\ImageManager\Path;

/**
 *
 * @author Milan Matějček
 */
class Fs extends Path {

    /** @var bool */
    private $isFile = NULL;

    public function getSeparator() {
        return DIRECTORY_SEPARATOR;
    }

    /**
     * @return string
     */
    public function getExtension() {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     *
     * @param NULL|string $suffix
     * @return string
     */
    public function getBasename($suffix = NULL) {
        return basename($this->path, $suffix);
    }

    /** @return string */
    public function getDirname() {
        if ($this->isFile()) {
            return dirname($this->path);
        }
        return $this->path;
    }

    /** @return bool */
    public function mkdirMe() {
        return @mkdir($this->getDirname(), 0777, TRUE);
    }

    /**
     *
     * @param string $name
     * @return Fs
     */
    public function setFilename($name) {
        $this->setPath($name);
        return $this->iAmFile();
    }

    /** @return Fs */
    public function iAmFile() {
        $this->isFile = TRUE;
        return $this;
    }

    /**
     * Is file? NULL = unknown
     *
     * @return boolean
     */
    public function isFile() {
        if (file_exists($this->path) && is_file($this->path)) {
            return TRUE;
        }
        return $this->isFile;
    }

}
