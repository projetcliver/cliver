<?php

class ArgvParser {
	private $args = [];

	public function ArgvParser($a = null) {
		global $argv;
		if ($a == null) {
			$a = $argv;
			array_shift($a);
		}
		$this->args = $this->arguments($a);
	}

	protected function arguments($a) {
		$_ARG = array();
		foreach ($a as $arg) {
			if (ereg('--([^=]+)=(.*)',$arg,$reg)) {
				$_ARG[$reg[1]] = $reg[2];
			} elseif (ereg('([^=]+)=(.*)',$arg,$reg)) {
				$_ARG[$reg[1]] = $reg[2];
			} elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)) {
				$_ARG[$reg[1]] = true;
			} elseif(ereg('([a-zA-Z0-9]+)',$arg,$reg)) {
				$_ARG[$reg[1]] = true;
			}

		}
		return $_ARG;
	}

	public static function getInstance() {
		return new self();
	}

	public function getOption($key) {
		return ($this->exists($key) ? $this->args[$key] : null);
	}

	public function exists($key) {
		return array_key_exists($key, $this->args);
	}

	public function getArgs() {
		return $this->args;
	}
}

?>
