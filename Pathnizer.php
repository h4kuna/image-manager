<?php

namespace h4kuna;

use Nette\Object;
use Nette\Http\Request;
use Nette\FileNotFoundException;
use Nette\Utils\Strings;

/**
 * Pathnizer
 * add slash on start path and on the end remove slash
 * @example
 * $pathnizer->buildFs('/css/', '/screen.css'); // /absolute_path/css/screen.css
 * $pathnizer->buildFs('/css', 'screen.css'); // /absolute_path/css/screen.css
 * $pathnizer->buildFs('css', 'screen.css'); // /absolute_path/css/screen.css
 * $pathnizer->buildFs('css/screen.css'); // /absolute_path/css/screen.css
 * $pathnizer->buildFs('../css/screen.css'); // /absolute_path../css/screen.css
 * $pathnizer->buildFs('./css/screen.css'); // /absolute_path./css/screen.css
 * $pathnizer->buildFs('css/screen'); // /absolute_path/css/screen
 * $pathnizer->buildFs('css/screen/'); // /absolute_path/css/screen
 *
 * // add basePath from Request
 * $pathnizer->buildUrl('/css/screen.css'); // /sandbox/www/css/screen.css
 * $pathnizer->buildUrl('css/screen.css'); // /sandbox/www/css/screen.css
 * $pathnizer->buildUrl('//css/screen.css'); // http://localhost/sandbox/www/css/screen.css
 *
 * @author Milan Matějček
 */
class Pathnizer extends Object {

    /** @var Path\Url */
    private $url;

    /** @var Request */
    //private $request;

    /** @var Path\Fs */
    private $fs;

    /** @var string */
    private $filename;

    /**
     *
     * @param string $wwwDir
     * @param Request $request
     * @throws FileNotFoundException
     */
    public function __construct($wwwDir, Request $request) {
        if (!($wwwDir = realpath($wwwDir))) {
            throw new FileNotFoundException('Path does not exists: ' . $wwwDir);
        }

        $this->fs = new Path\Fs($wwwDir);
        $this->url = new Path\Url(NULL, $request);
    }

    /**
     * Check if you have right setup. You don't use on production server
     *
     * @return void
     * @throws \RuntimeException
     */
    public function checkSetup() {
        $content = strval(microtime(TRUE));
        $file = 'PathnizerTest.txt';

        $fs = $this->fs->create(array($file));
        $this->fs->mkdirMe();
        if (!@file_put_contents($fs, $content)) {
            throw new \RuntimeException('Pathname must be writeable: ' . $fs);
        }
        $url = $this->url->create(array('//' . $file));
        $urlContent = @file_get_contents($url);
        if ($content == $urlContent) {
            unlink($fs);
            return;
        }
        throw new \RuntimeException('Access the URL must point to the same location as the path to the filesystem. Url: ' . $url . ', FS: ' . $fs);
    }

    /**
     *
     * @param string $path
     * @return Pathnizer
     */
    public function append($path /* , ... */) {
        $args = func_get_args();
        $this->fs->append($args);
        $this->url->append($args);
        return $this;
    }

    /**
     *
     * @param string $s
     * @return Pathnizer
     */
    public function setFilename($s) {
        $this->filename = $s;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     *
     * @return Pathnizer
     */
    public function prepareToSave() {
        $file = $this->fs->create(array($this->filename));
        $fileName = NULL;
        do {
            $ext = $file->getExtension();
            $name = trim(Strings::webalize($file->getBasename($ext), '.', FALSE), '.-');
            $fileName = $name . '.' . Strings::random(3) . '.' . $ext;
            $file = $this->fs->create(array($fileName));
        } while (file_exists($file));
        $this->filename = str_replace(basename($this->filename), $fileName, $this->filename);
        return $this;
    }

    /**
     * @return Path\Fs
     */
    public function getPathname() {
        return $this->fs->create(array($this->filename));
    }

    /**
     * @param bool $absolute
     * @return Path\Url
     */
    public function getUrlname($absolute = FALSE) {
        return $this->url->create(array(($absolute ? '//' : NULL) . $this->filename));
    }

    /**
     *
     * @return string
     */
    public function getPath() {
        return $this->url->getPathname($this->filename);
    }

    /**
     *
     * @return Pathnizer
     */
    public function mkdirMe() {
        $fs = $this->fs->create(array(dirname($this->filename)));
        $fs->mkdirMe();
        return $this;
    }

    /**
     * Build url path
     *
     * @params string
     * @return Path\Url
     */
    public function buildUrl($path /* , ... */) {
        return $this->url->create(func_get_args());
    }

    /**
     * Build filesystem path
     *
     * @params string
     * @return Path\Fs
     */
    public function buildFs($path /* , ... */) {
        return $this->fs->create(func_get_args());
    }

    /**
     * Deep copy
     */
    public function __clone() {
        $this->fs = clone $this->fs;
        $this->url = clone $this->url;
    }

}
