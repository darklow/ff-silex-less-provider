<?php
namespace ServiceProvider\Tests;

use FF\ServiceProvider\LessServiceProvider;

class LessServiceProviderTest extends \PHPUnit_Framework_TestCase
{
	public function testProvider()
	{

		$sourceFile = __DIR__.'/Resources/less/style.less';
		$targetFile = __DIR__.'/Resources/css/style.css';

		$fileExists = file_exists($targetFile);
		$fileExists and unlink($targetFile);

		$app = $this->getApp($sourceFile, $targetFile);
		$app->boot();
		$this->assertFileExists($targetFile);

		$fileMTime = filesize($targetFile);
		$app = $this->getApp($sourceFile, $targetFile);
		$app->boot();
		$this->assertEquals($fileMTime, filesize($targetFile));

		$this->switchFiles();
		$app = $this->getApp($sourceFile, $targetFile);
		$app->boot();
		$this->assertEquals($fileMTime, filesize($targetFile));

	}

	protected function getApp($sourceFile, $targetFile)
	{
		$app = new \Silex\Application();
		$app->register(new LessServiceProvider(), array(
			'less.sources'     => array($sourceFile),
			'less.target'      => $targetFile,
			'less.target_mode' => 0775,
		));
		return $app;
	}

	protected function switchFiles()
	{
		$importFile  = __DIR__.'/Resources/less2/import.less';
		$importFile2 = __DIR__.'/Resources/less2/import2.less';
		$importFileTmp = __DIR__.'/Resources/less2/import_tmp.less';

		rename($importFile, $importFileTmp);
		rename($importFile2, $importFile);
		rename($importFileTmp, $importFile2);

		touch($importFile);
		touch($importFile2);
	}
}
