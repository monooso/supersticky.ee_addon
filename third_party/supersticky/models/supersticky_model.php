<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * SuperSticky model.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 * @version         0.1.0
 */

require_once PATH_THIRD .'supersticky/classes/supersticky_entry.php';

class Supersticky_model extends CI_Model {

  private $EE;
  private $_namespace;
  private $_package_name;
  private $_package_version;
  private $_site_id;


  /* --------------------------------------------------------------
   * PRIVATE METHODS
   * ------------------------------------------------------------ */
  
  /**
   * Returns an associative array containing layout tab information.
   *
   * @access  private
   * @return  Array
   */
  private function _get_layout_tabs()
  {
    return array(
      'supersticky' => array(
        'supersticky_criteria' => array(
          'collapse'      => 'false',
          'htmlbuttons'   => 'false',
          'visible'       => 'true',
          'width'         => '100%'
        )
      )
    );
  }


  /**
   * Returns a references to the package cache. Should be called
   * as follows: $cache =& $this->_get_package_cache();
   *
   * @access  private
   * @return  array
   */
  private function &_get_package_cache()
  {
    return $this->EE->session->cache[$this->_namespace][$this->_package_name];
  }



  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */
  
  /**
   * Constructor.
   *
   * @access  public
   * @param   string    $package_name     Package name. Used for testing.
   * @param   string    $package_version  Package version. Used for testing.
   * @param   string    $namespace        Session namespace. Used for testing.
   * @return  void
   */
  public function __construct(
    $package_name = '',
    $package_version = '',
    $namespace = ''
  )
  {
    parent::__construct();

    $this->EE =& get_instance();

    $this->_namespace = $namespace
      ? strtolower($namespace) : 'experience';

    $this->_package_name = $package_name
      ? strtolower($package_name) : 'supersticky';

    $this->_package_version = $package_version
      ? $package_version : '0.1.0';

    // Initialise the add-on cache.
    if ( ! array_key_exists($this->_namespace, $this->EE->session->cache))
    {
      $this->EE->session->cache[$this->_namespace] = array();
    }

    if ( ! array_key_exists($this->_package_name,
      $this->EE->session->cache[$this->_namespace])
    )
    {
      $this->EE->session->cache[$this->_namespace][$this->_package_name]
        = array();
    }

    // Load the OmniLogger class.
    if (file_exists(PATH_THIRD .'omnilog/classes/omnilogger.php'))
    {
      include_once PATH_THIRD .'omnilog/classes/omnilogger.php';
    }
  }


  /**
   * Returns an associative array of member groups, suitable for use
   * with the 'form_dropdown' form helper method.
   *
   * @access  public
   * @return  Array
   */
  public function get_member_group_options()
  {
    $db_groups = $this->EE->db
      ->select('group_id, group_title')
      ->get_where('member_groups',
          array('site_id' => $this->get_site_id()));

    $member_groups = array('' => $this->EE->lang->line(
      'lbl__select_member_group'));

    foreach ($db_groups->result_array() AS $db_group)
    {
      $member_groups[$db_group['group_id']] = $db_group['group_title'];
    }

    return $member_groups;
  }


  /**
   * Returns the package name.
   *
   * @access  public
   * @return  string
   */
  public function get_package_name()
  {
    return $this->_package_name;
  }


  /**
   * Returns the package theme folder URL, appending a forward slash if required.
   *
   * @access    public
   * @return    string
   */
  public function get_package_theme_url()
  {
    $theme_url = $this->EE->config->item('theme_folder_url');

    $theme_url .= substr($theme_url, -1) == '/'
      ? 'third_party/' : '/third_party/';

    return $theme_url .$this->get_package_name() .'/';
  }


  /**
   * Returns the package version.
   *
   * @access  public
   * @return  string
   */
  public function get_package_version()
  {
    return $this->_package_version;
  }


  /**
   * Returns the site ID.
   *
   * @access  public
   * @return  int
   */
  public function get_site_id()
  {
    if ( ! $this->_site_id)
    {
      $this->_site_id = intval($this->EE->config->item('site_id'));
    }

    return $this->_site_id;
  }


