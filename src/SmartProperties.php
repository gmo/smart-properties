<?php
namespace GMO\SmartProperties;

trait SmartProperties {

	/** @return string[] List of properties that are public via magic */
	abstract protected function publicProperties();

	public function __get($property) {
		if (method_exists($this, 'get' . ucfirst($property))) {
			return $this->{'get' . ucfirst($property)}();
		}
		$this->checkProperty($property, 'read');
		return $this->$property;
	}

	public function __set($property, $value) {
		if (method_exists($this, 'set' . ucfirst($property))) {
			$this->{'set' . ucfirst($property)}($value);
			return;
		}
		$this->checkProperty($property, 'write');

		$this->checkType($property, $value);

		$this->$property = $value;
	}

	private function checkProperty($property, $opt = null) {
		$properties = $this->publicProperties();
		if (!isset($properties[$property])) {
			throw new \RuntimeException("Property \"$property\" does not exist or is not public");
		}
		if ($opt === null || !is_array($properties[$property])) {
			return;
		}
		$propOpts = $properties[$property];
		if ($opt === 'read' && isset($propOpts['read']) && !$propOpts['read']) {
			throw new \RuntimeException("Property \"$property\" does not exist or is not public");
		}
		if ($opt === 'write' && isset($propOpts['write']) && !$propOpts['write']) {
			throw new \RuntimeException("Property \"$property\" does not exist or is not public");
		}
	}

	private function checkType($property, $value) {
		$properties = $this->publicProperties();
		if (is_array($properties[$property]) && isset($properties[$property]['type'])) {
			$types = $properties[$property]['type'];
			$types = is_array($types) ? $types : [$types];
		} else {
			$type = gettype($this->$property) !== 'object' ? gettype($this->$property) : get_class($this->$property);
			$types = [$type];
		}
		$this->validateType($property, $value, $types);
	}

	private function validateType($property, $value, array $types) {
		foreach ($types as $type) {
			if ($type === false || $type === 'mixed') {
				return;
			}
			$type = strtolower($type);
			if (function_exists('is_' . $type)) {
				if (call_user_func('is_' . $type, $value)) {
					return;
				}
			} elseif (is_a($value, $type)) {
				return;
			}
		}
		$givenType = gettype($value) !== 'object' ? gettype($value) : get_class($value);
		throw new \UnexpectedValueException("Property \"$property\" expects type: (" . implode('|', $types) . "), but was given: $givenType");
	}
}
