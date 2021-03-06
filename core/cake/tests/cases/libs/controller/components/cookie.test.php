<?php
/**
 * CookieComponentTest file
 *
 * PHP 5
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/view/1196/Testing>
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/view/1196/Testing CakePHP(tm) Tests
 * @package       cake.tests.cases.libs.controller.components
 * @since         CakePHP(tm) v 1.2.0.5435
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::import('Controller', array('Component', 'Controller'), false);
App::import('Component', 'Cookie');

/**
 * CookieComponentTestController class
 *
 * @package       cake.tests.cases.libs.controller.components
 */
class CookieComponentTestController extends Controller {

/**
 * components property
 *
 * @var array
 * @access public
 */
	public $components = array('Cookie');

/**
 * beforeFilter method
 *
 * @access public
 * @return void
 */
	function beforeFilter() {
		$this->Cookie->name = 'CakeTestCookie';
		$this->Cookie->time = 10;
		$this->Cookie->path = '/';
		$this->Cookie->domain = '';
		$this->Cookie->secure = false;
		$this->Cookie->key = 'somerandomhaskey';
	}
}

/**
 * CookieComponentTest class
 *
 * @package       cake.tests.cases.libs.controller.components
 */
class CookieComponentTest extends CakeTestCase {

/**
 * Controller property
 *
 * @var CookieComponentTestController
 * @access public
 */
	public $Controller;

/**
 * start
 *
 * @access public
 * @return void
 */
	function setUp() {
		$_COOKIE = array();
		$Collection = new ComponentCollection();
		$this->Cookie = $this->getMock('CookieComponent', array('_setcookie'), array($Collection));
		$this->Controller = new CookieComponentTestController();
		$this->Cookie->initialize($this->Controller);
		
		$this->Cookie->name = 'CakeTestCookie';
		$this->Cookie->time = 10;
		$this->Cookie->path = '/';
		$this->Cookie->domain = '';
		$this->Cookie->secure = false;
		$this->Cookie->key = 'somerandomhaskey';

		$this->Cookie->startup($this->Controller);
	}

/**
 * end
 *
 * @access public
 * @return void
 */
	function tearDown() {
		$this->Cookie->destroy();
	}

/**
 * sets up some default cookie data.
 *
 * @return void
 */
	protected function _setCookieData() {
		$this->Cookie->write(array('Encrytped_array' => array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!')));
		$this->Cookie->write(array('Encrytped_multi_cookies.name' => 'CakePHP'));
		$this->Cookie->write(array('Encrytped_multi_cookies.version' => '1.2.0.x'));
		$this->Cookie->write(array('Encrytped_multi_cookies.tag' => 'CakePHP Rocks!'));

		$this->Cookie->write(array('Plain_array' => array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!')), null, false);
		$this->Cookie->write(array('Plain_multi_cookies.name' => 'CakePHP'), null, false);
		$this->Cookie->write(array('Plain_multi_cookies.version' => '1.2.0.x'), null, false);
		$this->Cookie->write(array('Plain_multi_cookies.tag' => 'CakePHP Rocks!'), null, false);
	}

/**
 * test that initialize sets settings from components array
 *
 * @return void
 */
	function testSettings() {
		$settings = array(
			'time' => '5 days',
			'path' => '/'
		);
		$Cookie = new CookieComponent(new ComponentCollection(), $settings);
		$this->assertEqual($Cookie->time, $settings['time']);
		$this->assertEqual($Cookie->path, $settings['path']);
	}

/**
 * testCookieName
 *
 * @access public
 * @return void
 */
	function testCookieName() {
		$this->assertEqual($this->Cookie->name, 'CakeTestCookie');
	}

/**
 * testReadEncryptedCookieData
 *
 * @access public
 * @return void
 */
	function testReadEncryptedCookieData() {
		$this->_setCookieData();
		$data = $this->Cookie->read('Encrytped_array');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!');
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Encrytped_multi_cookies');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!');
		$this->assertEqual($data, $expected);
	}

/**
 * testReadPlainCookieData
 *
 * @access public
 * @return void
 */
	function testReadPlainCookieData() {
		$this->_setCookieData();

		$data = $this->Cookie->read('Plain_array');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!');
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Plain_multi_cookies');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!');
		$this->assertEqual($data, $expected);
	}

/**
 * test a simple write()
 *
 * @return void
 */
	function testWriteSimple() {
		$this->Cookie->expects($this->once())->method('_setcookie');

		$this->Cookie->write('Testing', 'value');
		$result = $this->Cookie->read('Testing');

		$this->assertEquals('value', $result);
	}

/**
 * test write with httpOnly cookies
 *
 * @return void
 */
	function testWriteHttpOnly() {
		$this->Cookie->httpOnly = true;
		$this->Cookie->secure = false;
		$this->Cookie->expects($this->once())->method('_setcookie')
			->with('CakeTestCookie[Testing]', 'value', time() + 10, '/', '', false, true);

		$this->Cookie->write('Testing', 'value', false);
	}

/**
 * test delete with httpOnly
 *
 * @return void
 */
	function testDeleteHttpOnly() {
		$this->Cookie->httpOnly = true;
		$this->Cookie->secure = false;
		$this->Cookie->expects($this->once())->method('_setcookie')
			->with('CakeTestCookie[Testing]', '', time() - 42000, '/', '', false, true);

		$this->Cookie->delete('Testing', false);
	}

/**
 * testWritePlainCookieArray
 *
 * @access public
 * @return void
 */
	function testWritePlainCookieArray() {
		$this->Cookie->write(array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' => 'CakePHP Rocks!'), null, false);

		$this->assertEqual($this->Cookie->read('name'), 'CakePHP');
		$this->assertEqual($this->Cookie->read('version'), '1.2.0.x');
		$this->assertEqual($this->Cookie->read('tag'), 'CakePHP Rocks!');

		$this->Cookie->delete('name');
		$this->Cookie->delete('version');
		$this->Cookie->delete('tag');
	}

/**
 * testReadingCookieValue
 *
 * @access public
 * @return void
 */
	function testReadingCookieValue() {
		$this->_setCookieData();
		$data = $this->Cookie->read();
		$expected = array(
			'Encrytped_array' => array(
				'name' => 'CakePHP',
				'version' => '1.2.0.x',
				'tag' => 'CakePHP Rocks!'),
			'Encrytped_multi_cookies' => array(
				'name' => 'CakePHP',
				'version' => '1.2.0.x',
				'tag' => 'CakePHP Rocks!'),
			'Plain_array' => array(
				'name' => 'CakePHP',
				'version' => '1.2.0.x',
				'tag' => 'CakePHP Rocks!'),
			'Plain_multi_cookies' => array(
				'name' => 'CakePHP',
				'version' => '1.2.0.x',
				'tag' => 'CakePHP Rocks!'));
		$this->assertEqual($data, $expected);
	}

/**
 * testDeleteCookieValue
 *
 * @access public
 * @return void
 */
	function testDeleteCookieValue() {
		$this->_setCookieData();
		$this->Cookie->delete('Encrytped_multi_cookies.name');
		$data = $this->Cookie->read('Encrytped_multi_cookies');
		$expected = array('version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!');
		$this->assertEqual($data, $expected);

		$this->Cookie->delete('Encrytped_array');
		$data = $this->Cookie->read('Encrytped_array');
		$this->assertNull($data);

		$this->Cookie->delete('Plain_multi_cookies.name');
		$data = $this->Cookie->read('Plain_multi_cookies');
		$expected = array('version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!');
		$this->assertEqual($data, $expected);

		$this->Cookie->delete('Plain_array');
		$data = $this->Cookie->read('Plain_array');
		$this->assertNull($data);
	}

/**
 * testReadingCookieArray
 *
 * @access public
 * @return void
 */
	function testReadingCookieArray() {
		$this->_setCookieData();

		$data = $this->Cookie->read('Encrytped_array.name');
		$expected = 'CakePHP';
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Encrytped_array.version');
		$expected = '1.2.0.x';
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Encrytped_array.tag');
		$expected = 'CakePHP Rocks!';
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Encrytped_multi_cookies.name');
		$expected = 'CakePHP';
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Encrytped_multi_cookies.version');
		$expected = '1.2.0.x';
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Encrytped_multi_cookies.tag');
		$expected = 'CakePHP Rocks!';
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Plain_array.name');
		$expected = 'CakePHP';
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Plain_array.version');
		$expected = '1.2.0.x';
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Plain_array.tag');
		$expected = 'CakePHP Rocks!';
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Plain_multi_cookies.name');
		$expected = 'CakePHP';
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Plain_multi_cookies.version');
		$expected = '1.2.0.x';
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Plain_multi_cookies.tag');
		$expected = 'CakePHP Rocks!';
		$this->assertEqual($data, $expected);
	}

/**
 * testReadingCookieDataOnStartup
 *
 * @access public
 * @return void
 */
	function testReadingCookieDataOnStartup() {

		$data = $this->Cookie->read('Encrytped_array');
		$this->assertNull($data);

		$data = $this->Cookie->read('Encrytped_multi_cookies');
		$this->assertNull($data);

		$data = $this->Cookie->read('Plain_array');
		$this->assertNull($data);

		$data = $this->Cookie->read('Plain_multi_cookies');
		$this->assertNull($data);

		$_COOKIE['CakeTestCookie'] = array(
				'Encrytped_array' => $this->__encrypt(array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!')),
				'Encrytped_multi_cookies' => array(
						'name' => $this->__encrypt('CakePHP'),
						'version' => $this->__encrypt('1.2.0.x'),
						'tag' => $this->__encrypt('CakePHP Rocks!')),
				'Plain_array' => 'name|CakePHP,version|1.2.0.x,tag|CakePHP Rocks!',
				'Plain_multi_cookies' => array(
						'name' => 'CakePHP',
						'version' => '1.2.0.x',
						'tag' => 'CakePHP Rocks!'));
		$this->Cookie->startup(null);

		$data = $this->Cookie->read('Encrytped_array');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!');
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Encrytped_multi_cookies');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!');
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Plain_array');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!');
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Plain_multi_cookies');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!');
		$this->assertEqual($data, $expected);
		$this->Cookie->destroy();
		unset($_COOKIE['CakeTestCookie']);
	}

/**
 * testReadingCookieDataWithoutStartup
 *
 * @access public
 * @return void
 */
	function testReadingCookieDataWithoutStartup() {
		$data = $this->Cookie->read('Encrytped_array');
		$expected = null;
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Encrytped_multi_cookies');
		$expected = null;
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Plain_array');
		$expected = null;
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Plain_multi_cookies');
		$expected = null;
		$this->assertEqual($data, $expected);

		$_COOKIE['CakeTestCookie'] = array(
				'Encrytped_array' => $this->__encrypt(array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!')),
				'Encrytped_multi_cookies' => array(
						'name' => $this->__encrypt('CakePHP'),
						'version' => $this->__encrypt('1.2.0.x'),
						'tag' => $this->__encrypt('CakePHP Rocks!')),
				'Plain_array' => 'name|CakePHP,version|1.2.0.x,tag|CakePHP Rocks!',
				'Plain_multi_cookies' => array(
						'name' => 'CakePHP',
						'version' => '1.2.0.x',
						'tag' => 'CakePHP Rocks!'));

		$data = $this->Cookie->read('Encrytped_array');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!');
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Encrytped_multi_cookies');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!');
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Plain_array');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!');
		$this->assertEqual($data, $expected);

		$data = $this->Cookie->read('Plain_multi_cookies');
		$expected = array('name' => 'CakePHP', 'version' => '1.2.0.x', 'tag' =>'CakePHP Rocks!');
		$this->assertEqual($data, $expected);
		$this->Cookie->destroy();
		unset($_COOKIE['CakeTestCookie']);
	}


/**
 * test that no error is issued for non array data.
 *
 * @return void
 */
	function testNoErrorOnNonArrayData() {
		$this->Cookie->destroy();
		$_COOKIE['CakeTestCookie'] = 'kaboom';

		$this->assertNull($this->Cookie->read('value'));
	}

/**
 * encrypt method
 *
 * @param mixed $value
 * @return string
 * @access private
 */
	function __encrypt($value) {
		if (is_array($value)) {
			$value = $this->__implode($value);
		}
		return "Q2FrZQ==." . base64_encode(Security::cipher($value, $this->Cookie->key));
	}

/**
 * implode method
 *
 * @param array $value
 * @return string
 * @access private
 */
	function __implode($array) {
		$string = '';
		foreach ($array as $key => $value) {
			$string .= ',' . $key . '|' . $value;
		}
		return substr($string, 1);
	}
}
