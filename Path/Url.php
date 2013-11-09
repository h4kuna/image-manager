<?php

namespace h4kuna\Path;

use Nette\Http\Request;
use Nette\Http\Url AS NHU;

/**
 *
 * @author Milan Matějček
 */
class Url extends Path {

    private $absolute;

    /** @var string */
    private static $baseUrl;

    /** @var string */
    private static $basePath;

    /** @var Request */
    private static $request;

    /**
     *
     * @param string $path
     * @param Request $request
     */
    public function __construct($path, Request $request) {
        parent::__construct($path);

        if (!self::$request) {
            self::$request = $request;
            self::$basePath = $request->getUrl()->getBasePath();
            self::$baseUrl = $request->getUrl()->getBaseUrl();
        }
    }

    /** @return string */
    public function getSeparator() {
        return '/';
    }

    /**
     * Append to path
     *
     * @param array $path
     * @return string
     */
    public function append(array $path) {
        $this->absolute = self::isAbsolute($path);
        return $this->joinPath(array(self::getBase($this->absolute), parent::append($path)));
    }

    /**
     * Create new instance and append
     *
     * @param array $path
     * @return Url
     */
    public function create(array $path) {
        $absolute = self::isAbsolute($path);
        $url = new static($this->joinPath(array(-1 => $this->path) + $path), self::$request);
        return $url->setAbsolute($absolute);
    }

    /**
     * Url path
     *
     * @return string
     */
    public function getPath() {
        return $this->joinPath(array(self::getBase($this->absolute), $this->path));
    }

    /**
     *
     * @param string $file
     * @return string
     */
    public function getPathname($file) {
        return $this->joinPath(array($this->path, $file));
    }

    /**
     *
     * @param bool $v
     * @return Url
     */
    private function setAbsolute($v) {
        $this->absolute = (bool) $v;
        return $this;
    }

    /**
     *
     * @param array $path
     * @return boolean
     */
    private static function isAbsolute(array &$path) {
        $absolute = FALSE;
        if (strpos($path[0], '//') !== FALSE) {
            $path[0] = substr($path[0], 2);
            $absolute = TRUE;
        }
        return $absolute;
    }

    /**
     * Relative or absolute path?
     *
     * @param arary $path
     * @return string
     */
    private static function getBase($absolute) {
        return $absolute ? self::$baseUrl : self::$basePath;
    }

}
