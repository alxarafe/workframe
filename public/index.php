<?php

require __DIR__ . '/../vendor/autoload.php';

use Alxarafe\Tools\Dispatcher\WebDispatcher;
use Alxarafe\Base\Config;
use Alxarafe\Lib\Trans;
use Alxarafe\Tools\Debug;

// Define base paths
if (!defined('BASE_PATH')) {
	define('BASE_PATH', __DIR__); // public/
}

if (!defined('APP_PATH')) {
	define('APP_PATH', realpath(__DIR__ . '/../') ?: dirname(__DIR__));
}

if (!defined('ALX_PATH')) {
	if (is_dir(APP_PATH . '/src/Core')) {
		define('ALX_PATH', APP_PATH);
	} else {
		$alxPath = realpath(APP_PATH . '/vendor/alxarafe/alxarafe');
		define('ALX_PATH', $alxPath ?: APP_PATH);
	}
}

if (!defined('BASE_URL')) {
	define('BASE_URL', \Alxarafe\Lib\Functions::getUrl());
}

// Load Configuration
$config = Config::getConfig();

if ($config && isset($config->main)) {
	$config->main->appName = 'WorkFrame';
	$config->main->appIcon = 'fas fa-hard-hat';
}

// Bootstrap
Debug::initialize();
Trans::initialize();

if ($config && isset($config->main->language)) {
	Trans::setLang($config->main->language);
}

// Asset auto-publish
if (!file_exists(__DIR__ . '/themes/default/css/default.css') || !is_dir(__DIR__ . '/css')) {
	if (class_exists(\Alxarafe\Scripts\ComposerScripts::class)) {
		$io = new class {
			public function write($msg)
			{
				error_log("[AssetAutoPublish] " . $msg);
			}
			public function getIO()
			{
				return $this;
			}
		};
		$event = new class($io) {
			private $io;
			public function __construct($io)
			{
				$this->io = $io;
			}
			public function getIO()
			{
				return $this->io;
			}
		};
		\Alxarafe\Scripts\ComposerScripts::postUpdate($event);
	}
}

// Load Routes
$routesPath = APP_PATH . '/routes.php';
if (file_exists($routesPath)) {
	require_once $routesPath;
}

// Routing
if (php_sapi_name() === 'cli') {
	$module = $argv[1] ?? 'WorkFrame';
	$controller = $argv[2] ?? 'Index';
	$method = $argv[3] ?? 'index';
} else {
	$match = !isset($_GET['module']) ? \Alxarafe\Lib\Router::match($_SERVER['REQUEST_URI']) : null;
	if ($match) {
		$module = $match['module'];
		$controller = $match['controller'];
		$method = $match['action'];
		$_GET = array_merge($_GET, $match['params']);
	} else {
		$module = $_GET['module'] ?? 'WorkFrame';
		$controller = $_GET['controller'] ?? 'Index';
		$method = $_GET['method'] ?? 'index';
	}
}

try {
	WebDispatcher::run($module, $controller, $method);
} catch (Throwable $e) {
	if (class_exists(\CoreModules\Admin\Controller\ErrorController::class) && !headers_sent()) {
		$url = \CoreModules\Admin\Controller\ErrorController::url(true, false) . '&message=' . urlencode($e->getMessage()) . '&trace=' . urlencode($e->getTraceAsString());
		\Alxarafe\Lib\Functions::httpRedirect($url);
	}
	echo "<h1>Application Error</h1>";
	echo "<pre>" . $e->getMessage() . "</pre>";
	echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
