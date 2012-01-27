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

    /**
     * Retrieve all the SuperSticky criteria for the current date.
     *
     * NOTE:
     * At present, SuperSticky does not support pipe-separated channel names.
     * One for the future.
     */

    $ss_entries = $this->_model->get_supersticky_entries_for_date(
      new DateTime(), $this->EE->TMPL->fetch_param('channel', NULL));

    // If there are no SuperSticky entries for the current date, we're done.
    if ( ! $ss_entries)
    {
      return;
    }

    $ss_length    = count($ss_entries);
    $ss_items     = array();
    $ss_entry_ids = array();
    $where_sql    = '';

    for ($count = 1; $count <= $ss_length; $count++)
    {
      $ss_entry = $ss_entries[$count - 1];
      $include  = FALSE;

      // Does this criterion apply to the current member group.
      foreach ($ss_entry->get_criteria() AS $criterion)
      {
        if (in_array($group_id, $criterion->get_member_groups()))
        {
          $ss_items[] = (count($ss_items) === 0)
            ? "SELECT {$ss_entry->get_entry_id()} AS entry_id,
              {$count} AS order_index"
            : "SELECT {$ss_entry->get_entry_id()}, {$count}";

          $ss_entry_ids[] = $ss_entry->get_entry_id();
          break;
        }
      }
    }

    /**
     * If there are no SuperSticky entries for the current date and member
     * group, we're done.
     */

    if ( ! $ss_items)
    {
      return;
    }

    $ss_items[0] .= (count($ss_items) > 1 ? ' UNION ALL' : '');

    $select_sql = 'SELECT IFNULL(ss.order_index, 999999) AS ss_order_index,';
    $order_sql  = 'ORDER BY ss_order_index ASC,';
    $where_sql  = 'LEFT JOIN (' .implode(' ', $ss_items) .')
      AS ss ON ss.entry_id = t.entry_id WHERE';

    $sql = str_replace('SELECT', $select_sql, $sql);
    $sql = str_replace('ORDER BY', $order_sql, $sql);
    $sql = str_replace('WHERE', $where_sql, $sql);

    /**
     * TRICKY:
     * The standard EE SQL query lists all the possible entry IDs matching the
     * given criteria in an `IN(...)` clause.
     *
     * [explitives redacted].
     *
     * This has an unfortunate side effect when the `limit` parameter is used.
     * Namely, the `IN` SQL clause may not include the required SuperSticky
     * entry IDs.
     *
     * As such we need to:
     * 1. Add the SuperSticky entry IDs to the `IN` clause, as requred.
     * 2. Add a `LIMIT` clause to the SQL statement, if required.
     */

    if (preg_match('/IN ?\(([0-9, ]+)\)/', $sql, $in_matches))
    {
      $in_entry_ids     = explode(',', $in_matches[1]);
      $clean_entry_ids  = array();

      foreach ($in_entry_ids AS $in_entry_id)
      {
        $clean_entry_ids[] = (int) trim($in_entry_id);
      }

      $clean_entry_ids = array_merge($clean_entry_ids, $ss_entry_ids);
      sort($clean_entry_ids);

      $sql = str_replace($in_matches[0],
        'IN(' .implode(',', $clean_entry_ids) .')', $sql);
    }

    /**
     * Because we're overridding the 'IN' clause, we also need to ensure the
     * `limit` parameter is honoured.
     */

    if (valid_int($this->EE->TMPL->fetch_param('limit', 0), 1))
    {
      $sql .= ' LIMIT ' .$this->EE->TMPL->fetch_param('limit');
    }

    $this->sql = $sql;
  }

    
}


/* End of file      : mod.supersticky.php */
/* File location    : third_party/supersticky/mod.supersticky.php */
