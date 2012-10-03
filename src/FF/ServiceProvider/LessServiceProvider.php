<?php
namespace FF\ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class LessServiceProvider implements ServiceProviderInterface
{
	public function register(Application $app)
	{
	}

	public function boot(Application $app)
	{
		if (!isset($app['less.sources'], $app['less.target'])) {
			throw new \Exception("Application['less.sources'] and ['less.target'] must be defined");
		}

		$sources   = $app['less.sources'];
		$target    = $app['less.target'];
		$targetDir = dirname($app['less.target']);

		if (!is_writable($targetDir)) {
			throw new \Exception("Target file directory \"$targetDir\" is not writable");
		}

		$targetContent = '';
		!is_array($sources) and $sources = array($sources);
		foreach ($sources as $source) {
			if (!file_exists($source)) {
				throw new \Exception('Could not find less source dir or file "'.$source.'"');
			}
			if ($this->targetNeedsRecompile($source, $target)) {
				$handle = new \lessc($source);
				$targetContent .= $handle->parse();
			}
		}

		if (isset($handle)) {
			if ($targetContent) {
				file_put_contents($target, $targetContent, isset($app['less.target_mode']) ? $app['less.target_mode'] : null);
			} else {
				throw new \Exception("No content after parsing less source files. Please check your .less files");
			}
		}
	}

	private function targetNeedsRecompile($source, $target)
	{
		if (!file_exists($target) || filemtime($source) > filemtime($target)) {
			return true;
		}
	}
}