  /**
   * Retrieves a SuperSticky Entry, given the entry ID.
   *
   * @access  public
   * @param   int|string      $entry_id     The entry ID.
   * @return  Supersticky_entry|FALSE
   */
  public function get_supersticky_entry_by_id($entry_id)
  {
    // Get out early.
    if ( ! valid_int($entry_id, 1))
    {
      return FALSE;
    }

    $db_result = $this->EE->db
      ->select('entry_id, date_from, date_to, member_groups')
      ->get_where('supersticky_entries', array('entry_id' => $entry_id));

    if ( ! $db_result->num_rows())
    {
      return FALSE;
    }

    $supersticky_entry = new Supersticky_entry(array(
      'entry_id' => $entry_id));

    foreach ($db_result->result() AS $row)
    {
      /**
       * TRICKY:
       * An empty date string will be converted to the current date, so we
       * need to perform a separate check here.
       */

      if ( ! $row->date_from OR ! $row->date_to)
      {
        continue;
      }

      // Attempt to wrest the dates in valid DateTime objects.
      try
      {
        $date_from  = new DateTime($row->date_from);
        $date_to    = new DateTime($row->date_to);
      }
      catch (Exception $e)
      {
        continue;
      }

      // Do we have valid member groups.
      $invalid_member_group = FALSE;

      foreach (($member_groups = explode('|', $row->member_groups))
        AS $member_group
      )
      {
        if ( ! valid_int($member_group, 1))
        {
          // Oh for named continues.
          $invalid_member_group = TRUE;
          break;
        }
      }

      if ($invalid_member_group)
      {
        continue;
      }

      $supersticky_entry->add_criterion(new Supersticky_criterion(array(
        'date_from'     => $date_from,
        'date_to'       => $date_to,
        'member_groups' => $member_groups
      )));
    }

    return $supersticky_entry;
  }


  /**
   * Installs the module.
   *
   * @access  public
   * @return  bool
   */
  public function install_module()
  {
    $this->install_module_register();
    $this->install_module_actions();
    $this->install_module_tables();
    $this->install_module_tabs();

    return TRUE;
  }


  /**
   * Register the module actions in the database.
   *
   * @access  public
   * @return  void
   */
  public function install_module_actions()
  {
    $this->EE->db->insert('actions', array(
      'class'     => ucfirst($this->get_package_name()),
      'method'    => ''
    ));
  }


  /**
   * Registers the module in the database.
   *
   * @access  public
   * @return  void
   */
  public function install_module_register()
  {
    $this->EE->db->insert('modules', array(
      'has_cp_backend'      => 'y',
      'has_publish_fields'  => 'y',
      'module_name'         => ucfirst($this->get_package_name()),
      'module_version'      => $this->get_package_version()
    ));
  }


  /**
   * Creates the module tables in the database.
   *
   * @access  public
   * @return  void
   */
  public function install_module_tables()
  {
    $this->EE->load->dbforge();

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

    $this->EE->dbforge->add_field($fields);
    $this->EE->dbforge->add_key('entry_id');
    $this->EE->dbforge->add_key('date_from');
    $this->EE->dbforge->add_key('date_to');
    $this->EE->dbforge->create_table('supersticky_entries', TRUE);
  }


  /**
   * Adds the layout tabs to any saved Publish Layouts.
   *
   * @access  public
   * @return  void
   */
  public function install_module_tabs()
  {
    $this->EE->load->library('layout');
    $this->EE->layout->add_layout_tabs($this->_get_layout_tabs(),
      'supersticky');
  }

  /**
   * Logs a message to OmniLog.
   *
   * @access  public
   * @param   string      $message        The log entry message.
   * @param   int         $severity       The log entry 'level'.
   * @param   array       $emails         An array of "admin" email addresses.
   * @param   string      $extended_data  Additional data.
   * @return  void
   */
  public function log_message(
    $message,
    $severity = 1,
    Array $emails = array(),
    $extended_data = ''
  )
  {
    if (class_exists('Omnilog_entry') && class_exists('Omnilogger'))
    {
      switch ($severity)
      {
        case 3:
          $notify = TRUE;
          $type   = Omnilog_entry::ERROR;
          break;

        case 2:
          $notify = FALSE;
          $type   = Omnilog_entry::WARNING;
          break;

        case 1:
        default:
          $notify = FALSE;
          $type   = Omnilog_entry::NOTICE;
          break;
      }

      $omnilog_entry = new Omnilog_entry(array(
        'addon_name'    => $this->get_package_name(),
        'admin_emails'  => $emails,
        'date'          => time(),
        'extended_data' => $extended_data,
        'message'       => $message,
        'notify_admin'  => $notify,
        'type'          => $type
      ));

      Omnilogger::log($omnilog_entry);
    }
  }


