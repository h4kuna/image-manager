<?php

namespace h4kuna\ImageManager\Path;

use Nette\Http\Request;

/**
 *
 * @author Milan Matějček
 */
class Url extends Path {

    private $absolute = FALSE;

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
            self::$basePath = rtrim($request->getUrl()->getBasePath(), '/');
            self::$baseUrl = rtrim($request->getUrl()->getBaseUrl(), '/');
        }
    }

    /** @return string */
    public function getSeparator() {
        return '/';
    }

    /**
     * Url path
     *
     * @return string
     */
    public function getPath() {
        return self::getBase($this->absolute) . $this->path;
    }

    /**
     *
     * @param bool $v
     * @return Url
     */
    public function setAbsolute($v) {
        $this->absolute = (bool) $v;
        return $this;
    }

    /**
     *
     * @param string $path
     * @return Url
     */
    public function setPath($path) {
        $this->setAbsolute(self::isAbsolute($path));
        return parent::setPath($path);
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

    /**
     *
     * @param array $path
     * @return boolean
     */
    private static function isAbsolute(&$path) {
        if (strpos($path, '//') !== FALSE) {
            $path = substr($path, 2);
            return TRUE;
        }
        return FALSE;
    }

}
