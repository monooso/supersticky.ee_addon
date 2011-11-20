<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * SuperSticky Criterion datatype.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

require_once PATH_THIRD .'supersticky/helpers/EI_number_helper.php';

class Supersticky_criterion {

  private $_date_from;
  private $_date_to;
  private $_member_groups;


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
   * Adds a group ID to the member groups array.
   *
   * @access  public
   * @param   int|string  $group_id     The member group ID.
   * @return  Array
   */
  public function add_member_group($group_id)
  {
    if (valid_int($group_id, 1))
    {
      $this->_member_groups[] = (int) $group_id;
    }

    return $this->get_member_groups();
  }


  /**
   * Returns the 'from' date.
   *
   * @access public
   * @return DateTime or NULL
   */
  public function get_date_from()
  {
    return $this->_date_from;
  }


  /**
   * Returns to 'to' date.
   *
   * @access public
   * @return DateTime or NULL
   */
  public function get_date_to()
  {
    return $this->_date_to;
  }


  /**
   * Returns the member groups to which this criterion applies.
   *
   * @access public
   * @return Array
   */
  public function get_member_groups()
  {
    return $this->_member_groups;
  }


  /**
   * Resets in the instance properties.
   *
   * @access  public
   * @return  Supersticky_criterion
   */
  public function reset()
  {
    $this->_date_from = NULL;
    $this->_date_to = NULL;
    $this->_member_groups = array();

    return $this;
  }


  /**
   * Sets the 'from' date.
   *
   * @access  public
   * @param   DateTime    $date_from    The 'from' date.
   * @return  DateTime
   */
  public function set_date_from(DateTime $date_from)
  {
    $this->_date_from = $date_from;
    return $this->get_date_from();
  }


  /**
   * Sets the 'to' date.
   *
   * @access  public
   * @param   DateTime    $date_to    The 'to' date.
   * @return  DateTime
   */
  public function set_date_to(DateTime $date_to)
  {
    $this->_date_to = $date_to;
    return $this->get_date_to();
  }


  /**
   * Sets the member groups to which this criterion applies.
   *
   * @access  public
   * @param   Array     $member_groups    An array of member group IDs.
   * @return  Array
   */
  public function set_member_groups(Array $member_groups = array())
  {
    $this->_member_groups = array();

    foreach ($member_groups AS $member_group)
    {
      $this->add_member_group($member_group);
    }

    return $this->get_member_groups();
  }


  /**
   * Returns the instance as an associative array.
   *
   * @access  public
   * @return  Array
   */
  public function to_array()
  {
    return array(
      'date_from'     => $this->get_date_from(),
      'date_to'       => $this->get_date_to(),
      'member_groups' => $this->get_member_groups()
    );
  }


}


/* End of file      : supersticky_criterion.php */
/* File location    : third_party/supersticky/classes/supersticky_criterion.php */