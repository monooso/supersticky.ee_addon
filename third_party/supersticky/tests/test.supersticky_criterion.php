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


  public function test__set_date_from__valid_date_works()
  {
    $subject = new Supersticky_criterion();

    $test_date = new DateTime('1973-02-19');
    $this->assertIdentical($test_date,
      $subject->set_date_from($test_date));
  }


  public function test__set_date_from__invalid_argument_throws_error()
  {
    $subject = new Supersticky_criterion();

    $test_date = new StdClass();
    $this->expectError();
    $subject->set_date_from($test_date);
  }


  public function test__set_date_to__valid_date_works()
  {
    $subject = new Supersticky_criterion();

    $test_date = new DateTime('1973-02-19');
    $this->assertIdentical($test_date,
      $subject->set_date_to($test_date));
  }


  public function test__set_date_to__invalid_argument_throws_error()
  {
    $subject = new Supersticky_criterion();

    $test_date = new StdClass();
    $this->expectError();
    $subject->set_date_to($test_date);
  }


  public function test__add_member_group__works_with_valid_group_id()
  {
    $subject = new Supersticky_criterion();

    $this->assertIdentical(10,
      array_pop($subject->add_member_group(10)));
    $this->assertIdentical(101,
      array_pop($subject->add_member_group('101')));
    
    $this->assertIdentical(2, count($subject->get_member_groups()));
  }


  public function test__add_member_group__ignores_invalid_group_ids()
  {
    $subject = new Supersticky_criterion();

    $subject->add_member_group(NULL);
    $subject->add_member_group(TRUE);
    $subject->add_member_group(FALSE);
    $subject->add_member_group(10);     // Valid.
    $subject->add_member_group(array('101', '202'));
    $subject->add_member_group(new StdClass());
    $subject->add_member_group(0);

    $this->assertIdentical(array(10), $subject->get_member_groups());
  }


  public function test__set_member_group__resets_member_groups_array()
  {
    $subject = new Supersticky_criterion(array(
      'member_groups' => array('10', '20', '30')
    ));

    $member_groups = array(15, 25, 35);

    $this->assertIdentical($member_groups,
      $subject->set_member_groups($member_groups));
  }


  public function test__to_array__success()
  {
    $criterion_array = array(
      'date_from' => new DateTime('1973-02-19'),
      'date_to'   => new DateTime('2011-10-03'),
      'member_groups' => array(10, 20, 30)
    );

    $subject = new Supersticky_criterion($criterion_array);
    $this->assertIdentical($criterion_array, $subject->to_array());
  }


}


/* End of file      : test.supersticky_criterion.php */
/* File location    : third_party/supersticky/tests/test.supersticky_criterion.php */