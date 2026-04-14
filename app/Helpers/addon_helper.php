<?php
/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

if (! function_exists('addon_status')) {
  function addon_status($unique_identifier = '') {
    $db = \Config\Database::connect();
    $result = get_where('addons', array('unique_identifier' => $unique_identifier));
    if ($result->numRows() > 0) {
      $result = $result->getRowArray();
      return $result['status'];
    }else{
      return 0;
    }
  }
}
// ------------------------------------------------------------------------
/* End of file addon_helper.php */
