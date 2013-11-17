<?php

namespace h4kuna\ImageManager\Path;

/**
 *
 * @author Milan Matějček
 */
class Fs extends Path {

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

    /** @return bool */
    public function mkdirMe() {
        return @mkdir($this->path, 0777, TRUE);
    }

    public function create(array $path) {
        return new static($this->joinPath(array(-1 => $this->path) + $path));
    }

}
