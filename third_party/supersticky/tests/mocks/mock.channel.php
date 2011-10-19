<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * Very quick-and-dirty "mock" Channel class. Not a mock in the true sense
 * of the word, more of a replacement for the real Channel class.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

class Channel {

  protected $EE;

  public function __construct()
  {
    $this->EE =& get_instance();
  }

  public function build_sql_query($qstring = '') {}

}


/* End of file		: mock.channel.php */
/* File location	: third_party/supersticky/tests/mocks/mock.channel.php */
