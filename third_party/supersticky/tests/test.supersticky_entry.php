<?php 

/**
 * SuperSticky Entry tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

require_once PATH_THIRD .'supersticky/classes/supersticky_entry.php';

class Test_supersticky_entry extends Testee_unit_test_case {

  private $_props;
  private $_subject;


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


  public function test__add_criterion__works_with_valid_criterion()
  {
    $criterion = new Supersticky_criterion(array(
      'date_from'     => new DateTime('1935-06-14'),
      'date_to'       => new DateTime('1944-02-18'),
      'member_groups' => array(10, 20, 30)
    ));

    $subject = new Supersticky_entry();

    $this->assertIdentical(
      array($criterion),
      $subject->add_criterion($criterion)
    );
  }


  public function test__set_criteria__works_with_valid_criteria()
  {
    $criteria = array(
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1935-06-14'),
        'date_to'       => new DateTime('1944-02-18'),
        'member_groups' => array(10, 20, 30)
      )),
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1973-02-19'),
        'date_to'       => new DateTime('2011-10-03'),
        'member_groups' => array(15, 25, 35)
      ))
    );

    $subject = new Supersticky_entry();
    $this->assertIdentical($criteria, $subject->set_criteria($criteria));
  }


  public function test__set_criteria__ignores_invalid_criteria()
  {
    $criteria = array(
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1935-06-14'),
        'date_to'       => new DateTime('1944-02-18'),
        'member_groups' => array(10, 20, 30)
      )),
      array('apple', 'orange', 'pear'),
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1973-02-19'),
        'date_to'       => new DateTime('2011-10-03'),
        'member_groups' => array(15, 25, 35)
      )),
      new StdClass()
    );

    $subject = new Supersticky_entry();
    $this->assertIdentical(
      array($criteria[0], $criteria[2]),
      $subject->set_criteria($criteria)
    );
  }


  public function test__set_criteria__clears_criteria_before_proceeding()
  {
    $start_criteria = array(
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1935-06-14'),
        'date_to'       => new DateTime('1944-02-18'),
        'member_groups' => array(10, 20, 30)
      ))
    );

    $new_criteria = array(
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1973-02-19'),
        'date_to'       => new DateTime('2011-10-03'),
        'member_groups' => array(15, 25, 35)
      ))
    );

    $subject = new Supersticky_entry($start_criteria);

    $this->assertIdentical($new_criteria,
      $subject->set_criteria($new_criteria));
  }


  public function test__set_entry_id__works_with_valid_values()
  {
    $subject = new Supersticky_entry();
    $this->assertIdentical(123, $subject->set_entry_id(123));
    $this->assertIdentical(456, $subject->set_entry_id('456'));
  }


  public function test__set_entry_id__fails_with_invalid_values()
  {
    $entry_id = 999;
    $subject  = new Supersticky_entry(array('entry_id' => $entry_id));

    $this->assertIdentical($entry_id, $subject->set_entry_id('wibble'));
    $this->assertIdentical($entry_id, $subject->set_entry_id(FALSE));
    $this->assertIdentical($entry_id, $subject->set_entry_id(TRUE));
    $this->assertIdentical($entry_id, $subject->set_entry_id(0));
    $this->assertIdentical($entry_id, $subject->set_entry_id(-100));
    $this->assertIdentical($entry_id, $subject->set_entry_id(array('apple')));
    $this->assertIdentical($entry_id, $subject->set_entry_id(new StdClass()));
    $this->assertIdentical($entry_id, $subject->set_entry_id(NULL));
  }


  public function test__to_array__success()
  {
    $criteria = array(
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1935-06-14'),
        'date_to'       => new DateTime('1944-02-18'),
        'member_groups' => array(10, 20, 30)
      )),
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1973-02-19'),
        'date_to'       => new DateTime('2011-10-03'),
        'member_groups' => array(15, 25, 35)
      ))
    );

    $entry_id = 999;

    $expected_result = array(
      'criteria' => array(
        $criteria[0]->to_array(),
        $criteria[1]->to_array()
      ),
      'entry_id' => $entry_id
    );

    $subject = new Supersticky_entry(array(
      'criteria' => $criteria,
      'entry_id' => $entry_id
    ));

    $actual_result = $subject->to_array();
    
    ksort($actual_result);
    ksort($expected_result);

    $this->assertIdentical($expected_result, $actual_result);
  }


}


/* End of file      : test.supersticky_entry.php */
/* File location    : third_party/supersticky/tests/test.supersticky_entry.php */
