<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * SuperSticky module tests.
 *
 * @author          Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright       Experience Internet
 * @package         Supersticky
 */

require_once PATH_THIRD .'supersticky/mod.supersticky.php';
require_once PATH_THIRD .'supersticky/tests/mocks/mock.supersticky_model.php';

class Test_supersticky extends Testee_unit_test_case {

    private $_model;
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

        Mock::generate('Mock_supersticky_model',
          get_class($this) .'_mock_model');

        $this->_ee->supersticky_model = $this->_get_mock('model');

        $this->_model   = $this->_ee->supersticky_model;
        $this->_subject = new Supersticky();
    }


    public function test__build_sql_query__works_with_basic_data()
    {
      $group_id     = 10;
      $querystring  = '';

      $this->_ee->session->expectOnce('userdata', array('group_id'));
      $this->_ee->session->setReturnValue('userdata', $group_id);

      $ss_entries = array(
        new Supersticky_entry(array(
          'entry_id' => '300',
          'criteria' => array(
            new Supersticky_criterion(array(
              'date_from' => new DateTime('-1 day'),
              'date_to'   => new DateTime('+ 1 day'),
              'member_groups' => array(10, 20, 30)
            ))
          )
        )),
        new Supersticky_entry(array(
          'entry_id' => '100',
          'criteria' => array(
            new Supersticky_criterion(array(
              'date_from' => new DateTime('-1 day'),
              'date_to'   => new DateTime('+ 1 day'),
              'member_groups' => array(10)
            ))
          )
        ))
      );

      $this->_model->expectOnce('get_supersticky_entries_for_date',
        array(new DateTime()));

      $this->_model->setReturnValue('get_supersticky_entries_for_date',
        $ss_entries);

      // Set the current SQL.
      $initial_sql = "SELECT a.b FROM c WHERE d = 'e' ORDER BY f ASC";

      $order_sql = 'ORDER BY ss_order_index ASC,';

      $where_sql = 'LEFT JOIN (
          SELECT 300 AS entry_id, 1 AS order_index
          UNION ALL
          SELECT 100, 2
        ) AS ss ON ss.entry_id = t.entry_id
        WHERE';

      $expected_result = str_replace(
        'SELECT',
        'SELECT IFNULL(ss.order_index, 999999) AS ss_order_index,',
        $initial_sql
      );

      $expected_result  = str_replace('ORDER BY', $order_sql, $expected_result);
      $expected_result  = str_replace('WHERE', $where_sql, $expected_result);
      
      $this->_subject->sql = $initial_sql;
      $this->_subject->build_sql_query($querystring);

      $actual_result = $this->_subject->sql;

      /**
       * HACK ALERT:
       * We need to ignore whitespace when checking that the actual result
       * and the expected result are equal.
       */

      $pattern = '/[\t\n ]+/';

      $this->assertIdentical(
        preg_replace($pattern, '', $expected_result),
        preg_replace($pattern, '', $actual_result)
      );
    }


    public function test__build_sql_query__works_with_no_supersticky_entries()
    {

    }


    public function test__build_sql_query__ignores_non_member_group_entries()
    {

    }


}


/* End of file      : test.mod_supersticky.php */
/* File location    : third_party/supersticky/tests/test.mod_supersticky.php */
