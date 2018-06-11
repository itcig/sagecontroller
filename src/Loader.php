<?php

namespace Cig\Sage\Controller;

use Cig\Sage\Controller\Utils;
use Brain\Hierarchy\Hierarchy;

class Loader
{
	// Dep
	private $hierarchy;

	// User
	private $namespace;
	private $path;

	// Internal
	private $listOfFiles;
	private $classesToRun = [];

	/**
	 * Construct
	 *
	 * Initialise the Loader methods
	 */
	public function __construct(Hierarchy $hierarchy)
	{
		// Pass in WordPress hierarchy and set to param for reference
		$this->hierarchy = $hierarchy;

		// Set the default or custom namespace used for Controller files
		$this->setNamespace();

		// Set the path using $this->namespace assuming PSR4 autoloading
		$this->setPath();

		// Return if there are no Controller files
		if (!file_exists($this->path)) {
			return;
		}

		// Set the list of files from the Controller files namespace/path
		$this->setListOfFiles();

		// Set the classes to run from the list of files
		$this->setClassesToRun();

		// Set the aliases for static functions from the list of classes to run
		$this->setClassesAlias();

		// Add the -data body classes for the Twig filter
		$this->addBodyDataClasses();
	}

	/**
	 * Set Namespace
	 *
	 * Set the namespace from the filter or use the default
	 */
	protected function setNamespace()
	{
		$this->namespace = (has_filter('sage/controller/namespace')
			? apply_filters('sage/controller/namespace', rtrim($this->namespace))
			: 'App\Controllers');
	}

	/**
	 * Set Path
	 *
	 * Set the path assuming PSR4 autoloading from $this->namespace
	 */
	protected function setPath()
	{
		$this->path = get_theme_file_path() . '/' . str_replace('\\', '/', $this->namespace);
	}

	/**
	 * Set File List
	 *
	 * Recursively get file list and place into array
	 */
	protected function setListOfFiles()
	{
		$this->listOfFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->path));
	}

	/**
	 * Set Class Instances
	 *
	 * Load each Class instance and store in $instances[]
	 */
	protected function setClassesToRun()
	{
		foreach ($this->listOfFiles as $filename => $file) {
			// Exclude non-PHP files
			if (!Utils::isFilePhp($filename)) {
				continue;
			}

			// Exclude non-Controller classes
			if (!Utils::doesFileContain($filename, 'extends Controller')) {
				continue;
			}

			// Build namespace off directory which allows for subdirectors of controllers
			// /app/controllers -> App\Controllers\
			// /app/controllers/subdir -> App\Controllers\Subdir\
			$namespaceAppend = str_replace('/', '\\', ltrim(str_replace($this->path, '', pathinfo($filename, PATHINFO_DIRNAME)), '/'));

			// Uppercase each directory in path
			if (!empty($namespaceAppend)) implode('\\', array_map('ucfirst', explode('\\', $namespaceAppend)));

			// Set the classes to run
			$this->classesToRun[] = $this->namespace . ( empty($namespaceAppend) ? '' : '\\' . $namespaceAppend) . '\\' . pathinfo($filename, PATHINFO_FILENAME);
		}
	}

	/**
	 * Set Class Alias
	 *
	 * Remove namespace from static functions
	 */
	public function setClassesAlias()
	{
		// Alias each class from $this->classesToRun
		foreach ($this->classesToRun as $class) {
			class_alias($class, (new \ReflectionClass($class))->getShortName());
		}
	}

	/**
	 * Set Document Classes
	 *
	 * Set the classes required for the twig filter to pass on data
	 * @return array
	 */
	protected function addBodyDataClasses()
	{
		add_filter('body_class', function ($body) {
			global $wp_query;
			// Get the template hierarchy from WordPress
			$templates = $this->hierarchy->getTemplates($wp_query);
			// Reverse the templates returned from $this->hierarchy
			$templates = array_reverse($templates);
			// Add app-data to classes array
			$classes[] = 'data-app';

			foreach ($templates as $template) {
				if (strpos($template, '.twig') || $template === 'index.php') {
					continue;
				}
				if ($template === 'index') {
					$template = 'index.php';
				}
				$classes[] = 'data-' . basename(str_replace(['.twig', '.php'], '', $template));
			}

			// Special exception for Wordpress 404 template so that  we don't have a class name beginging with an integer
			if (in_array('data-404', $classes)) {
				$classes[] = 'data-not-found';
			}

			// Return the new body class list for WordPress
			return array_merge($body, $classes);
		});
	}

	/**
	 * Get Classes To Run
	 *
	 * @return array
	 */
	public function getClassesToRun()
	{
		return $this->classesToRun;
	}
}
