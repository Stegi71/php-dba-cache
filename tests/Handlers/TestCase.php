<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'DummyFixtures.php';

class CacheHandlersTestCase extends PHPUnit_Framework_TestCase
{
  /**
   * @var stdClass
   */
  protected $_object;

  /**
   * @var string
   */
  protected $_identifier;

  /**
   * Prepares the environment before running a test.
   */
  protected function setUp()
  {
    parent::setUp();

    $stdClass        = new stdClass();
    $stdClass->title = 'Zweiundvierz';
    $stdClass->from  = 'Joe';
    $stdClass->to    = 'Jane';
    $stdClass->body  = new Dummy();

    $this->_object     = $stdClass;
    $this->_identifier = md5('stdClass' . time());
    $this->_general_file = dirname(dirname(dirname(__FILE__))) . '/tests/_drafts/flatfile.db';
  }

  /**
   * Cleans up the environment after running a test.
   */
  protected function tearDown()
  {
    unset($this->_object, $this->_identifier);
    parent::tearDown();
  }


  #general test

  /**
   * Will be overridden
   * @var string
   */
  protected $_general_file = 'flatfile.db', $_general_handler = 'flatfile' , $_general_mode = 'c';


  public function testPuttingForever()
  {
    try {
      $cache = new Cache($this->_general_file, $this->_general_handler, $this->_general_mode);
    } catch(RuntimeException $e) {
     $this->markTestSkipped($e->getMessage());
    }

    $cache->forever('forever', array('forever'=>123));


    $res = $cache->getIds();

    $this->assertInstanceOf('ArrayObject', $res);
    $this->assertNotEmpty($res->getArrayCopy());

    $cache->closeDba();
  }

  public function badHandlersProvider()
  {
    return array(
      array('bad-bad-handler'),
      array(123),
      array(1),
      array('0'),
      array(' '),
      array(null),
      array(true),
      array(false),
    );
  }

  /**
   * @expectedException RuntimeException
   * @dataProvider badHandlersProvider
   */
  public function testIfBadHandlerGiven()
  {
    new Cache($this->_general_file, 'bad-bad-handler');
  }

  /**
   * @expectedException RuntimeException
   */
  public function testIfBadDbFileGiven()
  {
    new Cache('/path/to/bad-bad-file.db', $this->_general_handler, 'r');
  }

}
