<?php

namespace h4kuna\ImageManager;

abstract class ImageManagerException extends \Exception {}

class RemoteFileDoesNotExistsException extends ImageManagerException {}

class ResolutionIsNotAllowedException extends ImageManagerException {}