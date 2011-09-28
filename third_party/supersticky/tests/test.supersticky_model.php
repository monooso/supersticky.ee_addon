<?php if ( ! defined('EXT')) exit('Invalid file request.');

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

    $this->_ee->config->setReturnValue('item', $this->_site_id,
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


  public function test__get_site_id__success()
  {
    $this->_ee->config->expectOnce('item', array('site_id'));
    $this->assertIdentical(intval($this->_site_id), $this->_subject->get_site_id());
  }


  public function test__get_supersticky_entry_by_id__entry_found()
  {
    // Build our dummy data.
    $entry_id = 10;

    $criteria = array(
      new Supersticky_criterion(array(
        'type'    => Supersticky_criterion::TYPE_DATE_RANGE,
        'value'   => '2011-09-01T08:45:00+0:00 2011-10-01T02:30:45+0:00'
      )),
      new Supersticky_criterion(array(
        'type'    => Supersticky_criterion::TYPE_MEMBER_GROUP,
        'value'   => 100
      ))
    );

    // Build the database query result.
    $db_row = (object) array(
      'entry_id'  => (string) $entry_id,
      'supersticky_criteria' => json_encode(array(
        $criteria[0]->to_array(),
        $criteria[1]->to_array()
      ))
    );

    $db_result = $this->_get_mock('db_query');
  
    // What we expect to happen.
    $this->_ee->db->expectOnce(
      'select',
      array('entry_id, supersticky_criteria')
    );

    $this->_ee->db->expectOnce(
      'get_where',
      array('supersticky_entries', array('entry_id' => $entry_id), 1)
    );

    $this->_ee->db->setReturnReference('get_where', $db_result);
    $db_result->setReturnValue('num_rows', 1);
    $db_result->setReturnValue('row', $db_row);

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

    $this->_ee->db->expectOnce('select');
    $this->_ee->db->expectOnce('get_where');

    $this->_ee->db->setReturnReference('get_where', $db_result);
    $db_result->setReturnValue('num_rows', 0);
  
    $this->assertIdentical(
      FALSE,
      $this->_subject->get_supersticky_entry_by_id($entry_id)
    );
  }


  public function test__get_supersticky_entry_by_id__invalid_entry_id()
  {
    $this->_ee->db->expectNever('select');
    $this->_ee->db->expectNever('get_where');

    $this->assertIdentical(FALSE,
      $this->_subject->get_supersticky_entry_by_id(0));

    $this->assertIdentical(FALSE,
      $this->_subject->get_supersticky_entry_by_id(-100));

    $this->assertIdentical(FALSE,
      $this->_subject->get_supersticky_entry_by_id('wibble'));

    $this->assertIdentical(FALSE,
      $this->_subject->get_supersticky_entry_by_id(new StdClass()));
  }


  public function test__get_supersticky_entry_by_id__criteria_is_not_array()
  {
    // Build our dummy data.
    $entry_id = 10;

    // Build the database query result.
    $db_row = (object) array(
      'entry_id'  => (string) $entry_id,
      'supersticky_criteria' => json_encode('apple')
    );

    $db_result = $this->_get_mock('db_query');
  
    // What we expect to happen.
    $this->_ee->db->expectOnce(
      'select',
      array('entry_id, supersticky_criteria')
    );

    $this->_ee->db->expectOnce(
      'get_where',
      array('supersticky_entries', array('entry_id' => $entry_id), 1)
    );

    $this->_ee->db->setReturnReference('get_where', $db_result);
    $db_result->setReturnValue('num_rows', 1);
    $db_result->setReturnValue('row', $db_row);

    $this->assertIdentical(
      FALSE,
      $this->_subject->get_supersticky_entry_by_id($entry_id)
    );
  }


  public function test__install_module_actions__success()
  {
    $query_data = array(
      array('class' => ucfirst($this->_package_name), 'method' => '')
    );

    $query_count = count($query_data);
    $this->_ee->db->expectCallCount('insert', $query_count);

    for ($count = 0; $count < $query_count; $count++)
    {
      $this->_ee->db->expectAt($count, 'insert',
        array('actions', $query_data[$count]));
    }

    $this->_subject->install_module_actions();
  }


  public function test__install_module_register__success()
  {
    $query_data = array(
      'has_cp_backend'        => 'y',
      'has_publish_fields'    => 'y',
      'module_name'           => ucfirst($this->_package_name),
      'module_version'        => $this->_package_version
    );

    $this->_ee->db->expectOnce('insert', array('modules', $query_data));
    $this->_subject->install_module_register();
  }


  public function test__install_module_tables__success()
  {
    $this->_ee->load->expectOnce('dbforge');

    $fields = array(
      'entry_id' => array(
        'constraint'  => 10,
        'type'        => 'INT',
        'unsigned'    => TRUE
      ),
      'supersticky_criteria' => array(
        'type'        => 'TEXT'
      )
    );

    $this->_ee->dbforge->expectOnce('add_field', array($fields));
    $this->_ee->dbforge->expectOnce('add_key', array('entry_id', TRUE));

    $this->_ee->dbforge->expectOnce('create_table',
      array('supersticky_entries', TRUE));
  
    $this->_subject->install_module_tables();
  }


  public function test__save_supersticky_entry__entry_saved()
  {
    // Create the dummy entry.
    $criteria = array(
      new Supersticky_criterion(array(
        'type'  => Supersticky_criterion::TYPE_DATE_RANGE,
        'value' => '2011-01-01T00:00:01+0:00 2011-12-31T23:59:59+0:00'
      )),
      new Supersticky_criterion(array(
        'type'  => Supersticky_criterion::TYPE_MEMBER_GROUP,
        'value' => 999
      ))
    );

    $entry_id = 123;

    $entry = new Supersticky_entry(array(
      'entry_id' => $entry_id,
      'criteria' => $criteria
    ));

    // Build the expected insert data.
    $insert_criteria = array();

    foreach ($criteria AS $criterion)
    {
      $insert_criteria[] = $criterion->to_array();
    }

    $insert_data = array(
      'entry_id' => $entry_id,
      'supersticky_criteria' => json_encode($insert_criteria)
    );

    // What we expect to happen.
    $this->_ee->db->expectOnce('delete', array(
      'supersticky_entries',
      array('entry_id' => $entry_id)
    ));

    $this->_ee->db->expectOnce('insert', array(
      'supersticky_entries', $insert_data));
  
    $this->assertIdentical(TRUE,
      $this->_subject->save_supersticky_entry($entry));
  }


  public function test__save_supersticky_entry__fails_when_missing_entry_id()
  {
    // Create the dummy entry.
    $criteria = array(
      new Supersticky_criterion(array(
        'type'  => Supersticky_criterion::TYPE_DATE_RANGE,
        'value' => '2011-01-01T00:00:01+0:00 2011-12-31T23:59:59+0:00'
      )),
      new Supersticky_criterion(array(
        'type'  => Supersticky_criterion::TYPE_MEMBER_GROUP,
        'value' => 999
      ))
    );

    $entry = new Supersticky_entry(array('criteria' => $criteria));

    // What we expect to happen.
    $this->_ee->db->expectNever('delete');
    $this->_ee->db->expectNever('insert');
  
    $this->assertIdentical(FALSE,
      $this->_subject->save_supersticky_entry($entry));
  }


  public function test__save_supersticky_entry__fails_with_invalid_criterion()
  {
    // Create the dummy entry.
    $criteria = array(
      new Supersticky_criterion(array(
        'type'  => Supersticky_criterion::TYPE_DATE_RANGE,
        'value' => '2011-01-01T00:00:01+0:00 2011-12-31T23:59:59+0:00'
      )),
      new Supersticky_criterion(array('value' => 999))
    );

    $entry_id = 123;

    $entry = new Supersticky_entry(array(
      'criteria' => $criteria,
      'entry_id' => $entry_id
    ));

    // What we expect to happen.
    $this->_ee->db->expectNever('delete');
    $this->_ee->db->expectNever('insert');
  
    $this->assertIdentical(FALSE,
      $this->_subject->save_supersticky_entry($entry));
  }


  public function test__save_supersticky_entry__fails_with_no_criteria()
  {
    // Create the dummy entry.
    $entry = new Supersticky_entry(array(
      'criteria' => array(),
      'entry_id' => 123
    ));

    // What we expect to happen.
    $this->_ee->db->expectNever('delete');
    $this->_ee->db->expectNever('insert');
  
    $this->assertIdentical(FALSE,
      $this->_subject->save_supersticky_entry($entry));
  }


  public function test__uninstall_module__success()
  {
    $db_module_result           = $this->_get_mock('db_query');
    $db_module_row              = new StdClass();
    $db_module_row->module_id   = '10';
    $module_name                = ucfirst($this->_package_name);

    // Retrieve the module information.
    $this->_ee->db->expectOnce('select', array('module_id'));
    $this->_ee->db->expectOnce('get_where', array('modules',
      array('module_name' => $module_name), 1));

    $this->_ee->db->setReturnReference('get_where', $db_module_result);
    $db_module_result->setReturnValue('num_rows', 1);
    $db_module_result->setReturnValue('row', $db_module_row);

    // Delete all traces of the module...
    $this->_ee->db->expectCallCount('delete', 3);

    // Delete the module member groups.
    $this->_ee->db->expectAt(0, 'delete', array('module_member_groups',
      array('module_id' => $db_module_row->module_id)));

    // Delete the module actions.
    $this->_ee->db->expectAt(1, 'delete', array('actions',
      array('class' => $module_name)));

    // Delete the module.
    $this->_ee->db->expectAt(2, 'delete', array('modules',
      array('module_name' => $module_name)));

    // Drop the module tables.
    $this->_ee->load->expectOnce('dbforge');
    $this->_ee->dbforge->expectOnce('drop_table', array('supersticky_entries'));

    // Delete any saved layout tabs.
    $this->_ee->load->expectOnce('library', array('layout'));
    $this->_ee->layout->expectOnce('delete_layout_tabs');

    $this->assertIdentical(TRUE, $this->_subject->uninstall_module());
  }


  public function test__uninstall_module__module_not_found()
  {
    $db_module_result = $this->_get_mock('db_query');

    $this->_ee->db->expectOnce('select');
    $this->_ee->db->expectOnce('get_where');
    $this->_ee->db->expectNever('delete');
    $this->_ee->load->expectNever('dbforge');
    $this->_ee->load->expectNever('library');

    $this->_ee->db->setReturnReference('get_where', $db_module_result);
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


}


/* End of file    : test.supersticky_model.php */
/* File location  : third_party/supersticky/tests/test.supersticky_model.php */
