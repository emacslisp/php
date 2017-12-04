<?php
/**
 * Handles adding and dispatching events
 *
 * @package Requests
 * @subpackage Utilities
 */

/**
 * Handles adding and dispatching events
 *
 * @package Requests
 * @subpackage Utilities
 */
class Requests_Hooks implements Requests_Hooker {
	/**
	 * Registered callbacks for each hook
	 *
	 * @var array
	 */
	protected $hooks = array();

	/**
	 * Constructor
	 */
	public function __construct() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		// pass
	}

	/**
	 * Register a callback for a hook
	 *
	 * @param string $hook Hook name
	 * @param callback $callback Function/method to call on event
	 * @param int $priority Priority number. <0 is executed earlier, >0 is executed later
	 */
	public function register($hook, $callback, $priority = 0) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		if (!isset($this->hooks[$hook])) {
			$this->hooks[$hook] = array();
		}
		if (!isset($this->hooks[$hook][$priority])) {
			$this->hooks[$hook][$priority] = array();
		}

		$this->hooks[$hook][$priority][] = $callback;
	}

	/**
	 * Dispatch a message
	 *
	 * @param string $hook Hook name
	 * @param array $parameters Parameters to pass to callbacks
	 * @return boolean Successfulness
	 */
	public function dispatch($hook, $parameters = array()) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		if (empty($this->hooks[$hook])) {
			return false;
		}

		foreach ($this->hooks[$hook] as $priority => $hooked) {
			foreach ($hooked as $callback) {
				call_user_func_array($callback, $parameters);
			}
		}

		return true;
	}
}