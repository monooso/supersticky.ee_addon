<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * SuperSticky Criterion datatype.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

class Supersticky_criterion {

  // Known criterion types.
  const TYPE_DATE_RANGE   = 'date_range';
  const TYPE_MEMBER_GROUP = 'member_group';

  private $_type;
  private $_value;


  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Constructor.
   *
   * @access  public
   * @param   array       $properties         An associative array of instance properties. Optional.
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
   * Returns the Criterion Type.
   *
   * @access  public
   * @return  string
   */
  public function get_type()
  {
    return $this->_type;
  }


  /**
   * Returns the Criterion Value.
   *
   * @access  public
   * @return  string
   */
  public function get_value()
  {
    return $this->_value;
  }


  /**
   * Resets in the instance properties.
   *
   * @access  public
   * @return  Supersticky_criterion
   */
  public function reset()
  {
    $this->_type  = '';
    $this->_value = '';

    return $this;
  }


  /**
   * Sets the Criterion Type.
   *
   * @access  public
   * @param   string	type	The Criterion Type.
   * @return  string
   */
  public function set_type($type)
  {
    if ($this->_is_valid_type($type))
    {
      $this->_type = $type;
    }

    return $this->get_type();
  }


  /**
   * Sets the Criterion Value.
   *
   * @access  public
   * @param   mixed     $value    The Criterion Value.
   * @return  mixed
   */
  public function set_value($value)
  {
    if (is_float($value) OR is_int($value) OR is_string($value))
    {
      $this->_value = $value;
    }

    return $this->get_value();
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
      'type'  => $this->get_type(),
      'value' => $this->get_value()
    );
  }


  /* --------------------------------------------------------------
   * PRIVATE METHODS
   * ------------------------------------------------------------ */
  
  /**
   * Determines whether the supplied 'type' is valid (i.e. recognised).
   *
   * @access  private
   * @param   string    $type    The type.
   * @return  bool
   */
  private function _is_valid_type($type)
  {
    return in_array(
      $type,
      array(self::TYPE_DATE_RANGE, self::TYPE_MEMBER_GROUP)
    );
  }


}


/* End of file      : supersticky_criterion.php */
/* File location    : third_party/supersticky/classes/supersticky_criterion.php */
