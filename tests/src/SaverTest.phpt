<?php

namespace h4kuna\ImageManager;

use Tester\Assert;

$container = require __DIR__ . '/../bootstrap.php';

class SaverTest extends \Tester\TestCase
{

	/** @var Saver */
	private $saver;

	public function __construct(Saver $saver)
	{
		$this->saver = $saver;
	}

	public function testSave()
	{
		$data = $this->saver->save(__DIR__ . '/../config/noImage.jpg');
		Assert::same(100, $data->getHeight());
		Assert::same(81, $data->getWidth());
		Assert::same($data->getFileInfo()->getFilename(), $data->getRelativePath());
		unlink($data);
	}

	public function testSaveSubfolder()
	{
		$data = $this->saver->save(__DIR__ . '/../config/noImage.jpg', 'sub/folder');
		Assert::same(100, $data->getHeight());
		Assert::same(81, $data->getWidth());
		Assert::same('sub/folder/' . $data->getFileInfo()->getFilename(), $data->getRelativePath());
		unlink($data);
	}

	public function testSaveFileupload()
	{
		$filePath = __DIR__ . '/../config/noImage.jpg';
		$fileUpload = new \Nette\Http\FileUpload([
			'name' => basename($filePath),
			'type' => 'image/jpeg',
			'size' => filesize($filePath),
			'tmp_name' => $filePath,
			'error' => 0
		]);
		$data = $this->saver->saveFileUpload($fileUpload, 'sub/folder');
		Assert::same(100, $data->getHeight());
		Assert::same(81, $data->getWidth());
		Assert::same('sub/folder/' . $data->getFileInfo()->getFilename(), $data->getRelativePath());
		unlink($data);
	}

}

/* @var $saver Saver */
$saver = $container->getService('imageManager.saver');

(new SaverTest($saver))->run();
