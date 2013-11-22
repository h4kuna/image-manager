<?php

namespace h4kuna;

use Nette\Object;
use Nette\Http\Request;
use Nette\FileNotFoundException;
use Nette\Utils\Strings;
use h4kuna\ImageManager\Path\Fs;
use h4kuna\ImageManager\Path\Url;

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

    /** @var Url */
    private $url;

    /** @var Request */
    //private $request;

    /** @var Fs */
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

        $this->fs = new Fs($wwwDir);
        $this->url = new Url(NULL, $request);
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

        $fs = clone $this->fs;
        $fs->setFilename($file)->mkdirMe();
        if (!@file_put_contents($fs, $content)) {
            throw new \RuntimeException('Pathname must be writeable: ' . $fs);
        }
        $url = clone $this->url;
        $url->setPath('//' . $file);
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
    public function append($path) {
        $this->fs->setPath($path);
        $this->url->setPath($path);
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
     * @repair
     * @return Pathnizer
     */
    public function prepareToSave() {
        $file = clone $this->fs;
        $file->setFilename($this->filename);
        $temp = $random = $fileName = NULL;
        $ext = $file->getExtension();
        $name = trim(Strings::webalize($file->getBasename($ext), '.', FALSE), '.-');
        do {
            $fileName = $name . $random . '.';
            $random = '.' . Strings::random(3);
            $temp = str_replace(basename($this->filename, $ext), $fileName, $this->filename);
            $file = clone $this->fs;
            $file->setPath($temp);
        } while (file_exists((string) $file));

        $this->filename = $temp;
        return $this;
    }

    /**
     * @return Fs
     */
    public function getPathname() {
        $fs = clone $this->fs;
        $fs->setFilename($this->filename);
        return $fs;
    }

    /**
     * @param bool $absolute
     * @return Url
     */
    public function getUrlname($absolute = FALSE) {
        $url = clone $this->url;
        return $url->setPath(($absolute ? '//' : NULL) . $this->filename);
    }

    /**
     *
     * @return string
     */
    public function getPath() {
        $url = clone $this->url;
        return $url->setPath($this->filename);
    }

    /**
     *
     * @return Pathnizer
     */
    public function mkdirMe() {
        $fs = clone $this->fs;
        $fs->setFilename($this->filename)->mkdirMe();
        return $this;
    }

    /**
     * Build url path
     *
     * @params string
     * @return Url
     */
    public function buildUrl($path) {
        $url = clone $this->url;
        return $url->setPath($path);
    }

    /**
     * Build filesystem path
     *
     * @params string
     * @return Fs
     */
    public function buildFs($path) {
        $fs = clone $this->fs;
        return $fs->setPath($path);
    }

    /**
     * Deep copy
     */
    public function __clone() {
        $this->fs = clone $this->fs;
        $this->url = clone $this->url;
    }

}
