<?php
namespace FF\ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Create a LESS service provider to generate CSS file from LESS files
 */
class LessServiceProvider implements ServiceProviderInterface
{
	/**
	 * Value for classic CSS generated from LESS source files.
	 *
	 * @var string
	 */
	const FORMATTER_CLASSIC    = 'classic';

	/**
	 * Value for compressed CSS generated from LESS source files.
	 *
	 * @var string
	 */
	const FORMATTER_COMPRESSED = 'compressed';

	public function register(Application $app)
	{
	}

	public function boot(Application $app)
	{
		// Validate this params.
		$this->validate($app);

		// Define default formatter if not already set.
		$formatter = isset($app['less.formatter']) ? $app['less.formatter'] : self::FORMATTER_CLASSIC;
		$sources   = $app['less.sources'];
		$target    = $app['less.target'];

		$targetContent   = '';
		$needToRecompile = false;
		!is_array($sources) and $sources = array($sources);

		foreach ($sources as $source) {
			if (!$needToRecompile) {
				$needToRecompile = $this->targetNeedsRecompile($source, $target);
			}
			if ($needToRecompile) {
				$handle = new \lessc($source);
				$handle->setFormatter($formatter);
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

	/**
	 * Check if is required to recompile LESS file.
	 *
	 * @param string $source
	 *   File to compile (if required)
	 *
	 * @param string $target
	 *   Destination file for parsed LESS
	 *
	 * @return bool
	 *   Indicate fi LESS file must be parsed
	 */
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

		return false;
	}

	/**
	 * Validate application settings.
	 *
	 * @param \Silex\Application $app
	 *   Application to validate
	 *
	 * @throws \Exception
	 *   If some params is not valid throw exception.
	 */
	private function validate(Application $app) {
		// Params must be defined.
		if (!isset($app['less.sources'], $app['less.target'])) {
			throw new \Exception("Application['less.sources'] and ['less.target'] must be defined");
		}

		// Destination directory must be writable.
		$targetDir = dirname($app['less.target']);
		if (!is_writable($targetDir)) {
			throw new \Exception("Target file directory \"$targetDir\" is not writable");
		}

		// Validate formatter type.
		if (isset($app['less.formatter']) && !in_array($app['less.formatter'], array('classic', 'compressed'))) {
			throw new \Exception("Application['less.formatter'] can be 'classic' or 'compressed'");
		}

		// Validate source files.
		$sources = $app['less.sources'];
		!is_array($sources) and $sources = array($sources);
		foreach ($sources as $source) {
			if (!file_exists($source)) {
				throw new \Exception('Could not find less source dir or file "'.$source.'"');
			}
		}
	}
}
