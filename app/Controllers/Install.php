<?php

namespace App\Controllers;


/*
 *  @author   : Creativeitem
 *  date    : .17 December, 2019
 *  Academy
 *  http://codecanyon.net/user/Creativeitem
 *  http://support.creativeitem.com
 */

     ini_set('max_execution_time', 0);
     ini_set('memory_limit','2048M');

class Install extends BaseController {

  public function index() {
    if ($this->router->default_controller == 'install') {
      return redirect()->to(site_url('install/step0'));
    }
    return redirect()->to(site_url('login'));
  }

  function step0() {
    if ($this->router->default_controller != 'install') {
      return redirect()->to(site_url('login'));
      }
    $page_data['page_name'] = 'step0';
    return view('install/index', $page_data);
  }

  function step1() {
    if ($this->router->default_controller != 'install') {
      return redirect()->to(site_url('login'));
    }
    $page_data['page_name'] = 'step1';
    return view('install/index', $page_data);
  }

  function step2($param1 = '', $param2 = '') {
    if ($this->router->default_controller != 'install') {
      return redirect()->to(site_url('login'));
     }
 
     if ($param1 == 'error') {
      $page_data['error'] = 'Purchase Code Verification Failed';
    }
    $page_data['page_name'] = 'step2';
    return view('install/index', $page_data);
  }

  function validate_purchase_code() {
    $this->loadModel('install_model');
    $purchase_code = $this->request->getPost('purchase_code');
    $validation_response = $this->install_model->api_request($purchase_code);
    if ($validation_response == true) {
      // keeping the purchase code in users session
      session_start();
      $_SESSION['purchase_code']  = $purchase_code;
      $_SESSION['purchase_code_verified'] = 1;
      //move to step 3
      return redirect()->to(site_url('install/step3'));
    } else {
      //remain on step 2 and show error
      session_start();
      $_SESSION['purchase_code_verified'] = 0;
      return redirect()->to(site_url('install/step2/error'));
    }
  }

  function step3($param1 = '', $param2 = '') {
    if ($this->router->default_controller != 'install') {
      return redirect()->to(site_url('login'));
    }

    $this->check_purchase_code_verification();

    if ($param1 == 'error_con_fail') {
      $page_data['error_con_fail'] = 'Error establishing a database conenction using your provided information. Please
      recheck hostname, username, password and try again with correct information';
    }
    if ($param1 == 'error_nodb') {
      $page_data['error_con_fail'] = 'The database you are trying to use for the application does not exist. Please create
      the database first';
    }
    if ($param1 == 'configure_database') {
      $hostname = $this->request->getPost('hostname');
      $username = $this->request->getPost('username');
      $password = $this->request->getPost('password');
      $dbname   = $this->request->getPost('dbname');
      // check db connection using the above credentials
      $db_connection = $this->check_database_connection($hostname, $username, $password, $dbname);
      if ($db_connection == 'failed') {
        return redirect()->to(site_url('install/step3/error_con_fail'));
      } else if ($db_connection == 'db_not_exist') {
        return redirect()->to(site_url('install/step3/error_nodb'));
      } else {
        // proceed to step 4
        session_start();
        $_SESSION['hostname'] = $hostname;
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        $_SESSION['dbname']   = $dbname;
        return redirect()->to(site_url('install/step4'));
      }
    }
    $page_data['page_name'] = 'step3';
    return view('install/index', $page_data);
  }

