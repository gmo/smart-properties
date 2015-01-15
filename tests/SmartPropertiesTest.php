<?php

use GMO\SmartProperties\SmartProperties;

require_once __DIR__ . '/../vendor/autoload.php';

class SmartPropertiesTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @expectedException \RuntimeException
	 */
	public function testInternalProperty() {
		$tester = new SmartPropertiesTester();
		$tester->internal;
	}

	public function testPublicProperty() {
		$tester = new SmartPropertiesTester();
		$this->assertSame('test', $tester->name);
	}

	//region Read/Write Only Test
	public function testReadOnlyPropertyRead() {
		$tester = new SmartPropertiesTester();
		$this->assertSame(1234, $tester->ssn);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testReadOnlyPropertyWrite() {
		$tester = new SmartPropertiesTester();
		$tester->ssn = 0;
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testWriteOnlyPropertyRead() {
		$tester = new SmartPropertiesTester();
		$tester->password;
	}

	public function testWriteOnlyPropertyWrite() {
		$tester = new SmartPropertiesTester();
		$tester->password = '1234';
		$this->assertSame('1234', $tester->testGetPassword());
	}
	//endregion

	public function testOverrideGetterSetter() {
		$tester = new SmartPropertiesTester();
		$tester->url = 'google';
		$this->assertSame('www.google.com', $tester->url);
	}

	//region Type Checking Tests
	public function testSetterTypesImplied() {
		$tester = new SmartPropertiesTester();
		$tester->typeFirstName = 'Tester';
	}

	/**
	 * @expectedException \UnexpectedValueException
	 */
	public function testSetterTypesImpliedFailure() {
		$tester = new SmartPropertiesTester();
		$tester->typeFirstName = null;
	}

	public function testSetterTypesImpliedObject() {
		$tester = new SmartPropertiesTester();
		$tester->iterator = new RecursiveArrayIterator();
	}

	/**
	 * @expectedException \UnexpectedValueException
	 */
	public function testSetterTypesImpliedObjectFailure() {
		$tester = new SmartPropertiesTester();
		$tester->iterator = new EmptyIterator();
	}

	public function testSetterSingleType() {
		$tester = new SmartPropertiesTester();
		$tester->typeLastName = 'Derp';
	}

	/**
	 * @expectedException \UnexpectedValueException
	 */
	public function testSetterSingleTypeFailure() {
		$tester = new SmartPropertiesTester();
		$tester->typeLastName = null;
	}

	public function testSetterMultipleTypes() {
		$tester = new SmartPropertiesTester();
		$tester->typeMidName = 'Derp';
		$tester->typeMidName = null;
	}

	/**
	 * @expectedException \UnexpectedValueException
	 */
	public function testSetterMultipleTypesFailure() {
		$tester = new SmartPropertiesTester();
		$tester->typeMidName = 0;
	}

	public function testSetterTypesDisabled() {
		$tester = new SmartPropertiesTester();
		$tester->generic = 'asdf';
		$tester->generic2 = 'asdf';
	}
	//endregion
}

/**
 * Class SmartPropertiesTester
 * @property $name
 * @property-read $ssn
 * @property-write $password
 * @property $url
 * @property ArrayIterator iterator
 * @property string $typeFirstName
 * @property string|null $typeMidName
 * @property string $typeLastName
 * @property mixed $generic
 * @property mixed $generic2
 */
class SmartPropertiesTester {
	use SmartProperties;

	protected $name = 'test';
	protected $ssn = 1234;
	protected $password = '';
	protected $internal;
	protected $url;

	// Type checking properties
	protected $typeFirstName = '';
	protected $typeMidName;
	protected $typeLastName;
	protected $iterator;
	protected $generic;
	protected $generic2;

	public function __construct() {
		$this->iterator = new ArrayIterator();
	}

	public function testGetPassword() {
		return $this->password;
	}

	public function getUrl() {
		return 'www.' . $this->url;
	}

	public function setUrl($url) {
		$this->url = $url . '.com';
	}

	/** @return string[] List of properties that are public via magic */
	protected function publicProperties() {
		return [
			'name' => true,
			'ssn' => ['write' => false],
			'password' => ['read' => false],
			'url' => true,

			'typeFirstName' => true,
			'typeLastName' => ['type' => 'string'],
			'typeMidName' => ['type' => ['string', 'null']],
			'iterator' => true,
			'generic' => ['type' => false],
			'generic2' => ['type' => 'mixed'],
		];
	}
}
