<?php
namespace GMO\SmartProperties;

use ReflectionClass;

trait DynamicSmartProperties {
	use SmartProperties;

	private static $generatedProperties;

	protected function publicProperties() {
		if (!static::$generatedProperties) {
			static::$generatedProperties = $this->generateProperties();
		}
		return static::$generatedProperties;
	}

	private function generateProperties() {
		$properties = [];

		$reflection = new ReflectionClass($this);
		$lines = explode("\n", $reflection->getDocComment());
		foreach ($lines as $line) {

			if (preg_match('#@property(-(?<opts>read|write))?\s+(?<type>.+)?\s*\$(?<var>\w+)#', $line, $matches)) {
				$opts = [];
				if ($matches['opts'] == 'read') {
					$opts = ['read' => true, 'write' => false];
				} elseif ($matches['opts'] == 'write') {
					$opts = ['read' => false, 'write' => true];
				}

				$opts['type'] = false;
				if (($types = $matches['type']) !== '') {
					$types = explode('|', $types);
					$types = array_map('trim', $types);
					$opts['type'] = $types;
				}

				$properties[$matches['var']] = $opts;
			}
		}

		return $properties;
	}
}
