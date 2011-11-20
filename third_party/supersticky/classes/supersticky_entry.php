<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * SuperSticky Entry datatype.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

require_once PATH_THIRD .'supersticky/classes/supersticky_criterion.php';
require_once PATH_THIRD .'supersticky/helpers/EI_number_helper.php';

class Supersticky_entry {

  private $_criteria;
  private $_entry_id;


  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Constructor.
   *
   * @access  public
   * @param   array     $properties   Associative array of instance properties.
   * @return  void
   */
  public function __construct(Array $properties = array())
  {
    $this->reset();

    foreach ($properties AS $prop_name => $prop_value)
    {
      $method_name = 'set_' .$prop_name;

      if (method_exists($this, $method_name))
      {
        $this->$method_name($prop_value);
      }
    }
  }


  /**
   * Adds a criterion to the criteria array.
   *
   * @access  public
   * @param   Supersticky_criterion    $criterion    The criterion to add.
   * @return  Array
   */
  public function add_criterion(Supersticky_criterion $criterion)
  {
    $this->_criteria[] = $criterion;
    return $this->_criteria;
  }


  /**
   * Returns the criteria.
   *
   * @access  public
   * @return  Array
   */
  public function get_criteria()
  {
    return $this->_criteria;
  }


  /**
   * Returns the entry ID.
   *
   * @access  public
   * @return  int
   */
  public function get_entry_id()
  {
    return $this->_entry_id;
  }


  /**
   * Resets in the instance properties.
   *
   * @access  public
   * @return  Supersticky_entry
   */
  public function reset()
  {
    $this->_criteria = array();
    $this->_entry_id = 0;

    return $this;
  }


  /**
   * Sets the criteria.
   *
   * @access  public
   * @param   Array     criteria    The criteria.
   * @return  Array
   */
  public function set_criteria(Array $criteria = array())
  {
    $this->_criteria = array();

    foreach ($criteria AS $criterion)
    {
      if ($criterion instanceof Supersticky_criterion)
      {
        $this->add_criterion($criterion);
      }
    }

    return $this->get_criteria();
  }


  /**
   * Sets the entry ID.
   *
   * @access  public
   * @param   int	entry_id	The entry ID.
   * @return  int
   */
  public function set_entry_id($entry_id)
  {
    if (valid_int($entry_id, 1))
    {
      $this->_entry_id = (int) $entry_id;
    }

    return $this->get_entry_id();
  }


  /**
   * Returns the instance as an associative array.
   *
   * @access  public
   * @return  Array
   */
  public function to_array()
  {
    $criteria = $this->get_criteria();
    $return_criteria = array();

    foreach ($criteria AS $criterion)
    {
      $return_criteria[] = $criterion->to_array();
    }

    return array(
      'criteria' => $return_criteria,
      'entry_id' => $this->get_entry_id()
    );
  }


}


/* End of file      : supersticky_entry.php */
/* File location    : third_party/supersticky/classes/supersticky_entry.php */
