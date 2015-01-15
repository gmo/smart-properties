<?php

use GMO\SmartProperties\DynamicSmartProperties;

class DynamicSmartPropertiesTest extends SmartPropertiesTest {

	protected function getTester() {
		return new DynamicSmartPropertiesTester();
	}
}

/**
 * Class SmartPropertiesTester
 * @property $name
 * @property-read $ssn
 * @property-write $password
 * @property $url
 * @property ArrayIterator $iterator
 * @property string $typeFirstName
 * @property string|null $typeMidName
 * @property string $typeLastName
 * @property mixed $generic
 * @property mixed $generic2
 */
class DynamicSmartPropertiesTester extends PropertiesTester {
	use DynamicSmartProperties;
}
