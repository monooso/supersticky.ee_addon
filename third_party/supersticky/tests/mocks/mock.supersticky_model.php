<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * Mock SuperSticky model.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

class Mock_supersticky_model {

  public function get_criterion_type_options() {}
  public function get_package_name() {}
  public function get_package_theme_url() {}
  public function get_package_version() {}
  public function get_site_id() {}
  public function get_supersticky_entry_by_id($entry_id) {}
  public function install_module() {}
  public function install_module_actions() {}
  public function install_module_register() {}
  public function install_module_tables() {}
  public function install_module_tabs() {}

  public function log_message($message, $severity = 1,
    Array $emails = array(), $extended_data = '') {}

  public function save_supersticky_entry(Supersticky_entry $entry) {}
  public function uninstall_module() {}
  public function update_module($installed_version = '') {}

  public function update_supersticky_entry_with_post_data(
    Supersticky_entry $entry) {}

}


/* End of file		: mock.supersticky_model.php */
/* File location	: third_party/supersticky/tests/mocks/mock.supersticky_model.php */