  /**
   * Saves the supplied SuperSticky Entry to the database. Overwrites
   * any existing SuperSticky Entries with the same entry ID.
   *
   * @access  public
   * @param   Supersticky_entry     $entry    The entry to save.
   * @return  bool
   */
  public function save_supersticky_entry(Supersticky_entry $entry)
  {
    // Can't do anything without an entry ID or criteria.
    if ( ! ($entry_id = $entry->get_entry_id())
      OR ! ($criteria = $entry->get_criteria())
    )
    {
      return FALSE;
    }

    // Check that the supplied criteria are valid.
    foreach ($criteria AS $criterion)
    {
      if ( ! $criterion->get_date_from()
        OR ! $criterion->get_date_to()
        OR ! $criterion->get_member_groups()
      )
      {
        return FALSE;
      }
    }

    // Delete any existing rows for this entry.
    $this->EE->db->delete('supersticky_entries',
      array('entry_id' => $entry_id));

    // Create the new rows.
    foreach ($criteria AS $criterion)
    {
      $this->EE->db->insert('supersticky_entries', array(
        'entry_id'      => $entry_id,
        'date_from'     => $criterion->get_date_from()->format(DATE_W3C),
        'date_to'       => $criterion->get_date_to()->format(DATE_W3C),
        'member_groups' => implode('|', $criterion->get_member_groups())
      ));
    }

    return TRUE;
  }


  /**
   * Uninstalls the module.
   *
   * @access  public
   * @return  bool
   */
  public function uninstall_module()
  {
    $module_name = ucfirst($this->get_package_name());

    // Retrieve the module information.
    $db_module = $this->EE->db
      ->select('module_id')
      ->get_where('modules', array('module_name' => $module_name), 1);

    if ($db_module->num_rows() !== 1)
    {
      return FALSE;
    }

    // Delete all traces of the module from the EE DB tables.
    $this->EE->db->delete('module_member_groups',
      array('module_id' => $db_module->row()->module_id));

    $this->EE->db->delete('actions', array('class' => $module_name));
    $this->EE->db->delete('modules', array('module_name' => $module_name));

    // Drop the SuperSticky database table.
    $this->EE->load->dbforge();
    $this->EE->dbforge->drop_table('supersticky_entries');

    // Delete the layout tabs from any saved Publish Layouts.
    $this->EE->load->library('layout');
    $this->EE->layout->delete_layout_tabs($this->_get_layout_tabs(),
      'supersticky');

    return TRUE;
  }


  /**
   * Updates the module.
   *
   * @access  public
   * @param   string        $installed_version        The installed version.
   * @return  bool
   */
  public function update_module($installed_version = '')
  {
    if (version_compare($installed_version, $this->get_package_version(), '>='))
    {
      return FALSE;
    }

    return TRUE;
  }


  /**
   * Updates a SuperSticky Entry with any POST data.
   *
   * @access  public
   * @param   Supersticky_entry     $entry    The SuperSticky Entry.
   * @return  Supersticky_entry
   */
  public function update_supersticky_entry_with_post_data(
    Supersticky_entry $entry
  )
  {
    $in = $this->EE->input;
    $new_entry = clone $entry;   // Don't touch the original.

    // Retrieve the POST data.
    if ( ! is_array(($in_criteria = $in->post('supersticky_criteria'))))
    {
      return $new_entry;
    }

    foreach ($in_criteria AS $in_criterion)
    {
      if ( ! array_key_exists('date_from', $in_criterion)
        OR ! array_key_exists('date_to', $in_criterion)
        OR ! array_key_exists('member_groups', $in_criterion)
        OR ($date_from = date_create($in_criterion['date_from'])) === FALSE
        OR ($date_to = date_create($in_criterion['date_to'])) === FALSE
        OR ! is_array($in_criterion['member_groups'])
      )
      {
        continue;
      }

      $new_entry->add_criterion(new Supersticky_criterion(array(
        'date_from'     => new DateTime($in_criterion['date_from']),
        'date_to'       => new DateTime($in_criterion['date_to']),
        'member_groups' => $in_criterion['member_groups']
      )));
    }

    return $new_entry;
  }


}


/* End of file      : supersticky_model.php */
/* File location    : third_party/supersticky/models/supersticky_model.php */
