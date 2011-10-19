<?php if ( ! defined('BASEPATH')) exit('Direct script access is not permitted.');

/**
 * SuperSticky module.
 *
 * @author          Stephen Lewis (http://experienceinternet.co.uk/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

if ( ! class_exists('Channel'))
{
  require_once APPPATH .'modules/channel/mod.channel.php';
}

class Supersticky extends Channel {
    
  private $_model;
  public $return_data = '';
  
  
  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Constructor.
   *
   * @access  public
   * @return  void
   */
  public function __construct()
  {
    parent::__construct();

    $this->EE->load->model('supersticky_model');
    $this->_model = $this->EE->supersticky_model;
  }
  
  
  /* --------------------------------------------------------------
   * TEMPLATE TAG METHODS
   * ------------------------------------------------------------ */
  
  /**
   * Overrides the 'build' SQL query method.
   *
   * @access  public
   * @param   string      $qstring        Query string.
   * @return  void
   */
  public function build_sql_query($qstring = '')
  {
    parent::build_sql_query($qstring);

    $group_id = $this->EE->session->userdata('group_id');
    $sql      = $this->sql;

    // Retrieve all the SuperSticky criteria for the current date.
    $ss_entries = $this->_model->get_supersticky_entries_for_date(
      new DateTime());

    // If there are no SuperSticky entries for the current date, we're done.
    if ( ! $ss_entries)
    {
      return;
    }

    $ss_length  = count($ss_entries);
    $ss_items   = array();
    $where_sql  = '';

    for ($count = 1; $count <= $ss_length; $count++)
    {
      $ss_entry = $ss_entries[$count - 1];
      $include  = FALSE;

      // Does this criterion apply to the current member group.
      foreach ($ss_entry->get_criteria() AS $criterion)
      {
        if (in_array($group_id, $criterion->get_member_groups()))
        {
          $ss_items[] = ($count === 1)
            ? "SELECT {$ss_entry->get_entry_id()} AS entry_id,
              {$count} AS order_index"
            : "SELECT {$ss_entry->get_entry_id()}, {$count}";

          break;
        }
      }
    }

    if ($ss_items)
    {
      $ss_items[0] .= (count($ss_items) > 1 ? ' UNION ALL' : '');
      $where_sql = 'LEFT JOIN (' .implode(' ', $ss_items) .')
        AS ss ON ss.entry_id = t.entry_id WHERE';
    }

    $select_sql = 'SELECT IFNULL(ss.order_index, 999999) AS ss_order_index,';
    $order_sql  = 'ORDER BY ss_order_index ASC,';

    $sql = str_replace('SELECT', $select_sql, $sql);
    $sql = str_replace('ORDER BY', $order_sql, $sql);
    $sql = str_replace('WHERE', $where_sql, $sql);

    $this->sql = $sql;
  }

    
}


/* End of file      : mod.supersticky.php */
/* File location    : third_party/supersticky/mod.supersticky.php */