  function check_purchase_code_verification() {
    if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
      //return 'running_locally';
    } else {
      session_start();
      if (!isset($_SESSION['purchase_code_verified']))
          return redirect()->to(site_url('install/step2'));
      else if ($_SESSION['purchase_code_verified'] == 0)
          return redirect()->to(site_url('install/step2'));
        }
  }

  function check_database_connection($hostname, $username, $password, $dbname) {
    $link = mysqli_connect($hostname, $username, $password, $dbname);
        if (!$link) {
          mysqli_close($link);
          return 'failed';
        }
        $db_selected = mysqli_select_db($link, $dbname);
        if (!$db_selected) {
          mysqli_close($link);
          return "db_not_exist";
        }
        mysqli_close($link);
        return 'success';
  }

  function step4($param1 = '') {
    if ($this->router->default_controller != 'install') {
      return redirect()->to(site_url('login'));
    }

    if ($param1 == 'confirm_install') {
      // write database.php
      $this->configure_database();

      // run sql
      $this->run_blank_sql();

      // redirect to admin creation page
      return redirect()->to(site_url('install/finalizing_setup'));
    }

    $page_data['page_name'] = 'step4';
    return view('install/index', $page_data);
  }

  function configure_database() {
    // write database.php
    $data_db = file_get_contents('./application/config/database.php');
    session_start();
    $data_db = str_replace('db_name',    $_SESSION['dbname'],    $data_db);
    $data_db = str_replace('db_user',    $_SESSION['username'],    $data_db);
    $data_db = str_replace('db_pass',    $_SESSION['password'],    $data_db);
    $data_db = str_replace('db_host',    $_SESSION['hostname'],    $data_db);
    file_put_contents('./application/config/database.php', $data_db);
  }

  function run_blank_sql() {
    // Set line to collect lines that wrap
    $templine = '';
    // Read in entire file
    $lines = file('./assets/install.sql');
    // Loop through each line
    foreach ($lines as $line) {
      // Skip it if it's a comment
      if (substr($line, 0, 2) == '--' || $line == '')
        continue;
      // Add this line to the current templine we are creating
      $templine .= $line;
      // If it has a semicolon at the end, it's the end of the query so can process this templine
      if (substr(trim($line), -1, 1) == ';') {
        // Perform the query
        db()->query($templine);
        // Reset temp variable to empty
        $templine = '';
      }
    }
  }

  function finalizing_setup($param1 = '', $param2 = '') {
    if ($this->router->default_controller != 'install') {
      return redirect()->to(site_url('login'));
    }

    if ($param1 == 'setup_admin') {

      /*school data*/
      $school_data['name']         = html_escape($this->request->getPost('school_name'));
      $school_data['address']      = "School Address";
      $school_data['phone']        = "+123123123123";
      db()->table('schools')->insert($school_data);
      $school_id = db()->insertID();

      /*session data*/
      $session_data['name']        = html_escape($this->request->getPost('current_session'));
      $session_data['status']      = 1;
      db()->table('sessions')->insert($session_data);
      $session_id = db()->insertID();

      /*system data*/
      $system_data['system_name']  = html_escape($this->request->getPost('system_name'));
      $system_data['timezone']  = html_escape($this->request->getPost('timezone'));
      session_start();
      if (isset($_SESSION['purchase_code'])) {
        $system_data['purchase_code']  = $_SESSION['purchase_code'];
      }
      session_destroy();
      $system_data['school_id'] = $school_id;
      $system_data['running_session'] = $session_id;

      db()->table('settings')->where('id', 1)->update($system_data);

      /*superadmin data*/
      $superadmin_data['name']      = html_escape($this->request->getPost('superadmin_name'));
      $superadmin_data['email']     = html_escape($this->request->getPost('superadmin_email'));
      $superadmin_data['password']  = sha1($this->request->getPost('superadmin_password'));
      $superadmin_data['role']      = 'superadmin';
      $superadmin_data['school_id'] = $school_id;
      $superadmin_data['watch_history'] = "[]";

      db()->table('users')->insert($superadmin_data);

      return redirect()->to(site_url('install/success'));
    }

    $page_data['page_name'] = 'finalizing_setup';
    return view('install/index', $page_data);
  }

  function success($param1 = '') {
    if ($this->router->default_controller != 'install') {
      return redirect()->to(site_url('login'));
    }

    if ($param1 == 'database') {
      $this->configure_routes();
      return redirect()->to(site_url('login'));
    }

    $superadmin_email = db()->table('users', array('id' => 1))->getRow()->email;

    $page_data['admin_email'] = $superadmin_email;
    $page_data['page_name'] = 'success';
    return view('install/index', $page_data);
  }

  function configure_routes() {
    // write routes.php
    $data_routes = file_get_contents('./application/config/routes.php');
    $data_routes = str_replace('install',    'home',    $data_routes);
    file_put_contents('./application/config/routes.php', $data_routes);
  }

}
