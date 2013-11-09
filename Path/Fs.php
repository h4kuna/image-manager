<?php

namespace h4kuna\Path;

/**
 *
 * @author Milan Matějček
 */
class Fs extends Path {

    public function getSeparator() {
        return DIRECTORY_SEPARATOR;
    }

    /** @return bool */
    public function mkdirMe() {
        return @mkdir($this->path, 0777, TRUE);
    }

    public function create(array $path) {
        return new static($this->joinPath(array(-1 => $this->path) + $path));
    }

}
