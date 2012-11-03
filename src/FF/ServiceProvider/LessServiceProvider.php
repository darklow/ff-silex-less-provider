<?php
namespace FF\ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class LessServiceProvider implements ServiceProviderInterface
{
	const CLASSIC    = 'classic';
	const COMPRESSED = 'compressed';

	public function register(Application $app)
	{
	}

	public function boot(Application $app)
	{
		if (!isset($app['less.sources'], $app['less.target'])) {
			throw new \Exception("Application['less.sources'] and ['less.target'] must be defined");
		}

		$formatter = isset($app['less.formatter']) ? $app['less.formatter'] : self::CLASSIC;
		$sources   = $app['less.sources'];
		$target    = $app['less.target'];
		$targetDir = dirname($app['less.target']);

		if (!is_writable($targetDir)) {
			throw new \Exception("Target file directory \"$targetDir\" is not writable");
		}

		$targetContent   = '';
		$needToRecompile = false;
		!is_array($sources) and $sources = array($sources);
		foreach ($sources as $source) {
			if (!file_exists($source)) {
				throw new \Exception('Could not find less source dir or file "'.$source.'"');
			}
			if (!$needToRecompile) {
				$needToRecompile = $this->targetNeedsRecompile($source, $target);
			}
			if ($needToRecompile) {
				$handle = new \lessc($source);
				$handle->setFormatter($formattar);
				$targetContent .= $handle->parse();
			}
		}

		if (isset($handle)) {
			if ($targetContent) {
				file_put_contents($target, $targetContent);
				if(isset($app['less.target_mode'])){
					chmod($target, $app['less.target_mode']);
				}
			} else {
				throw new \Exception("No content after parsing less source files. Please check your .less files");
			}
		}
	}

	private function targetNeedsRecompile($source, $target)
	{
		if (!file_exists($target)) {
			return true;
		}

		$sourceDir   = dirname($source);
		$targetMtime = filemtime($target);
		foreach (new \DirectoryIterator($sourceDir) as $lessFile) {
			/** @var $lessFile \DirectoryIterator */
			if ($lessFile->isFile() && substr($lessFile->getFilename(), -5) === '.less') {
				if ($lessFile->getMTime() > $targetMtime) {
					return true;
				}
			}
		}
	}
}
