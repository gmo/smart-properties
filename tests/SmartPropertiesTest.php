<?php

use GMO\SmartProperties\SmartProperties;

class SmartPropertiesTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @expectedException \RuntimeException
	 */
	public function testInternalProperty() {
		$tester = $this->getTester();
		$tester->__get('internal');
	}

	public function testPublicProperty() {
		$tester = $this->getTester();
		$this->assertSame('test', $tester->name);
	}

	//region Read/Write Only Test
	public function testReadOnlyPropertyRead() {
		$tester = $this->getTester();
		$this->assertSame(1234, $tester->ssn);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testReadOnlyPropertyWrite() {
		$tester = $this->getTester();
		$tester->ssn = 0;
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testWriteOnlyPropertyRead() {
		$tester = $this->getTester();
		$tester->password;
	}

	public function testWriteOnlyPropertyWrite() {
		$tester = $this->getTester();
		$tester->password = '1234';
		$this->assertSame('1234', $tester->testGetPassword());
	}
	//endregion

	public function testOverrideGetterSetter() {
		$tester = $this->getTester();
		$tester->url = 'google';
		$this->assertSame('www.google.com', $tester->url);
	}

	//region Type Checking Tests
	public function testSetterTypesImplied() {
		$tester = $this->getTester();
		$tester->typeFirstName = 'Tester';
	}

	/**
	 * @expectedException \UnexpectedValueException
	 */
	public function testSetterTypesImpliedFailure() {
		$tester = $this->getTester();
		$tester->typeFirstName = null;
	}

	public function testSetterTypesImpliedObject() {
		$tester = $this->getTester();
		$tester->iterator = new RecursiveArrayIterator();
	}

	/**
	 * @expectedException \UnexpectedValueException
	 */
	public function testSetterTypesImpliedObjectFailure() {
		$tester = $this->getTester();
		$tester->iterator = new EmptyIterator();
	}

	public function testSetterSingleType() {
		$tester = $this->getTester();
		$tester->typeLastName = 'Derp';
	}

	/**
	 * @expectedException \UnexpectedValueException
	 */
	public function testSetterSingleTypeFailure() {
		$tester = $this->getTester();
		$tester->typeLastName = null;
	}

	public function testSetterMultipleTypes() {
		$tester = $this->getTester();
		$tester->typeMidName = 'Derp';
		$tester->typeMidName = null;
	}

	/**
	 * @expectedException \UnexpectedValueException
	 */
	public function testSetterMultipleTypesFailure() {
		$tester = $this->getTester();
		$tester->typeMidName = 0;
	}

	public function testSetterTypesDisabled() {
		$tester = $this->getTester();
		$tester->generic = 'asdf';
		$tester->generic2 = 'asdf';
	}
	//endregion

	protected function getTester() {
		return new SmartPropertiesTester();
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
class PropertiesTester {

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

}

class SmartPropertiesTester extends PropertiesTester {

	use SmartProperties;

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
