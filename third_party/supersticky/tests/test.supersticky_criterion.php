<?php 

/**
 * SuperSticky Criterion tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

require_once PATH_THIRD .'supersticky/classes/supersticky_criterion.php';

class Test_supersticky_criterion extends Testee_unit_test_case {

  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Constructor.
   *
   * @access  public
   * @return  void
   */
  public function setUp()
  {
    parent::setUp();
  }


  public function test__set_type__valid_types_work()
  {
    $subject = new Supersticky_criterion();

    $this->assertIdentical(Supersticky_criterion::TYPE_DATE_RANGE,
      $subject->set_type(Supersticky_criterion::TYPE_DATE_RANGE));
  
    $this->assertIdentical(Supersticky_criterion::TYPE_MEMBER_GROUP,
      $subject->set_type(Supersticky_criterion::TYPE_MEMBER_GROUP));
  }


  public function test__set_type__invalid_types_do_not_work()
  {
    $subject = new Supersticky_criterion(array(
      'type' => Supersticky_criterion::TYPE_DATE_RANGE));

    $this->assertIdentical(Supersticky_criterion::TYPE_DATE_RANGE,
      $subject->set_type('day_of_month'));
  
    $this->assertIdentical(Supersticky_criterion::TYPE_DATE_RANGE,
      $subject->set_type(42));
    
    $this->assertIdentical(Supersticky_criterion::TYPE_DATE_RANGE,
      $subject->set_type(new StdClass()));
    
    $this->assertIdentical(Supersticky_criterion::TYPE_DATE_RANGE,
      $subject->set_type(array('apple', 'orange')));
  }


  public function test__set_type__valid_values_work()
  {
    $subject = new Supersticky_criterion();

    $this->assertIdentical(123.456, $subject->set_value(123.456));
    $this->assertIdentical(123456, $subject->set_value(123456));
    $this->assertIdentical('A, B', $subject->set_value('A, B'));
  }


  public function test__set_type__invalid_values_do_not_work()
  {
    $subject = new Supersticky_criterion(array('value' => 'ABC123'));

    $this->assertIdentical('ABC123', $subject->set_value(new StdClass()));
    $this->assertIdentical('ABC123', $subject->set_value(array('apple')));
    $this->assertIdentical('ABC123', $subject->set_value(TRUE));
    $this->assertIdentical('ABC123', $subject->set_value(FALSE));
    $this->assertIdentical('ABC123', $subject->set_value(NULL));
  }


  public function test__to_array__success()
  {
    $props_array = array(
      'type'  => Supersticky_criterion::TYPE_MEMBER_GROUP,
      'value' => 99
    );

    $subject          = new Supersticky_criterion($props_array);
    $actual_result    = $subject->to_array();
    $expected_result  = $props_array;
    
    ksort($actual_result);
    ksort($expected_result);

    $this->assertIdentical($expected_result, $actual_result);
  }


}


/* End of file      : test.supersticky_criterion.php */
/* File location    : third_party/supersticky/tests/test.supersticky_criterion.php */
