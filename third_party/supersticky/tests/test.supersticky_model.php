<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * SuperSticky model tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

require_once PATH_THIRD .'supersticky/models/supersticky_model.php';

class Test_supersticky_model extends Testee_unit_test_case {

  private $_package_name;
  private $_package_version;
  private $_site_id;
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

    $this->_package_name    = 'example_package';
    $this->_package_version = '1.0.0';
    $this->_site_id         = 10;

    $this->EE->config->setReturnValue('item', $this->_site_id,
      array('site_id'));

    $this->_subject = new Supersticky_model($this->_package_name,
      $this->_package_version);
  }


  public function test__constructor__package_name_and_version()
  {
    $package_name       = 'Example_package';
    $package_version    = '1.0.0';

    $subject = new Supersticky_model($package_name, $package_version);

    $this->assertIdentical(strtolower($package_name),
      $subject->get_package_name());

    $this->assertIdentical($package_version, $subject->get_package_version());
  }


  public function test__get_member_group_options__returns_member_groups()
  {
    $lang       = $this->EE->lang;
    $db         = $this->EE->db;
    $db_result  = $this->_get_mock('db_query');

    $db_rows = array(
      array('group_id' => '1', 'group_title' => 'Super Admins'),
      array('group_id' => '2', 'group_title' => 'Banned'),
      array('group_id' => '3', 'group_title' => 'Guests'),
      array('group_id' => '4', 'group_title' => 'Pending'),
      array('group_id' => '5', 'group_title' => 'Members'),
      array('group_id' => '6', 'group_title' => 'Custom Member Group')
    );

    // Query the database.
    $db->expectOnce('select', array('group_id, group_title'));
    $db->expectOnce('get_where', array('member_groups',
      array('site_id' => $this->_site_id)));

    $db->setReturnReference('get_where', $db_result);

    $db_result->expectOnce('result_array');
    $db_result->setReturnValue('result_array', $db_rows);

    // Retrieve the 'instructions' label.
    $lbl_instruction = 'Instructions';

    $lang->expectOnce('line', array('lbl__select_member_group'));
    $lang->setReturnValue('line', $lbl_instruction,
      array('lbl__select_member_group'));

    $expected_result = array('' => $lbl_instruction);

    foreach ($db_rows AS $db_row)
    {
      $expected_result[$db_row['group_id']] = $db_row['group_title'];
    }
  
    $this->assertIdentical(
      $expected_result,
      $this->_subject->get_member_group_options()
    );
  }


  public function test__get_member_group_options__handles_no_member_groups()
  {
    $lang       = $this->EE->lang;
    $db         = $this->EE->db;
    $db_result  = $this->_get_mock('db_query');

    // Query the database.
    $db->expectOnce('select', array('group_id, group_title'));
    $db->expectOnce('get_where', array('member_groups',
      array('site_id' => $this->_site_id)));

    $db->setReturnReference('get_where', $db_result);

    $db_result->expectOnce('result_array');
    $db_result->setReturnValue('result_array', array());

    // Retrieve the 'instructions' label.
    $lbl_instruction = 'Instructions';

    $lang->expectOnce('line', array('lbl__select_member_group'));
    $lang->setReturnValue('line', $lbl_instruction,
      array('lbl__select_member_group'));

    $expected_result = array('' => $lbl_instruction);

    $this->assertIdentical(
      $expected_result,
      $this->_subject->get_member_group_options()
    );
  }


  public function test__get_site_id__success()
  {
    $this->EE->config->expectOnce('item', array('site_id'));
    $this->assertIdentical(intval($this->_site_id), $this->_subject->get_site_id());
  }


  public function test__get_supersticky_entries_for_date__works()
  {
    $db         = $this->EE->db;
    $date       = new DateTime('20110615T12:00:00+00:00');
    $date_from  = new DateTime('20110101T00:00:01+00:00');
    $date_to    = new DateTime('20111231T23:59:00+00:00');

    $db->expectOnce('select', array('supersticky_entries.*'));

    $db->expectOnce(
      'join',
      array(
        'channel_titles',
        'channel_titles.entry_id = supersticky_entries.entry_id',
        'inner'
      )
    );

    $db->expectOnce('where', array(array(
      'supersticky_entries.date_from <=' => $date->format(DATE_W3C),
      'supersticky_entries.date_to >=' => $date->format(DATE_W3C)
    )));

    $db->expectOnce('order_by',
      array('channel_titles.sticky DESC, channel_titles.entry_date ASC'));

    $db->expectOnce('get', array('supersticky_entries'));
  
    $db_result = $this->_get_mock('db_query');
    $db_rows = array(
      (object) array(
        'entry_id'  => '10',
        'date_from' => $date_from->format(DATE_W3C),
        'date_to'   => $date_to->format(DATE_W3C),
        'member_groups' => '10|20|30'
      ),
      (object) array(
        'entry_id'  => '20',
        'date_from' => $date_from->format(DATE_W3C),
        'date_to'   => $date_to->format(DATE_W3C),
        'member_groups' => '40'
      ),
      (object) array(
        'entry_id'  => '30',
        'date_from' => $date_from->format(DATE_W3C),
        'date_to'   => $date_to->format(DATE_W3C),
        'member_groups' => '50|60'
      )
    );

    $db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('num_rows', count($db_rows));
    $db_result->setReturnValue('result', $db_rows);

    $expected_result = array(
      new Supersticky_entry(array(
        'entry_id'  => $db_rows[0]->entry_id,
        'criteria'  => array(
          new Supersticky_criterion(array(
            'date_from' => $date_from,
            'date_to'   => $date_to,
            'member_groups' => explode('|', $db_rows[0]->member_groups)
          ))
        )
      )),
      new Supersticky_entry(array(
        'entry_id'  => $db_rows[1]->entry_id,
        'criteria'  => array(
          new Supersticky_criterion(array(
            'date_from' => $date_from,
            'date_to'   => $date_to,
            'member_groups' => explode('|', $db_rows[1]->member_groups)
          ))
        )
      )),
      new Supersticky_entry(array(
        'entry_id'  => $db_rows[2]->entry_id,
        'criteria'  => array(
          new Supersticky_criterion(array(
            'date_from' => $date_from,
            'date_to'   => $date_to,
            'member_groups' => explode('|', $db_rows[2]->member_groups)
          ))
        )
      ))
    );

    $this->assertIdentical(
      $expected_result,
      $this->_subject->get_supersticky_entries_for_date($date)
    );
  }


  public function test__get_supersticky_entries_for_date__groups_items_with_same_entry_id()
  {
    $db         = $this->EE->db;
    $date       = new DateTime('20110615T12:00:00+00:00');
    $date_from  = new DateTime('20110101T00:00:01+00:00');
    $date_to    = new DateTime('20111231T23:59:00+00:00');

    $db_result = $this->_get_mock('db_query');
    $db_rows = array(
      (object) array(
        'entry_id'  => '10',
        'date_from' => $date_from->format(DATE_W3C),
        'date_to'   => $date_to->format(DATE_W3C),
        'member_groups' => '10|20|30'
      ),
      (object) array(
        'entry_id'  => '10',
        'date_from' => $date_from->format(DATE_W3C),
        'date_to'   => $date_to->format(DATE_W3C),
        'member_groups' => '40'
      ),
      (object) array(
        'entry_id'  => '20',
        'date_from' => $date_from->format(DATE_W3C),
        'date_to'   => $date_to->format(DATE_W3C),
        'member_groups' => '50|60'
      )
    );

    $db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('num_rows', count($db_rows));
    $db_result->setReturnValue('result', $db_rows);

    $expected_result = array(
      new Supersticky_entry(array(
        'entry_id'  => $db_rows[0]->entry_id,
        'criteria'  => array(
          new Supersticky_criterion(array(
            'date_from' => $date_from,
            'date_to'   => $date_to,
            'member_groups' => explode('|', $db_rows[0]->member_groups)
          )),
          new Supersticky_criterion(array(
            'date_from' => $date_from,
            'date_to'   => $date_to,
            'member_groups' => explode('|', $db_rows[1]->member_groups)
          ))
        )
      )),
      new Supersticky_entry(array(
        'entry_id'  => $db_rows[2]->entry_id,
        'criteria'  => array(
          new Supersticky_criterion(array(
            'date_from' => $date_from,
            'date_to'   => $date_to,
            'member_groups' => explode('|', $db_rows[2]->member_groups)
          ))
        )
      ))
    );

    $this->assertIdentical(
      $expected_result,
      $this->_subject->get_supersticky_entries_for_date($date)
    );
  }


  public function test__get_supersticky_entries_for_date__works_with_no_results()
  {
    $db   = $this->EE->db;
    $date = new DateTime('20110615T12:00:00+00:00');

    $db_result = $this->_get_mock('db_query');
    $db_rows = array();

    $db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('num_rows', count($db_rows));
    $db_result->setReturnValue('result', $db_rows);

    $this->assertIdentical(
      array(),
      $this->_subject->get_supersticky_entries_for_date($date)
    );
  }


  public function test__get_supersticky_entries_for_date__honours_channel()
  {
    $db       = $this->EE->db;
    $date     = new DateTime('20110615T12:00:00+00:00');
    $channel  = 'my_lovely_channel';

    $db->expectOnce('select', array('supersticky_entries.*'));

    $db->expectCallCount('join', 2);

    $db->expectAt(0, 'join', array('channel_titles',
        'channel_titles.entry_id = supersticky_entries.entry_id', 'inner'));

    $db->expectAt(1, 'join', array('channels',
        'channels.channel_id = channel_titles.channel_id', 'inner'));

    $db->expectCallCount('where', 2);

    $db->expectAt(0, 'where', array(array(
      'channels.channel_name' => $channel)));

    $db->expectAt(1, 'where', array(array(
      'supersticky_entries.date_from <=' => $date->format(DATE_W3C),
      'supersticky_entries.date_to >=' => $date->format(DATE_W3C))));

    $db->expectOnce('order_by',
      array('channel_titles.sticky DESC, channel_titles.entry_date ASC'));

    $db->expectOnce('get', array('supersticky_entries'));
  
    $db_result = $this->_get_mock('db_query');
    $db_rows = array();

    $db->setReturnReference('get', $db_result);
    $db_result->setReturnValue('num_rows', count($db_rows));
    $db_result->setReturnValue('result', $db_rows);

    $this->assertIdentical(
      array(),
      $this->_subject->get_supersticky_entries_for_date($date, $channel)
    );
  }


  public function test__get_supersticky_entry_by_id__entry_found()
  {
    // Build our dummy data.
    $entry_id = 10;

    $criteria = array(
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1973-02-19T15:00:00+0:00'),
        'date_to'       => new DateTime('2011-10-03T17:23:00+0:00'),
        'member_groups' => array(10, 20, 30)
      )),
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1935-06-14T21:00:59+01:00'),
        'date_to'       => new DateTime('1944-02-18T22:22:00+02:00'),
        'member_groups' => array(15, 25, 35, 45)
      ))
    );

    // Build the database query result.
    $db_rows = array(
      (object) array(
        'entry_id'      => (string) $entry_id,
        'date_from'     => $criteria[0]->get_date_from()->format(DATE_W3C),
        'date_to'       => $criteria[0]->get_date_to()->format(DATE_W3C),
        'member_groups' => implode('|', $criteria[0]->get_member_groups())
      ),
      (object) array(
        'entry_id'      => (string) $entry_id,
        'date_from'     => $criteria[1]->get_date_from()->format(DATE_W3C),
        'date_to'       => $criteria[1]->get_date_to()->format(DATE_W3C),
        'member_groups' => implode('|', $criteria[1]->get_member_groups())
      )
    );

    $db_result = $this->_get_mock('db_query');
  
    // What we expect to happen.
    $this->EE->db->expectOnce('select',
      array('entry_id, date_from, date_to, member_groups'));

    $this->EE->db->expectOnce('get_where',
      array('supersticky_entries', array('entry_id' => $entry_id)));

    $this->EE->db->setReturnReference('get_where', $db_result);
    $db_result->setReturnValue('num_rows', count($db_rows));
    $db_result->setReturnValue('result', $db_rows);

    $expected_result = new Supersticky_entry(array(
      'entry_id' => $entry_id,
      'criteria' => $criteria
    ));

    $actual_result = $this->_subject->get_supersticky_entry_by_id($entry_id);

    $this->assertTrue($actual_result instanceof Supersticky_entry);

    $this->assertIdentical(
      $expected_result->to_array(),
      $actual_result->to_array()
    );
  }


  public function test__get_supersticky_entry_by_id__entry_not_found()
  {
    $entry_id   = 10;
    $db_result  = $this->_get_mock('db_query');

    $this->EE->db->expectOnce('select');
    $this->EE->db->expectOnce('get_where');

    $this->EE->db->setReturnReference('get_where', $db_result);
    $db_result->setReturnValue('num_rows', 0);
  
    $this->assertIdentical(
      FALSE,
      $this->_subject->get_supersticky_entry_by_id($entry_id)
    );
  }


  public function test__get_supersticky_entry_by_id__invalid_entry_id()
  {
    $this->EE->db->expectNever('select');
    $this->EE->db->expectNever('get_where');

    $this->assertIdentical(FALSE,
      $this->_subject->get_supersticky_entry_by_id(0));

    $this->assertIdentical(FALSE,
      $this->_subject->get_supersticky_entry_by_id(-100));

    $this->assertIdentical(FALSE,
      $this->_subject->get_supersticky_entry_by_id('wibble'));

    $this->assertIdentical(FALSE,
      $this->_subject->get_supersticky_entry_by_id(new StdClass()));
  }


  public function test__get_supersticky_entry_by_id__entries_have_invalid_dates()
  {
    $entry_id = 10;
    $db_result = $this->_get_mock('db_query');

    $db_rows = array(
      // Invalid date_from
      (object) array(
        'entry_id'      => (string) $entry_id,
        'date_from'     => '',
        'date_to'       => '20110101T01:02:03+04:00',
        'member_groups' => '10|20|30'
      ),
      // Invalid date_to
      (object) array(
        'entry_id'      => (string) $entry_id,
        'date_from'     =>  '20110101T01:02:03+04:00',
        'date_to'       => 'wibble',
        'member_groups' => '10|20|30'
      )
    );

    $this->EE->db->setReturnReference('get_where', $db_result);
    $db_result->setReturnValue('num_rows', count($db_rows));
    $db_result->setReturnValue('result', $db_rows);
  
    /**
     * Invalid rows should be ignored, so we get a SuperSticky Entry with the
     * entry_id defined, and nothing else.
     */

    $expected_result  = new Supersticky_entry(array('entry_id' => $entry_id));
    $actual_result    = $this->_subject->get_supersticky_entry_by_id($entry_id);

    $this->assertIdentical($expected_result, $actual_result);
  }


  public function test__get_supersticky_entry_by_id__entry_has_no_member_groups()
  {
    $entry_id = 10;
    $db_result = $this->_get_mock('db_query');

    $db_rows = array(
      (object) array(
        'entry_id'      => (string) $entry_id,
        'date_from'     => '20110101T01:02:03+04:00',
        'date_to'       => '20110111T01:02:03+04:00',
        'member_groups' => ''
      )
    );

    $this->EE->db->setReturnReference('get_where', $db_result);
    $db_result->setReturnValue('num_rows', count($db_rows));
    $db_result->setReturnValue('result', $db_rows);
  
    /**
     * Invalid rows should be ignored, so we get a SuperSticky Entry with the
     * entry_id defined, and nothing else.
     */

    $expected_result  = new Supersticky_entry(array('entry_id' => $entry_id));
    $actual_result    = $this->_subject->get_supersticky_entry_by_id($entry_id);

    $this->assertIdentical($expected_result, $actual_result);
  }


  public function test__get_supersticky_entry_by_id__entry_has_invalid_member_groups()
  {
    $entry_id = 10;
    $db_result = $this->_get_mock('db_query');

    $db_rows = array(
      (object) array(
        'entry_id'      => (string) $entry_id,
        'date_from'     => '20110101T01:02:03+04:00',
        'date_to'       => '20110111T01:02:03+04:00',
        'member_groups' => '10|20|thirty|40'
      )
    );

    $this->EE->db->setReturnReference('get_where', $db_result);
    $db_result->setReturnValue('num_rows', count($db_rows));
    $db_result->setReturnValue('result', $db_rows);
  
    /**
     * Invalid rows should be ignored, so we get a SuperSticky Entry with the
     * entry_id defined, and nothing else.
     */

    $expected_result  = new Supersticky_entry(array('entry_id' => $entry_id));
    $actual_result    = $this->_subject->get_supersticky_entry_by_id($entry_id);

    $this->assertIdentical($expected_result, $actual_result);
  }


  public function test__install_module_register__success()
  {
    $query_data = array(
      'has_cp_backend'        => 'n',
      'has_publish_fields'    => 'y',
      'module_name'           => ucfirst($this->_package_name),
      'module_version'        => $this->_package_version
    );

    $this->EE->db->expectOnce('insert', array('modules', $query_data));
    $this->_subject->install_module_register();
  }


  public function test__install_module_tables__success()
  {
    $this->EE->load->expectOnce('dbforge');

    $fields = array(
      'entry_id' => array(
        'constraint'  => 10,
        'type'        => 'INT',
        'unsigned'    => TRUE
      ),
      'date_from' => array(
        'constraint'  => 32,
        'type'        => 'VARCHAR'
      ),
      'date_to' => array(
        'constraint'  => 32,
        'type'        => 'VARCHAR'
      ),
      'member_groups' => array(
        'constraint'  => 64,
        'type'        => 'VARCHAR'
      )
    );

    $this->EE->dbforge->expectOnce('add_field', array($fields));

    $this->EE->dbforge->expectCallCount('add_key', 3);
    $this->EE->dbforge->expectAt(0, 'add_key', array('entry_id'));
    $this->EE->dbforge->expectAt(1, 'add_key', array('date_from'));
    $this->EE->dbforge->expectAt(2, 'add_key', array('date_to'));

    $this->EE->dbforge->expectOnce('create_table',
      array('supersticky_entries', TRUE));
  
    $this->_subject->install_module_tables();
  }


  public function test__save_supersticky_entry__entry_saved()
  {
    $db = $this->EE->db;

    // Create the dummy entry.
    $criteria = array(
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1973-02-19T01:01:01+01:00'),
        'date_to'       => new DateTime('2011-10-03T02:02:02+02:00'),
        'member_groups' => array(10, 20, 30)
      )),
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1935-06-14T03:03:03+03:00'),
        'date_to'       => new DateTime('1944-02-18T04:04:04+04:00'),
        'member_groups' => array(15, 25, 35, 45)
      ))
    );

    $entry_id = 123;

    $entry = new Supersticky_entry(array(
      'entry_id' => $entry_id,
      'criteria' => $criteria
    ));

    // Delete any existing criteria.
    $db->expectOnce('delete', array(
      'supersticky_entries',
      array('entry_id' => $entry_id)
    ));

    // Add the new criteria.
    $db->expectCallCount('insert', count($criteria));

    for ($count = 0; $count < count($criteria); $count++)
    {
      $criterion = $criteria[$count];

      $db->expectAt($count, 'insert', array(
        'supersticky_entries',
        array(
          'entry_id'      => $entry_id,
          'date_from'     => $criterion->get_date_from()->format(DATE_W3C),
          'date_to'       => $criterion->get_date_to()->format(DATE_W3C),
          'member_groups' => implode('|', $criterion->get_member_groups())
        )
      ));
    }

    $this->assertIdentical(TRUE,
      $this->_subject->save_supersticky_entry($entry));
  }


  public function test__save_supersticky_entry__fails_with_missing_data()
  {
    // Missing entry ID.
    $entry_a = new Supersticky_entry(array(
      'criteria' => array(
        new Supersticky_criterion(array(
          'date_from'     => new DateTime('1935-06-14'),
          'date_to'       => new DateTime('1944-02-18'),
          'member_groups' => array(10, 20, 30)
        ))
      )
    ));

    // Missing criteria.
    $entry_b = new Supersticky_entry(array('entry_id' => 10));

    // Missing criterion date from.
    $entry_c = new Supersticky_entry(array(
      'entry_id' => 10,
      'criteria' => array(
        new Supersticky_criterion(array(
          'date_to'       => new DateTime('1944-02-18'),
          'member_groups' => array(10, 20, 30)
        ))
      )
    ));

    // Missing criterion date to.
    $entry_d = new Supersticky_entry(array(
      'entry_id' => 10,
      'criteria' => array(
        new Supersticky_criterion(array(
          'date_from'     => new DateTime('1935-06-14'),
          'member_groups' => array(10, 20, 30)
        ))
      )
    ));

    // Missing criterion member groups.
    $entry_e = new Supersticky_entry(array(
      'entry_id' => 10,
      'criteria' => array(
        new Supersticky_criterion(array(
          'date_from' => new DateTime('1935-06-14'),
          'date_to'   => new DateTime('1944-02-18')
        ))
      )
    ));

    // What we expect to happen.
    $this->EE->db->expectNever('delete');
    $this->EE->db->expectNever('insert');
  
    $this->assertIdentical(FALSE,
      $this->_subject->save_supersticky_entry($entry_a));

    $this->assertIdentical(FALSE,
      $this->_subject->save_supersticky_entry($entry_b));
  
    $this->assertIdentical(FALSE,
      $this->_subject->save_supersticky_entry($entry_c));
  
    $this->assertIdentical(FALSE,
      $this->_subject->save_supersticky_entry($entry_d));

    $this->assertIdentical(FALSE,
      $this->_subject->save_supersticky_entry($entry_e));
  }


  public function test__uninstall_module__success()
  {
    $db_module_result           = $this->_get_mock('db_query');
    $db_module_row              = new StdClass();
    $db_module_row->module_id   = '10';
    $module_name                = ucfirst($this->_package_name);

    // Retrieve the module information.
    $this->EE->db->expectOnce('select', array('module_id'));
    $this->EE->db->expectOnce('get_where', array('modules',
      array('module_name' => $module_name), 1));

    $this->EE->db->setReturnReference('get_where', $db_module_result);
    $db_module_result->setReturnValue('num_rows', 1);
    $db_module_result->setReturnValue('row', $db_module_row);

    // Delete all traces of the module...
    $this->EE->db->expectCallCount('delete', 2);

    // Delete the module member groups.
    $this->EE->db->expectAt(0, 'delete', array('module_member_groups',
      array('module_id' => $db_module_row->module_id)));

    // Delete the module.
    $this->EE->db->expectAt(1, 'delete', array('modules',
      array('module_name' => $module_name)));

    // Drop the module tables.
    $this->EE->load->expectOnce('dbforge');
    $this->EE->dbforge->expectOnce('drop_table', array('supersticky_entries'));

    // Delete any saved layout tabs.
    $this->EE->load->expectOnce('library', array('layout'));
    $this->EE->layout->expectOnce('delete_layout_tabs');

    $this->assertIdentical(TRUE, $this->_subject->uninstall_module());
  }


  public function test__uninstall_module__module_not_found()
  {
    $db_module_result = $this->_get_mock('db_query');

    $this->EE->db->expectOnce('select');
    $this->EE->db->expectOnce('get_where');
    $this->EE->db->expectNever('delete');
    $this->EE->load->expectNever('dbforge');
    $this->EE->load->expectNever('library');

    $this->EE->db->setReturnReference('get_where', $db_module_result);
    $db_module_result->setReturnValue('num_rows', 0);

    $this->assertIdentical(FALSE, $this->_subject->uninstall_module());
  }


  public function test__update_module__no_update_required()
  {
    $installed_version = $this->_package_version;
    $this->assertIdentical(FALSE,
      $this->_subject->update_module($installed_version));
  }


  public function test__update_module__update_required()
  {
    $installed_version = '0.9.0';
    $this->assertIdentical(TRUE,
      $this->_subject->update_module($installed_version));
  }


  public function test__update_module__no_installed_version()
  {
    $installed_version = '';
    $this->assertIdentical(TRUE,
      $this->_subject->update_module($installed_version));
  }


  public function test__update_supersticky_entry_with_post_data__preserves_existing_criteria()
  {
    $in = $this->EE->input;

    $entry_id = 100;

    $criteria = array(
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1935-06-14'),
        'date_to'       => new DateTime('1944-02-18'),
        'member_groups' => array(10, 20, 30)
      ))
    );

    $in_criteria = array(
      array(
        'date_from'     => '1973-02-19',
        'date_to'       => '2011-10-03',
        'member_groups' => array('15', '25', '35')
      )
    );

    $entry = new Supersticky_entry(array(
      'criteria' => $criteria,
      'entry_id' => $entry_id
    ));

    // POST data.
    $in->expectOnce('post', array('supersticky_criteria'));
    $in->setReturnValue('post', $in_criteria, array('supersticky_criteria'));

    $expected_result = clone $entry;
    $expected_result->add_criterion(new Supersticky_criterion(array(
      'date_from'     => new DateTime($in_criteria[0]['date_from']),
      'date_to'       => new DateTime($in_criteria[0]['date_to']),
      'member_groups' => $in_criteria[0]['member_groups']
    )));

    $actual_result
      = $this->_subject->update_supersticky_entry_with_post_data($entry);

    $this->assertIdentical($expected_result->get_entry_id(),
      $actual_result->get_entry_id());

    $actual_criteria = $actual_result->get_criteria();
    $expected_criteria = $expected_result->get_criteria();

    $this->assertIdentical(count($expected_criteria),
      count($actual_criteria));

    for ($count = 0, $limit = count($expected_criteria); $count < $limit; $count++)
    {
      $actual_criterion = $actual_criteria[$count];
      $expected_criterion = $expected_criteria[$count];

      $this->assertIdentical($expected_criterion->get_date_from()->format(DATE_W3C),
        $actual_criterion->get_date_from()->format(DATE_W3C));

      $this->assertIdentical($expected_criterion->get_date_to()->format(DATE_W3C),
        $actual_criterion->get_date_to()->format(DATE_W3C));

      $this->assertIdentical($expected_criterion->get_member_groups(),
        $actual_criterion->get_member_groups());
    }
  }


  public function test__update_supersticky_entry_with_post_data__ignores_criteria_with_missing_data()
  {
    $in = $this->EE->input;

    $entry_id = 100;

    $criteria = array(
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1935-06-14'),
        'date_to'       => new DateTime('1944-02-18'),
        'member_groups' => array(10, 20, 30)
      ))
    );

    $in_criteria = array(
      // Missing 'from' date.
      array(
        'date_to'       => '2011-10-03',
        'member_groups' => array('15', '25', '35')
      ),
      // Missing 'to' date.
      array(
        'date_from'     => '1973-02-19',
        'member_groups' => array('15', '25', '35')
      ),
      // Missing member groups.
      array(
        'date_from'     => '1973-02-19',
        'date_to'       => '2011-10-03'
      )
    );

    $entry = new Supersticky_entry(array(
      'criteria' => $criteria,
      'entry_id' => $entry_id
    ));

    // POST data.
    $in->expectOnce('post', array('supersticky_criteria'));
    $in->setReturnValue('post', $in_criteria, array('supersticky_criteria'));

    $expected_result = clone $entry;

    $actual_result
      = $this->_subject->update_supersticky_entry_with_post_data($entry);

    $this->assertIdentical($expected_result->get_entry_id(),
      $actual_result->get_entry_id());

    $actual_criteria = $actual_result->get_criteria();
    $expected_criteria = $expected_result->get_criteria();

    $this->assertIdentical(count($expected_criteria),
      count($actual_criteria));

    for ($count = 0, $limit = count($expected_criteria); $count < $limit; $count++)
    {
      $actual_criterion = $actual_criteria[$count];
      $expected_criterion = $expected_criteria[$count];

      $this->assertIdentical($expected_criterion->get_date_from()->format(DATE_W3C),
        $actual_criterion->get_date_from()->format(DATE_W3C));

      $this->assertIdentical($expected_criterion->get_date_to()->format(DATE_W3C),
        $actual_criterion->get_date_to()->format(DATE_W3C));

      $this->assertIdentical($expected_criterion->get_member_groups(),
        $actual_criterion->get_member_groups());
    }
  }


  public function test__update_supersticky_entry_with_post_data__ignores_criteria_with_invalid_data()
  {
    $in = $this->EE->input;

    $entry_id = 100;

    $criteria = array(
      new Supersticky_criterion(array(
        'date_from'     => new DateTime('1935-06-14'),
        'date_to'       => new DateTime('1944-02-18'),
        'member_groups' => array(10, 20, 30)
      ))
    );

    $in_criteria = array(
      // Invalid 'from' date.
      array(
        'date_from'     => 'Not a real date',
        'date_to'       => '2011-10-03',
        'member_groups' => array('15', '25', '35')
      ),
      // Invalid 'to' date.
      array(
        'date_from'     => '1973-02-19',
        'date_to'       => 'Utter gibberish',
        'member_groups' => array('15', '25', '35')
      ),
      // Invalid member groups.
      array(
        'date_from'     => '1973-02-19',
        'date_to'       => '2011-10-03',
        'member_groups' => 'This should be an array'
      )
    );

    $entry = new Supersticky_entry(array(
      'criteria' => $criteria,
      'entry_id' => $entry_id
    ));

    // POST data.
    $in->expectOnce('post', array('supersticky_criteria'));
    $in->setReturnValue('post', $in_criteria, array('supersticky_criteria'));

    $expected_result = clone $entry;

    $actual_result
      = $this->_subject->update_supersticky_entry_with_post_data($entry);

    $this->assertIdentical($expected_result->get_entry_id(),
      $actual_result->get_entry_id());

    $actual_criteria = $actual_result->get_criteria();
    $expected_criteria = $expected_result->get_criteria();

    $this->assertIdentical(count($expected_criteria),
      count($actual_criteria));

    for ($count = 0, $limit = count($expected_criteria); $count < $limit; $count++)
    {
      $actual_criterion = $actual_criteria[$count];
      $expected_criterion = $expected_criteria[$count];

      $this->assertIdentical($expected_criterion->get_date_from()->format(DATE_W3C),
        $actual_criterion->get_date_from()->format(DATE_W3C));

      $this->assertIdentical($expected_criterion->get_date_to()->format(DATE_W3C),
        $actual_criterion->get_date_to()->format(DATE_W3C));

      $this->assertIdentical($expected_criterion->get_member_groups(),
        $actual_criterion->get_member_groups());
    }
  }


}


/* End of file    : test.supersticky_model.php */
/* File location  : third_party/supersticky/tests/test.supersticky_model.php */
