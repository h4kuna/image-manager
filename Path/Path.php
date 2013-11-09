<?php

namespace h4kuna\Path;

use Nette\Object;

interface IPath {

    /** @return string */
    public function getSeparator();

    /**
     * Create new instance and append
     *
     * @param array $path
     * @return Url
     */
    public function create(array $path);
}

/**
 * @author Milan Matějček
 */
abstract class Path extends Object implements IPath {

    /** @var string */
    protected $path;

    /** @var bool */
    private static $used;

    /**
     *
     * @param string $path
     */
    public function __construct($path) {
        $this->path = $path;
    }

    /**
     * Join params to path
     *
     * @param array $path
     * @return string
     */
    protected function joinPath(array $path) {
        $outPath = NULL;
        self::$used = FALSE;
        foreach ($path as $p) {
            if (!$p) {
                continue;
                self::$used = TRUE;
            }
            if (self::isBegin($p)) {
                $outPath = rtrim($p, '\/');
            } else {
                $outPath .= $this->getSeparator() . trim($p, '\/');
            }
        }
        return $outPath;
    }

    /**
     * @param string $path
     * @return boolean
     */
    private static function isBegin($path) {
        if (self::$used) {
            return FALSE;
        }
        self::$used = TRUE;
        // windows or relative
        return preg_match('~^([a-z]{1,4}:|\.{1,2})~i', $path);
    }

    /** @return string */
    public function getPath() {
        return $this->path;
    }

    /**
     *
     * @param array $path
     * @return string
     */
    public function append(array $path) {
        return $this->path = $this->joinPath(array(-1 => $this->path) + $path);
    }

    /** @return string */
    public function __toString() {
        return $this->getPath();
    }

}
