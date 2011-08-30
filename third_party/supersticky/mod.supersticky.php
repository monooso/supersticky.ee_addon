<?php if ( ! defined('BASEPATH')) exit('Direct script access is not permitted.');

/**
 * SuperSticky module.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

require_once APPPATH .'modules/channel/mod.channel.php';

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

        // Customise the SQL.
        $sql = $this->sql;

        $select = 'SELECT IFNULL(ss.order_index, 999999) AS ss_order_index,';

        $where = 'LEFT JOIN
            (SELECT 1 AS entry_id, 10 AS order_index UNION ALL SELECT 2, 20)
            AS ss ON ss.entry_id = t.entry_id
            WHERE';

        $order = 'ORDER BY ss_order_index ASC,';

        $sql = str_replace('SELECT', $select, $sql);
        $sql = str_replace('WHERE', $where, $sql);
        $sql = str_replace('ORDER BY', $order, $sql);

        $this->sql = $sql;
    }

    
}


/* End of file      : mod.supersticky.php */
/* File location    : third_party/supersticky/mod.supersticky.php */
