<?php

namespace App\Controllers\api;

use App\Libraries\REST_Controller;
use App\Libraries\TokenHandler;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;


require APPPATH . 'Libraries/TokenHandler.php';
//include Rest Controller library
require APPPATH . 'Libraries/REST_Controller.php';

class Admin extends REST_Controller {

  protected $token;
  public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
  {
    parent::initController($request, $response, $logger);
    
    /*LOADING ALL THE MODELS HERE*/
    $this->loadModel('Crud_model',     'crud_model');
    $this->loadModel('User_model',     'user_model');
    $this->loadModel('Settings_model', 'settings_model');
    $this->loadModel('Payment_model',  'payment_model');
    $this->loadModel('Email_model',    'email_model');
    $this->loadModel('Addon_model',    'addon_model');
    $this->loadModel('Frontend_model', 'frontend_model');

    /*API MODEL*/
    $this->loadModel('api/Api_admin_model','admin_model');

    /*SET DEFAULT TIMEZONE*/
		timezone();
    
    // creating object of TokenHandler class at first
    $this->tokenHandler = new TokenHandler();
  }

  /*
  * Unprotected routes will be located here.
  **/

  // FETCH ALL THE LANGUAGES
  public function languages_get() {
    $languages = $this->admin_model->languages_get();
    $this->set_response($languages, REST_Controller::HTTP_OK);
  }

  // VALIDATE USER TOKEN
  public function user_get() {
      $authHeader = $this->request->getHeaderLine('Authorization');
      $token = null;

      if ($authHeader) {
          $matches = array();
          if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
              $token = $matches[1];
          }
      }

      if (!$token) {
          $this->response(['status' => false, 'message' => 'Token not provided'], REST_Controller::HTTP_UNAUTHORIZED);
          return;
      }

      try {
          $decodedToken = $this->tokenHandler->DecodeToken($token);
          
          if (!isset($decodedToken['user_id'])) {
               throw new Exception('User ID missing in token');
          }

          // Fetch full user details from database
          $userDetails = $this->user_model->get_user_details($decodedToken['user_id']);
          
          if (!$userDetails) {
              $this->response(['status' => false, 'message' => 'User not found'], REST_Controller::HTTP_NOT_FOUND);
              return;
          }

          // AJOUT: Inclure l'URL de l'avatar avec cache busting pour la synchro Chat
          $userDetails['avatar'] = $this->user_model->get_user_image($decodedToken['user_id']);
          $userDetails['photo_profil'] = $userDetails['avatar']; // Alias pour compatibilité

          // Ensure email and id are present (CrossAuthController requirement)
          // The database column is 'id', but we can also add 'user_id' to match token if needed
          // CrossAuthController looks for 'id' and 'email'
          
          $payload = ['status' => true, 'data' => $userDetails];
          $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
          if ($json === false) {
              return $this->response(['status' => false, 'message' => 'Invalid user payload'], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
          }

          return $this->response
              ->setStatusCode(REST_Controller::HTTP_OK)
              ->setContentType('application/json')
              ->setBody($json);
      } catch (\Throwable $e) {
          $this->response(['status' => false, 'message' => 'Invalid Token'], REST_Controller::HTTP_UNAUTHORIZED);
      }
  }

  // menu
  public function menu_get() {
   
    $user_id = $this->get('user_id');
    if ($user_id) {
        $user_role = $this->getUserRole($user_id); 

        $user_type = array(
            'user_id' => $user_id,
            'role' => $user_role,
           
        );
    } else {
       
        $user_type = array();
    }


    $userdata = $this->admin_model->menu($user_type);

    $this->response($userdata, REST_Controller::HTTP_OK);
}



public function getUserRole($user_id) {

  $user = $this->admin_model->get_by_query([
      'select' => 'role',
      'from' => 'users',
      'where' => ['user_id' => $user_id],
      'return' => 'row'
  ]);

  if ($user) {
      return $user->role;
  } else {
      return null;
  }
}
//Online Admission API CALL
public function fetchStudentsByName_get($school_id, $name) {
  $like = [];
  if (!empty($name)) {
      if (strlen($name) === 1) {
          $like = ['name' => $name];
      } else {
          $like = ['name' => $name];
      }
  }

  $students = $this->admin_model->get_by_query([
      'select' => '*',
      'from' => 'users',
      'where' => ['role' => 'student', 'school_id' => $school_id],
      'like' => $like
  ]);

  // Check if students were found
  if ($students) {
      // Students found, return success response
      $response['status'] = 200;
      $response['message'] = 'Students retrieved successfully';
      $response['students'] = $students;
  } else {
      // No students found
      $response['status'] = 404;
      $response['message'] = 'No students found with the specified name';
      $response['students'] = [];
  }

  // Send response
  return $this->response->setJSON($response);
  
  // Debugging: Print the generated SQL query
  // echo db()->getLastQuery();
}



public function onlineadmission_post() {
  $response = array();
  $data = array(
      'name' => $this->post('name'),
      'email' => $this->post('email'),
      'password' => sha1($this->post('password')),
      'role' => 'student',
      'address' => $this->post('address'),
      'phone' => $this->post('phone'),
      'birthday' => date('Y-m-d', strtotime($this->post('birthday'))),
      'gender' => $this->post('gender'),
      'school_id' => $this->post('school_id'), 
      'status' => $this->post('status') ?? 1, // Set status to 1 by default for students
      'watch_history' => json_encode($this->post('watch_history')), 
  );
  $existing_email = $this->admin_model->get_user_by_email($data['email']);

  if ($existing_email) {
      $response['status'] = 409; 
      $response['message'] = 'Email already exists';
  } else {
      $insert = $this->admin_model->insert_table('users', $data);
      if ($insert) {
          $user_id = db()->insertID();
          if (isset($_FILES['student_image']) && $_FILES['student_image']['error'] === UPLOAD_ERR_OK) {
              move_uploaded_file($_FILES['student_image']['tmp_name'], 'uploads/users/'.$user_id.'.jpg');
          }
          $data['watch_history'] = json_decode($data['watch_history']);

          $response['status'] = 201;
          $response['message'] = 'User registered successfully';
          $response['user_id'] = $user_id;
          $response['name'] = $data['name'];
          $response['email'] = $data['email'];
          $response['role'] = $data['role'];
          $response['address'] = $data['address'];
          $response['phone'] = $data['phone'];
          $response['birthday'] = date('d-M-Y', strtotime($data['birthday']));
          $response['gender'] = $data['gender'];
          $response['school_id'] = $data['school_id'];
          $response['status'] = $data['status']; 
          $response['watch_history'] = $data['watch_history']; 

      } else {
          $response['status'] = 500;
          $response['message'] = 'Failed to register user';
      }
  }

  // Send response
  $this->set_response($response, REST_Controller::HTTP_CREATED);
}

public function onlineadmissionEdit_put($id) {
  $response = array();

  // Get user input data
  $data = array(
      'name' => $this->put('name'),
      'email' => $this->put('email'),
      'address' => $this->put('address'),
      'phone' => $this->put('phone'),
      'birthday' => date('Y-m-d', strtotime($this->put('birthday'))),
      'gender' => $this->put('gender'),
      'watch_history' => json_encode($this->put('watch_history')), // Convert watch_history array to JSON
  );

  // Check if user with given id exists in the database
  $existing_user = $this->admin_model->get_user_by_id($id);

  if ($existing_user) {
      // Update user data in the database
      $update = $this->admin_model->update_table('users', $data, ['id' => $id]);

      if ($update) {
          // User data updated successfully
          $response['status'] = 200;
          $response['message'] = 'User data updated successfully';

          // Handle image update
          $user_id = db()->insertID(); // Get the ID of the updated user
          if (isset($_FILES['student_image']) && $_FILES['student_image']['error'] === UPLOAD_ERR_OK) {
              // Move the uploaded image to the desired location with the new filename based on the user's ID
              move_uploaded_file($_FILES['student_image']['tmp_name'], 'uploads/users/'.$user_id.'.jpg');
          }
      } else {
          // Failed to update user data
          $response['status'] = 500;
          $response['message'] = 'Failed to update user data';
      }
  } else {
      // User with given id does not exist
      $response['status'] = 404;
      $response['message'] = 'User not found';
  }

  // Send response
  $this->set_response($response, REST_Controller::HTTP_OK);
}


public function onlineadmissionList_get($school_id) {
  // Fetch online admissions for new students (status = 3) from the database
  $online_admissions = $this->admin_model->get_where_result('users', [
      'role' => 'student',
      'status' => 3
  ]);
  
  $num_students = count($online_admissions); // Count the number of students
  
  if ($online_admissions) {
      // Online admissions found, return success response
      $response['status'] = 200; // Use status code 200 for success
      $response['message'] = 'Online admissions retrieved successfully';
      $response['num_students'] = $num_students; // Include the number of students in the response
      
      // Iterate through each online admission entry
      foreach ($online_admissions as &$admission) {
          // Add school_id to the admission entry
          $admission['school_id'] = $school_id;
          
          // Fetch and add image URL for the user
          $user_id = $admission['user_id']; // Assuming user_id is the primary key
          $image_path = 'uploads/users/'.$user_id.'.jpg'; // Construct image path
          
          // Check if the image file exists
          if (file_exists($image_path)) {
              // Image exists, include its URL in the admission entry
              $admission['image_url'] = base_url($image_path);
          } else {
              // Image does not exist, set image URL to null
              $admission['image_url'] = null;
          }
          
          // Add an empty watch_history array to the admission entry
          $admission['watch_history'] = [];
          
          // Optionally, you can remove 'user_id' from the admission entry if it's not needed in the response
          unset($admission['user_id']);
      }
      
      $response['online_admissions'] = $online_admissions;
  } else {
      // No online admissions found
      $response['status'] = 404; // Use status code 404 for not found
      $response['message'] = 'No new online admissions found for students';
      $response['num_students'] = 0; // Set the number of students to 0
      
      $response['online_admissions'] = [];
  }
  
  // Send response
  return $this->response->setJSON($response);
}

public function onlineadmissionListApprovedAndDesapproved_get($school_id) {
   $online_admissions = $this->admin_model->get_by_query([
       'select' => '*',
       'from' => 'users',
       'where' => "role = 'student' AND (status = 0 OR status = 1)"
   ]);
   
   $num_students = count($online_admissions);
   
   if ($online_admissions) {
       
       $response['status'] = 200; 
       $response['message'] = 'Online admissions retrieved successfully';
       $response['num_students'] = $num_students;
       $response['online_admissions'] = $online_admissions;
   } else {
       
       $response['status'] = 404; 
       $response['message'] = 'No new online admissions found for students with status = 0 or status = 1';
       $response['num_students'] = 0;
       $response['online_admissions'] = [];
   }
   return $this->response->setJSON($response);
}

public function approveOnlineAdmissions_put($id) {
  // Retrieve online admission with status 3 for the specified user ID
  $online_admission = $this->admin_model->get_row('users', [
      'id' => $id,
      'role' => 'student',
      'status' => 3
  ]);

  // Check if online admission with status 3 is found for the specified ID
  if ($online_admission) {
      // Update status to 1
      $this->admin_model->update_table('users', array('status' => 1), ['id' => $id]);

      // Return success response
      $response['status'] = 200;
      $response['message'] = 'Status updated successfully from 3 to 1';
  } else {
      // No online admission with status 3 found for the specified ID
      $response['status'] = 404;
      $response['message'] = 'No online admission found with status 3 for the specified ID';
  }

  // Send response
  return $this->response->setJSON($response);
}

public function deactivate_put($id) {
  // Retrieve online admission with status 1 for the specified user ID
  $online_admission = $this->admin_model->get_row('users', [
      'id' => $id,
      'role' => 'student',
      'status' => 1
  ]);

  // Check if online admission with status 1 is found for the specified ID
  if ($online_admission) {
      // Update status to 0
      $this->admin_model->update_table('users', array('status' => 0), ['id' => $id]);

      // Return success response
      $response['status'] = 200;
      $response['message'] = 'Status updated successfully from 1 to 0';
  } else {
      // No online admission with status 1 found for the specified ID
      $response['status'] = 404;
      $response['message'] = 'No online admission found with status 1 for the specified ID';
  }

  // Send response
  return $this->response->setJSON($response);
}

public function activate_put($id) {
  // Retrieve online admission with status 0 for the specified user ID
  $online_admission = $this->admin_model->get_row('users', [
      'id' => $id,
      'role' => 'student',
      'status' => 0
  ]);

  // Check if online admission with status 0 is found for the specified ID
  if ($online_admission) {
      // Update status to 1
      $this->admin_model->update_table('users', array('status' => 1), ['id' => $id]);

      // Return success response
      $response['status'] = 200;
      $response['message'] = 'Status updated successfully from 0 to 1';
  } else {
      // No online admission with status 0 found for the specified ID
      $response['status'] = 404;
      $response['message'] = 'No online admission found with status 0 for the specified ID';
  }

  // Send response
  return $this->response->setJSON($response);
}

public function onlineadmissionDelete_delete($id) {
  $existing_user = $this->admin_model->get_user_by_id($id);
  
  if ($existing_user) {

      $delete = $this->admin_model->delete_table('users', ['id' => $id]);
      
      if ($delete) {
      
          $response['status'] = 200;
          $response['message'] = 'User deleted successfully';
      } else {
         
          $response['status'] = 500;
          $response['message'] = 'Failed to delete user';
      }
  } else {
     
      $response['status'] = 404;
      $response['message'] = 'User not found';
  }
  
  // Send response
  $this->set_response($response, REST_Controller::HTTP_OK);
}

//Edit API CALL
public function editProfile_post() {
  
  $response = $this->admin_model->editProfile();

  return $this->set_response($response, REST_Controller::HTTP_OK);
}

//Update Current Password
public function updatePassword_post() {
  
  $response = $this->admin_model->updatePassword();

  return $this->set_response($response, REST_Controller::HTTP_OK);
}
//dashbord API CALL
public function get_dashboard_data_get() {
  $user_id = $this->request->getGet('user_id');

  $response = $this->admin_model->get_dashboard_data($user_id);

  return $this->set_response($response, REST_Controller::HTTP_OK);
}

//CREATE SCHOOL API CALL
public function categoriesByName_get() {
  $categories = $this->admin_model->get_ordered('categories', 'name', 'ASC');

  $category_names = array_map(function($category) {
      return $category->name;
  }, $categories);

  // Format the response to include only names
  $category_names = array_map(function($category) {
      return $category->name;
  }, $categories);

  // Return the response in JSON format
  $response = array(
      'status' => true,
      'categories' => $category_names
  );

  echo json_encode($response);
}

public function online_admission_school_post()
{
    // Begin transaction
    db()->transBegin();

    // Decode JSON data from the request body
    $json_data = json_decode($this->request->getPost('json_data'), true);

    if (is_null($json_data)) {
        db()->transRollback();
        $response = array(
            'status' => false,
            'message' => 'Invalid JSON data'
        );
        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        return;
    }

    // Check for duplicates
    $duplication_status = $this->user_model->check_duplication_school('on_create', $json_data['schoolName']);

    if ($duplication_status) {
        // Extract school data from JSON
        $school_data = array_merge(array(
            'name' => htmlspecialchars($json_data['schoolName']),
            'address' => htmlspecialchars($json_data['schoolAddress']),
            'phone' => htmlspecialchars($json_data['schoolPhone']),
            'status' => 0,
            'description' => htmlspecialchars($json_data['description']),
            'access' => htmlspecialchars($json_data['access']),
            'category' => htmlspecialchars($json_data['selectedCategory']),
        ), community_subscription_seed());

        // Insert school data into the database
        $school_id = $this->admin_model->insert_table('schools', $school_data);

        // Extract admin data from JSON
        $admin_data = array(
            'name' => htmlspecialchars($json_data['adminName']),
            'email' => htmlspecialchars($json_data['adminEmail']),
            'gender' => htmlspecialchars($json_data['adminGender']),
            'phone' => htmlspecialchars($json_data['adminPhone']),
            'password' => sha1($json_data['adminPassword']),
            'role' => 'admin',
            'school_id' => $school_id,
            'status' => 1,
            'watch_history' => '[]',
        );

        // Insert admin data into the database
        $this->admin_model->insert_table('users', $admin_data);
        $user_id = db()->insertID();

        // Handle image upload if it exists
        if (!empty($_FILES['image_file']['name'])) {
            $image_path = 'uploads/schools/' . $school_id . '.jpg';
            if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $image_path)) {
                // Image upload failed, rollback transaction
                db()->transRollback();
                $response = array(
                    'status' => false,
                    'message' => 'Failed to upload school image'
                );
                $this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
                return;
            }
        }

        // Commit transaction
        db()->transCommit();

        // Send success response
        $response = array('status' => 1, 'message' => 'School created successfully.');
        $this->response($response, REST_Controller::HTTP_OK);
    } else {
        // Rollback transaction in case of duplication error
        db()->transRollback();

        // Send duplication error response
        $response = array('status' => 0, 'message' => 'This school name already exists.');
        $this->response($response, REST_Controller::HTTP_CONFLICT);
    }
}

public function update_school_post()
{
    // Begin transaction
    db()->transBegin();

    // Fetch raw POST data and decode JSON
    $raw_post_data = $this->request->getRawInput();
    log_message('debug', 'Raw POST data: ' . $raw_post_data);

    $json_data = json_decode($raw_post_data, true);

    if (is_null($json_data) || !isset($json_data['schoolId'])) {
        db()->transRollback();
        $response = array(
            'status' => false,
            'message' => 'Invalid JSON data or missing school ID'
        );
        log_message('error', 'Invalid JSON data or missing school ID');
        $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
        return;
    }

    // Extract school ID from JSON
    $school_id = htmlspecialchars($json_data['schoolId']);
    log_message('debug', 'Extracted school ID: ' . $school_id);

    // Check for duplicates (if updating the school name)
    if (isset($json_data['schoolName'])) {
        $duplication_status = $this->user_model->check_duplication_school('on_update', $json_data['schoolName'], $school_id);
        log_message('debug', 'Duplication check status: ' . $duplication_status);

       
    }

    // Prepare school data for update
    $school_data = array(
        'name' => isset($json_data['schoolName']) ? htmlspecialchars($json_data['schoolName']) : null,
        'address' => isset($json_data['schoolAddress']) ? htmlspecialchars($json_data['schoolAddress']) : null,
        'phone' => isset($json_data['schoolPhone']) ? htmlspecialchars($json_data['schoolPhone']) : null,
        'description' => isset($json_data['description']) ? htmlspecialchars($json_data['description']) : null,
        'access' => isset($json_data['access']) ? htmlspecialchars($json_data['access']) : null,
        'category' => isset($json_data['selectedCategory']) ? htmlspecialchars($json_data['selectedCategory']) : null,
    );

    // Remove null values from the update array
    $school_data = array_filter($school_data, function($value) {
        return !is_null($value);
    });

    log_message('debug', 'School data to update: ' . json_encode($school_data));

    // Update school data in the database
    if (!$this->admin_model->update_table('schools', $school_data, ['id' => $school_id])) {
        db()->transRollback();
        $response = array(
            'status' => false,
            'message' => 'Failed to update school.'
        );
        log_message('error', 'Failed to update school.');
        $this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        return;
    }

    // Prepare admin data for update
    $admin_data = array(
        'name' => isset($json_data['adminName']) ? htmlspecialchars($json_data['adminName']) : null,
        'email' => isset($json_data['adminEmail']) ? htmlspecialchars($json_data['adminEmail']) : null,
        'gender' => isset($json_data['adminGender']) ? htmlspecialchars($json_data['adminGender']) : null,
        'phone' => isset($json_data['adminPhone']) ? htmlspecialchars($json_data['adminPhone']) : null,
        'password' => isset($json_data['adminPassword']) ? sha1($json_data['adminPassword']) : null,
    );

    // Remove null values from the update array
    $admin_data = array_filter($admin_data, function($value) {
        return !is_null($value);
    });

    log_message('debug', 'Admin data to update: ' . json_encode($admin_data));

    // Update admin data in the database
    if (!empty($admin_data)) {
        if (!$this->admin_model->update_table('users', $admin_data, ['school_id' => $school_id, 'role' => 'admin'])) {
            db()->transRollback();
            $response = array(
                'status' => false,
                'message' => 'Failed to update admin user.'
            );
            log_message('error', 'Failed to update admin user.');
            $this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }
    }

    // Handle image upload if it exists
    if (!empty($_FILES['image_file']['name'])) {
        $image_path = 'uploads/schools/' . $school_id . '.jpg';
        if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $image_path)) {
            // Image upload failed, rollback transaction
            db()->transRollback();
            $response = array(
                'status' => false,
                'message' => 'Failed to upload school image.'
            );
            log_message('error', 'Failed to upload school image.');
            $this->response($response, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }
    }

    // Commit transaction
    db()->transCommit();

    // Send success response
    $response = array(
        'status' => true,
        'message' => 'School updated successfully.'
    );
    log_message('debug', 'School updated successfully.');
    $this->response($response, REST_Controller::HTTP_OK);
}



public function delete_school_post($param1) {
  // Update the school's state to 0 (inactive) instead of deleting the record
  $this->admin_model->update_table('schools', array('etat' => 0), ['id' => $param1]);

  // Find and update the status of the admin user associated with the school
  $admin_user = $this->admin_model->get_by_query([
      'select' => '*',
      'from' => 'users',
      'where' => ['school_id' => $param1, 'role' => 'admin'],
      'return' => 'row_array'
  ]);

  if ($admin_user) {
      $this->admin_model->update_table('users', array('status' => 0), ['id' => $admin_user['id']]);
  }

  // Prepare the response
  $response = array(
      'status' => true,
      'notification' => get_phrase('school_has_been_deactivated_and_admin_status_updated_successfully')
  );

  // Send the response
  $this->response($response, REST_Controller::HTTP_OK);
}



public function schools_get() {
  // Query to retrieve all schools
  $schools = $this->admin_model->get_result('schools');

  // Check if schools are found
  if ($schools) {
      // Iterate over each school to add image path and admin info
      foreach ($schools as &$school) {
          // Add image path
          $image_path = 'uploads/schools/' . $school['id'] . '.jpg';
          if (file_exists($image_path)) {
              $school['image_url'] = base_url($image_path);
          } else {
              $school['image_url'] = null; // Or set a default image path
          }

          // Retrieve admin info from users table
          $admin = $this->admin_model->get_by_query([
              'select' => 'name, email, gender, phone, role, status, watch_history',
              'from' => 'users',
              'where' => ['school_id' => $school['id'], 'role' => 'admin'],
              'return' => 'row_array'
          ]);

          if ($admin) {
              $school['admin'] = array(
                  'name' => htmlspecialchars($admin['name']),
                  'email' => htmlspecialchars($admin['email']),
                  'gender' => htmlspecialchars($admin['gender']),
                  'phone' => htmlspecialchars($admin['phone']),
                  'role' => htmlspecialchars($admin['role']),
                  'status' => (int) $admin['status'],
                  'watch_history' => json_decode($admin['watch_history'])
              );
          } else {
              $school['admin'] = null; // Or provide default admin info
          }
      }

      // Prepare success response
      $response = array(
          'status' => true,
          'schools' => $schools
      );
  } else {
      // Prepare failure response
      $response = array(
          'status' => false,
          'notification' => 'No schools found'
      );
  }

  // Return the response
  return $this->response->setJSON($response);
}

public function schools_by_category_get() {
    // Set response content type to JSON
    $this->response->setContentType('application/json');

    // Query to retrieve all schools
    $schools = $this->admin_model->get_by_query([
        'select' => 'schools.*',
        'from' => 'schools'
    ]);

    // Check if schools are found
    if ($schools) {
        // Initialize an array to hold schools grouped by category
        $schools_by_category = array();

        // Iterate over each school to add image path and admin info
        foreach ($schools as &$school) {
            // Add image path
            $image_path = 'uploads/schools/' . $school['id'] . '.jpg';
            if (file_exists($image_path)) {
                $school['image_url'] = base_url($image_path);
            } else {
                $school['image_url'] = null; // Or set a default image path
            }

            // Retrieve admin info from users table
            $admin = $this->admin_model->get_by_query([
                'select' => 'name, email, gender, phone, role, status, watch_history',
                'from' => 'users',
                'where' => ['school_id' => $school['id'], 'role' => 'admin'],
                'return' => 'row_array'
            ]);

            if ($admin) {
                $school['admin'] = array(
                    'name' => htmlspecialchars($admin['name']),
                    'email' => htmlspecialchars($admin['email']),
                    'gender' => htmlspecialchars($admin['gender']),
                    'phone' => htmlspecialchars($admin['phone']),
                    'role' => htmlspecialchars($admin['role']),
                    'status' => (int) $admin['status'],
                    'watch_history' => json_decode($admin['watch_history'])
                );
            } else {
                $school['admin'] = null; // Or provide default admin info
            }

            // Group schools by category attribute
            $category_name = $school['category'];
            if (!isset($schools_by_category[$category_name])) {
                $schools_by_category[$category_name] = array();
            }
            $schools_by_category[$category_name][] = $school;
        }

        // Add "All" category that includes all schools
        $schools_by_category['All'] = $schools;

        // Prepare success response
        $response = array(
            'status' => true,
            'schools_by_category' => $schools_by_category
        );
    } else {
        // Prepare failure response
        $response = array(
            'status' => false,
            'notification' => 'No schools found'
        );
    }

    // Return the response
    return $this->response->setJSON($response);
}

public function search_schools_post() {
    // Set response content type to JSON
    $this->response->setContentType('application/json');

    // Decode JSON data from the request body
    $json_data = json_decode($this->request->getRawInput(), true);

    if (is_null($json_data)) {
        $response = array(
            'status' => false,
            'message' => 'Invalid JSON data'
        );
        return $this->response->setJSON($response);
        return;
    }

    // Extract search parameters
    $search_name = isset($json_data['name']) ? htmlspecialchars($json_data['name']) : '';
    $search_category = isset($json_data['category']) ? htmlspecialchars($json_data['category']) : '';

    // Start building the query
    $like = [];
    $where = [];
    
    if (!empty($search_name)) {
        $like['name'] = $search_name;
    }
    if (!empty($search_category) && $search_category !== 'All') {
        $where['category'] = $search_category;
    }

    // Execute the query
    $schools = $this->admin_model->get_by_query([
        'select' => 'schools.*',
        'from' => 'schools',
        'like' => $like,
        'where' => $where
    ]);

    // Check if schools are found
    if ($schools) {
        // Iterate over each school to add image path and admin info
        foreach ($schools as &$school) {
            // Add image path
            $image_path = 'uploads/schools/' . $school['id'] . '.jpg';
            if (file_exists($image_path)) {
                $school['image_url'] = base_url($image_path);
            } else {
                $school['image_url'] = null; // Or set a default image path
            }

           
            // Retrieve admin info from users table
            $admin = $this->admin_model->get_by_query([
                'select' => 'name, email, gender, phone, role, status, watch_history',
                'from' => 'users',
                'where' => ['school_id' => $school['id'], 'role' => 'admin'],
                'return' => 'row_array'
            ]);

            if ($admin) {
                $school['admin'] = array(
                    'name' => htmlspecialchars($admin['name']),
                    'email' => htmlspecialchars($admin['email']),
                    'gender' => htmlspecialchars($admin['gender']),
                    'phone' => htmlspecialchars($admin['phone']),
                    'role' => htmlspecialchars($admin['role']),
                    'status' => (int) $admin['status'],
                    'watch_history' => json_decode($admin['watch_history'])
                );
            } else {
                $school['admin'] = null; // Or provide default admin info
            }
        }

        // Prepare success response
        $response = array(
            'status' => true,
            'schools' => $schools
        );
    } else {
        // Prepare failure response
        $response = array(
            'status' => false,
            'message' => 'No schools found'
        );
    }

    // Return the response
    return $this->response->setJSON($response);
}

///////////////////////////////////////

public function create_admin_post()
{
    // Check if all required fields are present
    $required_fields = ['adminName', 'adminEmail', 'adminGender', 'adminPhone', 'adminPassword', 'school_name', 'adminAddress'];
    foreach ($required_fields as $field) {
        if ($this->request->getPost($field) === false || empty($this->request->getPost($field))) {
            $this->response(array(
                'status' => false,
                'message' => 'Missing field: ' . $field
            ), REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
    }

    // Retrieve the school ID based on the school name
    $school_name = $this->request->getPost('school_name');
    $school_id = $this->getSchoolId($school_name);

    if (!$school_id) {
        $this->response(array(
            'status' => false,
            'message' => 'Invalid school name'
        ), REST_Controller::HTTP_BAD_REQUEST);
        return;
    }

    // Extract admin data from POST using CI3 input library
    $admin_data = array(
        'name' => $this->request->getPost('adminName'),
        'email' => $this->request->getPost('adminEmail'),
        'gender' => $this->request->getPost('adminGender'),
        'phone' => $this->request->getPost('adminPhone'),
        'password' => password_hash($this->request->getPost('adminPassword'), PASSWORD_BCRYPT),
        'role' => 'admin',
        'school_id' => $school_id,
        'status' => 1,
        'watch_history' => '[]',
        'address' => $this->request->getPost('adminAddress'),
    );

    // Insert admin data into the database
    $user_id = $this->admin_model->insert_table('users', $admin_data);

    // Handle image upload if it exists
    if (!empty($_FILES['image_file']['name'])) {
        $image_path = 'uploads/users/' . $user_id . '.jpg';
        if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $image_path)) {
            // Image upload failed
            $this->response(array(
                'status' => false,
                'message' => 'Failed to upload user image'
            ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }
    }

    // Send success response
    $this->response(array('status' => true, 'message' => 'Admin created successfully.'), REST_Controller::HTTP_OK);
}


private function getSchoolId($school_name) {
    return get_school_id_by_name($school_name);
}

public function edit_admin_put($admin_id)
{
    if (empty($admin_id)) {
        $this->response([
            'status' => false,
            'message' => 'Missing admin ID'
        ], REST_Controller::HTTP_BAD_REQUEST);
        return;
    }
    // Check if the admin exists
    $admin_exists = $this->admin_model->get_where('users', array('id' => $admin_id, 'role' => 'admin'));
    if ($admin_exists) {
        $this->response(array(
            'status' => false,
            'message' => 'Admin not found'
        ), REST_Controller::HTTP_NOT_FOUND);
        return;
    }

    // Validate required fields

    // Handle image upload if it exists
    if (isset($_FILES['image_file']['name']) && !empty($_FILES['image_file']['name'])) {
        $config['upload_path'] = './uploads/users/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['file_name'] = $admin_id . '.jpg';  // Ensure extension matches
        $config['overwrite'] = TRUE;  // Overwrite the file if it already exists
    
        // TODO: Replace with Config\Services::upload(null, config);
    
        if (!$this->upload->do_upload('image_file')) {
            $error = $this->upload->display_errors();
            $this->response([
                'status' => false,
                'message' => 'Failed to upload user image: ' . $error
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
            return;
        } else {
            // Optionally, you might want to add the path to the uploaded file in the database
            $upload_data = $this->upload->data();
            $admin_data['image_path'] = 'uploads/users/' . $upload_data['file_name'];
        }
    }
    
    // Retrieve the school ID based on the school name
    $school_name = $this->put('school_name');
    $school_id = $this->getSchoolId($school_name);
    if (!$school_id) {
        $this->response(array(
            'status' => false,
            'message' => "Invalid school name provided: $school_name"
        ), REST_Controller::HTTP_BAD_REQUEST);
        return;
    }

    // Extract admin data from PUT request
    $admin_data = array(
        'name' => $this->put('adminName'),
        'email' => $this->put('adminEmail'),
        'gender' => $this->put('adminGender'),
        'phone' => $this->put('adminPhone'),
        'password' => password_hash($this->put('adminPassword'), PASSWORD_BCRYPT), // Consider only updating if password is actually changed
        'role' => 'admin',
        'school_id' => $school_id,
        'status' => 1,
        'watch_history' => '[]',
        'address' => $this->put('adminAddress'),
    );

    // Update the admin data in the database
    $this->admin_model->update_table('users', $admin_data, ['id' => $admin_id]);

    // Check if the update was successful
    if (db()->affectedRows() > 0) {
        $this->response(array(
            'status' => true,
            'message' => 'Admin updated successfully'
        ), REST_Controller::HTTP_OK);
    } else {
        $this->response(array(
            'status' => false,
            'message' => 'No changes made or update failed'
        ), REST_Controller::HTTP_BAD_REQUEST);
    }
}


public function all_school_names_get()
{
    $schools = $this->admin_model->get('schools', 'name');

    if (!empty($schools)) {
        $school_names = $schools;
        $response = array(
            'status' => true,
            'data' => $school_names
        );
        $this->response($response, REST_Controller::HTTP_OK);
    } else {
        // No schools found
        $response = array(
            'status' => false,
            'message' => 'No schools found'
        );
        $this->response($response, REST_Controller::HTTP_NOT_FOUND);
    }
}

public function Deladmin_delete($admin_id)
{
    // Validate that admin ID is provided and is numeric
    if (empty($admin_id) || !is_numeric($admin_id)) {
        $this->response(array(
            'status' => false,
            'message' => 'Invalid or missing admin ID'
        ), REST_Controller::HTTP_BAD_REQUEST);
        return;
    }

    // Start transaction
    db()->transBegin();

    // Check if the admin exists
    $admin = $this->admin_model->get_row('users', ['id' => $admin_id]);
    if (!$admin) {
        db()->transRollback();
        $this->response(array(
            'status' => false,
            'message' => 'Admin not found'
        ), REST_Controller::HTTP_NOT_FOUND);
        return;
    }

    // Delete the admin from the database
    $this->admin_model->delete_table('users', ['id' => $admin_id]);

    // Check if there's an associated image and delete it
    $image_path = 'uploads/users/' . $admin_id . '.jpg';
    if (file_exists($image_path) && !unlink($image_path)) {
        db()->transRollback();
        $this->response(array(
            'status' => false,
            'message' => 'Failed to delete admin image'
        ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        return;
    }

    // Complete the transaction
    db()->transCommit();
    if (db()->transStatus() === FALSE) {
        $this->response(array(
            'status' => false,
            'message' => 'Failed to delete admin'
        ), REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    } else {
        $this->response(array(
            'status' => true,
            'message' => 'Admin deleted successfully'
        ), REST_Controller::HTTP_OK);
    }
}





public function all_admins_get()
{
    // Retrieve all admins including their hashed passwords (Not recommended)
    $admins = $this->admin_model->get_by_query([
        'select' => 'users.id, users.name, users.email, users.phone, users.address, users.gender, schools.name as school_name',
        'from' => 'users',
        'join' => [['schools', 'users.school_id = schools.id', 'left']],
        'where' => ['users.role' => 'admin'],
        'order_by' => 'users.name',
        'order_dir' => 'ASC'
    ]);

    // Check if any admins were found
    if (empty($admins)) {
        $response = array(
            'status' => false,
            'notification' => 'No admins found'
        );
    } else {
        // Iterate through each admin and check if image exists
        foreach ($admins as &$admin) {
            $image_path = 'uploads/users/' . $admin['id'] . '.jpg'; // Assuming 'id' is the unique identifier for each admin
            if (file_exists($image_path)) {
                $admin['image_url'] = base_url($image_path); // Assuming you're using CodeIgniter and 'base_url' is configured
            }
        
        }

        $response = array(
            'status' => true,
            'admins' => $admins
        );
    }

    // Return the JSON response
    return $this->response->setJSON($response);
}



public function fetch_admins_by_name_get()
{
    // Retrieve the name, alphabet, and school_name query parameters
    $name = $this->request->getGet('name');
    $alphabet = $this->request->getGet('alphabet');
    $school_name = $this->request->getGet('school_name');

    // Log or echo the name, alphabet, and school_name for debugging
    log_message('debug', 'Provided name: ' . $name);
    log_message('debug', 'Provided alphabet: ' . $alphabet);
    log_message('debug', 'Provided school name: ' . $school_name);

    // Check if name, alphabet, or school_name is provided
    if (!$name && !$alphabet && !$school_name) {
        $response = array(
            'status' => false,
            'notification' => 'No name, alphabet, or school name provided'
        );
        return $this->response->setJSON($response);
        return;
    }

    // Retrieve admins from the 'users' table where role is 'admin' and apply filters
    $like = [];
    $where = ['role' => 'admin'];
    
    if ($name) {
        $like['users.name'] = $name;
    }
    if ($alphabet) {
        $like['users.name'] = $alphabet;
    }
    if ($school_name) {
        $where['schools.name'] = $school_name;
    }

    // Execute the query to retrieve admins
    $admins = $this->admin_model->get_by_query([
        'select' => 'users.*, schools.name as school_name',
        'from' => 'users',
        'join' => [['schools', 'users.school_id = schools.id', 'left']],
        'where' => $where,
        'like' => $like
    ]);

    // Log or echo the generated SQL query for debugging
    log_message('debug', 'Generated SQL query: ' . db()->getLastQuery());

    // Check if any admins were found
    if (empty($admins)) {
        $response = array(
            'status' => false,
            'notification' => 'No admins found with the provided criteria'
        );
    } else {
        // Iterate through each admin and check if image exists
        foreach ($admins as &$admin) {
            $image_path = 'uploads/users/' . $admin['id'] . '.jpg'; // Assuming 'id' is the unique identifier for each admin
            if (file_exists($image_path)) {
                $admin['image_url'] = base_url($image_path); // Assuming you're using CodeIgniter and 'base_url' is configured
            }
        }

        $response = array(
            'status' => true,
            'admins' => $admins
        );
    }

    // Return the JSON response
    return $this->response->setJSON($response);
}

//END ADMIN PART////////
 

//Teacher Part

public function create_teacher_post() {
    // Validate the input data
    if (!$this->request->getPost('name') || !$this->request->getPost('email') || !$this->request->getPost('password') || !$this->request->getPost('address') || !$this->request->getPost('phone') || !$this->request->getPost('gender') || !$this->request->getPost('department_id')) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid input data']));
        return;
    }

    $data = $this->request->getPost();

    // Log the received department ID and the data array
    log_message('debug', 'Department ID received: ' . $data['department_id']);
    log_message('debug', 'Full data received: ' . json_encode($data));

    // Fetch department details based on department ID, including school_id
    $department = $this->admin_model->get_row('departments', ['id' => $data['department_id']]);
    
    if (!$department) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid department ID']));
        return;
    }

    $department_id = $department['id'];
    $school_id = $department['school_id'];

    // Prepare social links as JSON
    $social_links = json_encode([
        'facebook' => $data['facebook'],
        'linkedin' => $data['linkedin'],
        'twitter' => $data['twitter'],
    ]);

    // Insert the user data into the 'users' table
    $user_data = [
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => password_hash($data['password'], PASSWORD_BCRYPT), // Hash the password
        'address' => $data['address'],
        'phone' => $data['phone'],
        'gender' => $data['gender'],
        'school_id' => $school_id // Include school_id
    ];
    $user_id = $this->admin_model->insert_table('users', $user_data);

    if (!$user_id) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['error' => 'Failed to create user']));
        return;
    }

    // Insert the teacher data into the 'teachers' table
    $teacher_data = [
        'user_id' => $user_id,
        'department_id' => $department_id,
        'school_id' => $school_id, // Use school_id from department
        'designation' => $data['designation'],
        'about' => $data['about'],
        'social_links' => $social_links,
    ];
    $teacher_id = $this->admin_model->insert_table('teachers', $teacher_data);

    if (!$teacher_id) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['error' => 'Failed to create teacher']));
        return;
    }

    // Handle image upload

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['success' => 'Teacher created successfully', 'teacher_id' => $teacher_id]));
}

public function edit_teacher_put($teacher_id) {
    // Read the raw input
    $raw_input = file_get_contents('php://input');
    log_message('debug', 'Raw input data: ' . $raw_input);

    // Decode the JSON input
    $data = json_decode($raw_input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid JSON input']));
        return;
    }

    // Validate the input data
    $required_fields = ['name', 'email', 'address', 'phone', 'gender', 'department_id', 'designation', 'about'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field])) {
            $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'Invalid input data: Missing ' . $field]));
            return;
        }
    }

    // Log the received data
    log_message('debug', 'Received data: ' . json_encode($data));

    // Fetch department details based on department ID, including school_id
    $department = $this->admin_model->get_row('departments', ['id' => $data['department_id']]);

    if (!$department) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid department ID']));
        return;
    }

    $department_id = $department['id'];
    $school_id = $department['school_id'];

    // Prepare social links as JSON
    $social_links = json_encode([
        'facebook' => $data['facebook'] ?? '',
        'linkedin' => $data['linkedin'] ?? '',
        'twitter' => $data['twitter'] ?? '',
    ]);

    // Fetch the existing teacher data
    $teacher = $this->admin_model->get_row('teachers', ['id' => $teacher_id]);

    if (!$teacher) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid teacher ID']));
        return;
    }

    $user_id = $teacher['user_id'];

    // Update the user data in the 'users' table
    $user_data = [
        'name' => $data['name'],
        'email' => $data['email'],
        'address' => $data['address'],
        'phone' => $data['phone'],
        'gender' => $data['gender'],
        'school_id' => $school_id // Include school_id
    ];

    $this->admin_model->update_table('users', $user_data, ['id' => $user_id]);

    // Update the teacher data in the 'teachers' table
    $teacher_data = [
        'department_id' => $department_id,
        'school_id' => $school_id, // Use school_id from department
        'designation' => $data['designation'],
        'about' => $data['about'],
        'social_links' => $social_links,
    ];

    $this->admin_model->update_table('teachers', $teacher_data, ['id' => $teacher_id]);

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $upload_path = './uploads/users/';
        $image_path = $upload_path . $user_id . '.jpg'; // Save the image as 'user_id.jpg'

        // Load the upload library
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['file_name'] = $user_id . '.jpg';
        $config['overwrite'] = true;
        // TODO: Replace with Config\Services::upload(null, config);

        if (!$this->upload->do_upload('image')) {
            $this->output
                ->set_status_header(500)
                ->set_output(json_encode(['error' => $this->upload->display_errors()]));
            return;
        }
    }

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['success' => 'Teacher updated successfully']));
}


public function all_teacher_get() {
    // Fetch all teacher data along with user info and department name from the database
    $school_id = $this->request->getGet('school_id');
    if (!$school_id) {
        $school_id = school_id(); // Assuming you have a function to get current school ID
    }

    // Join teachers table with users and departments table
    $teachers = $this->admin_model->get_by_query([
        'select' => 'teachers.*, users.name, users.address, users.email, users.phone, users.gender, departments.name as department_name',
        'from' => 'teachers',
        'join' => [
            ['users', 'teachers.user_id = users.id', 'inner'],
            ['departments', 'teachers.department_id = departments.id', 'inner']
        ],
        'where' => ['teachers.school_id' => $school_id]
    ]);

    // Add image URL to each teacher
    foreach ($teachers as &$teacher) {
        $image_path = 'uploads/users/' . $teacher['user_id'] . '.jpg'; // Assuming 'user_id' is the unique identifier for each user
        if (file_exists($image_path)) {
            $teacher['image_url'] = base_url($image_path); // Assuming you're using CodeIgniter and 'base_url' is configured
        } else {
            $teacher['image_url'] = base_url('uploads/users/default.jpg'); // Default image if user image doesn't exist
        }
    }

    // Send the data as JSON
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($teachers));
}

public function teacher_by_id_get($teacher_id) {
    // Validate the input data
    if (!$teacher_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid teacher ID']));
        return;
    }

    // Join teachers table with users and departments table
    $teacher = $this->admin_model->get_by_query([
        'select' => 'teachers.*, users.name, users.address, users.email, users.phone, users.gender, departments.name as department_name',
        'from' => 'teachers',
        'join' => [
            ['users', 'teachers.user_id = users.id', 'inner'],
            ['departments', 'teachers.department_id = departments.id', 'inner']
        ],
        'where' => ['teachers.id' => $teacher_id],
        'return' => 'row_array'
    ]);

    if (!$teacher) {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['error' => 'Teacher not found']));
        return;
    }

    // Add image URL to the teacher
    $image_path = 'uploads/users/' . $teacher['user_id'] . '.jpg'; // Assuming 'user_id' is the unique identifier for each user
    if (file_exists(FCPATH . $image_path)) { // Ensure the path check is accurate
        $teacher['image_url'] = base_url($image_path); // Assuming you're using CodeIgniter and 'base_url' is configured
    } else {
        $teacher['image_url'] = base_url('uploads/users/default.jpg'); // Default image if user image doesn't exist
    }

    // Decode social links
    $social_links = json_decode($teacher['social_links'], true);
    if ($social_links) {
        $teacher['facebook'] = $social_links['facebook'];
        $teacher['linkedin'] = $social_links['linkedin'];
        $teacher['twitter'] = $social_links['twitter'];
    } else {
        $teacher['facebook'] = null;
        $teacher['linkedin'] = null;
        $teacher['twitter'] = null;
    }

    // Remove the social_links field as it is now split into individual fields
    unset($teacher['social_links']);

    // Send the data as JSON
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($teacher));
}


public function department_get() {
    // Fetch all department data from the database
    $school_id = $this->request->getGet('school_id');
    $where = [];
    if ($school_id) {
        $where['school_id'] = $school_id;
    }

    $departments = $this->admin_model->get('departments', '*', $where);

    // Send the data as JSON
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($departments));
}

public function search_get() {
    $school_id = $this->request->getGet('school_id');
    $name = $this->request->getGet('name');

    if (!$school_id) {
        $school_id = school_id(); // Assuming you have a function to get current school ID
    }

    if (!$name) {
        // Return an error response if the name parameter is missing
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['error' => 'Name parameter is required']));
        return;
    }

    // Join teachers table with users and departments table
    $teachers = $this->admin_model->get_by_query([
        'select' => 'teachers.*, users.name, users.address, users.email, users.phone, users.gender, departments.name as department_name',
        'from' => 'teachers',
        'join' => [
            ['users', 'teachers.user_id = users.id', 'inner'],
            ['departments', 'teachers.department_id = departments.id', 'inner']
        ],
        'where' => ['teachers.school_id' => $school_id],
        'like' => ['users.name' => $name]
    ]);

    // Add image URL to each teacher
    foreach ($teachers as &$teacher) {
        $image_path = 'uploads/users/' . $teacher['user_id'] . '.jpg'; // Assuming 'user_id' is the unique identifier for each user
        if (file_exists($image_path)) {
            $teacher['image_url'] = base_url($image_path); // Assuming you're using CodeIgniter and 'base_url' is configured
        } else {
            $teacher['image_url'] = base_url('uploads/users/default.jpg'); // Default image if user image doesn't exist
        }
    }

    // Send the data as JSON
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($teachers));
}

public function delete_teacher_delete($teacher_id) {
    // Validate the input data
    if (!$teacher_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid teacher ID']));
        return;
    }

    // Fetch the existing teacher data
    $teacher = $this->admin_model->get_row('teachers', ['id' => $teacher_id]);

    if (!$teacher) {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['error' => 'Teacher not found']));
        return;
    }

    $user_id = $teacher['user_id'];

    // Delete the teacher record
    $this->admin_model->delete_table('teachers', ['id' => $teacher_id]);

    // Delete the user record
    $this->admin_model->delete_table('users', ['id' => $user_id]);

    // Handle image deletionC:\xampp\htdocs\SchoolManagementWeb\application\config\hooks.php
    $image_path = './uploads/users/' . $user_id . '.jpg'; // Save the image as 'user_id.jpg'
    if (file_exists($image_path)) {
        unlink($image_path); // Remove the image file
    }

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['success' => 'Teacher deleted successfully']));
}



//End Part Teacher//
//Online Courses ///

public function change_course_status_post($course_id) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->output
             ->set_status_header(405)
             ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    // Fetch the current status of the course
    $course = $this->admin_model->get_row('course', ['id' => $course_id]);

    if ($course) {
        $current_status = $course['status'];

        // Determine the new status
        $new_status = ($current_status == 'active') ? 'inactive' : 'active';

        // Update the status in the database
        $update = $this->admin_model->update_table('course', ['status' => $new_status], ['id' => $course_id]);

        if ($update) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['message' => 'Course status updated successfully', 'new_status' => $new_status]));
        } else {
            $this->output
                 ->set_status_header(500)
                 ->set_output(json_encode(['message' => 'Failed to update course status']));
        }
    } else {
        $this->output
             ->set_status_header(404)
             ->set_output(json_encode(['message' => 'Course not found']));
    }
}


public function courses_by_class_get($class_name) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $this->output
             ->set_status_header(405)
             ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    if ($class_name === 'All') {
        // Call a separate function to fetch all courses
        $this->get_all_courses();
    } else {
        // Fetch class ID by class name
        $class = $this->admin_model->get_row('classes', ['name' => $class_name]);

        if ($class) {
            $class_id = $class['id'];

            // Fetch courses by class ID from the database
            $courses = $this->admin_model->get_by_query([
                'select' => 'course.*, classes.name as class_name, users.name as instructor_name, CONCAT("http://10.0.2.2/SchoolManagementWeb/uploads/course_thumbnail/", course.thumbnail) as thumbnail',
                'from' => 'course',
                'join' => [
                    ['classes', 'classes.id = course.class_id', 'inner'],
                    ['users', 'users.id = course.user_id', 'inner']
                ],
                'where' => ['course.class_id' => $class_id]
            ]);

            if (!empty($courses)) {
                $this->output
                     ->set_content_type('application/json')
                     ->set_output(json_encode($courses));
            } else {
                $this->output
                     ->set_status_header(404)
                     ->set_output(json_encode(['message' => 'No courses found for this class']));
            }
        } else {
            $this->output
                 ->set_status_header(404)
                 ->set_output(json_encode(['message' => 'Class not found']));
        }
    }
}


public function courses_by_user_get($username) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $this->output
             ->set_status_header(405)
             ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    if ($username === 'All') {
        // Call a separate function to fetch all courses
        $this->get_all_courses();
    } else {
        // Fetch user ID by username
        $user = $this->admin_model->get_row('users', ['name' => $username]);

        if ($user) {
            $user_id = $user['id'];

            // Fetch courses by user ID from the database
            $courses = $this->admin_model->get_by_query([
                'select' => 'course.*, classes.name as class_name, users.name as instructor_name, CONCAT("http://10.0.2.2/SchoolManagementWeb/uploads/course_thumbnail/", course.thumbnail) as thumbnail',
                'from' => 'course',
                'join' => [
                    ['classes', 'classes.id = course.class_id', 'inner'],
                    ['users', 'users.id = course.user_id', 'inner']
                ],
                'where' => ['course.user_id' => $user_id]
            ]);

            if (!empty($courses)) {
                $this->output
                     ->set_content_type('application/json')
                     ->set_output(json_encode($courses));
            } else {
                $this->output
                     ->set_status_header(404)
                     ->set_output(json_encode(['message' => 'No courses found for this user']));
            }
        } else {
            $this->output
                 ->set_status_header(404)
                 ->set_output(json_encode(['message' => 'User not found']));
        }
    }
}



public function courses_by_status_get($status) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $this->output
             ->set_status_header(405)
             ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    if ($status === 'All') {
        // Call a separate function to fetch all courses
        $this->get_all_courses();
    } else {
        // Fetch courses by status from the database
        $courses = $this->admin_model->get_by_query([
            'select' => 'course.*, classes.name as class_name, users.name as instructor_name, CONCAT("http://10.0.2.2/SchoolManagementWeb/uploads/course_thumbnail/", course.thumbnail) as thumbnail',
            'from' => 'course',
            'join' => [
                ['classes', 'classes.id = course.class_id', 'inner'],
                ['users', 'users.id = course.user_id', 'inner']
            ],
            'where' => ['course.status' => $status]
        ]);

        if (!empty($courses)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode($courses));
        } else {
            $this->output
                 ->set_status_header(404)
                 ->set_output(json_encode(['message' => 'No courses found with this status']));
        }
    }
}



public function all_courses_get() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $this->output
             ->set_status_header(405)
             ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    $courses = $this->admin_model->get_by_query([
        'select' => 'course.*, classes.name as class_name, users.name as instructor_name, CONCAT("http://10.0.2.2/SchoolManagementWeb/uploads/course_thumbnail/", course.thumbnail) as thumbnail',
        'from' => 'course',
        'join' => [
            ['classes', 'classes.id = course.class_id', 'inner'],
            ['users', 'users.id = course.user_id', 'inner']
        ]
    ]);

    if (!empty($courses)) {
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($courses));
    } else {
        $this->output
             ->set_status_header(404)
             ->set_output(json_encode(['message' => 'No courses found']));
    }
}


public function get_all_courses() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $this->output
             ->set_status_header(405)
             ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    $courses = $this->admin_model->get_by_query([
        'select' => 'course.*, classes.name as class_name, users.name as instructor_name',
        'from' => 'course',
        'join' => [
            ['classes', 'classes.id = course.class_id', 'inner'],
            ['users', 'users.id = course.user_id', 'inner']
        ]
    ]);

    if (!empty($courses)) {
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($courses));
    } else {
        $this->output
             ->set_status_header(404)
             ->set_output(json_encode(['message' => 'No courses found']));
    }
}

    
public function lessons_and_sections_get($course_id)
{
    // Get total lessons
    $total_lessons = $this->admin_model->count_all('lesson', ['course_id' => $course_id]);

    // Get total sections
    $total_sections = $this->admin_model->count_all('course_section', ['course_id' => $course_id]);

    // Prepare the result array
    $result = array(
        'total_lessons' => $total_lessons,
        'total_sections' => $total_sections
    );

    // Output the result as JSON
    $this->output
         ->set_content_type('application/json')
         ->set_output(json_encode($result));
}



public function all_teacher_names_get() {
    // Ensure the request method is GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $this->output
             ->set_status_header(405)
             ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    // Fetch all teacher names from the 'users' table where the role is 'teacher'
    $teachers = $this->admin_model->get_where_result('users', ['role' => 'teacher'], 'name');

    if (!empty($teachers)) {
        // Return the result as a JSON response
        $this->output
             ->set_status_header(200)
             ->set_content_type('application/json')
             ->set_output(json_encode($teachers));
    } else {
        // Return a 404 status if no teachers are found
        $this->output
             ->set_status_header(404)
             ->set_output(json_encode(['message' => 'No teachers found']));
    }
}



 public function course_details_get($course_id) {
    // Ensure the request method is GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $this->output
            ->set_status_header(405)
            ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    // Fetch the course details from the database
    $course = $this->admin_model->get_row('course', ['id' => $course_id]);

    if ($course) {
        // Get class name
        $class_id = $course['class_id'];
        $class = $this->admin_model->get_row('classes', ['id' => $class_id]);
        $course['class_id'] = $class ? $class['name'] : 'Unknown Class';

        // Get user name
        $user_id = $course['user_id'];
        $user = $this->admin_model->get_row('users', ['id' => $user_id]);
        $course['user_id'] = $user ? $user['name'] : 'Unknown User';

        // Get subject name
        $subject_id = $course['subject_id'];
        $subject = $this->admin_model->get_row('subjects', ['id' => $subject_id]);
        $course['subject_id'] = $subject ? $subject['name'] : 'Unknown Subject';

        $this->output
            ->set_status_header(200)
            ->set_output(json_encode($course));
    } else {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['message' => 'Course not found']));
    }
}

public function edit_course_post($course_id) {
    // Ensure the request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->output
            ->set_status_header(405)
            ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    // Fetch input data from request
    $input_data = $this->request->getPost();
    log_message('debug', 'Input Data: ' . print_r($input_data, true));

    if (empty($input_data)) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['message' => 'Invalid input data']));
        return;
    }

    // Ensure all expected keys are present
    $expected_keys = ['title', 'class_id', 'user_id', 'subject_id', 'description', 'outcomes', 'course_overview_provider', 'course_overview_url', 'school_id'];
    foreach ($expected_keys as $key) {
        if (!array_key_exists($key, $input_data)) {
            log_message('error', "Missing field: $key");
            $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['message' => "Missing field: $key"]));
            return;
        }
    }

    // Validate input data
    $this->form_validation->set_data($input_data);
    $this->form_validation->set_rules('title', 'Title', 'required');
    $this->form_validation->set_rules('class_id', 'Class ID', 'required');
    $this->form_validation->set_rules('user_id', 'User ID', 'required');
    $this->form_validation->set_rules('subject_id', 'Subject ID', 'required');
    $this->form_validation->set_rules('description', 'Description', 'required');
    $this->form_validation->set_rules('outcomes', 'Outcomes', 'required');
    $this->form_validation->set_rules('course_overview_provider', 'Course Overview Provider', 'required');
    $this->form_validation->set_rules('course_overview_url', 'Course Overview URL', 'required|valid_url');
    $this->form_validation->set_rules('school_id', 'School ID', 'required');

    if ($this->form_validation->run() == FALSE) {
        log_message('error', 'Validation Errors: ' . validation_errors());
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['message' => validation_errors()]));
        return;
    }

    // Fetch the class_id, user_id, and subject_id from the database based on the names
    $class_id = $this->get_class_id_by_name($input_data['class_id']);
    $user_id = $this->get_user_id_by_name($input_data['user_id']);
    $subject_id = $this->get_subject_id_by_name($input_data['subject_id']);
    $school_id = $input_data['school_id'];

    log_message('debug', 'Fetched class_id: ' . $class_id);
    log_message('debug', 'Fetched user_id: ' . $user_id);
    log_message('debug', 'Fetched subject_id: ' . $subject_id);
    log_message('debug', 'School ID: ' . $school_id);

    if (!$class_id || !$user_id || !$school_id || !$subject_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['message' => 'Invalid class, user, school, or subject name']));
        return;
    }

    // Prepare data for updating
    $data = [
        'title' => $input_data['title'],
        'class_id' => $class_id,
        'user_id' => $user_id,
        'subject_id' => $subject_id,
        'description' => $input_data['description'],
        'outcomes' => $input_data['outcomes'],
        'course_overview_provider' => $input_data['course_overview_provider'],
        'course_overview_url' => $input_data['course_overview_url'],
        'school_id' => $school_id
    ];

    log_message('debug', 'Data to be updated: ' . print_r($data, true));

    try {
        // Start transaction
        db()->transBegin();

        // Update data in the database
        $this->admin_model->update_table('course', $data, ['id' => $course_id]);

        // Check if the course was updated successfully
        if (db()->affectedRows() > 0) {
            log_message('debug', 'Course updated in database');

            // Handle file upload for thumbnail if provided
            if (isset($_FILES['course_thumbnail']) && $_FILES['course_thumbnail']['error'] == UPLOAD_ERR_OK) {
                $upload_path = 'uploads/course_thumbnail/' . $course_id . '.jpg';
                if (!move_uploaded_file($_FILES['course_thumbnail']['tmp_name'], $upload_path)) {
                    throw new Exception('File upload failed');
                }
                log_message('debug', 'File uploaded to: ' . $upload_path);
            } else {
                log_message('debug', 'No file uploaded or file upload error');
            }

            // Commit transaction
            if (db()->transStatus() === FALSE) {
                db()->transRollback();
                log_message('error', 'Transaction failed');
                throw new Exception('Transaction failed');
            } else {
                db()->transCommit();
                log_message('debug', 'Transaction committed successfully');
                $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode(['message' => 'Course updated successfully', 'course_id' => $course_id]));
            }
        } else {
            db()->transRollback();
            log_message('error', 'Failed to update course');
            throw new Exception('Failed to update course');
        }
    } catch (Exception $e) {
        // Log the exception
        log_message('error', 'Exception: ' . $e->getMessage());
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['message' => $e->getMessage()]));
    }
}




 public function create_course_post() {
    // Ensure the request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->output
            ->set_status_header(405)
            ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    // Fetch input data from request
    $input_data = $this->request->getPost();

    if (empty($input_data)) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['message' => 'Invalid input data']));
        return;
    }

    // Log the input data for debugging
    log_message('debug', 'Input Data: ' . print_r($input_data, true));

    // Validate input data
    $this->form_validation->set_data($input_data);
    $this->form_validation->set_rules('title', 'Title', 'required');
    $this->form_validation->set_rules('class_id', 'Class ID', 'required');
    $this->form_validation->set_rules('user_id', 'User ID', 'required');
    $this->form_validation->set_rules('subject_id', 'Subject ID', 'required');
    $this->form_validation->set_rules('description', 'Description', 'required');
    $this->form_validation->set_rules('outcomes', 'Outcomes', 'required');
    $this->form_validation->set_rules('course_overview_provider', 'Course Overview Provider', 'required');
    $this->form_validation->set_rules('course_overview_url', 'Course Overview URL', 'required|valid_url');
    $this->form_validation->set_rules('school_id', 'School ID', 'required');

    if ($this->form_validation->run() == FALSE) {
        // Log validation errors
        log_message('error', 'Validation Errors: ' . validation_errors());

        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['message' => validation_errors()]));
        return;
    }

    // Fetch the class_id, user_id, and subject_id from the database based on the names
    $class_id = $this->get_class_id_by_name($input_data['class_id']);
    $user_id = $this->get_user_id_by_name($input_data['user_id']);
    $subject_id = $this->get_subject_id_by_name($input_data['subject_id']);
    $school_id = $input_data['school_id']; // Directly use school_id from input data

    if (!$class_id || !$user_id || !$school_id || !$subject_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['message' => 'Invalid class, user, school, or subject name']));
        return;
    }

    // Prepare data for insertion
    $data = [
        'title' => $input_data['title'],
        'class_id' => $class_id,
        'user_id' => $user_id,
        'subject_id' => $subject_id,
        'description' => $input_data['description'],
        'outcomes' => $input_data['outcomes'],
        'course_overview_provider' => $input_data['course_overview_provider'],
        'course_overview_url' => $input_data['course_overview_url'],
        'thumbnail' => rand() . '.jpg',
        'status' => 'active',
        'date_added' => strtotime(date('d M Y')),
        'school_id' => $school_id
    ];

    try {
        // Log the data to be inserted
        log_message('debug', 'Data to be inserted: ' . print_r($data, true));

        // Start transaction
        db()->transBegin();

        // Insert data into the database
        $insert_id = $this->admin_model->insert_table('course', $data);

        // Check if the course was created successfully
        if ($insert_id) {
            // Handle file upload for thumbnail
            if (isset($_FILES['course_thumbnail']['tmp_name'])) {
                $upload_path = 'uploads/course_thumbnail/' . $data['thumbnail'];
                if (!move_uploaded_file($_FILES['course_thumbnail']['tmp_name'], $upload_path)) {
                    throw new Exception('File upload failed');
                }
            }

            // Commit transaction
            if (db()->transStatus() === FALSE) {
                db()->transRollback();
                throw new Exception('Transaction failed');
            } else {
                db()->transCommit();
                $this->output
                    ->set_status_header(201)
                    ->set_output(json_encode(['message' => 'Course created successfully', 'course_id' => $insert_id]));
            }
        } else {
            db()->transRollback();
            throw new Exception('Failed to create course');
        }
    } catch (Exception $e) {
        // Log the exception
        log_message('error', 'Exception: ' . $e->getMessage());

        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['message' => $e->getMessage()]));
    }
}

private function get_class_id_by_name($name) {
    return get_class_id_by_name($name);
}

private function get_user_id_by_name($name) {
    return get_user_id_by_name($name);
}

private function get_subject_id_by_name($name) {
    return get_subject_id_by_name($name);
}


public function subjects_names_get() {
    // Ensure the request method is GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
      $this->output
           ->set_status_header(405)
           ->set_output(json_encode(['message' => 'Method not allowed']));
      return;
    }

    // Fetch all subject names from the 'subjects' table
    $subjects = $this->admin_model->get('subjects', 'name');

    if (!empty($subjects)) {
      $this->output
           ->set_status_header(200)
           ->set_content_type('application/json')
           ->set_output(json_encode($subjects));
    } else {
      $this->output
           ->set_status_header(404)
           ->set_output(json_encode(['message' => 'No subjects found']));
    }
  }

  public function course_status_counts_get() {
    // Vérifie que la méthode de requête est GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
      $this->output
           ->set_status_header(405)
           ->set_output(json_encode(['message' => 'Method not allowed']));
      return;
    }

    // Compter les cours avec le statut "active"
    $active_count = $this->admin_model->count_all('course', ['status' => 'active']);

    // Compter les cours avec le statut "inactive"
    $inactive_count = $this->admin_model->count_all('course', ['status' => 'inactive']);

    // Retourner les résultats en JSON
    $status_counts = [
      'active' => $active_count,
      'inactive' => $inactive_count
    ];

    $this->output
         ->set_content_type('application/json')
         ->set_output(json_encode($status_counts));
  }




  ////////
















  //Teacher Permission//


  public function classes_get() {
    // Fetch all class names from the 'classes' table
    $classes = $this->admin_model->get('classes', 'name');

    // Return the result as a JSON response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($classes));
}

public function delete_course_delete($course_id) {
    // Ensure the request method is DELETE
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        $this->output
            ->set_status_header(405)
            ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    // Fetch the course from the database to check if it exists
    $course = $this->admin_model->get_row('course', ['id' => $course_id]);

    if ($course) {
        // Begin transaction
        db()->transBegin();

        // Delete the course
        $this->admin_model->delete_table('course', ['id' => $course_id]);

        // Check if the course was deleted successfully
        if (db()->affectedRows() > 0) {
            // Commit transaction
            if (db()->transStatus() === FALSE) {
                db()->transRollback();
                $this->output
                    ->set_status_header(500)
                    ->set_output(json_encode(['message' => 'Transaction failed']));
            } else {
                db()->transCommit();
                $this->output
                    ->set_status_header(200)
                    ->set_output(json_encode(['message' => 'Course deleted successfully']));
            }
        } else {
            db()->transRollback();
            $this->output
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'Failed to delete course']));
        }
    } else {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['message' => 'Course not found']));
    }
}


public function add_course_section_post($course_id) {
    // Ensure the request is a POST request
    if ($this->request->getServer('REQUEST_METHOD') == 'POST') {
        // Retrieve input data
        $input_data = $this->request->getPost();
        $title = isset($input_data['title']) ? $input_data['title'] : null;

        // Log received title for debugging
        log_message('debug', 'Received title: ' . $title);
        log_message('debug', 'Received course_id: ' . $course_id);

        // Validate the input
        if (empty($title)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'Title is required']));
            return;
        }

        // Prepare data for insertion
        $data['title'] = $title;
        $data['course_id'] = $course_id;

        // Get the highest order value for the course and increment it
        $existing_sections = $this->admin_model->get_where_result('course_section', ['course_id' => $course_id]);
     
        // Insert the new section
        $section_id = $this->admin_model->insert_table('course_section', $data);
        if ($section_id) {
            log_message('debug', 'Section inserted with ID: ' . $section_id);
            
            // Fetch course details
            $course_details = $this->admin_model->get_row('course', ['id' => $course_id]);
            log_message('debug', 'Course details: ' . print_r($course_details, true));

            // Decode the section array or initialize it if empty
            $previous_sections = isset($course_details['section']) ? json_decode($course_details['section'], true) : [];

            if (is_array($previous_sections)) {
                array_push($previous_sections, $section_id);
            } else {
                $previous_sections = array($section_id);
            }

            // Update the course with the new section list
            $updater['section'] = json_encode($previous_sections);
            if ($this->admin_model->update_table('course', $updater, ['id' => $course_id])) {
                log_message('debug', 'Course updated successfully with new sections');
                $this->output
                     ->set_content_type('application/json')
                     ->set_output(json_encode(['status' => 'success', 'message' => 'Section added successfully']));
            } else {
                log_message('error', 'Failed to update course sections');
                $this->output
                     ->set_content_type('application/json')
                     ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to update course sections']));
            }
        } else {
            log_message('error', 'Failed to insert new section');
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to add section']));
        }
    } else {
        // Return method not allowed error
        log_message('error', 'Invalid request method');
        $this->output
             ->set_content_type('application/json')
             ->set_status_header(405)
             ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}

public function sections_get($course_id) {
    // Ensure the request is a GET request
    if ($this->request->getServer('REQUEST_METHOD') == 'GET') {
        // Fetch sections based on the course ID
        $sections = $this->admin_model->get_where_result('course_section', ['course_id' => $course_id]);

        // Check if sections are found
        if ($sections) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'success', 'sections' => $sections]));
        } else {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'No sections found']));
        }
    } else {
        // Return method not allowed error
        $this->output
             ->set_content_type('application/json')
             ->set_status_header(405)
             ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}

public function sections_title_post($course_id, $section_id) {
    // Ensure the request is a POST request
    if ($this->request->getServer('REQUEST_METHOD') == 'POST') {
        // Get the new title from the POST data
        $new_title = $this->request->getPost('title');

        // Validate the input
        if (empty($new_title)) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'Title cannot be empty']));
            return;
        }

        // Update the section title in the database
        $this->admin_model->update_table('course_section', ['title' => $new_title], ['course_id' => $course_id, 'id' => $section_id]);

        // Check if the update was successful
        if (db()->affectedRows() > 0) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'success', 'message' => 'Section title updated successfully']));
        } else {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to update section title']));
        }
    } else {
        // Return method not allowed error
        $this->output
             ->set_content_type('application/json')
             ->set_status_header(405)
             ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}

public function sections_del_delete($course_id, $section_id) {
    // Ensure the request is a DELETE request
    if ($this->request->getServer('REQUEST_METHOD') == 'DELETE') {
        // Delete the section from the database
        $deleted = $this->admin_model->delete_table('course_section', [
            'course_id' => $course_id,
            'id' => $section_id
        ]);

        // Check if the deletion was successful
        if ($deleted) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'success', 'message' => 'Section deleted successfully']));
        } else {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to delete section']));
        }
    } else {
        // Return method not allowed error
        $this->output
             ->set_content_type('application/json')
             ->set_status_header(405)
             ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}


public function quizzes_by_section_get($section_id) {
    // Ensure the request is a GET request
    if ($this->request->getServer('REQUEST_METHOD') == 'GET') {
        // Fetch quizzes based on the section ID
        $quizzes = $this->admin_model->get_where_result('lesson', [
            'section_id' => $section_id,
            'lesson_type' => 'quiz'
        ]);

        // Check if quizzes are found
        if ($quizzes) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'success', 'quizzes' => $quizzes]));
        } else {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'No quizzes found']));
        }
    } else {
        // Return method not allowed error
        $this->output
             ->set_content_type('application/json')
             ->set_status_header(405)
             ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}

public function quizzes_by_course_get($course_id) {
    // Ensure the request is a GET request
    if ($this->request->getServer('REQUEST_METHOD') == 'GET') {
        // Fetch quizzes based on the course ID
        $quizzes = $this->admin_model->get_where_result('lesson', [
            'course_id' => $course_id,
            'lesson_type' => 'quiz'
        ]);

        // Check if quizzes are found
        if ($quizzes) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'success', 'quizzes' => $quizzes]));
        } else {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'No quizzes found']));
        }
    } else {
        // Return method not allowed error
        $this->output
             ->set_content_type('application/json')
             ->set_status_header(405)
             ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}


public function quizzes_get($course_id = null, $section_id = null) {
    // Ensure the request is a GET request
    if ($this->request->getServer('REQUEST_METHOD') == 'GET') {
        $where = ['lesson_type' => 'quiz'];
        
        if ($section_id !== null) {
            $where['section_id'] = $section_id;
        } else if ($course_id !== null) {
            $where['course_id'] = $course_id;
        } else {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'No course_id or section_id provided']));
            return;
        }

        $quizzes = $this->admin_model->get_ordered('lesson', 'order', 'ASC', $where);

        // Check if quizzes are found
        if ($quizzes) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'success', 'quizzes' => $quizzes]));
        } else {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'No quizzes found']));
        }
    } else {
        // Return method not allowed error
        $this->output
             ->set_content_type('application/json')
             ->set_status_header(405)
             ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}


public function update_quiz_order_post($section_id = null) {
    // Ensure the request is a POST request
    if ($this->request->getServer('REQUEST_METHOD') == 'POST') {
        // Get the POST data
        $input_data = $this->request->getPost();

        // Check if section_id is provided
        if ($section_id === null) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'No section_id provided']));
            return;
        }

        // Check if the input data is an array of quizzes with their order
        if (!isset($input_data['quizzes']) || !is_array($input_data['quizzes'])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid input data']));
            return;
        }

        // Start transaction
        db()->transBegin();

        // Update the order of each quiz
        foreach ($input_data['quizzes'] as $quiz) {
            if (isset($quiz['id']) && isset($quiz['order'])) {
                $this->admin_model->update_table('lesson', ['order' => $quiz['order']], ['id' => $quiz['id'], 'lesson_type' => 'quiz', 'section_id' => $section_id]);
            }
        }

        // Complete the transaction
        db()->transCommit();

        if (db()->transStatus() === FALSE) {
            // Transaction failed, return error response
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to update quiz order']));
        } else {
            // Transaction succeeded, return success response
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Quiz order updated successfully']));
        }
    } else {
        // Return method not allowed error
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(405)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}



public function quiz_questions_get($quiz_id) {
    // Ensure the request is a GET request
    if ($this->request->getServer('REQUEST_METHOD') == 'GET') {
        // Fetch quiz questions based on the quiz ID
        $questions = $this->admin_model->get_ordered('question', 'order', 'ASC', ['quiz_id' => $quiz_id]);

        // Check if questions are found
        if ($questions) {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'success', 'questions' => $questions]));
        } else {
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'No questions found']));
        }
    } else {
        // Return method not allowed error
        $this->output
             ->set_content_type('application/json')
             ->set_status_header(405)
             ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}

/* public function add_quiz_post() {
    // Ensure the request is a POST request
    if ($this->request->getServer('REQUEST_METHOD') == 'POST') {
        // Retrieve input data using $this->input->post
        $title = $this->request->getPost('title');
        $section_name = $this->request->getPost('section_name');
        $summary = $this->request->getPost('instruction');  // Changed 'instruction' to 'summary'

        // Log received data for debugging
        log_message('debug', 'Received title: ' . $title);
        log_message('debug', 'Received section_name: ' . $section_name);
        log_message('debug', 'Received summary: ' . $summary);  // Log 'summary'

        // Validate the input
        if (empty($title) || empty($section_name)) {
            log_message('error', 'Validation failed: Title and Section Name are required');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Title and Section Name are required']));
            return;
        }

        // Fetch section ID from section name
        $section = $this->admin_model->get_row('course_section', ['title' => $section_name]);

        if (!$section) {
            log_message('error', 'Invalid Section Name: ' . $section_name);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid Section Name']));
            return;
        }

        $section_id = $section['id'];

        // Prepare data for insertion
        $data = [
            'title' => $title,
            'section_id' => $section_id,
            'summary' => $summary,  // Changed 'instruction' to 'summary'
            'lesson_type' => 'quiz', // Assuming 'lesson_type' column marks a lesson as a quiz
            // Removed 'created_at'
        ];

        // Log data to be inserted
        log_message('debug', 'Data to be inserted: ' . json_encode($data));

        // Insert the new quiz
        $quiz_id = $this->admin_model->insert_table('lesson', $data);
        if ($quiz_id) {
            log_message('debug', 'Quiz inserted with ID: ' . $quiz_id);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Quiz added successfully', 'quiz_id' => $quiz_id]));
        } else {
            log_message('error', 'Failed to insert new quiz');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to add quiz']));
        }
    } else {
        // Return method not allowed error
        log_message('error', 'Invalid request method');
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(405)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
} */

public function add_quiz_post() {
    // Ensure the request is a POST request
    if ($this->request->getServer('REQUEST_METHOD') == 'POST') {
        // Retrieve input data using $this->input->post
        $title = $this->request->getPost('title');
        $section_name = $this->request->getPost('section_name');
        $summary = $this->request->getPost('instruction');  // Changed 'instruction' to 'summary'

        // Log received data for debugging
        log_message('debug', 'Received title: ' . $title);
        log_message('debug', 'Received section_name: ' . $section_name);
        log_message('debug', 'Received summary: ' . $summary);  // Log 'summary'

        // Validate the input
        if (empty($title) || empty($section_name)) {
            log_message('error', 'Validation failed: Title and Section Name are required');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Title and Section Name are required']));
            return;
        }

        // Fetch section ID from section name
        $section = $this->admin_model->get_row('course_section', ['title' => $section_name]);

        if (!$section) {
            log_message('error', 'Invalid Section Name: ' . $section_name);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid Section Name']));
            return;
        }

        $section_id = $section['id'];

        // Prepare data for insertion
        $data = [
            'title' => $title,
            'section_id' => $section_id,
            'summary' => $summary,  // Changed 'instruction' to 'summary'
            'lesson_type' => 'quiz', // Assuming 'lesson_type' column marks a lesson as a quiz
            // Removed 'created_at'
        ];

        // Log data to be inserted
        log_message('debug', 'Data to be inserted: ' . json_encode($data));

        // Insert the new quiz
        $quiz_id = $this->admin_model->insert_table('lesson', $data);
        if ($quiz_id) {
            log_message('debug', 'Quiz inserted with ID: ' . $quiz_id);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Quiz added successfully', 'quiz_id' => $quiz_id]));
        } else {
            log_message('error', 'Failed to insert new quiz');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to add quiz']));
        }
    } else {
        // Return method not allowed error
        log_message('error', 'Invalid request method');
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(405)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}


 public function update_quiz_post() {
    // Ensure the request is a POST request
    if ($this->request->getServer('REQUEST_METHOD') == 'POST') {
        // Retrieve input data using $this->input->post
        $quiz_id = $this->request->getPost('quiz_id');
        $title = $this->request->getPost('title');
        $section_name = $this->request->getPost('section_name');
        $summary = $this->request->getPost('summary'); // Changed 'instruction' to 'summary'

        // Log received data for debugging
        log_message('debug', 'Received quiz_id: ' . $quiz_id);
        log_message('debug', 'Received title: ' . $title);
        log_message('debug', 'Received section_name: ' . $section_name);
        log_message('debug', 'Received summary: ' . $summary);  // Log 'summary'

        // Validate the input
        if (empty($quiz_id) || empty($title) || empty($section_name)) {
            log_message('error', 'Validation failed: Quiz ID, Title, and Section Name are required');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Quiz ID, Title, and Section Name are required']));
            return;
        }

        // Fetch section ID from section name
        $section = $this->admin_model->get_row('course_section', ['title' => $section_name]);

        if (!$section) {
            log_message('error', 'Invalid Section Name: ' . $section_name);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid Section Name']));
            return;
        }

        $section_id = $section['id'];

        // Prepare data for updating
        $data = [
            'title' => $title,
            'section_id' => $section_id,
            'summary' => $summary,  // Changed 'instruction' to 'summary'
            'lesson_type' => 'quiz', // Assuming 'lesson_type' column marks a lesson as a quiz
        ];

        // Log data to be updated
        log_message('debug', 'Data to be updated: ' . json_encode($data));

        // Update the quiz
        if ($this->admin_model->update_table('lesson', $data, ['id' => $quiz_id])) {
            log_message('debug', 'Quiz updated with ID: ' . $quiz_id);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Quiz updated successfully', 'quiz_id' => $quiz_id]));
        } else {
            log_message('error', 'Failed to update quiz');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to update quiz']));
        }
    } else {
        // Return method not allowed error
        log_message('error', 'Invalid request method');
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(405)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}

public function delete_quiz_post() {
    // Ensure the request is a POST request
    if ($this->request->getServer('REQUEST_METHOD') == 'POST') {
        // Retrieve input data using $this->input->post
        $quiz_id = $this->request->getPost('quiz_id');

        // Log received data for debugging
        log_message('debug', 'Received quiz_id: ' . $quiz_id);

        // Validate the input
        if (empty($quiz_id)) {
            log_message('error', 'Validation failed: Quiz ID is required');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Quiz ID is required']));
            return;
        }

        // Check if the quiz exists
        $quiz = $this->admin_model->get_row('lesson', ['id' => $quiz_id]);

        if (!$quiz) {
            log_message('error', 'Invalid Quiz ID: ' . $quiz_id);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid Quiz ID']));
            return;
        }

        // Delete the quiz
        if ($this->admin_model->delete_table('lesson', ['id' => $quiz_id])) {
            log_message('debug', 'Quiz deleted with ID: ' . $quiz_id);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Quiz deleted successfully', 'quiz_id' => $quiz_id]));
        } else {
            log_message('error', 'Failed to delete quiz');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to delete quiz']));
        }
    } else {
        // Return method not allowed error
        log_message('error', 'Invalid request method');
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(405)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}

public function add_question_post() {
    // Ensure the request is a POST request
    if ($this->request->getServer('REQUEST_METHOD') == 'POST') {
        // Retrieve input data using $this->input->post
        $quiz_id = $this->request->getPost('quiz_id');
        $title = $this->request->getPost('title');
        $type = $this->request->getPost('type');
        $number_of_options = $this->request->getPost('number_of_options');
        $options = $this->request->getPost('options'); // Expecting JSON format
        $correct_answers = $this->request->getPost('correct_answers'); // Expecting JSON format
        $order = $this->request->getPost('order');

        // Log received data for debugging
        log_message('debug', 'Received quiz_id: ' . $quiz_id);
        log_message('debug', 'Received title: ' . $title);
        log_message('debug', 'Received type: ' . $type);
        log_message('debug', 'Received number_of_options: ' . $number_of_options);
        log_message('debug', 'Received options: ' . $options);
        log_message('debug', 'Received correct_answers: ' . $correct_answers);
        log_message('debug', 'Received order: ' . $order);

        // Validate the input
        if (empty($quiz_id) || empty($title) || empty($type) || empty($number_of_options) || empty($options) || empty($correct_answers)) {
            log_message('error', 'Validation failed: Quiz ID, Title, Type, Number of Options, Options, and Correct Answers are required');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Quiz ID, Title, Type, Number of Options, Options, and Correct Answers are required']));
            return;
        }

        // Prepare data for insertion
        $data = [
            'quiz_id' => $quiz_id,
            'title' => $title,
            'type' => $type,
            'number_of_options' => $number_of_options,
            'options' => $options,
            'correct_answers' => $correct_answers,
            'order' => $order,
        ];

        // Log data to be inserted
        log_message('debug', 'Data to be inserted: ' . json_encode($data));

        // Insert the new question
        $question_id = $this->admin_model->insert_table('question', $data);
        if ($question_id) {
            log_message('debug', 'Question inserted with ID: ' . $question_id);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Question added successfully', 'question_id' => $question_id]));
        } else {
            log_message('error', 'Failed to insert new question');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to add question']));
        }
    } else {
        // Return method not allowed error
        log_message('error', 'Invalid request method');
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(405)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}

/* public function get_quiz_questions_get($quiz_id) {
    // Ensure the request is a GET request
    if ($this->request->getServer('REQUEST_METHOD') == 'GET') {
        // Fetch quiz questions based on the quiz ID
        $questions = $this->admin_model->get_ordered('question', 'order', 'ASC', ['quiz_id' => $quiz_id]);

        // Check if questions are found
        if ($questions) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'questions' => $questions]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'No questions found']));
        }
    } else {
        // Return method not allowed error
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(405)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
} */

/* public function get_quiz_questions_get($quiz_id) {
    if ($this->request->getServer('REQUEST_METHOD') == 'GET') {
        $questions = $this->admin_model->get_ordered('question', 'order', 'ASC', ['quiz_id' => $quiz_id]);

        if ($questions) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'questions' => $questions]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'No questions found']));
        }
    } else {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(405)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}
 */

 public function get_quiz_questions_get($quiz_id) {
    if ($this->request->getServer('REQUEST_METHOD') == 'GET') {
        $questions = $this->admin_model->get_ordered('question', 'order', 'ASC', ['quiz_id' => $quiz_id]);

        if ($questions) {
            // Decode options and add them to the response
            foreach ($questions as &$question) {
                $question['options'] = json_decode($question['options'], true);
            }
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'questions' => $questions]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'No questions found']));
        }
    } else {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(405)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}


public function edit_question_post() {
    if ($this->request->getServer('REQUEST_METHOD') == 'POST') {
        $question_id = $this->request->getPost('question_id');
        $quiz_id = $this->request->getPost('quiz_id');
        $title = $this->request->getPost('title');
        $type = $this->request->getPost('type');
        $number_of_options = (int)$this->request->getPost('number_of_options');
        $options = json_decode($this->request->getPost('options'), true);
        $correct_answers = json_decode($this->request->getPost('correct_answers'), true);
        $order = (int)$this->request->getPost('order');

        if (empty($question_id) || empty($quiz_id) || empty($title) || empty($type) || $number_of_options <= 0 || empty($options) || empty($correct_answers)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Validation failed: required fields are missing.']));
            return;
        }

        $data = [
            'quiz_id' => $quiz_id,
            'title' => $title,
            'type' => $type,
            'number_of_options' => $number_of_options,
            'options' => json_encode($options),
            'correct_answers' => json_encode($correct_answers),
            'order' => $order,
        ];

        if ($this->admin_model->update_table('question', $data, ['id' => $question_id])) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Question updated successfully']));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to update question']));
        }
    } else {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(405)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}


public function delete_question_post() {
    // Ensure the request is a POST request
    if ($this->request->getServer('REQUEST_METHOD') == 'POST') {
        // Retrieve input data using $this->input->post
        $question_id = $this->request->getPost('question_id');

        // Log received data for debugging
        log_message('debug', 'Received question_id: ' . $question_id);

        // Validate the input
        if (empty($question_id)) {
            log_message('error', 'Validation failed: Question ID is required');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Question ID is required']));
            return;
        }

        // Check if the question exists
        $question = $this->admin_model->get_row('question', ['id' => $question_id]);
        if (!$question) {
            log_message('error', 'Question not found with ID: ' . $question_id);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Question not found']));
            return;
        }

        // Delete the question
        if ($this->admin_model->delete_table('question', ['id' => $question_id])) {
            log_message('debug', 'Question deleted with ID: ' . $question_id);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'Question deleted successfully']));
        } else {
            log_message('error', 'Failed to delete question');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to delete question']));
        }
    } else {
        // Return method not allowed error
        log_message('error', 'Invalid request method');
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(405)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
    }
}

/* 
public function add_lesson_post() {
    // TODO: Replace with service: Config\Services::form_validation();

    // Set validation rules
    $this->form_validation->set_rules('course_id', 'Course ID', 'required');
    $this->form_validation->set_rules('title', 'Title', 'required');
    $this->form_validation->set_rules('section_id', 'Section ID', 'required');
    $this->form_validation->set_rules('lesson_type', 'Lesson Type', 'required');

    if ($this->form_validation->run() == FALSE) {
        $response = [
            'status' => 'error',
            'message' => validation_errors()
        ];
        return $this->response->setJSON($response);
        return;
    }

    $data['course_id'] = $this->request->getPost('course_id');
    $data['title'] = $this->request->getPost('title');
    $data['section_id'] = $this->request->getPost('section_id');
    $data['summary'] = $this->request->getPost('summary');
    $data['date_added'] = strtotime(date('D, d-M-Y'));

    $lesson_type_array = explode('-', $this->request->getPost('lesson_type'));
    $lesson_type = $lesson_type_array[0];
    $data['lesson_type'] = $lesson_type;
    $data['attachment_type'] = isset($lesson_type_array[1]) ? $lesson_type_array[1] : null;

    if ($lesson_type == 'video') {
        $lesson_provider = $this->request->getPost('lesson_provider');
        if ($lesson_provider == 'youtube' || $lesson_provider == 'vimeo') {
            $data['video_url'] = $this->request->getPost('video_url');
            $duration_formatter = explode(':', $this->request->getPost('duration'));
            $data['duration'] = sprintf('%02d:%02d:%02d', $duration_formatter[0], $duration_formatter[1], $duration_formatter[2]);
            $data['video_type'] = $lesson_provider;
        } elseif ($lesson_provider == 'html5') {
            $data['video_url'] = $this->request->getPost('html5_video_url');
            $duration_formatter = explode(':', $this->request->getPost('html5_duration'));
            $data['duration'] = sprintf('%02d:%02d:%02d', $duration_formatter[0], $duration_formatter[1], $duration_formatter[2]);
            $data['video_type'] = 'html5';
            $this->upload_file('thumbnail', 'uploads/thumbnails/lesson_thumbnails/', $inserted_id . '.jpg');
        } elseif ($lesson_provider == 'mydevice') {
            $data['video_type'] = 'mydevice';
            $data['video_upload'] = $this->upload_file('userfileMe', 'uploads/videos/');
        } else {
            $response = ['status' => 'error', 'message' => 'Invalid lesson provider'];
            return $this->response->setJSON($response);
            return;
        }
    } else {
        $data['duration'] = 0;
        $data['attachment'] = $this->upload_file('attachment', 'uploads/lesson_files/');
    }

    $this->admin_model->insert_table('lesson', $data);
    $response = ['status' => 'success', 'message' => 'Lesson added successfully'];
    return $this->response->setJSON($response);
}

private function upload_file($field_name, $upload_path, $file_name = null) {
    if (!file_exists($upload_path)) {
        mkdir($upload_path, 0777, true);
    }

    if (isset($_FILES[$field_name]) && $_FILES[$field_name]['error'] == 0) {
        $file_type = pathinfo($_FILES[$field_name]['name'], PATHINFO_EXTENSION);
        $file_name = $file_name ?? md5(uniqid(rand(), true)) . '.' . $file_type;
        $destination = $upload_path . $file_name;

        if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $destination)) {
            return $file_name;
        } else {
            $response = ['status' => 'error', 'message' => 'There was a problem moving the file.'];
            return $this->response->setJSON($response);
            exit;
        }
    } else {
        $response = ['status' => 'error', 'message' => 'No file uploaded or there was an upload error.'];
        return $this->response->setJSON($response);
        exit;
    }
}

 */

 public function add_lesson_post() {
    // TODO: Replace with service: Config\Services::form_validation();

    // Set validation rules
    $this->form_validation->set_rules('course_id', 'Course ID', 'required');
    $this->form_validation->set_rules('title', 'Title', 'required');
    $this->form_validation->set_rules('section_id', 'Section ID', 'required');
    $this->form_validation->set_rules('lesson_type', 'Lesson Type', 'required');

    if ($this->form_validation->run() == FALSE) {
        $response = [
            'status' => 'error',
            'message' => strip_tags(validation_errors()) // Cleaner JSON response
        ];
        return $this->response->setJSON($response);
    }

    // Prepare lesson data
    $data = [
        'course_id' => $this->request->getPost('course_id'),
        'title' => $this->request->getPost('title'),
        'section_id' => $this->request->getPost('section_id'),
        'summary' => $this->request->getPost('summary'),
        'date_added' => strtotime(date('D, d-M-Y')),
        'lesson_type' => $this->request->getPost('lesson_type'),
        'attachment_type' => null
    ];

    // Handle video types
    if ($data['lesson_type'] == 'video') {
        $lesson_provider = $this->request->getPost('lesson_provider');
        if ($lesson_provider == 'youtube' || $lesson_provider == 'vimeo') {
            $data['video_url'] = $this->request->getPost('video_url');
            $data['duration'] = $this->request->getPost('duration');
            $data['video_type'] = $lesson_provider;
        } elseif ($lesson_provider == 'mydevice') {
            $data['video_type'] = 'mydevice';
            // Attempt to upload video file and store filename
            $video_file_name = $this->upload_file('userfileMe', 'uploads/videos/');
            if ($video_file_name) {
                $data['video_upload'] = $video_file_name;
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Video upload failed']);
            }
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid lesson provider']);
        }
    } else {
        // Handle other types with an attachment
        $data['attachment'] = $this->upload_file('attachment', 'uploads/lesson_files/');
    }

    // Insert into the database
    $this->admin_model->insert_table('lesson', $data);
    $response = ['status' => 'success', 'message' => 'Lesson added successfully'];
    return $this->response->setJSON($response);
}

// Utility function to handle file uploads
private function upload_file($field_name, $upload_path) {
    if (!file_exists($upload_path)) {
        mkdir($upload_path, 0777, true);
    }

    if (isset($_FILES[$field_name]) && $_FILES[$field_name]['error'] == 0) {
        $file_type = pathinfo($_FILES[$field_name]['name'], PATHINFO_EXTENSION);
        $file_name = md5(uniqid(rand(), true)) . '.' . $file_type;
        $destination = $upload_path . $file_name;

        if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $destination)) {
            return $file_name;
        } else {
            // Return null if file move failed
            log_message('error', 'Failed to move uploaded file for ' . $field_name);
            return null;
        }
    } else {
        log_message('error', 'File upload error or no file provided for ' . $field_name);
        return null;
    }
}

/* public function all_lesson_types_get($section_id) {
    // Ensure the request method is GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $this->output
             ->set_status_header(405)
             ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    // Validate the section_id
    if (!is_numeric($section_id)) {
        $this->output
             ->set_status_header(400)
             ->set_output(json_encode(['message' => 'Invalid section ID']));
        return;
    }

    // Base URLs for attachments and video uploads
    $base_attachment_url = 'http://10.0.2.2/SchoolManagementWeb/uploads/lesson_files/';
    $base_video_url = 'http://10.0.2.2/SchoolManagementWeb/uploads/videos/';

    // Fetch distinct lesson types, lesson titles, attachments, and video uploads from the 'lesson' table for the given section_id
    $base_attachment_url = 'http://10.0.2.2/SchoolManagementWeb/uploads/lesson_files/';
    $base_video_url = 'http://10.0.2.2/SchoolManagementWeb/uploads/videos/';
    
    $lessons = $this->admin_model->get_by_query([
        'select' => "lesson.lesson_type, lesson.title as lesson_title, 
                       CONCAT('$base_attachment_url', lesson.attachment) as attachment, 
                       CONCAT('$base_video_url', lesson.video_upload) as video_upload",
        'from' => 'lesson',
        'where' => ['lesson.section_id' => $section_id]
    ]);

    // Check if lessons are found
    if (!empty($lessons)) {
        $this->output
             ->set_status_header(200)
             ->set_content_type('application/json')
             ->set_output(json_encode($lessons));
    } else {
        $this->output
             ->set_status_header(404)
             ->set_output(json_encode(['message' => 'No lessons found for the given section ID']));
    }
}
 */
public function all_lesson_types_get($section_id) {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        $this->output
             ->set_status_header(405)
             ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    if (!is_numeric($section_id)) {
        $this->output
             ->set_status_header(400)
             ->set_output(json_encode(['message' => 'Invalid section ID']));
        return;
    }

    $base_attachment_url = 'http://10.0.2.2/SchoolManagementWeb/uploads/lesson_files/';
    $base_video_url = 'http://10.0.2.2/SchoolManagementWeb/uploads/videos/';

    $lessons = $this->admin_model->get_by_query([
        'select' => "lesson.id, lesson.lesson_type, lesson.title as lesson_title, 
                       lesson.video_url,
                       CONCAT('$base_attachment_url', lesson.attachment) as attachment, 
                       CONCAT('$base_video_url', lesson.video_upload) as video_upload",
        'from' => 'lesson',
        'where' => ['lesson.section_id' => $section_id]
    ]);

    if (!empty($lessons)) {
        $this->output
             ->set_status_header(200)
             ->set_content_type('application/json')
             ->set_output(json_encode($lessons));
    } else {
        $this->output
             ->set_status_header(404)
             ->set_output(json_encode(['message' => 'No lessons found for the given section ID']));
    }
}



public function associate_user_with_school_post() {
    // Ensure the request is a POST request
    if ($this->request->getServer('REQUEST_METHOD') !== 'POST') {
        $this->output
             ->set_status_header(405)
             ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    // Log the entire $_POST array
    log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));

    // Log the raw POST data
    log_message('debug', 'Raw POST data: ' . file_get_contents('php://input'));

    // Retrieve input data
    $postData = json_decode(file_get_contents('php://input'), true);
    $user_id = isset($postData['user_id']) ? $postData['user_id'] : null;
    $school_id = isset($postData['school_id']) ? $postData['school_id'] : null;
    $session = isset($postData['session']) ? $postData['session'] : 1; // Default to 1 if not provided

    // Log received data for debugging
    log_message('debug', 'Received user_id: ' . json_encode($user_id));
    log_message('debug', 'Received school_id: ' . json_encode($school_id));
    log_message('debug', 'Received session: ' . json_encode($session));

    // Validate the input
    if (empty($user_id) || empty($school_id)) {
        log_message('error', 'Validation failed: User ID and School ID are required');
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(['message' => 'User ID and School ID are required']));
        return;
    }

    // Check if the user exists
    $user = $this->admin_model->get_row('users', ['id' => $user_id]);

    if (!$user) {
        log_message('error', 'Invalid User ID: ' . $user_id);
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(['message' => 'Invalid User ID']));
        return;
    }

    // Check if the school exists
    $school = $this->admin_model->get_row('schools', ['id' => $school_id]);

    if (!$school) {
        log_message('error', 'Invalid School ID: ' . $school_id);
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(['message' => 'Invalid School ID']));
        return;
    }

    // Prepare data for insertion
    $data = [
        'user_id' => $user_id,
        'school_id' => $school_id,
        'session' => $session, // Note the change here
        'status' => 0 // Default status to 0
    ];

    // Log data to be inserted
    log_message('debug', 'Data to be inserted: ' . json_encode($data));

    // Insert data into the students table
    $student_id = $this->admin_model->insert_table('students', $data);
    if ($student_id) {
        log_message('debug', 'Student associated successfully with ID: ' . $student_id);
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(['status' => 'success', 'message' => 'User associated with school successfully']));
    } else {
        log_message('error', 'Failed to associate user with school');
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to associate user with school']));
    }
}




public function get_students_list_get() {
    // Log the request for debugging purposes
    log_message('debug', 'Received request for students list');

    // Get school_id from the GET parameters
    $school_id = $this->request->getGet('school_id');

    // Check if school_id is provided
    if (!$school_id) {
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(['status' => 'error', 'message' => 'School ID is required']));
        return;
    }

    // Query the database for the list of students along with their user information
    $students = $this->admin_model->get_by_query([
        'select' => 'students.id, students.code, students.user_id, students.session, students.school_id, students.status, users.name, users.email, users.role, users.address, users.phone, users.birthday, users.gender',
        'from' => 'students',
        'join' => [['users', 'students.user_id = users.id', 'left']],
        'where' => ['students.school_id' => $school_id, 'students.status' => 0]
    ]);

    if (!empty($students)) {
        // Add full image URL to each student
        foreach ($students as &$student) {
            $image_path = 'uploads/users/' . $student['user_id'] . '.jpg'; // Assuming 'user_id' is the unique identifier for each user
            if (file_exists(FCPATH . $image_path)) {
                $student['image_url'] = base_url($image_path); // Assuming you're using CodeIgniter and 'base_url' is configured
            } else {
                $student['image_url'] = base_url('uploads/users/default.jpg'); // Default image if user image doesn't exist
            }
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(['status' => 'success', 'students' => $students]));
    } else {
        log_message('error', 'No students found');
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(['status' => 'error', 'message' => 'No students found']));
    }
}



public function approve_student_post() {
    // Validate the input data
    if (!$this->request->getPost('students_id')) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid input data']));
        return;
    }

    $student_id = $this->request->getPost('students_id');

    // Update the student status to 1
    $this->admin_model->update_table('students', ['status' => 1], ['id' => $student_id]);

    if (db()->affectedRows() > 0) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => 'Student approved successfully']));
    } else {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['error' => 'Failed to approve student']));
    }
}


public function section_get() {
    // Log the request for debugging purposes
    log_message('debug', 'Received request for sections list');

    // Query the database for the list of sections
    $sections = $this->admin_model->get_result('sections');

    if (!empty($sections)) {

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(['status' => 'success', 'sections' => $sections]));
    } else {
        log_message('error', 'No sections found');
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(['status' => 'error', 'message' => 'No sections found']));
    }
}



public function delete_student_post() {
    // Validate the input data
    if (!$this->request->getPost('students_id')) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid input data']));
        return;
    }

    $student_id = $this->request->getPost('students_id');

    // Delete the student
    $deleted = $this->admin_model->delete_table('students', ['id' => $student_id]);

    if ($deleted) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => 'Student deleted successfully']));
    } else {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['error' => 'Failed to delete student']));
    }
}

public function count_student_online_admission_get() {
    // Log the request for debugging purposes
    log_message('debug', 'Received request to count students for online admission');

    // Get school_id from the GET parameters
    $school_id = $this->request->getGet('school_id');

    // Check if school_id is provided
    if (!$school_id) {
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode(['status' => 'error', 'message' => 'School ID is required']));
        return;
    }

    // Query the database to count the number of students with status 0 for the given school_id
    $count = $this->admin_model->count_all('students', [
        'school_id' => $school_id,
        'status' => 0
    ]);

    // Return the count as a JSON response
    $this->output
         ->set_content_type('application/json')
         ->set_output(json_encode(['status' => 'success', 'count' => $count]));
}

public function get_student_id_post() {
    // Log the request for debugging purposes
    log_message('debug', 'Received request to get student id');

    // Validate the input data
    $user_id = $this->request->getPost('user_id');
    $school_id = $this->request->getPost('school_id');
    if (!$user_id || !$school_id) {
        log_message('error', 'Invalid input data: user_id or school_id is missing');
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid input data']));
        return;
    }

    // Fetch the student's id based on user_id and school_id
    $student = $this->admin_model->get_row('students', [
        'user_id' => $user_id,
        'school_id' => $school_id
    ]);

    if (!$student) {
        log_message('error', 'Student not found with user_id: ' . $user_id . ' and school_id: ' . $school_id);
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['error' => 'Student not found']));
        return;
    }

    // Return the student id as a JSON response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'success', 'student_id' => (int) $student['id']]));
}


public function get_appropriate_courses_post() {
    // Log the request for debugging purposes
    log_message('debug', 'Received request for appropriate courses');

    // Validate the input data
    $student_id = $this->request->getPost('students_id');
    if (!$student_id) {
        log_message('error', 'Invalid input data: students_id is missing');
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid input data']));
        return;
    }

    // Fetch the student's school_id and status
    $student = $this->admin_model->get_row('students', ['id' => $student_id]);

    if (!$student) {
        log_message('error', 'Student not found with id: ' . $student_id);
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['error' => 'Student not found']));
        return;
    }
    
    // Check if the student's status is 1
    if ($student['status'] != 1) {
        log_message('error', 'Student is not approved with id: ' . $student_id);
        $this->output
            ->set_status_header(403)
            ->set_output(json_encode(['error' => 'Student is not approved']));
        return;
    }

    // Fetch the active courses for the student's school_id with class prices
    $courses = $this->admin_model->get_by_query([
        'select' => 'course.*, classes.price, CONCAT("http://10.0.2.2/SchoolManagementWeb/uploads/course_thumbnail/", course.thumbnail) as thumbnail',
        'from' => 'course',
        'join' => [['classes', 'course.class_id = classes.id', 'left']],
        'where' => ['course.school_id' => $student->school_id, 'course.status' => 'active']
    ]);

    // Return the courses as a JSON response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'success', 'courses' => $courses]));
}


public function student_enrollments_post() {
    // Log the request for debugging purposes
    log_message('debug', 'Received request for student enrollments');

    // Validate the input data
    $student_id = $this->request->getPost('student_id');
    $course_id = $this->request->getPost('course_id');
    $section_id = $this->request->getPost('section_id'); // Ensure this is provided in the request

    if (!$student_id || !$course_id || !$section_id) {
        log_message('error', 'Invalid input data: student_id, course_id, or section_id is missing');
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid input data']));
        return;
    }

    // Fetch the course information
    $course = $this->admin_model->get_by_query([
        'select' => 'course.*, classes.id as class_id, classes.school_id',
        'from' => 'course',
        'join' => [['classes', 'course.class_id = classes.id', 'left']],
        'where' => ['course.id' => $course_id],
        'return' => 'row'
    ]);

    if (empty($course)) {
        log_message('error', 'Course not found with id: ' . $course_id);
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['error' => 'Course not found']));
        return;
    }

    // Prepare the enrollment data
    $enroll_data = [
        'student_id' => $student_id,
        'class_id' => $course->class_id,
        'section_id' => $section_id,
        'school_id' => $course->school_id,
        'session' => $this->request->getPost('session') ?? 1 // Default value of 1 if session is not provided
    ];

    // Insert the enrollment data into the enrols table
    $this->admin_model->insert_table('enrols', $enroll_data);

    if (db()->affectedRows() === 0) {
        log_message('error', 'Failed to insert enrollment data for student with id: ' . $student_id);
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['error' => 'Failed to insert enrollment data']));
        return;
    }

    // Fetch the student's updated enrollment information
    $enrollments = $this->admin_model->get_by_query([
        'select' => 'students.*, enrols.*, sections.name as section_name, classes.name as class_name, classes.price, schools.name as school_name',
        'from' => 'students',
        'join' => [
            ['enrols', 'students.id = enrols.student_id', 'left'],
            ['sections', 'enrols.section_id = sections.id', 'left'],
            ['classes', 'sections.class_id = classes.id', 'left'],
            ['schools', 'students.school_id = schools.id', 'left']
        ],
        'where' => ['students.id' => $student_id]
    ]);

    if (empty($enrollments)) {
        log_message('error', 'No enrollment found for student with id: ' . $student_id);
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['error' => 'No enrollment found for student']));
        return;
    }

    // Return the enrollment information as a JSON response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'success', 'enrollments' => $enrollments]));
}

public function sections_for_class_get() {
    // Log the request for debugging purposes
    log_message('debug', 'Received request for sections for class');

    // Validate the input data
    $class_id = $this->request->getGet('class_id');
    if (!$class_id) {
        log_message('error', 'Invalid input data: class_id is missing');
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid input data']));
        return;
    }

    // Fetch the sections for the given class ID
    $sections = $this->admin_model->get_where_result('sections', ['class_id' => $class_id], 'id, name');

    if (empty($sections)) {
        log_message('error', 'No sections found for class with id: ' . $class_id);
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['error' => 'No sections found for class']));
        return;
    }

    // Return the sections as a JSON response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => 'success', 'sections' => $sections]));
}


  public function create_invoice_post() {
    $student_id = $this->request->getPost('student_id');
    $class_id = $this->request->getPost('class_id');
    $total_amount = $this->request->getPost('total_amount');
    $paid_amount = $this->request->getPost('paid_amount');
    $session = $this->request->getPost('session');
    $status = 'unpaid'; // Set status to 'unpaid'
    $payment_method = $this->request->getPost('payment_method'); // Get payment method
    $school_id = $this->request->getPost('school_id'); // Get school_id
    $user_name = $this->request->getPost('user_name'); // Get user name
    $created_at = $this->request->getPost('created_at'); // Get created_at

    log_message('debug', 'createInvoice_post called with data: ' . json_encode($this->request->getPost()));

    if (!$student_id || !$class_id || !$total_amount || !$paid_amount || !$session || !$payment_method || !$school_id || !$user_name || !$created_at) {
        log_message('error', 'Invalid input data: one or more fields are missing');
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Add logging to see the original created_at value
    log_message('debug', 'Original created_at: ' . $created_at);

    // Convert the date to the correct format
    $created_at_formatted = DateTime::createFromFormat('d-m-Y H:i:s', $created_at);
    if ($created_at_formatted === false) {
        log_message('error', 'Invalid date format');
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid date format']));
        return;
    }
    $created_at_formatted = $created_at_formatted->format('Y-m-d H:i:s');

    // Add logging to see the formatted created_at value
    log_message('debug', 'Formatted created_at: ' . $created_at_formatted);

    $invoice_data = [
        'title' => 'Invoice for ' . $user_name,
        'total_amount' => $total_amount,
        'class_id' => $class_id,
        'student_id' => $student_id,
        'paid_amount' => $paid_amount,
        'status' => $status, // Use the 'unpaid' status
        'payment_method' => $payment_method, // Add payment method
        'school_id' => $school_id, // Use the provided school_id
        'session' => $session,
        'created_at' => $created_at_formatted, // Use the formatted date
        'updated_at' => $created_at_formatted, // Use the formatted date
    ];

    // Insert the invoice data into the database
    $this->admin_model->insert_table('invoices', $invoice_data);

    if (db()->affectedRows() == 0) {
        log_message('error', 'Failed to insert invoice data');
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to insert invoice data']));
        return;
    }

    // Get the newly inserted invoice ID
    $invoice_id = db()->insertID();

    log_message('debug', 'Invoice created successfully with ID: ' . $invoice_id);

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'invoice_id' => $invoice_id]));
}
  





public function paid_invoice_post() {
    // Retrieve POST data
    $input_data = json_decode(file_get_contents('php://input'), true);
    $invoice_id = isset($input_data['invoice_id']) ? $input_data['invoice_id'] : null;
    $payment_method = isset($input_data['payment_method']) ? $input_data['payment_method'] : null;

    // Log the received data for debugging purposes
    log_message('debug', 'paid_invoice_post called with data: ' . json_encode($input_data));

    // Validate the input data
    if (empty($invoice_id) || empty($payment_method)) {
        log_message('error', 'Invalid input data: one or more fields are missing');
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Prepare the update data
    $update_data = [
        'payment_method' => $payment_method,
        'status' => 'paid',
        'updated_at' => date('Y-m-d H:i:s')
    ];

    // Log the update data for debugging
    log_message('debug', 'Update data: ' . json_encode($update_data));

    // Update the invoice data in the database
    $this->admin_model->update_table('invoices', $update_data, ['id' => $invoice_id]);

    // Log the database query
    log_message('debug', 'Executed query: ' . db()->getLastQuery());

    // Check if the update was successful
    if (db()->affectedRows() == 0) {
        log_message('error', 'Failed to update invoice data');
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to update invoice data']));
        return;
    }

    // Fetch the updated invoice to return
    $invoice = $this->admin_model->get_by_query([
        'select' => 'invoices.*, users.name as student_name, classes.name as class_name',
        'from' => 'invoices',
        'join' => [
            ['students', 'invoices.student_id = students.id', 'left'],
            ['users', 'students.user_id = users.id', 'left'],
            ['classes', 'invoices.class_id = classes.id', 'left']
        ],
        'where' => ['invoices.id' => $invoice_id],
        'return' => 'row_array'
    ]);

    // Log the updated invoice data
    log_message('debug', 'Updated invoice: ' . json_encode($invoice));

    // Return the response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'invoice' => $invoice]));
}


public function user_name_by_student_id_get($student_id) {
    if (!$student_id) {
        log_message('error', 'Invalid input data: student_id is missing');
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid student ID']));
        return;
    }

    $result = $this->admin_model->get_by_query([
        'select' => 'users.name as user_name',
        'from' => 'students',
        'join' => [['users', 'students.user_id = users.id', 'left']],
        'where' => ['students.id' => $student_id],
        'return' => 'row_array'
    ]);

    if ($result) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'user_name' => $result['user_name']]));
    } else {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => false, 'message' => 'User not found']));
    }
} 


public function invoice_status_get() {
    $student_id = $this->request->getGet('student_id');
    $class_id = $this->request->getGet('class_id');

    if (!$student_id || !$class_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Query to get the status of invoices
    $invoices = $this->admin_model->get_where_result('invoices', [
        'student_id' => $student_id,
        'class_id' => $class_id
    ], 'status');

    if (!empty($invoices)) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'invoices' => $invoices]));
    } else {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => false, 'message' => 'No invoices found']));
    }
}

public function payment_settings_get($school_id)
{
    // Fetch payment settings
    $payment_settings = $this->admin_model->get_where_result('payment_settings', ['school_id' => $school_id]);

    // Fetch system currency settings
    $system_currency = $this->admin_model->get_row('settings', ['school_id' => $school_id]);

    // Combine the results
    $response = array(
        'status' => true,
        'data' => array(
            'payment_settings' => $payment_settings,
            'system_currency' => $system_currency
        )
    );

    // Return the response as JSON
    echo json_encode($response);
}

public function class_create_post() {
    // Get input data
    $data = json_decode($this->request->getRawInput(), true);

    // Validate input data
    if (!isset($data['name']) || !isset($data['school_id'])) {
        $response = array('status' => false, 'message' => 'Missing required fields: name or school_id');
        echo json_encode($response);
        return;
    }

    // Prepare data for insertion
    $class_data = array(
        'name' => $data['name'],
        'school_id' => $data['school_id'],
        'price' => isset($data['price']) ? $data['price'] : NULL
    );

    // Insert data directly in the controller
    $insert_id = $this->admin_model->insert_table('classes', $class_data);

    // Prepare response
    if ($insert_id) {
        $response = array('status' => true, 'message' => 'Class created successfully', 'class_id' => $insert_id);
    } else {
        $response = array('status' => false, 'message' => 'Failed to create class');
    }

    // Output response
    echo json_encode($response);
}

public function class_update_put() {
    // Get input data
    $data = json_decode($this->request->getRawInput(), true);

    // Validate input data
    if (!isset($data['id']) || !isset($data['name']) || !isset($data['school_id'])) {
        $response = array('status' => false, 'message' => 'Missing required fields: id, name or school_id');
        echo json_encode($response);
        return;
    }

    // Prepare data for update
    $class_data = array(
        'name' => $data['name'],
        'school_id' => $data['school_id'],
        'price' => isset($data['price']) ? $data['price'] : NULL
    );

    // Update class
    $update = $this->admin_model->update_table('classes', $class_data, ['id' => $data['id']]);

    // Prepare response
    if ($update) {
        $response = array('status' => true, 'message' => 'Class updated successfully');
    } else {
        $response = array('status' => false, 'message' => 'Failed to update class');
    }

    // Output response
    echo json_encode($response);
}

public function class_del_delete() {
    // Get input data
    $data = json_decode($this->request->getRawInput(), true);

    // Validate input data
    if (!isset($data['id'])) {
        $response = array('status' => false, 'message' => 'Missing required field: id');
        echo json_encode($response);
        return;
    }

    // Delete class
    $delete = $this->admin_model->delete('classes', ['id' => $data['id']]);

    // Prepare response
    if ($delete) {
        $response = array('status' => true, 'message' => 'Class deleted successfully');
    } else {
        $response = array('status' => false, 'message' => 'Failed to delete class');
    }

    // Output response
    echo json_encode($response);
}


public function class_get() {
    // Get pagination parameters from the request
    $limit = $this->request->getGet('limit') ? intval($this->request->getGet('limit')) : 8;
    $offset = $this->request->getGet('offset') ? intval($this->request->getGet('offset')) : 0;

    // Get all classes with the corresponding school name, applying limit and offset for pagination
    $classes = $this->admin_model->get_by_query([
        'select' => 'classes.*, schools.name as school_name',
        'from' => 'classes',
        'join' => [['schools', 'schools.id = classes.school_id']],
        'limit' => $limit,
        'offset' => $offset
    ]);

    // Get sections assigned to each class
    foreach ($classes as &$class) {
        $class['sections'] = $this->admin_model->get_where_result('sections', ['class_id' => $class['id']]);
    }

    // Get the total number of classes for pagination purposes
    $total_classes = $this->admin_model->count_all('classes');

    // Prepare response
    $response = array(
        'status' => true,
        'data' => $classes,
        'total' => $total_classes,
        'limit' => $limit,
        'offset' => $offset
    );

    // Output response
    echo json_encode($response);
}

public function submit_quiz_responses_post() {
    // Get input data
    $data = json_decode($this->request->getRawInput(), true);

    if (!isset($data['quiz_id']) || !isset($data['responses'])) {
        echo json_encode(array('status' => false, 'message' => 'Missing required fields: quiz_id or responses'));
        return;
    }

    $quiz_id = $data['quiz_id'];
    $responses = $data['responses'];

    // Get correct answers for the given quiz_id
    $questions = $this->admin_model->get_where_result('question', ['quiz_id' => $quiz_id]);

    $correct_answers = array();
    foreach ($questions as $question) {
        $correct_answers[$question['id']] = json_decode($question['correct_answers']);
    }

    // Calculate result
    $total_questions = count($questions);
    $correct_responses = 0;

    foreach ($responses as $question_id => $response) {
        if (isset($correct_answers[$question_id])) {
            $correct_answer = $correct_answers[$question_id];
            // Convert both to strings for comparison
            if (json_encode($correct_answer) === json_encode($response)) {
                $correct_responses++;
            }
        }
    }

    $result = array(
        'total_questions' => $total_questions,
        'correct_responses' => $correct_responses,
        'score' => ($correct_responses / $total_questions) * 100
    );

    // Output response
    echo json_encode(array('status' => true, 'result' => $result));
}

public function all_quiz_responses_get($quiz_id) {
    if (empty($quiz_id)) {
        echo json_encode(array('status' => false, 'message' => 'Missing required field: quiz_id'));
        return;
    }

    // Get all questions and their correct answers for the given quiz_id
    $questions = $this->admin_model->get_where_result('question', ['quiz_id' => $quiz_id]);

    if (empty($questions)) {
        echo json_encode(array('status' => false, 'message' => 'No questions found for the given quiz_id'));
        return;
    }

    // Prepare responses array
    $responses = array();
    foreach ($questions as $question) {
        $responses[] = array(
            'question_id' => $question['id'],
            'question' => $question['title'],
            'correct_answers' => json_decode($question['correct_answers'], true)
        );
    }

    // Output response
    echo json_encode(array('status' => true, 'responses' => $responses));
}


public function create_subject_post() {
    // Ensure the request method is POST
    if ($this->request->getServer('REQUEST_METHOD') !== 'POST') {
        $this->output
             ->set_status_header(405)
             ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    // Retrieve input data
    $input = json_decode(trim(file_get_contents('php://input')), true);

    $name = $input['name'] ?? null;
    $class_id = $input['class_id'] ?? null;
    $school_id = $input['school_id'] ?? null;

    // Validate the input
    if (empty($name) || empty($class_id) || empty($school_id)) {
        $this->output
             ->set_status_header(400)
             ->set_output(json_encode(['message' => 'Name, Class ID, and School ID are required']));
        return;
    }

    // Prepare data for insertion
    $data = [
        'name' => $name,
        'class_id' => $class_id,
        'school_id' => $school_id,
        'session' => 1 // Default session
    ];

    // Insert the new subject
    $this->admin_model->insert_table('subjects', $data);

    if (db()->affectedRows() > 0) {
        $this->output
             ->set_status_header(201)
             ->set_content_type('application/json')
             ->set_output(json_encode(['status' => 'success', 'message' => 'Subject created successfully']));
    } else {
        $this->output
             ->set_status_header(500)
             ->set_content_type('application/json')
             ->set_output(json_encode(['status' => 'error', 'message' => 'Failed to create subject']));
    }
}

public function subjects_by_class_id_get($class_id) {
    // Validate class_id
    if (!$class_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid class_id']));
        return;
    }

    // Fetch subjects by class_id
    $result = $this->admin_model->get_by_query([
        'select' => 'subjects.*, classes.name as class_name, schools.name as school_name',
        'from' => 'subjects',
        'join' => [
            ['classes', 'classes.id = subjects.class_id'],
            ['schools', 'schools.id = subjects.school_id']
        ],
        'where' => ['subjects.class_id' => $class_id]
    ]);

    // Check if any subjects found
    if (empty($result)) {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => false, 'message' => 'No subjects found']));
        return;
    }

    // Return success response with subjects
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'subjects' => $result]));
}


public function get_classes_by_school_id_get($school_id) {
    // Ensure the request method is GET
    if ($this->request->getServer('REQUEST_METHOD') !== 'GET') {
        $this->output
             ->set_status_header(405)
             ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    // Validate the input
    if (empty($school_id)) {
        $this->output
             ->set_status_header(400)
             ->set_output(json_encode(['message' => 'School ID is required']));
        return;
    }

    // Fetch classes by school_id
    $classes = $this->admin_model->get_where_result('classes', ['school_id' => $school_id]);

    // Check if any classes are found
    if (!empty($classes)) {
        $this->output
             ->set_status_header(200)
             ->set_content_type('application/json')
             ->set_output(json_encode(['status' => 'success', 'classes' => $classes]));
    } else {
        $this->output
             ->set_status_header(404)
             ->set_output(json_encode(['status' => 'error', 'message' => 'No classes found for the given school ID']));
    }
}

public function get_subjects_by_school_id_get($school_id) {
    // Ensure the request method is GET
    if ($this->request->getServer('REQUEST_METHOD') !== 'GET') {
        $this->output
             ->set_status_header(405)
             ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    // Validate the input
    if (empty($school_id)) {
        $this->output
             ->set_status_header(400)
             ->set_output(json_encode(['message' => 'School ID is required']));
        return;
    }

    // Retrieve limit and offset from GET parameters
    $limit = $this->request->getGet('limit') ?? 8; // Default limit to 8 if not provided
    $offset = $this->request->getGet('offset') ?? 0; // Default offset to 0 if not provided

    // Fetch subjects by school_id with limit and offset
    $subjects = $this->admin_model->get_by_query([
        'select' => 'subjects.id, subjects.name as subject_name, classes.name as class_name',
        'from' => 'subjects',
        'join' => [['classes', 'subjects.class_id = classes.id']],
        'where' => ['subjects.school_id' => $school_id],
        'limit' => $limit,
        'offset' => $offset
    ]);

    // Check if any subjects are found
    if (!empty($subjects)) {
        // Fetch total count of subjects for the given school_id
        $total_count = $this->admin_model->count('subjects', ['school_id' => $school_id]);

        $this->output
             ->set_status_header(200)
             ->set_content_type('application/json')
             ->set_output(json_encode([
                 'status' => 'success',
                 'total_count' => $total_count,
                 'subjects' => $subjects
             ]));
    } else {
        $this->output
             ->set_status_header(404)
             ->set_output(json_encode(['status' => 'error', 'message' => 'No subjects found for the given school ID']));
    }
}


//dropdowns//
public function schools_for_students_get() {
    // Fetch all schools
    $schools = $this->admin_model->get_result('schools');

    $this->output
         ->set_content_type('application/json')
         ->set_output(json_encode(['status' => 'success', 'schools' => $schools]));
}

public function classes_for_students_get() {
    // Fetch all classes
    $classes = $this->admin_model->get_result('classes');

    $this->output
         ->set_content_type('application/json')
         ->set_output(json_encode(['status' => 'success', 'classes' => $classes]));
}

public function instructors_for_students_get() {
    // Fetch all instructors
    $instructors = $this->admin_model->get_result('instructors');

    $this->output
         ->set_content_type('application/json')
         ->set_output(json_encode(['status' => 'success', 'instructors' => $instructors]));
}

public function subjects_for_students_get() {
    // Fetch all subjects
    $subjects = $this->admin_model->get_result('subjects');

    $this->output
         ->set_content_type('application/json')
         ->set_output(json_encode(['status' => 'success', 'subjects' => $subjects]));
}

//dropdowns//

 public function create_department_post()
{
        // Retrieve data from POST request
        $name = $this->request->getPost('name');
        $school_id = $this->request->getPost('school_id');

        // Check if the required data is provided
        if (!$name || !$school_id) {
            $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
            return;
        }

        // Prepare data to insert
        $department_data = [
            'name' => $name,
            'school_id' => $school_id
        ];

        // Insert data into the departments table
        $this->admin_model->insert_table('departments', $department_data);

        // Check if the insert was successful
        if (db()->affectedRows() == 0) {
            $this->output
                ->set_status_header(500)
                ->set_output(json_encode(['status' => false, 'message' => 'Failed to create department']));
            return;
        }

        // Fetch the created department to return
        $department_id = db()->insertID();
        $query = $this->admin_model->get_where('departments', ['id' => $department_id]);
        $department = $query;

        // Return success response
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'department' => $department]));
}

public function departments_by_school_id_get($school_id)
{
    // Validate school_id
    if (!$school_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid school_id']));
        return;
    }

    // Get pagination parameters from GET request
    $page = $this->request->getGet('page') ? (int)$this->request->getGet('page') : 1;
    $limit = $this->request->getGet('limit') ? (int)$this->request->getGet('limit') : 4;
    $offset = ($page - 1) * $limit;

    // Fetch departments and school name by school_id with pagination
    $result = $this->admin_model->get_by_query([
        'select' => 'departments.*, schools.name as school_name',
        'from' => 'departments',
        'join' => [['schools', 'schools.id = departments.school_id']],
        'where' => ['departments.school_id' => $school_id],
        'limit' => $limit,
        'offset' => $offset
    ]);

    // Check if any departments found
    if (empty($result)) {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => false, 'message' => 'No departments found']));
        return;
    }

    // Get total count of departments
    $total_departments = $this->admin_model->count_all('departments', ['school_id' => $school_id]);

    // Return success response with departments, school name, and pagination info
    $response = [
        'status' => true,
        'departments' => $result,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $limit,
            'total_pages' => ceil($total_departments / $limit),
            'total_departments' => $total_departments
        ]
    ];

    return $this->response->setJSON($response);
}

public function update_department_post()
{
    // Retrieve data from POST request
    $id = $this->request->getPost('id');
    $name = $this->request->getPost('name');

    // Check if the required data is provided
    if (!$id || !$name) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Prepare data to update
    $department_data = ['name' => $name];

    // Update data in the departments table
    $this->admin_model->update_table('departments', $department_data, ['id' => $id]);

    // Check if the update was successful
    if (db()->affectedRows() == 0) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to update department']));
        return;
    }

    // Fetch the updated department to return
    $query = $this->admin_model->get_where('departments', ['id' => $id]);
    $department = $query;

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'department' => $department]));
}

public function delete_department_post()
{
    // Retrieve data from POST request
    $id = $this->request->getPost('id');

    // Check if the required data is provided
    if (!$id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Delete data from the departments table
    $deleted = $this->admin_model->delete('departments', ['id' => $id]);

    // Check if the delete was successful
    if (!$deleted) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to delete department']));
        return;
    }

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'message' => 'Department deleted successfully']));
}

public function create_event_post()
{
    // Retrieve data from POST request
    $title = $this->request->getPost('title');
    $starting_date = $this->request->getPost('starting_date');
    $ending_date = $this->request->getPost('ending_date');
    $school_id = $this->request->getPost('school_id');
    $session = $this->request->getPost('session') ?? 2;

    // Log the received data for debugging
    log_message('debug', 'Received data: ' . json_encode($_POST));

    // Check if the required data is provided
    if (!$title || !$starting_date || !$ending_date || !$school_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Prepare data to insert
    $event_data = [
        'title' => $title,
        'starting_date' => $starting_date,
        'ending_date' => $ending_date,
        'school_id' => $school_id,
        'session' => $session
    ];

    // Insert data into the event_calendars table
    $this->admin_model->insert_table('event_calendars', $event_data);

    // Check if the insert was successful
    if (db()->affectedRows() == 0) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to create event']));
        return;
    }

    // Fetch the created event to return
    $event_id = db()->insertID();
    $query = $this->admin_model->get_where('event_calendars', ['id' => $event_id]);
    $event = $query;

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'event' => $event]));
}

public function edit_event_post($event_id)
{
    // Retrieve data from POST request
    $title = $this->request->getPost('title');
    $starting_date = $this->request->getPost('starting_date');
    $ending_date = $this->request->getPost('ending_date');
    $school_id = $this->request->getPost('school_id');
    $session = $this->request->getPost('session') ?? 2;

    // Log the received data for debugging
    log_message('debug', 'Received data: ' . json_encode($_POST));

    // Check if the required data is provided
    if (!$title || !$starting_date || !$ending_date || !$school_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Prepare data to update
    $event_data = [
        'title' => $title,
        'starting_date' => $starting_date,
        'ending_date' => $ending_date,
        'school_id' => $school_id,
        'session' => $session
    ];

    // Update the event in the event_calendars table
    $this->admin_model->update_table('event_calendars', $event_data, ['id' => $event_id]);

    // Check if the update was successful
    if (db()->affectedRows() == 0) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to update event']));
        return;
    }

    // Fetch the updated event to return
    $query = $this->admin_model->get_where('event_calendars', ['id' => $event_id]);
    $event = $query;

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'event' => $event]));
}




public function events_by_school_id_get($school_id)
{
    // Validate school_id
    if (!$school_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid school_id']));
        return;
    }

    // Get the page number from the query string
    $page = $this->request->getGet('page');
    $limit = 3; // Number of events per page
    $offset = ($page - 1) * $limit;

    // Fetch events by school_id with pagination
    $result = $this->admin_model->get_by_query([
        'select' => 'event_calendars.*, schools.name as school_name',
        'from' => 'event_calendars',
        'join' => [['schools', 'schools.id = event_calendars.school_id']],
        'where' => ['event_calendars.school_id' => $school_id],
        'limit' => $limit,
        'offset' => $offset
    ]);

    // Check if any events found
    if (empty($result)) {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => false, 'message' => 'No events found']));
        return;
    }

    // Return success response with events
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'events' => $result]));
}


public function delete_event_delete($event_id)
{
    // Validate event_id
    if (!$event_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid event_id']));
        return;
    }

    // Delete the event
    $deleted = $this->admin_model->delete('event_calendars', ['id' => $event_id]);

    // Check if the delete was successful
    if (!$deleted) {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => false, 'message' => 'Event not found or already deleted']));
        return;
    }

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'message' => 'Event deleted successfully']));
}

public function exams_by_school_id_get($school_id, $page = 1)
{
    // Validate school_id
    if (!$school_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid school_id']));
        return;
    }

    // Set pagination parameters
    if ($page < 1) {
        $page = 1;
    }
    $limit = 4; // Number of exams per page
    $offset = ($page - 1) * $limit;

    // Fetch exams by school_id with pagination and include school name
    $result = $this->admin_model->get_by_query([
        'select' => 'exams.*, schools.name as school_name',
        'from' => 'exams',
        'join' => [['schools', 'schools.id = exams.school_id']],
        'where' => ['exams.school_id' => $school_id],
        'limit' => $limit,
        'offset' => $offset
    ]);

    // Get the total count of exams
    $total = $this->admin_model->count('exams', ['school_id' => $school_id]);

    // Check if any exams found
    if (empty($result)) {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => false, 'message' => 'No exams found']));
        return;
    }

    // Return success response with exams and total count
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'exams' => $result, 'total' => $total]));
}



public function create_exam_post()
{
    // Retrieve data from POST request
    $name = $this->request->getPost('name');
    $starting_date = $this->request->getPost('starting_date');
    $ending_date = $this->request->getPost('ending_date');
    $school_id = $this->request->getPost('school_id');
    $session = $this->request->getPost('session') ?? 2;

    // Log the received data for debugging
    log_message('debug', 'Received data: ' . json_encode($_POST));

    // Check if the required data is provided
    if (!$name || !$starting_date || !$ending_date || !$school_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Prepare data to insert
    $exam_data = [
        'name' => $name,
        'starting_date' => $starting_date,
        'ending_date' => $ending_date,
        'school_id' => $school_id,
        'session' => $session
    ];

    // Insert data into the exams table
    $this->admin_model->insert_table('exams', $exam_data);

    // Check if the insert was successful
    if (db()->affectedRows() == 0) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to create exam']));
        return;
    }

    // Fetch the created exam to return
    $exam_id = db()->insertID();
    $query = $this->admin_model->get_where('exams', ['id' => $exam_id]);
    $exam = $query;

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'exam' => $exam]));
}

public function edit_exam_post()
{
    // Retrieve data from POST request
    $exam_id = $this->request->getPost('id');
    $name = $this->request->getPost('name');
    $starting_date = $this->request->getPost('starting_date');
    $ending_date = $this->request->getPost('ending_date');
    $school_id = $this->request->getPost('school_id');
    $session = $this->request->getPost('session') ?? 1;

    // Log the received data for debugging
    log_message('debug', 'Received data: ' . json_encode($_POST));

    // Check if the required data is provided
    if (!$exam_id || !$name || !$starting_date || !$ending_date || !$school_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Prepare data to update
    $exam_data = [
        'name' => $name,
        'starting_date' => $starting_date,
        'ending_date' => $ending_date,
        'school_id' => $school_id,
        'session' => $session
    ];

    // Update data in the exams table
    $this->admin_model->update_table('exams', $exam_data, ['id' => $exam_id]);

    // Check if the update was successful
    if (db()->affectedRows() == 0) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to update exam']));
        return;
    }

    // Fetch the updated exam to return
    $query = $this->admin_model->get_where('exams', ['id' => $exam_id]);
    $exam = $query;

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'exam' => $exam]));
}


public function delete_exam_delete($exam_id)
{
    // Validate exam_id
    if (!$exam_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid exam_id']));
        return;
    }

    // Delete the exam
    $deleted = $this->admin_model->delete('exams', ['id' => $exam_id]);

    // Check if the delete was successful
    if (!$deleted) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to delete exam']));
        return;
    }

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'message' => 'Exam deleted successfully']));
}

//Grades
public function grades_by_school_id_get($school_id, $page = 1)
{
    // Validate school_id
    if (!$school_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid school_id']));
        return;
    }

    // Set pagination parameters
    if ($page < 1) {
        $page = 1;
    }
    $limit = 3; // Number of grades per page
    $offset = ($page - 1) * $limit;

    // Fetch grades by school_id with pagination and optional search
    $config = [
        'select' => '*',
        'from' => 'grades',
        'where' => ['school_id' => $school_id],
        'limit' => $limit,
        'offset' => $offset
    ];
    
    if ($this->request->getGet('search')) {
        $search = $this->request->getGet('search');
        $config['like'] = [
            'name' => $search,
            'grade_point' => $search,
            'mark_from' => $search,
            'mark_upto' => $search,
            'comment' => $search
        ];
    }
    
    $result = $this->admin_model->get_by_query($config);

    // Check if any grades found
    if (empty($result)) {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => false, 'message' => 'No grades found']));
        return;
    }

    // Fetch the total number of grades for the school
    $total_grades = $this->admin_model->count_all('grades', ['school_id' => $school_id]);

    // Return success response with grades and total count
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'grades' => $result, 'total' => $total_grades]));
}

public function create_grade_post()
{
    $data = $this->request->getPost();

    if (!isset($data['school_id']) || !isset($data['name']) || !isset($data['grade_point']) || !isset($data['mark_from']) || !isset($data['mark_upto']) || !isset($data['comment'])) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Incomplete grade data']));
        return;
    }

    // Set session to 2 and add current date and time
    $data['session'] = 2;
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['updated_at'] = date('Y-m-d H:i:s');

    $insert_id = $this->admin_model->insert_table('grades', $data);

    if ($insert_id) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'message' => 'Grade created successfully', 'grade_id' => $insert_id]));
    } else {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to create grade']));
    }
}

public function edit_grade_post()
{
    $data = $this->request->getPost();

    if (!isset($data['id']) || !isset($data['school_id']) || !isset($data['name']) || !isset($data['grade_point']) || !isset($data['mark_from']) || !isset($data['mark_upto']) || !isset($data['comment'])) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Incomplete grade data']));
        return;
    }

    // Set updated_at to the current date and time
    $data['updated_at'] = date('Y-m-d H:i:s');

    $updated = $this->admin_model->update_table('grades', $data, ['id' => $data['id']]);

    if ($updated) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'message' => 'Grade updated successfully']));
    } else {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to update grade']));
    }
}


public function delete_grade_delete($id)
{
    if (!$id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid grade id']));
        return;
    }

    $deleted = $this->admin_model->delete('grades', ['id' => $id]);

    if ($deleted) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'message' => 'Grade deleted successfully']));
    } else {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to delete grade']));
    }
}


////
///Routines Part
public function create_routine_post()
{
    // Get the raw POST data
    $postData = json_decode(file_get_contents('php://input'), true);

    // Debug: Log the received data
    log_message('debug', 'Received raw POST data: ' . print_r($postData, TRUE));

    // Fetch data from the decoded JSON
    $data = array(
        'class_id' => isset($postData['class_id']) ? $postData['class_id'] : null,
        'section_id' => isset($postData['section_id']) ? $postData['section_id'] : null,
        'subject_id' => isset($postData['subject_id']) ? $postData['subject_id'] : null,
        'starting_hour' => isset($postData['starting_hour']) ? $postData['starting_hour'] : null,
        'ending_hour' => isset($postData['ending_hour']) ? $postData['ending_hour'] : null,
        'starting_minute' => isset($postData['starting_minute']) ? $postData['starting_minute'] : null,
        'ending_minute' => isset($postData['ending_minute']) ? $postData['ending_minute'] : null,
        'day' => isset($postData['day']) ? $postData['day'] : null,
        'teacher_id' => isset($postData['teacher_id']) ? $postData['teacher_id'] : null,
        'room_id' => isset($postData['room_id']) ? $postData['room_id'] : null,
        'school_id' => isset($postData['school_id']) ? $postData['school_id'] : null,
        'session_id' => isset($postData['session_id']) ? $postData['session_id'] : null
    );

    // Debug: Log the parsed data
    log_message('debug', 'Parsed data: ' . print_r($data, TRUE));

    // Insert data into the database
    $inserted = $this->admin_model->insert_table('routines', $data);

    // Debug: Log the insert status
    log_message('debug', 'Insert status: ' . ($inserted ? 'Success' : 'Failed'));

    // Return response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
            'status' => $inserted,
            'notification' => $inserted ? get_phrase('class_routine_added_successfully') : get_phrase('failed_to_add_class_routine')
        ]));
}

public function update_routine_put($id)
{
    // Get the raw PUT data
    $putData = json_decode(file_get_contents('php://input'), true);

    // Debug: Log the received data
    log_message('debug', 'Received raw PUT data: ' . print_r($putData, TRUE));

    // Fetch data from the decoded JSON
    $data = array(
        'class_id' => isset($putData['class_id']) ? $putData['class_id'] : null,
        'section_id' => isset($putData['section_id']) ? $putData['section_id'] : null,
        'subject_id' => isset($putData['subject_id']) ? $putData['subject_id'] : null,
        'starting_hour' => isset($putData['starting_hour']) ? $putData['starting_hour'] : null,
        'ending_hour' => isset($putData['ending_hour']) ? $putData['ending_hour'] : null,
        'starting_minute' => isset($putData['starting_minute']) ? $putData['starting_minute'] : null,
        'ending_minute' => isset($putData['ending_minute']) ? $putData['ending_minute'] : null,
        'day' => isset($putData['day']) ? $putData['day'] : null,
        'teacher_id' => isset($putData['teacher_id']) ? $putData['teacher_id'] : null,
        'room_id' => isset($putData['room_id']) ? $putData['room_id'] : null
    );

    // Debug: Log the parsed data
    log_message('debug', 'Parsed data: ' . print_r($data, TRUE));

    // Remove any null values from the $data array
    $data = array_filter($data, function($value) {
        return $value !== null;
    });

    // Ensure there is data to update
    if (empty($data)) {
        log_message('debug', 'No data to update.');
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => false,
                'notification' => get_phrase('no_data_provided_for_update')
            ]));
        return;
    }

    // Update data in the database
    $updated = $this->admin_model->update_table('routines', $data, ['id' => $id]);

    // Debug: Log the SQL query and any errors
    log_message('debug', 'SQL Query: ' . db()->getLastQuery());
    if (!$updated) {
        $error = db()->error();
        log_message('debug', 'DB Error: ' . $error['message']);
    }

    // Debug: Log the update status
    log_message('debug', 'Update status: ' . ($updated ? 'Success' : 'Failed'));

    // Return response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
            'status' => $updated,
            'notification' => $updated ? get_phrase('class_routine_updated_successfully') : get_phrase('failed_to_update_class_routine')
        ]));
}


public function delete_routine_delete($id)
{
    // No need to fetch data for deletion, just use the ID

    // Debug: Log the ID to be deleted
    log_message('debug', 'Deleting routine with ID: ' . $id);

    // Delete the routine from the database
    $deleted = $this->admin_model->delete('routines', ['id' => $id]);

    // Debug: Log the delete status
    log_message('debug', 'Delete status: ' . ($deleted ? 'Success' : 'Failed'));

    // Return response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
            'status' => $deleted,
            'notification' => $deleted ? get_phrase('class_routine_deleted_successfully') : get_phrase('failed_to_delete_class_routine')
        ]));
}


public function routines_by_school_id_get($school_id)
{
    // Debug: Log the received school_id
    log_message('debug', 'Fetching routines for school_id: ' . $school_id);

    // Fetch routines from the database
    $routines = $this->admin_model->get_where_result('routines', ['school_id' => $school_id]);

    // Debug: Log the fetched data
    log_message('debug', 'Fetched routines: ' . print_r($routines, TRUE));

    // Return response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
            'status' => true,
            'data' => $routines,
            'notification' => get_phrase('routines_fetched_successfully')
        ]));
}



public function routines_by_class_and_section_get($class_id, $section_id)
{
    // Debug: Log the received class_id and section_id
    log_message('debug', 'Fetching routines for class_id: ' . $class_id . ' and section_id: ' . $section_id);

    // Fetch routines from the database
    $routines = $this->admin_model->get_where_result('routines', [
        'class_id' => $class_id,
        'section_id' => $section_id
    ]);

    // Debug: Log the fetched data
    log_message('debug', 'Fetched routines: ' . print_r($routines, TRUE));

    // Return response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode([
            'status' => true,
            'data' => $routines,
            'notification' => get_phrase('routines_fetched_successfully')
        ]));
}


//End Routines
//Student Free Manager

public function invoice_by_date_range_post() {

    // Retrieve and log posted data
    $date_from = $this->request->getPost('date_from');
    $date_to = $this->request->getPost('date_to');
    $selected_class = $this->request->getPost('selected_class');
    $selected_status = $this->request->getPost('selected_status');

    log_message('debug', 'Received: date_from=' . $date_from . ', date_to=' . $date_to . ', selected_class=' . $selected_class . ', selected_status=' . $selected_status);

    // Build the query conditionally
    $conditions = [];
    $params = [];

    if (!empty($selected_class) && $selected_class != "all") {
        $conditions[] = 'class_id = ?';
        $params[] = $selected_class;
        log_message('debug', 'Applied class_id filter: ' . $selected_class);
    }

    if (!empty($selected_status) && $selected_status != "all") {
        $conditions[] = 'status = ?';
        $params[] = $selected_status;
        log_message('debug', 'Applied status filter: ' . $selected_status);
    }

    if (!empty($date_from) && !empty($date_to)) {
        $conditions[] = 'DATE(created_at) >= ?';
        $params[] = $date_from;
        $conditions[] = 'DATE(created_at) <= ?';
        $params[] = $date_to;
        log_message('debug', 'Applied date range filter: ' . $date_from . ' to ' . $date_to);
    }

    $sql = "SELECT * FROM invoices";
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $query = $this->admin_model->get_by_query($sql, $params);

    // Log the executed SQL query for debugging
    log_message('debug', 'Executed SQL Query: ' . db()->getLastQuery());

    // Return the results as a JSON response
    if (!empty($query)) {
        $response = array('status' => 'success', 'data' => $query);
    } else {
        $response = array('status' => 'error', 'message' => 'No invoices found for the given criteria');
    }

    return $this->response->setJSON($response);
}



/* public function invoices_by_filter_post()
{
    // Get the JSON input
    $json_input = json_decode(file_get_contents('php://input'), true);

    // Extract the parameters
    $class_id = isset($json_input['class_id']) ? $json_input['class_id'] : null;
    $start_date = isset($json_input['start_date']) ? $json_input['start_date'] : null;
    $end_date = isset($json_input['end_date']) ? $json_input['end_date'] : null;

    // Validate that all fields are provided
    if (!$class_id || !$start_date || !$end_date) {
        $response = array('status' => false, 'message' => 'Missing parameters');
        echo json_encode($response);
        return;
    }

    // Convert dates to a format that MySQL understands, if needed
    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = date('Y-m-d', strtotime($end_date));

    // Build the query with the necessary filters
    $invoices = $this->admin_model->get_by_query([
        'select' => '*',
        'from' => 'invoices',
        'where' => "class_id = $class_id AND created_at >= '$start_date' AND created_at <= '$end_date'"
    ]);

    // Check if any invoices were found
    if (count($invoices) > 0) {
        $response = array('status' => true, 'data' => $invoices);
    } else {
        $response = array('status' => false, 'message' => 'No invoices found');
    }

    // Send the response back as JSON
    echo json_encode($response);
} */

public function invoices_by_filter_post()
{
    // Get the JSON input
    $json_input = json_decode(file_get_contents('php://input'), true);

    // Extract the parameters
    $class_id = isset($json_input['class_id']) ? $json_input['class_id'] : null;
    $start_date = isset($json_input['start_date']) ? $json_input['start_date'] : null;
    $end_date = isset($json_input['end_date']) ? $json_input['end_date'] : null;

    // Validate that all fields are provided
    if (!$class_id || !$start_date || !$end_date) {
        $response = array('status' => false, 'message' => 'Missing parameters');
        echo json_encode($response);
        return;
    }

    // Convert dates to a format that MySQL understands, if needed
    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = date('Y-m-d', strtotime($end_date));

    // Build the query with the necessary filters and join with the users table
    $invoices = $this->admin_model->get_by_query([
        'select' => 'invoices.*, CONCAT(users.name) as student_name',
        'from' => 'invoices',
        'join' => [
            ['students', 'students.id = invoices.student_id', 'left'],
            ['users', 'users.id = students.user_id', 'left']
        ],
        'where' => [
            'invoices.class_id' => $class_id,
            'invoices.created_at >=' => $start_date,
            'invoices.created_at <=' => $end_date
        ]
    ]);

    // Check if any invoices were found
    if (count($invoices) > 0) {
        $response = array('status' => true, 'data' => $invoices);
    } else {
        $response = array('status' => false, 'message' => 'No invoices found');
    }

    // Send the response back as JSON
    echo json_encode($response);
}


public function classe_get() {
    // Fetch all class ids and names from the 'classes' table
    $classes = $this->admin_model->get_result('classes');

    // Return the result as a JSON response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($classes));
}


protected function get_class_name($class_id) {
    // Query the database to get the class name based on the class_id
    $class = $this->admin_model->get_row('classes', ['id' => $class_id]);

    if ($class) {
        return $class['name'];
    } else {
        return null; // or return a default value
    }
}

public function get_invoice_by_parent_id() {
    $parent_user_id = session()->get('user_id');
    $parent_data = $this->admin_model->get_where('parents', array('user_id' => $parent_user_id));
    $student_list = $this->user_model->get_student_list_of_logged_in_parent();
    $student_ids = array();

    foreach ($student_list as $student) {
        if (!in_array($student['student_id'], $student_ids)) {
            array_push($student_ids, $student['student_id']);
        }
    }

    if (count($student_ids) > 0) {
        $invoices = $this->admin_model->get_by_query([
            'from' => 'invoices',
            'where_in' => ['student_id' => $student_ids],
            'where' => [
                'school_id' => $this->school_id,
                'session' => $this->active_session
            ]
        ]);

        $response = array('status' => true, 'data' => $invoices);
    } else {
        $response = array('status' => false, 'notification' => 'No invoices found');
    }

    return $this->response->setJSON($response);
}

public function create_single_invoice_post() {
    // Get raw POST data
    $input_data = json_decode($this->request->getRawInput(), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        log_message('error', 'JSON decode error: ' . json_last_error_msg());
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid JSON input']));
        return;
    }

    // Log the input data for verification
    log_message('debug', 'Input data: ' . json_encode($input_data));

    // Validate and log the 'created_at' date
    $created_at = isset($input_data['created_at']) ? $input_data['created_at'] : date('Y-m-d H:i:s');
    $created_at_datetime = DateTime::createFromFormat('Y-m-d H:i:s', $created_at);

    if ($created_at_datetime === false) {
        $errors = DateTime::getLastErrors();
        log_message('error', 'Date parsing error: ' . $created_at . ' Errors: ' . print_r($errors, true));
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid date format. Expected format: Y-m-d H:i:s']));
        return;
    }

    $created_at_timestamp = $created_at_datetime->getTimestamp();
    log_message('debug', 'Parsed timestamp: ' . $created_at_timestamp);

    // Prepare invoice data
    $invoice_data = [
        'title' => isset($input_data['title']) ? $input_data['title'] : null,
        'total_amount' => isset($input_data['total_amount']) ? $input_data['total_amount'] : 0,
        'class_id' => isset($input_data['class_id']) ? $input_data['class_id'] : null,
        'student_id' => isset($input_data['student_id']) ? $input_data['student_id'] : null,
        'paid_amount' => isset($input_data['paid_amount']) ? $input_data['paid_amount'] : 0,
        'status' => isset($input_data['status']) ? $input_data['status'] : 'unpaid',
        'payment_method' => isset($input_data['payment_method']) ? $input_data['payment_method'] : null,
        'school_id' => isset($input_data['school_id']) ? $input_data['school_id'] : null,
        'session' => isset($input_data['session']) ? $input_data['session'] : null,
        'created_at' => $created_at_timestamp,
        'updated_at' => ($input_data['paid_amount'] > 0) ? $created_at_timestamp : null,
    ];

    // Check for required fields
    $required_fields = ['title', 'class_id', 'student_id', 'school_id', 'session'];
    foreach ($required_fields as $field) {
        if (empty($invoice_data[$field])) {
            log_message('error', ucfirst($field) . ' is required');
            $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['status' => false, 'message' => ucfirst($field) . ' is required']));
            return;
        }
    }

    // Insert the data into the database
    $invoice_id = $this->admin_model->insert_table('invoices', $invoice_data);
    if ($invoice_id) {
        log_message('debug', 'Invoice created successfully with ID: ' . $invoice_id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'invoice_id' => $invoice_id, 'message' => 'Invoice created successfully']));
    } else {
        log_message('error', 'Database insert error: ' . db()->error()['message']);
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to insert invoice data']));
    }
} 

public function create_mass_invoice_post() {
    // Récupérer les données JSON du POST
    $data = json_decode($this->request->getRawInput(), true);

    log_message('debug', 'Données POST reçues : ' . json_encode($data));

    // Valider les champs requis
    if (empty($data['school_id']) || empty($data['class_id']) || empty($data['section_id']) || empty($data['title']) || empty($data['total_amount']) || empty($data['paid_amount']) || empty($data['status'])) {
        log_message('error', 'Échec de la validation : champs requis manquants');
        $response = array('status' => false, 'notification' => 'Tous les champs sont requis', 'received_data' => $data);
        return $this->response->setJSON($response);
    }

    // Ajouter des champs supplémentaires
    $data['session'] = $this->active_session;
    $data['created_at'] = date('Y-m-d H:i:s');

    // Requête pour sélectionner les étudiants à partir de la table `enrols`
    $students = $this->admin_model->get_by_query([
        'select' => 'student_id',
        'from' => 'enrols',
        'where' => [
            'class_id' => $data['class_id'],
            'section_id' => $data['section_id'],
            'school_id' => $data['school_id']
        ]
    ]);

    // Vérifier si des étudiants existent
    if (empty($students)) {
        log_message('error', 'Aucun étudiant trouvé pour class_id: ' . $data['class_id'] . ', section_id: ' . $data['section_id'] . ', school_id: ' . $data['school_id']);
        $response = array('status' => false, 'notification' => 'Aucun étudiant trouvé pour la classe et la section sélectionnées');
        return $this->response->setJSON($response);
    }

    log_message('debug', 'Étudiants trouvés : ' . json_encode($students));

    // Créer une facture pour chaque étudiant
    foreach ($students as $student) {
        $invoice_data = $data;
        $invoice_data['student_id'] = $student['student_id'];
        $this->admin_model->insert_table('invoices', $invoice_data);
        log_message('debug', 'Facture créée pour student_id : ' . $student['student_id']);
    }

    log_message('debug', 'Toutes les factures ont été créées avec succès');
    $response = array('status' => true, 'notification' => 'Factures créées avec succès');
    return $this->response->setJSON($response);
}


public function update_invoice($id = "") {
    $previous_invoice_data = $this->admin_model->get_where('invoices', array('id' => $id));

    $data['title'] = $this->request->getPost('title');
    $data['total_amount'] = $this->request->getPost('total_amount');
    $data['class_id'] = $this->request->getPost('class_id');
    $data['student_id'] = $this->request->getPost('student_id');
    $data['paid_amount'] = $this->request->getPost('paid_amount');
    $data['status'] = $this->request->getPost('status');

    if ($data['paid_amount'] > $data['total_amount']) {
        $response = array('status' => false, 'notification' => 'Paid amount cannot be greater than total amount');
        return $this->response->setJSON($response);
    }
    if ($data['status'] == 'paid' && $data['total_amount'] != $data['paid_amount']) {
        $response = array('status' => false, 'notification' => 'Paid amount is not equal to total amount');
        return $this->response->setJSON($response);
    }

    if ($data['total_amount'] == $data['paid_amount']) {
        $data['status'] = 'paid';
    }

    if ($this->request->getPost('paid_amount') != $previous_invoice_data['paid_amount'] && $this->request->getPost('paid_amount') > 0) {
        $data['updated_at'] = strtotime(date('d-M-Y'));
    } elseif ($this->request->getPost('paid_amount') == 0 || $this->request->getPost('paid_amount') == "") {
        $data['updated_at'] = 0;
    }

    $this->admin_model->update_table('invoices', $data, ['id' => $id]);

    $response = array('status' => true, 'notification' => 'Invoice updated successfully');
    return $this->response->setJSON($response);
}

public function delete_invoice($id = "") {
    $this->admin_model->delete('invoices', ['id' => $id]);

    $response = array('status' => true, 'notification' => 'Invoice deleted successfully');
    return $this->response->setJSON($response);
}


//End Student Free Manager
//Class Room Part

public function add_class_room_post() {
    $data = json_decode($this->request->getRawInput(), true);
    if (!isset($data['name']) || !isset($data['school_id'])) {
        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Missing required fields']));
    }
    log_message('debug', 'Input data: ' . json_encode($data));
    $this->admin_model->insert_table('class_rooms', $data);
    if (db()->affectedRows() > 0) {
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode(['status' => true, 'message' => 'Class room added successfully']));
    } else {
        $error = db()->error();
        log_message('error', 'Database insert error: ' . $error['message']);
        return $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to add class room', 'error' => $error['message']]));
    }
}

public function update_class_room_put($id) {
    $data = json_decode($this->request->getRawInput(), true);
    if (!isset($data['name']) || !isset($data['school_id'])) {
        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Missing required fields']));
    }
    log_message('debug', 'Input data: ' . json_encode($data));
    $this->admin_model->update_table('class_rooms', $data, ['id' => $id]);
    if (db()->affectedRows() > 0) {
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode(['status' => true, 'message' => 'Class room updated successfully']));
    } else {
        $error = db()->error();
        log_message('error', 'Database update error: ' . $error['message']);
        return $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to update class room', 'error' => $error['message']]));
    }
}

public function delete_class_room_delete($id) {
    $deleted = $this->admin_model->delete('class_rooms', ['id' => $id]);
    if ($deleted) {
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode(['status' => true, 'message' => 'Class room deleted successfully']));
    } else {
        $error = db()->error();
        log_message('error', 'Database delete error: ' . $error['message']);
        return $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to delete class room', 'error' => $error['message']]));
    }
}


public function get_class_room_get($school_id) {
    $class_rooms = $this->admin_model->get_by_query([
        'select' => 'class_rooms.*, schools.name as school_name',
        'from' => 'class_rooms',
        'join' => [['schools', 'schools.id = class_rooms.school_id']],
        'where' => ['class_rooms.school_id' => $school_id]
    ]);

    if (!empty($class_rooms)) {
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode(['status' => true, 'data' => $class_rooms]));
    } else {
        return $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => false, 'message' => 'Class rooms not found']));
    }
}






//End of ClassRoom Part
///Marks Part

public function add_marks_post() {
    // Retrieve and decode input data
    $data = json_decode($this->request->getRawInput(), true);

    // Validate input data
    if (!isset($data['student_id']) || !isset($data['subject_id']) || !isset($data['class_id']) || !isset($data['section_id']) || !isset($data['exam_id']) || !isset($data['mark_obtained']) || !isset($data['comment']) || !isset($data['school_id'])) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Missing required fields']));
        return;
    }

    // Prepare data for insertion
    $data['session'] = 1; // Example session id, replace with your logic

    // Log the data for debugging
    log_message('debug', 'Input data: ' . json_encode($data));

    // Attempt to insert marks
    $this->admin_model->insert_table('marks', $data);

    if (db()->affectedRows() > 0) {
        $this->output
            ->set_status_header(200)
            ->set_output(json_encode(['status' => true, 'message' => 'Marks added successfully']));
    } else {
        // Log the database error for debugging
        $error = db()->error();
        log_message('error', 'Database insert error: ' . $error['message']);
        
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to add marks']));
    }
}


public function update_marks_put() {
    // Retrieve and decode input data
    $data = json_decode($this->request->getRawInput(), true);

    // Validate input data
    if (!isset($data['student_id']) || !isset($data['subject_id']) || !isset($data['class_id']) || !isset($data['section_id']) || !isset($data['exam_id']) || !isset($data['mark_obtained']) || !isset($data['comment']) || !isset($data['school_id'])) {
        return $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Missing required fields']));
    }

    // Log the data for debugging
    log_message('debug', 'Input data: ' . json_encode($data));

    // Prepare the data for the checker
    $checker = array(
        'student_id' => $data['student_id'],
        'class_id' => $data['class_id'],
        'section_id' => $data['section_id'],
        'subject_id' => $data['subject_id'],
        'exam_id' => $data['exam_id'],
        'school_id' => $data['school_id'],
        'session' => isset($data['session']) ? $data['session'] : 1 // Example session id, replace with your logic
    );

    // Prepare the update data
    $update_data = array(
        'mark_obtained' => $data['mark_obtained'],
        'comment' => $data['comment']
    );

    // Begin transaction
    db()->transBegin();

    // Check if the record exists
    $query = $this->admin_model->get_where('marks', $checker);

    if ($query > 0) {
        // Record found, proceed with update
        $row = $query;
        $this->admin_model->update_table('marks', $update_data, ['id' => $row->id]);
    } else {
        // Record not found
        db()->transCommit();
        return $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => false, 'message' => 'Record not found']));
    }

    // Complete transaction
    db()->transCommit();

    // Check transaction status
    if (db()->transStatus() === FALSE) {
        // Log the database error for debugging
        $error = db()->error();
        log_message('error', 'Database update error: ' . $error['message']);

        return $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to update marks']));
    } else {
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode(['status' => true, 'message' => 'Marks updated successfully']));
    }
}



public function filter_exams_by_school_id_get($school_id)
{
    // Validate school_id
    if (!$school_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid school_id']));
        return;
    }

    // Fetch exams by school_id and include school name
    $result = $this->admin_model->get_by_query([
        'select' => 'exams.*, schools.name as school_name',
        'from' => 'exams',
        'join' => [['schools', 'schools.id = exams.school_id']],
        'where' => ['exams.school_id' => $school_id]
    ]);

    // Check if any exams found
    if (empty($result)) {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => false, 'message' => 'No exams found']));
        return;
    }

    // Return success response with exams
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'exams' => $result]));
}

public function filter_student_get() {
    // Retrieve and validate input data
    $exam_id = $this->request->getGet('exam_id');
    $class_id = $this->request->getGet('class_id');
    $section_id = $this->request->getGet('section_id');
    $subject_id = $this->request->getGet('subject_id');
    $school_id = $this->request->getGet('school_id');
    $session = $this->request->getGet('session');

    // Validate required parameters
    if (empty($exam_id) || empty($class_id) || empty($section_id) || empty($subject_id) || empty($school_id) || empty($session)) {
        $this->output
             ->set_status_header(400)
             ->set_output(json_encode(['status' => 'error', 'message' => 'Missing required fields']));
        return;
    }

    // Build the query to filter students and join with the students table to get student names
    $result = $this->admin_model->get_by_query([
        'select' => 'marks.*, users.name as student_name',
        'from' => 'marks',
        'join' => [
            ['students', 'students.id = marks.student_id'],
            ['users', 'users.id = students.user_id']
        ],
        'where' => [
            'marks.exam_id' => $exam_id,
            'marks.class_id' => $class_id,
            'marks.section_id' => $section_id,
            'marks.subject_id' => $subject_id,
            'marks.school_id' => $school_id,
            'marks.session' => $session
        ]
    ]);

    // Check if any students found
    if (empty($result)) {
        $this->output
             ->set_status_header(404)
             ->set_output(json_encode(['status' => 'error', 'message' => 'No students found']));
        return;
    }

    // Return success response with students data
    $this->output
         ->set_content_type('application/json')
         ->set_output(json_encode(['status' => 'success', 'students' => $result]));
}


public function get_sections_by_class_id_get($class_id) {
    // Ensure the request method is GET
    if ($this->request->getServer('REQUEST_METHOD') !== 'GET') {
        $this->output
             ->set_status_header(405)
             ->set_output(json_encode(['message' => 'Method not allowed']));
        return;
    }

    // Validate the input
    if (empty($class_id)) {
        $this->output
             ->set_status_header(400)
             ->set_output(json_encode(['message' => 'Class ID is required']));
        return;
    }

    // Fetch sections by class_id
    $sections = $this->admin_model->get_where_result('sections', ['class_id' => $class_id]);

    // Check if any sections are found
    if (!empty($sections)) {
        $this->output
             ->set_status_header(200)
             ->set_content_type('application/json')
             ->set_output(json_encode(['status' => 'success', 'sections' => $sections]));
    } else {
        $this->output
             ->set_status_header(404)
             ->set_output(json_encode(['status' => 'error', 'message' => 'No sections found for the given class ID']));
    }
}

public function get_subject_id_by_name_get($subject_name = "")
{
    // Validate input
    if (empty($subject_name)) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Subject name is required']));
        return;
    }

    // Sanitize input
    $subject_name = urldecode($subject_name);

    // Query to get subject ID by name
    $result = $this->admin_model->get_row('subjects', ['name' => $subject_name]);

    // Check if any subject found
    if ($result) {
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'subject_id' => $result['id']]));
    } else {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => 'error', 'message' => 'Subject not found']));
    }
}


//End Marks

////BOOKS

public function books_by_school_id_get($school_id, $page = 1)
{
    // Validate school_id
    if (!$school_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid school_id']));
        return;
    }

    // Set pagination parameters
    if ($page < 1) {
        $page = 1;
    }
    $limit = 6; // Number of books per page
    $offset = ($page - 1) * $limit;

    // Fetch books by school_id with pagination
    $result = $this->admin_model->get_by_query([
        'select' => '*',
        'from' => 'books',
        'where' => ['school_id' => $school_id],
        'limit' => $limit,
        'offset' => $offset
    ]);

    // Check if any books found
    if (empty($result)) {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => false, 'message' => 'No books found']));
        return;
    }

    // Fetch the total number of books for the school
    $total_books = $this->admin_model->count_all('books', ['school_id' => $school_id]);

    // Return success response with books and total count
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'books' => $result, 'total' => $total_books]));
}

public function create_book_post()
{
    $data = $this->request->getPost();

    if (!isset($data['school_id']) || !isset($data['name']) || !isset($data['author']) || !isset($data['copies'])) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Incomplete book data']));
        return;
    }

    // Set session to 2 and add current date and time
    $data['session'] = 2;
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['updated_at'] = date('Y-m-d H:i:s');

    $this->admin_model->insert_table('books', $data);
    $insert_id = db()->insertID();

    if ($insert_id) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'message' => 'Book created successfully', 'book_id' => $insert_id]));
    } else {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to create book']));
    }
}


public function edit_book_post()
{
    $data = $this->request->getPost();

    if (!isset($data['id']) || !isset($data['school_id']) || !isset($data['name']) || !isset($data['author']) || !isset($data['copies'])) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Incomplete book data']));
        return;
    }

    // Set updated_at to the current date and time
    $data['updated_at'] = date('Y-m-d H:i:s');

    $updated = $this->admin_model->update_table('books', $data, ['id' => $data['id']]);

    if ($updated) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'message' => 'Book updated successfully']));
    } else {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to update book']));
    }
}


public function delete_book_delete($id)
{
    if (!$id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid book ID']));
        return;
    }

    $deleted = $this->admin_model->delete('books', ['id' => $id]);

    if ($deleted) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'message' => 'Book deleted successfully']));
    } else {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to delete book']));
    }
}


////
////Sesion Manager
public function sessions_get() {

    // Fetch all sessions from the database
    $result = $this->admin_model->get_result('sessions');

    // Check if any sessions found
    if (empty($result)) {
        $this->output
            ->set_status_header(404)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => false, 'message' => 'No sessions found']));
        return;
    }

    // Return success response with sessions data
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'sessions' => $result]));
}

public function create_session_post() {
    // Load the database
    
    // Get the session name from the POST request
    $data = $this->request->getPost();

    if (!isset($data['name'])) {
        $this->output
            ->set_status_header(400)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => false, 'message' => 'Session name is required']));
        return;
    }

    // Set the status to 0
    $data['status'] = 0;

    // Insert the new session into the database
    $this->admin_model->insert_table('sessions', $data);
    $insert_id = db()->insertID();

    if ($insert_id) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'message' => 'Session created successfully', 'session_id' => $insert_id]));
    } else {
        $this->output
            ->set_status_header(500)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to create session']));
    }
}


public function edit_session_post() {
    // Get the session data from the POST request
    $data = $this->request->getPost();

    // Validation: Ensure 'id', 'name', and 'status' are provided
    if (!isset($data['id']) || !isset($data['name']) || !isset($data['status'])) {
        $this->output
            ->set_status_header(400)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => false, 'message' => 'Session ID, name, and status are required']));
        return;
    }

    // Set updated_at to the current date and time
    $data['updated_at'] = date('Y-m-d H:i:s');

    // Update the session in the database
    $updated = $this->admin_model->update_table('sessions', $data, ['id' => $data['id']]);

    if ($updated) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'message' => 'Session updated successfully']));
    } else {
        $this->output
            ->set_status_header(500)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to update session']));
    }
}


public function delete_session_delete($id) {
    // Load the database
    // Validation: Ensure 'id' is provided
    if (!$id) {
        $this->output
            ->set_status_header(400)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid session ID']));
        return;
    }

    // Delete the session from the database
    $deleted = $this->admin_model->delete('sessions', ['id' => $id]);

    if ($deleted) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'message' => 'Session deleted successfully']));
    } else {
        $this->output
            ->set_status_header(500)
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to delete session']));
    }
}

////
/////Book issue



/////////////////////////////////
//TeAcher Permission//
public function add_teacher_permission_post() {
    // Validate the input data
    if (!$this->request->getPost('teacher_id') || !$this->request->getPost('permission_type') || $this->request->getPost('value') === NULL) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid input data']));
        return;
    }

    $data = [
        'teacher_id' => $this->request->getPost('teacher_id'),
        'permission_type' => $this->request->getPost('permission_type'),
        'value' => (int)$this->request->getPost('value'),  // Cast the value to integer
    ];

    // Check if the permission already exists
    $existing = $this->admin_model->get_row('permissions', [
        'teacher_id' => $data['teacher_id'],
        'permission_type' => $data['permission_type']
    ]);

    if ($existing) {
        // Update the existing permission
        $this->admin_model->update_table('permissions', ['value' => $data['value']], ['teacher_id' => $data['teacher_id'], 'permission_type' => $data['permission_type']]);
    } else {
        // Insert a new permission
        $this->admin_model->insert_table('permissions', $data);
    }

    if (db()->affectedRows() > 0) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => 'Permission updated successfully']));
    } else {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['error' => 'Failed to update permission']));
    }
}

public function teachers_by_class_get($class_id) {
    $class_id = (int)$class_id;
    
    $users = $this->admin_model->get_by_query([
        'select' => '
        users.id as user_id,
        users.name,
        users.email,
        users.role,
        users.address,
        users.phone,
        users.birthday,
        users.gender,
        users.school_id as user_school_id,
        users.status',
        'from' => 'users',
        'join' => [
            ['teachers', 'teachers.user_id = users.id'],
            ['teacher_permissions', 'teacher_permissions.teacher_id = teachers.id'],
            ['classes', 'teacher_permissions.class_id = classes.id']
        ],
        'where' => ['classes.id' => $class_id]
    ]);

    // Append image URL to each user
    foreach ($users as &$user) {
        $image_path = 'uploads/users/' . $user['user_id'] . '.jpg'; // Assuming 'user_id' is the unique identifier for each user
        if (file_exists(FCPATH . $image_path)) {
            $user['image_url'] = base_url($image_path); // Assuming you're using CodeIgniter and 'base_url' is configured
        } else {
            $user['image_url'] = base_url('uploads/users/default.jpg'); // Default image if user image doesn't exist
        }
    }

    // Return the result as a JSON response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($users));
}

public function assign_teacher_permission_to_class_post() {
    // Log the received POST data for debugging
    log_message('debug', 'Received POST data: ' . json_encode($this->request->getPost()));


    // Fetch class_id based on class_name
    $class = $this->admin_model->get_row('classes', ['name' => $this->request->getPost('class_name')]);


    $class_id = $class['id'];

    $data = [
        'teacher_id' => $this->request->getPost('teacher_id'),
        'class_id' => $class_id,
        'section_id' => $this->request->getPost('section_id') ?? null,
        'marks' => (int)$this->request->getPost('marks') ?? 0,
        'assignment' => (int)$this->request->getPost('assignment') ?? 0,
        'attendance' => (int)$this->request->getPost('attendance') ?? 0,
        'online_exam' => (int)$this->request->getPost('online_exam') ?? 0
    ];

    // Log input data for debugging
    log_message('debug', 'Assign Permission Data: ' . json_encode($data));

    // Check if the teacher permission already exists for the class and section
    $where = [
        'teacher_id' => $data['teacher_id'],
        'class_id' => $data['class_id']
    ];
    if ($data['section_id']) {
        $where['section_id'] = $data['section_id'];
    } else {
        $where['section_id'] = null;
    }
    $existing = $this->admin_model->get_row('teacher_permissions', $where);

    if ($existing) {
        // Update the existing teacher permission
        $where = ['teacher_id' => $data['teacher_id'], 'class_id' => $data['class_id']];
        if ($data['section_id']) {
            $where['section_id'] = $data['section_id'];
        } else {
            $where['section_id'] = null;
        }
        $this->admin_model->update_table('teacher_permissions', $data, $where);
    } else {
        // Insert a new teacher permission
        $this->admin_model->insert_table('teacher_permissions', $data);
    }

    if (db()->affectedRows() > 0) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => 'Permission assigned successfully']));
    } else {
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['success' => 'Permission assigned successfully']));
    }
}

public function get_class_id_by_name_get() {
    $class_name = $this->request->getGet('name');

    // Fetch class_id based on class_name
    $class = $this->admin_model->get_row('classes', ['name' => $class_name]);

    if (!$class) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['error' => 'Invalid class name']));
        return;
    }

    $class_id = $class['id'];

    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['class_id' => $class_id]));
}



/////////////////


//Expense API CALL

public function expense_get($school_id) {
  $expenses = $this->get_expenses_by_school($school_id);

  if ($expenses === null) {
      $this->response([
          'status' => false,
          'message' => 'No expenses data found for the specified school_id'
      ], REST_Controller::HTTP_NOT_FOUND);
  } else {
      $formatted_expenses = array();
      foreach ($expenses as $expense) {
        
          $category_name = $this->get_category_name($expense['expense_category_id']);
          
          $formatted_expenses[] = array(
              'id' => $expense['id'],
              'category_id' => $expense['expense_category_id'],
              'name' => $category_name,
              'date' => date('Y-m-d', $expense['date']), 
              'amount' => $expense['amount'],
              'school_id' => $expense['school_id'],
              'session' => $expense['session'],
              'created_at' => date('Y-m-d H:i:s', $expense['created_at']), 
              'updated_at' => date('Y-m-d H:i:s', $expense['updated_at']), 
          );
      }
      $this->response([
          'status' => true,
          'expenses' => $formatted_expenses
      ], REST_Controller::HTTP_OK);
  }
}

public function get_category_name($category_id) {
  $result = $this->admin_model->get_row('expense_categories', ['id' => $category_id]);
  if ($result) {
      return $result['name'];
  } else {
      return null;
  }
}

public function get_expenses_by_school($school_id) {
  $expenses = $this->admin_model->get_where_result('expenses', ['school_id' => $school_id]);
  if (!empty($expenses)) {
      return $expenses;
  } else {
      return null;
  }
}

public function expense_categories_get($school_id, $page = 1, $items_per_page = 10) {
    $offset = ($page - 1) * $items_per_page;

    // Retrieve data from the expense_categories table with pagination
    $categories = $this->admin_model->get_by_query([
        'from' => 'expense_categories',
        'where' => ['school_id' => $school_id],
        'limit' => $items_per_page,
        'offset' => $offset
    ]);

    if (!empty($categories)) {
        // Count total items for pagination
        $total_items = $this->admin_model->count_all('expense_categories', ['school_id' => $school_id]);

        // Return response with data and pagination info
        $response = [
            'status' => true,
            'categories' => $categories,
            'pagination' => [
                'current_page' => $page,
                'items_per_page' => $items_per_page,
                'total_items' => $total_items,
                'total_pages' => ceil($total_items / $items_per_page)
            ]
        ];
        return $this->response->setJSON($response);
    } else {
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => false, 'message' => 'No expense categories found']));
    }
}


public function create_expense_category_post() {
    // Retrieve data from POST request
    $name = $this->request->getPost('name');
    $school_id = $this->request->getPost('school_id');
    $session = $this->request->getPost('session') ? $this->request->getPost('session') : 1;

    // Log the received data for debugging
    log_message('debug', 'Received data: ' . json_encode($this->request->getPost()));

    // Check if the required data is provided
    if (!$name || !$school_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Prepare data to insert
    $expense_data = [
        'name' => $name,
        'school_id' => $school_id,
        'session' => $session
    ];

    // Insert data into the expense_categories table
    $this->admin_model->insert_table('expense_categories', $expense_data);

    // Check if the insert was successful
    if (db()->affectedRows() == 0) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to create expense category']));
        return;
    }

    // Fetch the created expense category to return
    $expense_id = db()->insertID();
    $query = $this->admin_model->get_where('expense_categories', ['id' => $expense_id]);
    $expense = $query;

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'expense_category' => $expense]));
}

public function edit_expense_category_post() {
    // Retrieve data from POST request
    $id = $this->request->getPost('id');
    $name = $this->request->getPost('name');
    $school_id = $this->request->getPost('school_id');
    $session = $this->request->getPost('session') ? $this->request->getPost('session') : 1;

    // Log the received data for debugging
    log_message('debug', 'Received data: ' . json_encode($this->request->getPost()));

    // Check if the required data is provided
    if (!$id || !$name || !$school_id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Prepare data to update
    $expense_data = [
        'name' => $name,
        'school_id' => $school_id,
        'session' => $session
    ];

    // Update data in the expense_categories table
    $this->admin_model->update_table('expense_categories', $expense_data, ['id' => $id]);

    // Check if the update was successful
    if (db()->affectedRows() == 0) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to update expense category']));
        return;
    }

    // Fetch the updated expense category to return
    $query = $this->admin_model->get_where('expense_categories', ['id' => $id]);
    $expense = $query;

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'expense_category' => $expense]));
}

public function delete_expense_category_post() {
    // Retrieve data from POST request
    $id = $this->request->getPost('id');

    // Log the received data for debugging
    log_message('debug', 'Received data: ' . json_encode($this->request->getPost()));

    // Check if the required data is provided
    if (!$id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Delete the expense category from the expense_categories table
    $deleted = $this->admin_model->delete('expense_categories', ['id' => $id]);

    // Check if the delete was successful
    if (!$deleted) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to delete expense category']));
        return;
    }

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'message' => 'Expense category deleted successfully']));
}

public function create_expense_post() {
    // Retrieve and decode JSON data from POST request
    $input_data = json_decode(file_get_contents('php://input'), true);

    $expense_category_id = isset($input_data['expense_category_id']) ? $input_data['expense_category_id'] : null;
    $date = isset($input_data['date']) ? $input_data['date'] : null;
    $amount = isset($input_data['amount']) ? $input_data['amount'] : null;
    $school_id = isset($input_data['school_id']) ? $input_data['school_id'] : null;
    $session = isset($input_data['session']) ? $input_data['session'] : null;
    $created_at = isset($input_data['created_at']) ? $input_data['created_at'] : null;
    $updated_at = isset($input_data['updated_at']) ? $input_data['updated_at'] : null;

    // Log the received data for debugging
    log_message('debug', 'Received data: ' . json_encode($input_data));

    // Check if the required data is provided
    if (!$expense_category_id || !$date || !$amount || !$school_id || !$session) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Prepare data to insert
    $expense_data = [
        'expense_category_id' => $expense_category_id,
        'date' => $date,
        'amount' => $amount,
        'school_id' => $school_id,
        'session' => $session,
        'created_at' => $created_at ? $created_at : date('Y-m-d H:i:s'),
        'updated_at' => $updated_at ? $updated_at : null
    ];

    // Insert data into the expenses table
    $this->admin_model->insert_table('expenses', $expense_data);

    // Check if the insert was successful
    if (db()->affectedRows() == 0) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to create expense']));
        return;
    }

    // Fetch the created expense to return
    $expense_id = db()->insertID();
    $query = $this->admin_model->get_where('expenses', ['id' => $expense_id]);
    $expense = $query;

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'expense' => $expense]));
}

public function get_expenses_get($school_id) {
    // Retrieve data from the expenses table based on school_id
    $query = $this->admin_model->get_where('expenses', ['school_id' => $school_id]);

    if ($query > 0) {
        $expenses = $query;

        // Return success response with the expenses data
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => true, 'expenses' => $expenses]));
    } else {
        // Return response indicating no expenses found
        $this->output
            ->set_status_header(404)
            ->set_output(json_encode(['status' => false, 'message' => 'No expenses found']));
    }
}

public function edit_expense_post() {
    // Retrieve data from POST request
    $id = $this->request->getPost('id');
    $expense_category_id = $this->request->getPost('expense_category_id');
    $date = $this->request->getPost('date');
    $amount = $this->request->getPost('amount');
    $school_id = $this->request->getPost('school_id');
    $session = $this->request->getPost('session');
    $updated_at = $this->request->getPost('updated_at');

    // Log the received data for debugging
    log_message('debug', 'Received data: ' . json_encode($this->request->getPost()));

    // Check if the required data is provided
    if (!$id || !$expense_category_id || !$date || !$amount || !$school_id || !$session) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Prepare data to update
    $expense_data = [
        'expense_category_id' => $expense_category_id,
        'date' => $date,
        'amount' => $amount,
        'school_id' => $school_id,
        'session' => $session,
        'updated_at' => $updated_at ? $updated_at : date('Y-m-d H:i:s')
    ];

    // Update data in the expenses table
    $this->admin_model->update_table('expenses', $expense_data, ['id' => $id]);

    // Check if the update was successful
    if (db()->affectedRows() == 0) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to update expense']));
        return;
    }

    // Fetch the updated expense to return
    $query = $this->admin_model->get_where('expenses', ['id' => $id]);
    $expense = $query;

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'expense' => $expense]));
}

public function delete_expense_post() {
    // Retrieve data from POST request
    $id = $this->request->getPost('id');

    // Log the received data for debugging
    log_message('debug', 'Received data: ' . json_encode($this->request->getPost()));

    // Check if the required data is provided
    if (!$id) {
        $this->output
            ->set_status_header(400)
            ->set_output(json_encode(['status' => false, 'message' => 'Invalid input data']));
        return;
    }

    // Delete the expense from the expenses table
    $deleted = $this->admin_model->delete('expenses', ['id' => $id]);

    // Check if the delete was successful
    if (!$deleted) {
        $this->output
            ->set_status_header(500)
            ->set_output(json_encode(['status' => false, 'message' => 'Failed to delete expense']));
        return;
    }

    // Return success response
    $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode(['status' => true, 'message' => 'Expense deleted successfully']));
}
//End of Expense

//iNVOICES API CALL




public function invoices_get($school_id) {
  // Assuming you have a function to fetch invoices based on school_id
  $invoices = $this->get_invoices_by_school($school_id);

  if ($invoices === null) {
      $this->response([
          'status' => false,
          'message' => 'No invoices found for the specified school_id'
      ], REST_Controller::HTTP_NOT_FOUND);
  } else {
      $formatted_invoices = array();
      foreach ($invoices as $invoice) {
          $formatted_invoices[] = array(
              'id' => $invoice['id'],
              'title' => $invoice['title'],
              'total_amount' => $invoice['total_amount'],
              'class_id' => $invoice['class_id'],
              'class_name' => $invoice['class_name'], 
              'student_id' => $invoice['student_id'],
              'student_name' => $invoice['student_name'],
              'payment_method' => $invoice['payment_method'],
              'paid_amount' => $invoice['paid_amount'],
              'status' => $invoice['status'],
              'school_id' => $invoice['school_id'],
              'session' => $invoice['session'],
              'created_at' => date('Y-m-d H:i:s', strtotime($invoice['created_at'])),
              'updated_at' => date('Y-m-d H:i:s', strtotime($invoice['updated_at']))
          );
      }
      $this->response([
          'status' => true,
          'invoices' => $formatted_invoices
      ], REST_Controller::HTTP_OK);
  }
}

public function get_invoices_by_school($school_id) {
  $sql = "SELECT invoices.*, users.name AS student_name, classes.name AS class_name
          FROM invoices 
          JOIN students ON invoices.student_id = students.id
          JOIN users ON students.user_id = users.id
          JOIN classes ON invoices.class_id = classes.id
          WHERE users.school_id = ?";

  $query = $this->admin_model->get_by_query($sql, [$school_id]);
  if (!empty($query)) {
      return $query;
  } else {
      return null;
  }
}

public function get_invoices_with_user($student_id) {
  $sql = "SELECT invoices.*, users.name AS student_name, classes.name AS class_name
          FROM invoices 
          JOIN students ON invoices.student_id = students.id
          JOIN users ON students.user_id = users.id 
          JOIN classes ON invoices.class_id = classes.id
          WHERE invoices.student_id = ?";

  $query = $this->admin_model->get_by_query($sql, [$student_id]);
  if (!empty($query)) {
      return $query;
  } else {
      return null;
  }
}


public function get_invoices_by_student($student_id) {
  $invoices = $this->admin_model->get_where_result('invoices', ['student_id' => $student_id]);
  if (!empty($invoices)) {
      return $invoices;
  } else {
      return null;
  }
}








/* 
public function login_post() {
    $response = array();
    try {
        // Ensure POST parameters are available
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        
        if (empty($email) || empty($password)) {
            return $this->set_response([
                'status' => 400,
                'message' => 'Email and Password are required',
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

        // Fetch user data based on credentials
        $credential = array(
            'email' => $email,
            'password' => sha1($password)
        );

        // Check credentials in the database
        $query = $this->admin_model->get_where('users', $credential);
        if ($query > 0) {
            $row = $query;

            // Generate token if user is valid
            $response = [
                'status' => 200,
                'message' => 'Logged in successfully',
                'user_id' => $row['id'],
                'name' => $row['name'],
                'email' => $row['email'],
                'role' => strtolower($row['role']),
                'school_id' => $row['school_id'],
                'address' => $row['address'],
                'phone' => $row['phone'],
                'birthday' => date('d-M-Y', $row['birthday']),
                'gender' => strtolower($row['gender']),
                'blood_group' => strtolower($row['blood_group']),
                'validity' => true,
                'token' => $this->tokenHandler->GenerateToken($row),  // Assuming tokenHandler is set up to generate JWT or any other token
            ];

            return $this->set_response($response, REST_Controller::HTTP_OK);
        } else {
            // User not found, return 404 status
            return $this->set_response([
                'status' => 404,
                'message' => 'Invalid email or password',
                'validity' => false,
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    } catch (Exception $e) {
        // Log error and return internal server error response
        log_message('error', 'Error occurred in login_post: ' . $e->getMessage());
        return $this->set_response([
            'status' => 500,
            'message' => 'An error occurred. Please try again later.',
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }
}

 */




    // Login API CALL
    public function login_post() {
        try {
            $userdata = $this->admin_model->login();
    
            if ($userdata['validity'] == 1) {
                $userdata['token'] = $this->tokenHandler->GenerateToken($userdata);
            }
    
            return $this->set_response($userdata, REST_Controller::HTTP_OK);
        } catch (Exception $e) {
            log_message('error', 'Error occurred in login_post: ' . $e->getMessage());
            return $this->set_response([
                'status' => 500,
                'message' => 'An error occurred. Please try again later.'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    } 
    
  // FORGOT PASSWORD API CALL
  public function forgot_password_post() {
    $response = $this->admin_model->forgot_password();
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }


  /*
  * Protected APIs. This APIs will require Authorization.
  **/

  // GET LOGGED IN USERDATA
  public function userdata_get() {
    $response = array();
    $auth_token = $this->request->getGet('auth_token');
    if (!empty($auth_token)) {
      $logged_in_user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->admin_model->get_userdata($logged_in_user_details['user_id']);
    }else{
      $response['status'] = 401;
      $response['message'] = 'Unauthorized';
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }
  public function dashboard_data_get() {
    $response = array();
    $auth_token = $this->request->getGet('auth_token');
    if (!empty($auth_token)) {
      $logged_in_user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->admin_model->get_dashboard_data($logged_in_user_details['user_id']);
    }else{
      $response['status'] = 401;
      $response['message'] = 'Unauthorized';
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  // GET DATA OF SUBJECTS
  public function subjects_get() {
    $response = array();
    $auth_token = $this->request->getGet('auth_token');
    if (!empty($auth_token)) {
      $logged_in_user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->admin_model->get_subjects($logged_in_user_details['user_id']);
    }else{
      $response['status'] = 401;
      $response['message'] = 'Unauthorized';
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  // GET STUDENT LIST BY CLASS ID
  public function students_get() {
    $response = array();
    $auth_token = $this->request->getGet('auth_token');
    if (!empty($auth_token)) {
      $logged_in_user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->admin_model->get_students($logged_in_user_details['user_id']);
    }else{
      $response['status'] = 401;
      $response['message'] = 'Unauthorized';
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }

  // GET STUDENT LIST BY CLASS ID
  public function student_wise_marks_get() {
    $response = array();
    $auth_token = $this->request->getGet('auth_token');
    if (!empty($auth_token)) {
      $logged_in_user_details = json_decode($this->token_data_get($auth_token), true);
      $response = $this->admin_model->get_student_wise_marks($logged_in_user_details['user_id']);
    }else{
      $response['status'] = 401;
      $response['message'] = 'Unauthorized';
    }
    return $this->set_response($response, REST_Controller::HTTP_OK);
  }




















  /////////// Generating Token and put user data into  token ///////////
  public function login_token_get()
  {
    $tokenData['user_id'] = '1';
    $tokenData['role'] = 'admin';
    $tokenData['first_name'] = 'Al';
    $tokenData['last_name'] = 'Mobin';
    $tokenData['phone'] = '+8801921040960';
    $jwtToken = $this->tokenHandler->GenerateToken($tokenData);
    $token = $jwtToken;
    echo json_encode(array('Token'=>$jwtToken));
  }

  //////// get data from token ////////////
  public function get_token_data()
  {
    $received_Token = $this->request->getHeaders('Authorization');
    if (isset($received_Token['Token'])) {
      try
      {
        $jwtData = $this->tokenHandler->DecodeToken($received_Token['Token']);
        return json_encode($jwtData);
      }
      catch (Exception $e)
      {
        http_response_code('401');
        echo json_encode(array( "status" => false, "message" => $e->getMessage()));
        exit;
      }
    }else{
      echo json_encode(array( "status" => false, "message" => "Invalid Token"));
    }
  }

  public function token_data_get($auth_token)
  {
    //$received_Token = $this->request->getHeaders('Authorization');
    if (isset($auth_token)) {
      try
      {

        $jwtData = $this->tokenHandler->DecodeToken($auth_token);
        return json_encode($jwtData);
      }
      catch (Exception $e)
      {
        echo 'catch';
        http_response_code('401');
        echo json_encode(array( "status" => false, "message" => $e->getMessage()));
        exit;
      }
    }else{
      echo json_encode(array( "status" => false, "message" => "Invalid Token"));
    }
  }

  /*
  * eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdGF0dXMiOjIwMCwibWVzc2FnZSI6Ik9LIiwidXNlcl9pZCI6IjI0MSIsIm5hbWUiOiJTdWJoYW4gTWlhIiwiZW1haWwiOiJzdWJoYW5AZXhhbXBsZS5jb20iLCJyb2xlIjoiYWRtaW4iLCJzY2hvb2xfaWQiOiI4IiwiYWRkcmVzcyI6IkJoYWlyYWIgQmF6YXIsIFJham5hZ2FyIiwicGhvbmUiOiIwMTkyMTA0MDk2MCIsImJpcnRoZGF5IjoiMDEtSmFuLTE5NzAiLCJnZW5kZXIiOiJtYWxlIiwiYmxvb2RfZ3JvdXAiOiJhYisiLCJ2YWxpZGl0eSI6dHJ1ZX0.z435NqyIgcVtVNVb7jnN1ewlF2omN6HGxVz23gQZBK8
  **/

  //Partie Quiz (stocker les reponses dans la table question_quiz)


public function submit_quiz_post() {
    // Get and decode JSON input
    $input_data = json_decode(file_get_contents("php://input"), true);

    // Initialize variables
    $submitted_quiz_info = array();
    $container = array();

    // Check for required fields in the input data
    if (!isset($input_data['user_id']) || !isset($input_data['quiz_id']) || !isset($input_data['submitted_answers'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing user ID, quiz ID, or submitted answers'
        ]);
        return;
    }

    // Assign values from JSON input
    $user_id = $input_data['user_id'];
    $quiz_id = $input_data['quiz_id'];
    $submitted_answers = $input_data['submitted_answers'];

    // Fetch quiz questions
    $quiz_questions = $this->admin_model->get_where_result('question', ['quiz_id' => $quiz_id]); // Ensure the table name is correct

    if (empty($quiz_questions)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No questions found for the quiz'
        ]);
        return;
    }

    $total_correct_answers = 0;

    // Process each quiz question
    foreach ($quiz_questions as $quiz_question) {
        $question_id = $quiz_question['id'];
        $correct_answers = json_decode($quiz_question['correct_answers'], true); // Correct answers

        // Get the submitted answers for this question
        $submitted_answers_for_question = isset($submitted_answers[$question_id]) ? $submitted_answers[$question_id] : array();

        // Sort both arrays for accurate comparison
        sort($correct_answers);
        sort($submitted_answers_for_question);

        // Compare submitted answers with correct answers
        $submitted_answer_status = ($correct_answers === $submitted_answers_for_question) ? 1 : 0;

        // Prepare data for insertion/updating in the database
        $response_data = array(
            'user_id' => $user_id,
            'quiz_id' => $quiz_id,
            'question_id' => $question_id,
            'submitted_answers' => json_encode($submitted_answers_for_question),
            'correct_answers' => json_encode($correct_answers),
            'submitted_answer_status' => $submitted_answer_status,
            'date_submitted' => date('Y-m-d H:i:s')  // Add timestamp
        );

        // Check if an entry exists already for this question and user
        $existing = $this->admin_model->get_row('quiz_responses', [
            'user_id' => $user_id,
            'quiz_id' => $quiz_id,
            'question_id' => $question_id
        ]);

        if (!$existing) {
            // Insert new response if it doesn't exist
            $this->admin_model->insert_table('quiz_responses', $response_data);
        } else {
            // Update existing response
            $this->admin_model->update_table('quiz_responses', $response_data, ['user_id' => $user_id, 'quiz_id' => $quiz_id, 'question_id' => $question_id]);
        }

        // Add question info to the response container
        $container = array(
            "question_id" => $question_id,
            "submitted_answer_status" => $submitted_answer_status, // 1 if correct, 0 if not
            "submitted_answers" => json_encode($submitted_answers_for_question),
            "correct_answers" => json_encode($correct_answers),
        );
        array_push($submitted_quiz_info, $container);

        // Count the correct answers
        if ($submitted_answer_status == 1) {
            $total_correct_answers++;
        }
    }

    // Get the related lesson_id by quiz_id
    $lesson_result = $this->admin_model->get_row('lesson', [
        'lesson_type' => 'quiz',
        'id' => $quiz_id
    ]);

    $lesson_id = $lesson_result ? $lesson_result['id'] : null;
    if (!$lesson_id) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No lesson associated with this quiz'
        ]);
        return;
    }

    // Save course progress
    // Get the current user_id from session
    if (!$user_id) {
        log_message('error', 'User ID not found in session.');
        return;
    }

    // Fetch the user's watch history directly from the 'users' table
    $user_details = $this->admin_model->get_row('users', ['id' => $user_id]);

    if (!$user_details) {
        log_message('error', "No user found with user_id: $user_id");
        return;
    }

    $watch_history = isset($user_details['watch_history']) ? $user_details['watch_history'] : '';

    // Initialize watch history array
    $watch_history_array = array();

    // If watch history is empty, add the current lesson and progress
    if (empty($watch_history)) {
        array_push($watch_history_array, array('lesson_id' => $lesson_id, 'progress' => 1));
    } else {
        // Decode existing watch history
        $watch_history_array = json_decode($watch_history, true);
        $found = false;

        // Update progress if lesson_id exists in watch history
        foreach ($watch_history_array as &$lesson) {
            if ($lesson['lesson_id'] == $lesson_id) {
                $lesson['progress'] = 1;
                $found = true;
                break;
            }
        }

        // If lesson_id is not found, add a new entry to the watch history
        if (!$found) {
            array_push($watch_history_array, array('lesson_id' => $lesson_id, 'progress' => 1));
        }
    }

    // Update the user's watch history in the database
    $data['watch_history'] = json_encode($watch_history_array);
    $this->admin_model->update_table('users', $data, ['id' => $user_id]);

    // Prepare the JSON response
    $response = array(
        'status' => 'success',
        'submitted_quiz_info' => $submitted_quiz_info,
        'total_correct_answers' => $total_correct_answers,
        'total_questions' => count($quiz_questions),
        'lesson_id' => $lesson_id,
        'progress' => 1 // Mark progress as complete
    );

    // Return the response as JSON
    echo json_encode($response);
}


public function check_progress_get($user_id, $quiz_id) {
    if (!$user_id || !$quiz_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid user ID or quiz ID']);
        return;
    }
    // Query the database to get user details directly
    $user_details = $this->admin_model->get_row('users', ['id' => $user_id]);

    // Check if the user exists
    if (!$user_details) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        return;
    }

    // Retrieve the watch history from the user details
    $watch_history = $user_details['watch_history'];

    // If no watch history is found, return a message
    if (empty($watch_history)) {
        echo json_encode(['status' => 'success', 'progress' => 0]); // 0 means not started
        return;
    }

    // Decode the watch history to retrieve progress details
    $watch_history_array = json_decode($watch_history, true);

    // Check if the quiz is present in the watch history
    foreach ($watch_history_array as $lesson_progress) {
        if ($lesson_progress['lesson_id'] == $quiz_id) {
            echo json_encode([
                'status' => 'success',
                'progress' => $lesson_progress['progress'] // Return 1 or 0
            ]);
            return;
        }
    }

    // If no progress is found for the quiz, return 0
    echo json_encode(['status' => 'success', 'progress' => 0]);
}




  //register 

public function register_post() {
    $response = array();

    // Retrieve the JSON input directly
    $input_data = json_decode(file_get_contents('php://input'), true);

    // Extract fields from the JSON input
    $name = isset($input_data['name']) ? $input_data['name'] : null;
    $email = isset($input_data['email']) ? $input_data['email'] : null;
    $password = isset($input_data['password']) ? $input_data['password'] : null;
    $role = isset($input_data['role']) ? $input_data['role'] : null;

    // Log the received input for debugging purposes
    log_message('debug', 'Input received: ' . print_r($input_data, true));

    // Input validation
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $response['status'] = 400;
        $response['message'] = 'Invalid input parameters';
        $response['validity'] = false;
        return $this->response->setJSON($response);
    }

    // Check if email already exists
    $existing_user = $this->admin_model->get_where('users', array('email' => $email));
    if ($existing_user) {
        $response['status'] = 409;
        $response['message'] = 'Email already registered';
        $response['validity'] = false;
        return $this->response->setJSON($response);
    }

    // Prepare user data for insertion
    $data = array(
        'name' => $name,
        'email' => $email,
        'password' => sha1($password), // Use bcrypt if needed for better security
        'role' => ucfirst(strtolower($role)), // Capitalize the role name
        'school_id' => isset($input_data['school_id']) ? $input_data['school_id'] : NULL,
        'address' => isset($input_data['address']) ? $input_data['address'] : NULL,
        'phone' => isset($input_data['phone']) ? $input_data['phone'] : NULL,
        'birthday' => isset($input_data['birthday']) ? strtotime($input_data['birthday']) : NULL,
        'gender' => isset($input_data['gender']) ? strtolower($input_data['gender']) : NULL,
 
    );

    // Insert into the 'users' table
    $this->admin_model->insert_table('users', $data);

    // Check if insertion was successful
    if (db()->affectedRows() > 0) {
        $user_id = db()->insertID(); // Get the ID of the inserted user
        $response['status'] = 201;
        $response['message'] = 'Registered Successfully';
        $response['user_id'] = $user_id;
        $response['name'] = $data['name'];
        $response['email'] = $data['email'];
        $response['role'] = strtolower($data['role']);
        $response['school_id'] = $data['school_id'];
        $response['address'] = $data['address'];
        $response['phone'] = $data['phone'];
        $response['birthday'] = $data['birthday'] ? date('d-M-Y', $data['birthday']) : null;
        $response['gender'] = $data['gender'];

        $response['validity'] = true;

        return $this->response->setJSON($response);
    } else {
        $response['status'] = 500;
        $response['message'] = 'Registration Failed';
        $response['validity'] = false;
        return $this->response->setJSON($response);
    }
}

  
  
  

  //Forget password

public function send_reset_link_api_post() {
    // Read the raw POST data as JSON
    $postData = json_decode(file_get_contents("php://input"), true);

    // Get the email from the decoded JSON data
    $email = isset($postData['email']) ? strtolower($postData['email']) : '';

    // Check if the email field is empty
    if (empty($email)) {
        $response = array(
            'status' => 'error',
            'message' => 'Email is required'
        );
        log_message('error', 'Email field is empty');
        $this->response->setStatusCode(400); // Set HTTP status code to 400 (Bad Request)
        echo json_encode($response);
        return;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = array(
            'status' => 'error',
            'message' => 'Invalid email format'
        );
        log_message('error', 'Invalid email format: ' . $email);
        $this->response->setStatusCode(400); // Set HTTP status code to 400 (Bad Request)
        echo json_encode($response);
        return;
    }

    // Query the database to check if the email exists
    $query = $this->admin_model->get_where('users', array('email' => $email));

    if ($query > 0) {
        $user = $query;

        // Generate a random 6-digit validation code
        $validation_code = mt_rand(100000, 999999);

        // Set the expiration time (46 seconds from now)
        $expires_at = date("Y-m-d H:i:s", strtotime('+46 seconds'));

        // Update the database with the validation code and expiration time
        try {
            $this->admin_model->update_table('users', array(
                'reset_token' => $validation_code,
                'reset_expires_at' => $expires_at
            ), ['id' => $user['id']]);
        } catch (Exception $e) {
            log_message('error', 'Database update failed: ' . $e->getMessage());
            $this->response->setStatusCode(500);
            echo json_encode(array('status' => 'error', 'message' => 'Database update failed.'));
            return;
        }

        // Prepare the email template
        $email_message = '
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Validation Code</title>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
                h2 { color: #5d0ea8; text-align: center; }
                p { margin: 10px 0; color: #555; text-align: center; }
                .code-box { text-align: center; margin: 20px 0; font-size: 24px; font-weight: bold; color: #5d0ea8; letter-spacing: 5px; }
                .footer { margin-top: 30px; font-size: 12px; color: #777; text-align: center; }
                .footer p { margin: 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>Code de Validation</h2>
                <p>Bonjour ' . ucfirst($user['name']) . ',</p>
                <p>Voici votre code de validation:</p>
                <div class="code-box">' . $validation_code . '</div>
                <p>Ce code est valide pendant 46 secondes.</p>
                <p>Si vous n\'avez pas demandé ce code, veuillez ignorer cet e-mail. Votre compte reste sécurisé.</p>
                <p>Pour toute assistance, veuillez contacter notre équipe support à l’adresse suivante : <a href="mailto:' . get_settings('system_email') . '">' . get_settings('system_email') . '</a>.</p>
                <div class="footer">
                    <p>&copy; ' . date("Y") . ' Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>';

        // Send the email
        try {
            if ($this->email_model->send_email_with_validation_code($email_message, $user['email'])) {
                $response = array('status' => 'success', 'message' => 'Validation code sent successfully to ' . $email);
                log_message('info', 'Validation code sent to: ' . $email);
                $this->response->setStatusCode(200); // Set HTTP status code to 200 (OK)
            } else {
                throw new Exception('Email sending failed.');
            }
        } catch (Exception $e) {
            log_message('error', 'Email sending failed: ' . $e->getMessage());
            $response = array('status' => 'error', 'message' => 'Failed to send validation code. Please try again later.');
            $this->response->setStatusCode(500); // Set HTTP status code to 500 (Internal Server Error)
        }

    } else {
        $response = array('status' => 'error', 'message' => 'Email not found');
        log_message('error', 'Email not found: ' . $email);
        $this->response->setStatusCode(404); // Set HTTP status code to 404 (Not Found)
    }

    echo json_encode($response);
}
public function resend_code_api_post() {
    // Read the raw POST data as JSON
    $postData = json_decode(file_get_contents("php://input"), true);

    // Get the email from the decoded JSON data
    $email = isset($postData['email']) ? strtolower($postData['email']) : '';

    // Check if the email field is empty
    if (empty($email)) {
        $response = array('status' => 'error', 'message' => 'Email is required');
        echo json_encode($response);
        return;
    }

    // Query the database to check if the email exists
    $query = $this->admin_model->get_where('users', array('email' => $email));

    if ($query > 0) {
        $user = $query;

        // Generate a new random 6-digit validation code
        $validation_code = mt_rand(100000, 999999);
        $expires_at = date("Y-m-d H:i:s", strtotime('+46 seconds'));

        // Update the database with the new validation code and expiration time
        $this->admin_model->update_table('users', array(
            'reset_token' => $validation_code,
            'reset_expires_at' => $expires_at
        ), ['id' => $user['id']]);
        // Prepare the email template
        $email_message = '
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Validation Code</title>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
                h2 { color: #5d0ea8; text-align: center; }
                p { margin: 10px 0; color: #555; text-align: center; }
                .code-box { text-align: center; margin: 20px 0; font-size: 24px; font-weight: bold; color: #5d0ea8; letter-spacing: 5px; }
                .footer { margin-top: 30px; font-size: 12px; color: #777; text-align: center; }
                .footer p { margin: 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>Code de Validation</h2>
                <p>Bonjour ' . ucfirst($user['name']) . ',</p>
                <p>Voici votre code de validation:</p>
                <div class="code-box">' . $validation_code . '</div>
                <p>Ce code est valide pendant 46 secondes.</p>
                <p>Si vous n\'avez pas demandé ce code, veuillez ignorer cet e-mail. Votre compte reste sécurisé.</p>
                <p>Pour toute assistance, veuillez contacter notre équipe support à l’adresse suivante : <a href="mailto:' . get_settings('system_email') . '">' . get_settings('system_email') . '</a>.</p>
                <div class="footer">
                    <p>&copy; ' . date("Y") . ' Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>';

        // Send the email with validation code
        if ($this->email_model->send_email_with_validation_code($email_message, $user['email'])) {
            $response = array('status' => 'success', 'message' => 'Validation code resent successfully to ' . $email);
        } else {
            $response = array('status' => 'error', 'message' => 'Failed to resend validation code. Please try again later.');
        }

        echo json_encode($response);
    } else {
        $response = array('status' => 'error', 'message' => 'Email not found');
        echo json_encode($response);
    }
}
public function verify_code_api_post() {
    // Read the raw POST data as JSON
    $postData = json_decode(file_get_contents("php://input"), true);

    // Get the email and the code from the decoded JSON data
    $email = isset($postData['email']) ? strtolower($postData['email']) : '';
    $code = isset($postData['code']) ? $postData['code'] : '';

    // Check if the email or code field is empty
    if (empty($email) || empty($code)) {
        $response = array('status' => 'error', 'message' => 'Email and code are required');
        echo json_encode($response);
        return;
    }

    // Query the database to check if the email exists
    $query = $this->admin_model->get_where('users', array('email' => $email));

    if ($query > 0) {
        $user = $query;

        // Check if the code matches and has not expired
        if ($user['reset_token'] == $code && strtotime($user['reset_expires_at']) > time()) {
            // Code is valid
            $response = array('status' => 'success', 'message' => 'Code verified successfully');
        } else {
            // Code is invalid or expired
            $response = array('status' => 'error', 'message' => 'Invalid or expired code');
        }
    } else {
        $response = array('status' => 'error', 'message' => 'Email not found');
    }

    echo json_encode($response);
}
public function getUserIdByEmail_post() {
    // Get the posted email
    $postData = json_decode(file_get_contents("php://input"), true);
    $email = isset($postData['email']) ? strtolower($postData['email']) : '';

    // Check if the email is empty
    if (empty($email)) {
        $response = array('status' => 'error', 'message' => 'Email is required');
        echo json_encode($response);
        return;
    }

    // Query the database to get the user_id by email
    $user = $this->admin_model->get_row('users', ['email' => $email]);

    if ($user) {
        // User found
        $response = array('status' => 'success', 'user_id' => $user['id']);
    } else {
        // User not found
        $response = array('status' => 'error', 'message' => 'User not found with this email');
    }

    echo json_encode($response);
}
public function update_Password_post() {
    $postData = json_decode(file_get_contents("php://input"), true);
    $user_id = isset($postData['user_id']) ? $postData['user_id'] : null;
    $new_password = isset($postData['new_password']) ? $postData['new_password'] : null;

    if (empty($user_id) || empty($new_password)) {
        $response = array('status' => 'error', 'message' => 'User ID or new password is missing');
        echo json_encode($response);
        return;
    }

    $encrypted_password = sha1($new_password);
    $update = $this->admin_model->update_table('users', array('password' => $encrypted_password), ['id' => $user_id]);

    // Log SQL query and error for debugging
    log_message('info', 'SQL Query: ' . db()->getLastQuery());
    if (db()->error()) {
        log_message('error', 'DB Error: ' . db()->error()['message']);
    }

    if ($update) {
        $response = array('status' => 'success', 'message' => 'Password updated successfully');
    } else {
        $response = array('status' => 'error', 'message' => 'Failed to update password');
    }

    echo json_encode($response);
}






























//SMTP API 

public function set_smtp_settings_post()
{
    // Fetch the POST data
    $smtp_host     = $this->request->getPost('smtp_host');
    $smtp_port     = $this->request->getPost('smtp_port');
    $smtp_user     = $this->request->getPost('smtp_username');
    $smtp_pass     = $this->request->getPost('smtp_password');
    $smtp_protocol = $this->request->getPost('smtp_protocol');
    $smtp_secure   = $this->request->getPost('smtp_crypto'); // For SSL or TLS
    $mail_sender   = $this->request->getPost('mail_sender'); // New mail_sender field

    // Log the POST data for debugging
    log_message('debug', 'SMTP Settings POST data: ' . print_r($this->request->getPost(), true));

    // Validate input
    $this->form_validation->set_rules('smtp_host', 'SMTP Host', 'required');
    $this->form_validation->set_rules('smtp_port', 'SMTP Port', 'required|integer');
    $this->form_validation->set_rules('smtp_username', 'SMTP User', 'required|valid_email');
    $this->form_validation->set_rules('smtp_password', 'SMTP Password', 'required');
    $this->form_validation->set_rules('smtp_protocol', 'SMTP Protocol', 'required');
    $this->form_validation->set_rules('smtp_crypto', 'SMTP Secure', 'in_list[ssl,tls]');
    $this->form_validation->set_rules('mail_sender', 'Mail Sender', 'required'); // New validation rule

    // Check if validation passed
    if ($this->form_validation->run() === FALSE) {
        // Log validation errors
        log_message('error', 'Validation errors: ' . validation_errors());
        // Return validation errors
        echo json_encode(array('status' => 'error', 'message' => validation_errors()));
        return;
    }

    // Check if an SMTP setting already exists
    $existing_setting = $this->admin_model->get_row('smtp_settings');

    // Log existing setting check
    log_message('debug', 'Existing SMTP settings: ' . print_r($existing_setting, true));

    // Prepare the data array for insertion/updation
    $data = array(
        'smtp_host'     => $smtp_host,
        'smtp_port'     => $smtp_port,
        'smtp_username' => $smtp_user,
        'smtp_password' => $smtp_pass,
        'smtp_protocol' => $smtp_protocol,
        'smtp_crypto'   => $smtp_secure,
        'mail_sender'   => $mail_sender, // Add mail_sender to the data array
    );

    // If a setting exists, update it; otherwise, insert a new one
    if ($existing_setting) {
        $this->admin_model->update_table('smtp_settings', $data, ['id' => $existing_setting['id']]);
        $message = 'SMTP settings updated successfully';
        log_message('info', $message);
    } else {
        $this->admin_model->insert_table('smtp_settings', $data);
        $message = 'SMTP settings saved successfully';
        log_message('info', $message);
    }

    // Return success message
    echo json_encode(array('status' => 'success', 'message' => $message));
}



public function get_smtp_settings_get() {
    // Call the helper function to fetch SMTP settings
    $smtp_settings = $this->get_smtp_settings();

    if (!empty($smtp_settings)) {
        // If settings are found, return them with a success status
        $this->response([
            'status' => true,
            'data' => $smtp_settings
        ], 200); // HTTP 200 OK
    } else {
        // If no settings are found, return an error
        log_message('error', 'SMTP settings could not be retrieved or are empty');
        $this->response([
            'status' => false,
            'message' => 'SMTP settings not found'
        ], 404); // HTTP 404 Not Found
    }
}

// Helper function to fetch SMTP settings
private function get_smtp_settings() {
    return get_smtp_settings();
}

public function get_alls_smtp_settings_get() {
    // Call the helper function to fetch all SMTP settings
    $smtp_settings = $this->get_all_smtp_settings();

    if (!empty($smtp_settings)) {
        // If settings are found, return them with a success status
        $this->response([
            'status' => true,
            'data' => $smtp_settings
        ], 200); // HTTP 200 OK
    } else {
        // If no settings are found, return an error
        log_message('error', 'SMTP settings could not be retrieved or are empty');
        $this->response([
            'status' => false,
            'message' => 'SMTP settings not found'
        ], 404); // HTTP 404 Not Found
    }
}
// Helper function to fetch all SMTP settings
private function get_all_smtp_settings() {
    return get_all_smtp_settings();
}
















public function set_payment_settings_post()
{
    // Fetch the POST data
    $school_id = $this->request->getPost('school_id');
    $payment_method = $this->request->getPost('payment_method');
    $payment_gateway = $this->request->getPost('payment_gateway');
    $api_key = $this->request->getPost('api_key');
    $enabled = $this->request->getPost('enabled');

    // Log the POST data for debugging
    log_message('debug', 'Payment Settings POST data: ' . print_r($this->request->getPost(), true));

    // Validate input
    $this->form_validation->set_rules('school_id', 'School ID', 'required|integer');
    $this->form_validation->set_rules('payment_method', 'Payment Method', 'required');
    $this->form_validation->set_rules('payment_gateway', 'Payment Gateway', 'required');
    $this->form_validation->set_rules('api_key', 'API Key', 'required');
    $this->form_validation->set_rules('enabled', 'Enabled', 'required|in_list[0,1]');

    // Check if validation passed
    if ($this->form_validation->run() === FALSE) {
        // Log validation errors
        log_message('error', 'Validation errors: ' . validation_errors());
        // Return validation errors
        echo json_encode(array('status' => 'error', 'message' => validation_errors()));
        return;
    }

    // Prepare data for payment settings
    $data = array(
        'payment_method' => $payment_method,
        'payment_gateway' => $payment_gateway,
        'api_key' => $api_key,
        'enabled' => $enabled,
        'school_id' => $school_id // Ensure school_id is included
    );

    // Log the prepared data
    log_message('debug', 'Prepared data for update: ' . print_r($data, true));

    // Handle Stripe settings
    if ($payment_gateway == 'Stripe') {
        $stripe_data = json_encode(array(
            'stripe_active' => $enabled ? 'yes' : 'no',
            'stripe_mode' => $this->request->getPost('stripe_mode'),
            'stripe_test_secret_key' => $this->request->getPost('stripe_test_secret_key'),
            'stripe_test_public_key' => $this->request->getPost('stripe_test_public_key'),
            'stripe_live_secret_key' => $this->request->getPost('stripe_live_secret_key'),
            'stripe_live_public_key' => $this->request->getPost('stripe_live_public_key'),
            'stripe_currency' => $this->request->getPost('stripe_currency')
        ));

        // Log Stripe data
        log_message('debug', 'Stripe data: ' . $stripe_data);

        // Update or insert Stripe settings
        $existing_stripe_setting = $this->admin_model->get_row('payment_settings', [
            'school_id' => $school_id,
            'key' => 'stripe_settings'
        ]);

        if ($existing_stripe_setting) {
            // Update existing Stripe settings
            $this->admin_model->update_table('payment_settings', array('value' => $stripe_data), ['id' => $existing_stripe_setting['id']]);
            log_message('debug', 'Updated Stripe settings for school_id: ' . $school_id);
        } else {
            // Insert new Stripe settings
            $this->admin_model->insert_table('payment_settings', array('school_id' => $school_id, 'key' => 'stripe_settings', 'value' => $stripe_data));
            log_message('debug', 'Inserted new Stripe settings for school_id: ' . $school_id);
        }
    }

    // Handle PayPal settings
    if ($payment_gateway == 'PayPal') {
        $paypal_data = json_encode(array(
            'paypal_active' => $enabled ? 'yes' : 'no',
            'paypal_mode' => $this->request->getPost('paypal_mode'),
            'paypal_client_id_sandbox' => $this->request->getPost('paypal_client_id_sandbox'),
            'paypal_client_id_production' => $this->request->getPost('paypal_client_id_production'),
            'paypal_currency' => $this->request->getPost('paypal_currency')
        ));

        // Log PayPal data
        log_message('debug', 'PayPal data: ' . $paypal_data);

        // Update or insert PayPal settings
        $existing_paypal_setting = $this->admin_model->get_row('payment_settings', [
            'school_id' => $school_id,
            'key' => 'paypal_settings'
        ]);

        if ($existing_paypal_setting) {
            // Update existing PayPal settings
            $this->admin_model->update_table('payment_settings', array('value' => $paypal_data), ['id' => $existing_paypal_setting['id']]);
            log_message('debug', 'Updated PayPal settings for school_id: ' . $school_id);
        } else {
            // Insert new PayPal settings
            $this->admin_model->insert_table('payment_settings', array('school_id' => $school_id, 'key' => 'paypal_settings', 'value' => $paypal_data));
            log_message('debug', 'Inserted new PayPal settings for school_id: ' . $school_id);
        }
    }

    // Return success message
    echo json_encode(array('status' => 'success', 'message' => 'Payment settings updated successfully'));
}



public function update_system_currency_post() {
    // Fetch the POST data
    $school_id = $this->request->getPost('school_id');
    $system_currency = $this->request->getPost('system_currency');
    $currency_position = $this->request->getPost('currency_position');

    // Validate input
    $this->form_validation->set_rules('school_id', 'School ID', 'required|integer');
    $this->form_validation->set_rules('system_currency', 'System Currency', 'required');
    $this->form_validation->set_rules('currency_position', 'Currency Position', 'required');

    if ($this->form_validation->run() === FALSE) {
        echo json_encode(array('status' => 'error', 'message' => validation_errors()));
        return;
    }

    // Prepare data for system currency update
    $currency_data = json_encode(array(
        'system_currency' => $system_currency,
        'currency_position' => $currency_position
    ));

    // Update or insert system currency settings
    $existing_currency = $this->admin_model->get_row('payment_settings', [
        'school_id' => $school_id,
        'key' => 'system_currency'
    ]);

    if ($existing_currency) {
        // Update existing currency settings
        $this->admin_model->update_table('payment_settings', array('value' => $currency_data), ['id' => $existing_currency['id']]);
    } else {
        // Insert new currency settings
        $this->admin_model->insert_table('payment_settings', array('school_id' => $school_id, 'key' => 'system_currency', 'value' => $currency_data));
    }

    echo json_encode(array('status' => 'success', 'message' => 'System currency updated successfully'));
}


public function update_paypal_settings_post() {
    // Fetch the POST data
    $school_id = $this->request->getPost('school_id');
    $paypal_active = $this->request->getPost('paypal_active');
    $paypal_mode = $this->request->getPost('paypal_mode');
    $paypal_client_id_sandbox = $this->request->getPost('paypal_client_id_sandbox');
    $paypal_client_id_production = $this->request->getPost('paypal_client_id_production');
    $paypal_currency = $this->request->getPost('paypal_currency');

    // Validate input
    $this->form_validation->set_rules('school_id', 'School ID', 'required|integer');
    $this->form_validation->set_rules('paypal_active', 'PayPal Active', 'required|in_list[yes,no]');
    $this->form_validation->set_rules('paypal_mode', 'PayPal Mode', 'required|in_list[production,sandbox]');
    $this->form_validation->set_rules('paypal_client_id_sandbox', 'Sandbox Client ID', 'required');
    $this->form_validation->set_rules('paypal_client_id_production', 'Production Client ID', 'required');
    $this->form_validation->set_rules('paypal_currency', 'PayPal Currency', 'required');

    if ($this->form_validation->run() === FALSE) {
        echo json_encode(array('status' => 'error', 'message' => validation_errors()));
        return;
    }

    // Prepare data for PayPal settings
    $paypal_data = json_encode(array(
        'paypal_active' => $paypal_active,
        'paypal_mode' => $paypal_mode,
        'paypal_client_id_sandbox' => $paypal_client_id_sandbox,
        'paypal_client_id_production' => $paypal_client_id_production,
        'paypal_currency' => $paypal_currency
    ));

    // Update or insert PayPal settings
    $existing_paypal_setting = $this->admin_model->get_row('payment_settings', [
        'school_id' => $school_id,
        'key' => 'paypal_settings'
    ]);

    if ($existing_paypal_setting) {
        $this->admin_model->update_table('payment_settings', array('value' => $paypal_data), ['id' => $existing_paypal_setting['id']]);
    } else {
        $this->admin_model->insert_table('payment_settings', array('school_id' => $school_id, 'key' => 'paypal_settings', 'value' => $paypal_data));
    }

    echo json_encode(array('status' => 'success', 'message' => 'PayPal settings updated successfully'));
}

public function update_stripe_settings_post() {
    // Fetch the POST data
    $school_id = $this->request->getPost('school_id');
    $stripe_active = $this->request->getPost('stripe_active');
    $stripe_mode = $this->request->getPost('stripe_mode');
    $stripe_test_secret_key = $this->request->getPost('stripe_test_secret_key');
    $stripe_test_public_key = $this->request->getPost('stripe_test_public_key');
    $stripe_live_secret_key = $this->request->getPost('stripe_live_secret_key');
    $stripe_live_public_key = $this->request->getPost('stripe_live_public_key');
    $stripe_currency = $this->request->getPost('stripe_currency');

    // Validate input
    $this->form_validation->set_rules('school_id', 'School ID', 'required|integer');
    $this->form_validation->set_rules('stripe_active', 'Stripe Active', 'required|in_list[yes,no]');
    $this->form_validation->set_rules('stripe_mode', 'Stripe Mode', 'required|in_list[on,off]');
    $this->form_validation->set_rules('stripe_test_secret_key', 'Test Secret Key', 'required');
    $this->form_validation->set_rules('stripe_test_public_key', 'Test Public Key', 'required');
    $this->form_validation->set_rules('stripe_live_secret_key', 'Live Secret Key', 'required');
    $this->form_validation->set_rules('stripe_live_public_key', 'Live Public Key', 'required');
    $this->form_validation->set_rules('stripe_currency', 'Stripe Currency', 'required');

    if ($this->form_validation->run() === FALSE) {
        echo json_encode(array('status' => 'error', 'message' => validation_errors()));
        return;
    }

    // Prepare data for Stripe settings
    $stripe_data = json_encode(array(
        'stripe_active' => $stripe_active,
        'stripe_mode' => $stripe_mode,
        'stripe_test_secret_key' => $stripe_test_secret_key,
        'stripe_test_public_key' => $stripe_test_public_key,
        'stripe_live_secret_key' => $stripe_live_secret_key,
        'stripe_live_public_key' => $stripe_live_public_key,
        'stripe_currency' => $stripe_currency
    ));

    // Update or insert Stripe settings
    $existing_stripe_setting = $this->admin_model->get_row('payment_settings', [
        'school_id' => $school_id,
        'key' => 'stripe_settings'
    ]);

    if ($existing_stripe_setting) {
        $this->admin_model->update_table('payment_settings', array('value' => $stripe_data), ['id' => $existing_stripe_setting['id']]);
    } else {
        $this->admin_model->insert_table('payment_settings', array('school_id' => $school_id, 'key' => 'stripe_settings', 'value' => $stripe_data));
    }

    echo json_encode(array('status' => 'success', 'message' => 'Stripe settings updated successfully'));
}



public function school_settings_get($school_id = null)
{
    // Check if the school ID is provided in the request, if not, return an error response
    if ($school_id === null) {
        echo json_encode(array(
            'status' => false,
            'message' => 'School ID is required'
        ));
        return;
    }

    // Manually fetch the school settings from the database

    // Query to fetch the school settings based on the provided school ID
    $school_settings = $this->admin_model->get_school_by_id($school_id);

    // Check if a record was found
    if (!empty($school_settings)) {

        // Dynamically build the image path based on the school's ID
        $image_path = 'uploads/schools/' . $school_settings['id'] . '.jpg';

        // Check if the image exists
        if (file_exists($image_path)) {
            $school_settings['image_url'] = base_url($image_path); // Set the image URL if the image exists
        } else {
            $school_settings['image_url'] = null; // Or set a default image path if needed
        }

        // Send a success response with the school settings data
        echo json_encode(array(
            'status' => true,
            'data' => $school_settings
        ));
    } else {
        // Send an error response if no school is found
        echo json_encode(array(
            'status' => false,
            'message' => 'No school found with the given ID'
        ));
    }
}



public function school_settings_update_post($school_id = null)
{
    // Check if the school ID is provided in the request, if not, return an error response
    if ($school_id === null) {
        echo json_encode(array(
            'status' => false,
            'message' => 'School ID is required'
        ));
        return;
    }

    // Load database if not already loaded

    // Validate the input data
    $this->form_validation->set_rules('school_name', 'School Name', 'required');
    $this->form_validation->set_rules('description', 'Description', 'required');
    $this->form_validation->set_rules('phone', 'Phone', 'required');
    $this->form_validation->set_rules('access', 'Access', 'required|in_list[0,1]');
    $this->form_validation->set_rules('address', 'Address', 'required');
    $this->form_validation->set_rules('category', 'Category', 'required');

    // Check if validation fails
    if ($this->form_validation->run() === FALSE) {
        echo json_encode(array(
            'status' => false,
            'message' => validation_errors()
        ));
        return;
    }

    // Prepare data for updating
    $update_data = array(
        'name' => htmlspecialchars($this->request->getPost('school_name')),
        'description' => htmlspecialchars($this->request->getPost('description')),
        'phone' => htmlspecialchars($this->request->getPost('phone')),
        'access' => htmlspecialchars($this->request->getPost('access')),
        'address' => htmlspecialchars($this->request->getPost('address')),
        'category' => htmlspecialchars($this->request->getPost('category'))
    );

    // Check if the school ID exists in the database
    $school = $this->admin_model->get_row('schools', ['id' => $school_id]);

    if ($school) {
        // Update the school settings
        $this->admin_model->update_table('schools', $update_data, ['id' => $school_id]);

        // Send a success response
        echo json_encode(array(
            'status' => true,
            'message' => 'School settings updated successfully'
        ));
    } else {
        // Error response if no school is found
        echo json_encode(array(
            'status' => false,
            'message' => 'No school found with the given ID'
        ));
    }
}


public function system_settings_get($school_id = null)
{
    // Check if the school_id is provided, if not, return an error response
    if ($school_id === null) {
        $response = array(
            'status' => false,
            'message' => 'School ID is required'
        );
        echo json_encode($response);
        return;
    }

    // Fetch system settings directly using a query
    $system_query = $this->admin_model->get_where('settings', array('id' => 1));
    $system_settings = $system_query;

    // Fetch school settings based on the provided school_id
    $school_settings = $this->admin_model->get_school_by_id($school_id);

    // Prepare the response
    if (!empty($system_settings) && !empty($school_settings)) {
        $response = array(
            'status' => true,
            'data' => array(
                'system_settings' => $system_settings,
       
            ),
            'message' => 'System  settings fetched successfully'
        );
    } else {
        $response = array(
            'status' => false,
            'message' => 'Settings not found'
        );
    }

    // Return the response as JSON
    echo json_encode($response);
}



/* public function update_system_settings_post()
{
    // Validate input: ensure necessary fields are provided
    // TODO: Replace with service: Config\Services::form_validation();
    $this->form_validation->set_rules('system_name', 'System Name', 'required');
    $this->form_validation->set_rules('system_email', 'System Email', 'required|valid_email');
    $this->form_validation->set_rules('system_title', 'System Title', 'required');
    $this->form_validation->set_rules('phone', 'Phone', 'required');
    
    // Check if the form validation passed
    if ($this->form_validation->run() == FALSE) {
        // Validation failed, return error message
        $response = array(
            'status' => false,
            'message' => validation_errors()
        );
        echo json_encode($response);
        return;
    }

    // Collect data from POST request
    $data = array(
        'system_name' => htmlspecialchars($this->request->getPost('system_name')),
        'system_email' => htmlspecialchars($this->request->getPost('system_email')),
        'system_title' => htmlspecialchars($this->request->getPost('system_title')),
        'phone' => htmlspecialchars($this->request->getPost('phone')),
        'purchase_code' => htmlspecialchars($this->request->getPost('purchase_code')),
        'address' => htmlspecialchars($this->request->getPost('address')),
        'fax' => htmlspecialchars($this->request->getPost('fax')),
        'footer_text' => htmlspecialchars($this->request->getPost('footer_text')),
        'footer_link' => htmlspecialchars($this->request->getPost('footer_link')),
        'timezone' => htmlspecialchars($this->request->getPost('timezone')),
        'youtube_api_key' => htmlspecialchars($this->request->getPost('youtube_api_key')),
        'vimeo_api_key' => htmlspecialchars($this->request->getPost('vimeo_api_key'))
    );

    // Update the system settings in the database (assuming id = 1 for system settings)
    $update = $this->admin_model->update_table('settings', $data, ['id' => 1]);

    // Check if the update was successful
    if ($update) {
        $response = array(
            'status' => true,
            'message' => 'System settings updated successfully'
        );
    } else {
        $response = array(
            'status' => false,
            'message' => 'Failed to update system settings'
        );
    }

    // Return the response as JSON
    echo json_encode($response);
} */

public function update_system_settings_post($school_id = null)
{
    // Check if the school_id is provided
    if ($school_id === null) {
        $response = array(
            'status' => false,
            'message' => 'School ID is required'
        );
        echo json_encode($response);
        return;
    }

    // Validate input: ensure necessary fields are provided for system settings
    // TODO: Replace with service: Config\Services::form_validation();
    $this->form_validation->set_rules('system_email', 'System Email', 'required|valid_email');
    $this->form_validation->set_rules('system_title', 'System Title', 'required');
    $this->form_validation->set_rules('phone', 'Phone', 'required');

    // Check if the form validation passed
    if ($this->form_validation->run() == FALSE) {
        // Validation failed, return error message
        $response = array(
            'status' => false,
            'message' => validation_errors()
        );
        echo json_encode($response);
        return;
    }

    // Collect data from POST request (specific to system settings)
    $data = array(
        'system_email' => htmlspecialchars($this->request->getPost('system_email')),  // Valid email
        'system_title' => htmlspecialchars($this->request->getPost('system_title')),  // Title
        'phone' => htmlspecialchars($this->request->getPost('phone')),                // Phone
        'purchase_code' => htmlspecialchars($this->request->getPost('purchase_code')),
        'address' => htmlspecialchars($this->request->getPost('address')),
        'fax' => htmlspecialchars($this->request->getPost('fax')),
        'footer_text' => htmlspecialchars($this->request->getPost('footer_text')),
        'footer_link' => htmlspecialchars($this->request->getPost('footer_link')),
        'timezone' => htmlspecialchars($this->request->getPost('timezone')),
        'youtube_api_key' => htmlspecialchars($this->request->getPost('youtube_api_key')),
        'vimeo_api_key' => htmlspecialchars($this->request->getPost('vimeo_api_key'))
    );

    // Update the system settings for the given school_id
    $update = $this->admin_model->update_table('settings', $data, ['school_id' => $school_id]);

    // Check if the update was successful
    if ($update) {
        $response = array(
            'status' => true,
            'message' => 'System settings updated successfully'
        );
    } else {
        $response = array(
            'status' => false,
            'message' => 'Failed to update system settings'
        );
    }

    // Return the response as JSON
    echo json_encode($response);
}




public function system_logo_get($school_id = null)
{
    // Check if school_id is provided, if not return error response
    if ($school_id === null) {
        $response = array(
            'status' => false,
            'message' => 'School ID is required'
        );
        echo json_encode($response);
        return;
    }

    // Fetch the logos based on the school ID
    $response = array(
        'status' => true,
        'dark_logo' => $this->settings_model->get_logo_dark($school_id),
        'light_logo' => $this->settings_model->get_logo_light($school_id),
        'small_logo' => $this->settings_model->get_logo_small($school_id),
        'favicon' => $this->settings_model->get_favicon($school_id),
        'notification' => get_phrase('logo_fetched_successfully')
    );
    
    echo json_encode($response);
}




public function update_system_logo_post($school_id = null)
{
    // Check if school_id is provided, if not return error response
    if ($school_id === null) {
        $response = array(
            'status' => false,
            'message' => 'School ID is required'
        );
        echo json_encode($response);
        return;
    }

    // Check if logos are provided via POST (using $_FILES)
    if (!isset($_FILES['dark_logo']) && !isset($_FILES['light_logo']) && !isset($_FILES['favicon'])) {
        $response = array(
            'status' => false,
            'message' => 'No logo files provided'
        );
        echo json_encode($response);
        return;
    }

    // Update dark logo if provided
    if ($_FILES['dark_logo']['name'] != "") {
        $this->update_logo($school_id, 'dark_logo', 'logo-dark');
    }

    // Update light logo if provided
    if ($_FILES['light_logo']['name'] != "") {
        $this->update_logo($school_id, 'light_logo', 'logo-light');
    }

    // Update favicon if provided
    if ($_FILES['favicon']['name'] != "") {
        $this->update_logo($school_id, 'favicon', 'favicon');
    }

    // Return a success response
    $response = array(
        'status' => true,
        'message' => 'Logos updated successfully'
    );
    echo json_encode($response);
}

private function update_logo($school_id, $file_key, $file_name)
{
    // Directory where the logo will be stored
    $upload_path = 'uploads/schools/' . $school_id . '/';
    
    // Ensure directory exists
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0777, true);
    }

    // Get the file extension
    $file_extension = pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION);
    
    // Set the full path with filename (e.g. logo-dark.png, favicon.png)
    $file_path = $upload_path . $file_name . '.' . $file_extension;

    // Check file type (e.g., PNG, SVG)
    $file_type = mime_content_type($_FILES[$file_key]['tmp_name']);
    $svg_type = 'image/svg+xml';

    // Delete the old file if exists
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // Move the new uploaded file
    move_uploaded_file($_FILES[$file_key]['tmp_name'], $file_path);
}













public function language_get()
{
    // Get all languages
    $languages = $this->get_all_languages();

    // Check if languages are found
    if (!empty($languages)) {
        $response = array(
            'status' => true,
            'languages' => $languages,
            'message' => 'Languages fetched successfully'
        );
    } else {
        $response = array(
            'status' => false,
            'message' => 'No languages found'
        );
    }

    // Return the response as JSON
    echo json_encode($response);
}

// Function to get all languages
private function get_all_languages()
{
    // Assuming your language files are stored as JSON in the 'language' folder
    $language_files = array();
    $all_files = $this->get_list_of_language_files();

    foreach ($all_files as $file) {
        $info = pathinfo($file);
        if (isset($info['extension']) && strtolower($info['extension']) == 'json') {
            $file_name = explode('.json', $info['basename']);
            array_push($language_files, $file_name[0]);
        }
    }
    return $language_files;
}

// Helper function to retrieve all language files
private function get_list_of_language_files($dir = APPPATH . '/language', &$results = array())
{
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
            $this->get_list_of_language_files($path, $results);
            $results[] = $path;
        }
    }
    return $results;
}



public function selected_language_get($school_id = null)
{
    // Check if school_id is provided
    if ($school_id === null) {
        $response = array(
            'status' => false,
            'message' => 'School ID is required'
        );
        echo json_encode($response);
        return;
    }

    // Get the language for the specific school from the database
    $setting = $this->admin_model->get_row('settings', ['school_id' => $school_id]);

    // Check if language is found
    if ($setting) {
        $language = $setting['language'];
        $response = array(
            'status' => true,
            'language' => $language,
            'message' => 'Language fetched successfully'
        );
    } else {
        $response = array(
            'status' => false,
            'message' => 'No language found for this school'
        );
    }

    // Return the response as JSON
    echo json_encode($response);
}

public function add_language_post($school_id = null)
{
    // Check if the language is provided
    if (!$this->request->getPost('language')) {
        log_message('error', 'Language not provided.');
        $response = array(
            'status' => false,
            'message' => 'Language is required'
        );
        echo json_encode($response);
        return;
    }

    // Fetch the posted language
    $new_language = $this->request->getPost('language');

    // Log received inputs for debugging
    log_message('debug', 'Received language: ' . $new_language);

    // Fetch the current available languages
    $current_languages = $this->get_all_languages();

    // Check if the language already exists
    if (!in_array($new_language, $current_languages)) {
        // Create a new JSON file for the language
        $new_file_path = APPPATH . "language/{$new_language}.json";

        // Create a default structure for the new language (you can modify the default structure as needed)
        $default_phrases = array(
            "welcome" => "Welcome",
            "name" => "Name",
            "option" => "Option"
            // Add more default phrases as needed
        );

        // Save the default phrases to the new JSON file
        if (file_put_contents($new_file_path, json_encode($default_phrases, JSON_PRETTY_PRINT))) {
            $response = array(
                'status' => true,
                'message' => 'Language added successfully'
            );
            log_message('debug', 'Language added successfully.');
        } else {
            $response = array(
                'status' => false,
                'message' => 'Failed to create the language file'
            );
            log_message('error', 'Failed to create the language file.');
        }
    } else {
        $response = array(
            'status' => false,
            'message' => 'Language already exists'
        );
        log_message('debug', 'Language already exists in the list.');
    }

    // Log the final response
    log_message('debug', 'Final Response: ' . json_encode($response));

    // Return the response as JSON
    echo json_encode($response);
}



public function update_language_post($school_id = null)
{
    // Check if school_id and language are provided
    if ($school_id === null || !$this->request->getPost('language')) {
        $response = array(
            'status' => false,
            'message' => 'School ID and language are required'
        );
        echo json_encode($response);
        return;
    }

    // Fetch the posted language
    $language = $this->request->getPost('language');

    // Update the language for the specific school in the settings table
    $data = array(
        'language' => $language
    );

    // Update the language where school_id matches
    $this->admin_model->update_table('settings', $data, ['school_id' => $school_id]);

    // Check if update was successful
    if (db()->affectedRows() > 0) {
        $response = array(
            'status' => true,
            'message' => 'Language updated successfully'
        );
    } else {
        $response = array(
            'status' => false,
            'message' => 'Failed to update language'
        );
    }

    // Return the response as JSON
    echo json_encode($response);
}

public function phrases_get($language = 'english')
{
    // Define the path to the language-specific JSON file
    $file_path = APPPATH . "language/{$language}.json";

    // Log the file path being used
    log_message('debug', 'Attempting to load phrases from: ' . $file_path);

    // Check if the file exists
    if (file_exists($file_path)) {
        // Get the file contents
        $phrases = file_get_contents($file_path);

        // Log the content of the file
        log_message('debug', 'Phrases file content: ' . $phrases);

        // Convert JSON content to an array
        $phrases_array = json_decode($phrases, true);

        // Check if the JSON data is valid
        if ($phrases_array !== null) {
            $response = array(
                'status' => true,
                'phrases' => $phrases_array,
                'message' => 'Phrases fetched successfully'
            );
        } else {
            $response = array(
                'status' => false,
                'message' => 'Error decoding the phrases JSON file'
            );
        }
    } else {
        // Log the missing file error
        log_message('error', 'Language phrases file not found: ' . $file_path);
        $response = array(
            'status' => false,
            'message' => 'Language phrases file not found'
        );
    }

    // Log the response before returning it
    log_message('debug', 'Response: ' . json_encode($response));

    // Return the response as JSON
    echo json_encode($response);
}



public function website_settings_get($school_id = null) {
    // Check if a valid school_id is provided
    if (empty($school_id)) {
        $response = array('status' => false, 'message' => 'School ID is required');
        echo json_encode($response); // Return the response as JSON
        return;
    }

    // Query the settings table to retrieve settings for the provided school_id
    $settings = $this->admin_model->get_row('settings', ['school_id' => $school_id]);

    // Check if the query returns any data
    if ($settings) {
        // Return the row as a JSON response
        $response = array('status' => true, 'settings' => $settings);
        echo json_encode($response); // Return the response as JSON
    } else {
        // Return an empty array if no data found
        $response = array('status' => false, 'message' => 'No settings found for this school');
        echo json_encode($response); // Return the response as JSON
    }
}


public function general_settings_get() {
    // Fetch general settings from the 'frontend_settings' table
    $settings = $this->admin_model->get_row('frontend_settings');

    // Check if any data is returned
    if ($settings) {

        // Decode the social_links field (assuming it's stored as JSON)
        $social_links = json_decode($settings['social_links'], true);

        // Prepare the response
        $response = array(
            'status' => true,
            'settings' => array(
                'website_title' => $settings['website_title'],
                'social_links' => $social_links, // Social links already in array format
                'homepage_note' => array(
                    'title' => $settings['homepage_note_title'],
                    'description' => strip_tags($settings['homepage_note_description']) // Strip HTML tags
                ),
                'copyright_text' => $settings['copyright_text'],
                'logos' => array(
                    'header_logo' => base_url('uploads/logos/' . $settings['header_logo']),
                    'footer_logo' => base_url('uploads/logos/' . $settings['footer_logo'])
                )
            )
        );
    } else {
        // If no settings found, return an error message
        $response = array(
            'status' => false,
            'message' => 'No general settings found'
        );
    }

    // Output the response as JSON
    echo json_encode($response);
}

public function other_settings_get()
{
    // Initialize an array to store the settings
    $settings = array();

    // Get recaptcha_status
    $recaptcha_status = $this->admin_model->get_row('common_settings', ['type' => 'recaptcha_status']);
    if ($recaptcha_status) {
        $settings['recaptcha_status'] = $recaptcha_status['description'];
    }

    // Get recaptcha_sitekey
    $recaptcha_sitekey = $this->admin_model->get_row('common_settings', ['type' => 'recaptcha_sitekey']);
    if ($recaptcha_sitekey) {
        $settings['recaptcha_sitekey'] = $recaptcha_sitekey['description'];
    }

    // Get recaptcha_secretkey
    $recaptcha_secretkey = $this->admin_model->get_row('common_settings', ['type' => 'recaptcha_secretkey']);
    if ($recaptcha_secretkey) {
        $settings['recaptcha_secretkey'] = $recaptcha_secretkey['description'];
    }

    if (!empty($settings)) {
        // Prepare the response
        $response = array(
            'status' => true,
            'settings' => array(
                'recaptcha_status' => $settings['recaptcha_status'],
                'recaptcha_sitekey' => $settings['recaptcha_sitekey'],
                'recaptcha_secretkey' => $settings['recaptcha_secretkey'],
                'login_page_banner' => base_url('assets/backend/images/bg-auth.jpg') // Assuming the banner is fixed
            )
        );
    } else {
        // If no settings found, return an error message
        $response = array(
            'status' => false,
            'message' => 'No settings found'
        );
    }

    // Output the response as JSON
    echo json_encode($response);
}

public function other_settings_update_post()
{
    try {
        // Initialize an array to store the updated settings
        $updated_settings = array();

        // Fetch the values from the POST request
        $recaptcha_status = $this->request->getPost('recaptcha_status', TRUE);
        $recaptcha_sitekey = $this->request->getPost('recaptcha_sitekey', TRUE);
        $recaptcha_secretkey = $this->request->getPost('recaptcha_secretkey', TRUE);
        
        // Check if the image is uploaded
        if (isset($_FILES['login_page_banner']) && $_FILES['login_page_banner']['size'] > 0) {
            // Load upload library
            $config['upload_path'] = './assets/backend/images/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = 'bg-auth';
            $config['overwrite'] = TRUE;
            
            // TODO: Replace with Config\Services::upload(null, config);
            
            if (!$this->upload->do_upload('login_page_banner')) {
                $this->response([
                    'status' => FALSE,
                    'message' => $this->upload->display_errors()
                ], REST_Controller::HTTP_BAD_REQUEST);
                return;
            } else {
                $uploaded_image = $this->upload->data();
                $login_page_banner = base_url('assets/backend/images/' . $uploaded_image['file_name']);
            }
        }

        // Update recaptcha_status if provided
        if ($recaptcha_status) {
            $this->admin_model->update_table('common_settings', array('description' => $recaptcha_status), ['type' => 'recaptcha_status']);
            $updated_settings['recaptcha_status'] = $recaptcha_status;
        }

        // Update recaptcha_sitekey if provided
        if ($recaptcha_sitekey) {
            $this->admin_model->update_table('common_settings', array('description' => $recaptcha_sitekey), ['type' => 'recaptcha_sitekey']);
            $updated_settings['recaptcha_sitekey'] = $recaptcha_sitekey;
        }

        // Update recaptcha_secretkey if provided
        if ($recaptcha_secretkey) {
            $this->admin_model->update_table('common_settings', array('description' => $recaptcha_secretkey), ['type' => 'recaptcha_secretkey']);
            $updated_settings['recaptcha_secretkey'] = $recaptcha_secretkey;
        }

        // Update the login_page_banner if the image was uploaded
        if (isset($login_page_banner)) {
            $updated_settings['login_page_banner'] = $login_page_banner;
        }

        if (!empty($updated_settings)) {
            // If updates were made, return success
            $this->response([
                'status' => TRUE,
                'message' => 'Settings updated successfully',
                'updated_settings' => $updated_settings
            ], REST_Controller::HTTP_OK);
        } else {
            // If no updates were made, return an error
            $this->response([
                'status' => FALSE,
                'message' => 'No updates made'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    } catch (Exception $e) {
        // Log the exception
        log_message('error', $e->getMessage());

        // Return error response
        $this->response([
            'status' => FALSE,
            'message' => 'An error occurred while updating the settings'
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }
}





public function terms_and_conditions_settings_get() {
    try {
        // Load the settings from the frontend_settings table
        $settings = $this->admin_model->get_row('frontend_settings');

        if ($settings) {
            $this->response([
                'status' => TRUE,
                'terms_conditions' => $data->terms_conditions
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'No terms and conditions found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    } catch (Exception $e) {
        // Log the exception
        log_message('error', $e->getMessage());

        $this->response([
            'status' => FALSE,
            'message' => 'An error occurred while fetching terms and conditions'
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }
}

public function upterms_and_conditions_settings_post()
{
    try {
        // Fetch the new terms and conditions from the POST request
        $terms_conditions = $this->request->getPost('terms_conditions', TRUE);

        if (!$terms_conditions) {
            // Return error if no terms are provided
            $this->response([
                'status' => FALSE,
                'message' => 'Terms and conditions cannot be empty'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Update the terms_conditions in the frontend_settings table
        $update_data = array('terms_conditions' => $terms_conditions);
        $updated = $this->admin_model->update_table('frontend_settings', $update_data, ['id' => 1]);

        if ($updated) {
            // Return success response
            $this->response([
                'status' => TRUE,
                'message' => 'Terms and conditions updated successfully'
            ], REST_Controller::HTTP_OK);
        } else {
            // Return error if update failed
            $this->response([
                'status' => FALSE,
                'message' => 'Failed to update terms and conditions'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    } catch (Exception $e) {
        // Log the exception
        log_message('error', $e->getMessage());

        // Return error response
        $this->response([
            'status' => FALSE,
            'message' => 'An error occurred while updating terms and conditions'
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }
}

public function privacy_policy_settings_get()
{
    try {
        // Sélectionner la politique de confidentialité depuis la table frontend_settings
        $settings = $this->admin_model->get_row('frontend_settings');

        if ($settings) {
            $this->response([
                'status' => TRUE,
                'privacy_policy' => $settings['privacy_policy']
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'No privacy policy found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    } catch (Exception $e) {
        // Logger l'exception
        log_message('error', $e->getMessage());

        $this->response([
            'status' => FALSE,
            'message' => 'An error occurred while fetching privacy policy'
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }
}

public function privacy_policy_settings_update_post()
{
    try {
        // Récupérer la nouvelle politique de confidentialité depuis la requête POST
        $privacy_policy = $this->request->getPost('privacy_policy', TRUE);

        if (!$privacy_policy) {
            // Retourner une erreur si la politique de confidentialité est vide
            $this->response([
                'status' => FALSE,
                'message' => 'Privacy policy cannot be empty'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Mettre à jour la politique de confidentialité dans la table frontend_settings
        $update_data = array('privacy_policy' => $privacy_policy);
        $updated = $this->admin_model->update_table('frontend_settings', $update_data, ['id' => 1]);

        if ($updated) {
            // Retourner un succès
            $this->response([
                'status' => TRUE,
                'message' => 'Privacy policy updated successfully'
            ], REST_Controller::HTTP_OK);
        } else {
            // Retourner une erreur si la mise à jour échoue
            $this->response([
                'status' => FALSE,
                'message' => 'Failed to update privacy policy'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }
    } catch (Exception $e) {
        // Logger l'exception
        log_message('error', $e->getMessage());

        // Retourner une réponse d'erreur
        $this->response([
            'status' => FALSE,
            'message' => 'An error occurred while updating privacy policy'
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }
}


public function create_gallery_post()
{
    try {
        // Fetch data from the POST request
        $title = $this->request->getPost('title', TRUE);
        $description = $this->request->getPost('description', TRUE);
        $date_added = $this->request->getPost('date_added', TRUE);
        $show_on_website = $this->request->getPost('show_on_website', TRUE);

        // Validate required fields
        if (empty($title) || empty($description) || empty($date_added)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Title, Description, and Date are required.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Handle image upload
        $config['upload_path'] = './uploads/images/gallery_cover/'; // Relative path to make it work on different environments
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['file_name'] = time(); // Create a unique file name using the timestamp
        $config['overwrite'] = TRUE;

        // TODO: Replace with Config\Services::upload(null, config);

        if (!$this->upload->do_upload('cover_image')) {
            $error = $this->upload->display_errors();
            log_message('error', 'Image upload error: ' . $error); // Log the error
            $this->response([
                'status' => FALSE,
                'message' => $error
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        } else {
            // Get the uploaded image data
            $uploaded_image = $this->upload->data();
            $cover_image_url = base_url('uploads/images/gallery_cover/' . $uploaded_image['file_name']);
        }

        // Insert gallery data into the database
        $gallery_data = [
            'title' => $title,
            'description' => $description,
            'date_added' => date('Y-m-d', strtotime($date_added)),
            'show_on_website' => $show_on_website,
            'image' => isset($cover_image_url) ? $cover_image_url : NULL // Make sure to use 'image' field as per your database schema
        ];

        log_message('info', 'Gallery data: ' . json_encode($gallery_data)); // Log gallery data for debugging

        $this->admin_model->insert_table('frontend_gallery', $gallery_data); // Insert into the correct table

        // Check if the insert was successful
        if (db()->affectedRows() > 0) {
            $this->response([
                'status' => TRUE,
                'message' => 'Gallery created successfully.'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Failed to create gallery.'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    } catch (Exception $e) {
        // Log error and return a response
        log_message('error', 'Error while creating gallery: ' . $e->getMessage());
        $this->response([
            'status' => FALSE,
            'message' => 'An error occurred while creating the gallery.'
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }
}


public function galleries_get()
{
    try {
        // Get the school_id from the request
        $school_id = $this->request->getGet('school_id', TRUE);

        // Validate that the school_id is provided
        if (empty($school_id)) {
            $this->response([
                'status' => FALSE,
                'message' => 'school_id is required.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Fetch all gallery entries from the 'frontend_gallery' table for the given school_id
        $galleries = $this->admin_model->get_where_result('frontend_gallery', ['school_id' => $school_id]);

        if (!empty($galleries)) {

            // Append full URLs for cover images
            foreach ($galleries as &$gallery) {
                if (!empty($gallery['image'])) {
                    $gallery['image'] = base_url('uploads/images/gallery_cover/' . basename($gallery['image']));
                } else {
                    $gallery['image'] = base_url('uploads/images/default_cover.jpg'); // Fallback image
                }
            }

            // Return galleries with status
            $this->response([
                'status' => TRUE,
                'galleries' => $galleries
            ], REST_Controller::HTTP_OK);
        } else {
            // Return if no galleries found for the given school_id
            $this->response([
                'status' => FALSE,
                'message' => 'No galleries found for this school.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    } catch (Exception $e) {
        // Log error and return a response
        log_message('error', 'An error occurred while fetching galleries: ' . $e->getMessage());

        $this->response([
            'status' => FALSE,
            'message' => 'An error occurred while fetching the galleries.'
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }
}

public function gallery_images_by_id_get()
{
    try {
        // Get the frontend_gallery_id from the request
        $frontend_gallery_id = $this->request->getGet('frontend_gallery_id', TRUE);

        // Validate that frontend_gallery_id is provided
        if (empty($frontend_gallery_id)) {
            $this->response([
                'status' => FALSE,
                'message' => 'frontend_gallery_id is required.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Fetch the images associated with the provided frontend_gallery_id
        $images = $this->admin_model->get_where_result('frontend_gallery_image', ['frontend_gallery_id' => $frontend_gallery_id]);

        if (!empty($images)) {

            // Append full URLs for the images in the gallery_images directory
            foreach ($images as &$image) {
                if (!empty($image['image'])) {
                    $image['image'] = base_url('uploads/images/gallery_images/' . basename($image['image']));
                } else {
                    $image['image'] = base_url('uploads/images/default_image.jpg'); // Fallback image
                }
            }

            // Return the images
            $this->response([
                'status' => TRUE,
                'images' => $images
            ], REST_Controller::HTTP_OK);
        } else {
            // If no images found for the given frontend_gallery_id
            $this->response([
                'status' => FALSE,
                'message' => 'No images found for this gallery.'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    } catch (Exception $e) {
        // Log the error and return a response
        log_message('error', 'An error occurred while fetching gallery images: ' . $e->getMessage());

        $this->response([
            'status' => FALSE,
            'message' => 'An error occurred while fetching the gallery images.'
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }
}


public function gallery_by_id_delete()
{
    try {
        // Get the frontend_gallery_id from the DELETE request
        $frontend_gallery_id = $this->request->getGet('frontend_gallery_id', TRUE);

        // Validate that frontend_gallery_id is provided
        if (empty($frontend_gallery_id)) {
            $this->response([
                'status' => FALSE,
                'message' => 'frontend_gallery_id is required.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Fetch the gallery to ensure it exists
        $gallery = $this->db_helper_model->get('frontend_gallery', ['frontend_gallery_id' => $frontend_gallery_id], 'row_array');
        if (empty($gallery)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Gallery not found.'
            ], REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        // Fetch the associated images
        $images = $this->db_helper_model->get('frontend_gallery_image', ['frontend_gallery_id' => $frontend_gallery_id], 'result_array');

        // Delete each associated image from the file system and database
        foreach ($images as $image) {
            $image_path = './uploads/images/gallery_images/' . basename($image['image']);
            if (file_exists($image_path)) {
                unlink($image_path); // Delete the image file
            }
        }

        // Delete images from the database
        $this->admin_model->delete('frontend_gallery_image', ['frontend_gallery_id' => $frontend_gallery_id]);

        // Delete the gallery from the database
        $deleted = $this->admin_model->delete('frontend_gallery', ['frontend_gallery_id' => $frontend_gallery_id]);

        // Check if the delete was successful
        if ($deleted) {
            $this->response([
                'status' => TRUE,
                'message' => 'Gallery and associated images deleted successfully.'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Failed to delete gallery.'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    } catch (Exception $e) {
        // Log the error and return a response
        log_message('error', 'An error occurred while deleting the gallery: ' . $e->getMessage());

        $this->response([
            'status' => FALSE,
            'message' => 'An error occurred while deleting the gallery.'
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }
}


public function gallery_image_by_id_delete()
{
    try {
        // Get the frontend_gallery_image_id from the DELETE request
        $frontend_gallery_image_id = $this->request->getGet('frontend_gallery_image_id', TRUE);

        // Validate that frontend_gallery_image_id is provided
        if (empty($frontend_gallery_image_id)) {
            $this->response([
                'status' => FALSE,
                'message' => 'frontend_gallery_image_id is required.'
            ], REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        // Fetch the image to ensure it exists
        $image = $this->db_helper_model->get('frontend_gallery_image', ['frontend_gallery_image_id' => $frontend_gallery_image_id], 'row_array');
        if (empty($image)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Gallery image not found.'
            ], REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        // Delete the image file from the file system
        $image_path = './uploads/images/gallery_images/' . basename($image['image']);
        if (file_exists($image_path)) {
            unlink($image_path); // Delete the image file
        }

        // Delete the image record from the database
        $deleted = $this->admin_model->delete('frontend_gallery_image', ['frontend_gallery_image_id' => $frontend_gallery_image_id]);

        // Check if the delete was successful
        if ($deleted) {
            $this->response([
                'status' => TRUE,
                'message' => 'Gallery image deleted successfully.'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Failed to delete gallery image.'
            ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
        }

    } catch (Exception $e) {
        // Log the error and return a response
        log_message('error', 'An error occurred while deleting the gallery image: ' . $e->getMessage());

        $this->response([
            'status' => FALSE,
            'message' => 'An error occurred while deleting the gallery image.'
        ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
    }
}



public function homepage_slider_get()
{
    // Retrieve the slider images data from frontend settings
    $slider_images_json = get_frontend_settings('slider_images');
    
    // Decode the JSON to an array
    $slider_images = json_decode($slider_images_json);

    // Check if there are slider images available
    if (!empty($slider_images)) {
        // Prepare the response with status and data
        foreach ($slider_images as $key => $slider) {
            $slider->image = base_url('uploads/images/slider/' . basename($slider->image));
        }

        $response = [
            'status' => true,
            'slider_images' => $slider_images
        ];
    } else {
        // In case no slider images are found
        $response = [
            'status' => false,
            'message' => 'No homepage slider information found'
        ];
    }

    // Return the slider info in the response
    $this->response($response, REST_Controller::HTTP_OK);
}

public function update_sliders_post()
{
    // Initialize an array to hold the slider images data
    $slider_images_array = [];

    // Assume we're receiving data for 3 sliders
    $number_of_sliders = 3; // You can adjust this based on your requirement
    for ($i = 0; $i < $number_of_sliders; $i++) {
        // Get title, description, and existing image from POST request
        $title = $this->request->getPost('title_' . $i);
        $description = $this->request->getPost('description_' . $i);
        $existing_image = $this->request->getPost('existing_image_' . $i);

        // Initialize slider data
        $slider_data = [
            'title' => $title,
            'description' => $description
        ];

        // Process uploaded image if available
        $field_name = 'slider_image_' . $i;
        if (isset($_FILES[$field_name]) && $_FILES[$field_name]['error'] == UPLOAD_ERR_OK) {
            $uploaded_file = $_FILES[$field_name]['tmp_name'];
            $file_name = uniqid() . basename($_FILES[$field_name]['name']);
            $file_path = 'uploads/images/slider/' . $file_name;

            // Ensure the directory exists
            if (!file_exists('uploads/images/slider/')) {
                mkdir('uploads/images/slider/', 0755, true);
            }

            // Move uploaded file to uploads directory
            if (move_uploaded_file($uploaded_file, $file_path)) {
                // Store the new image URL in the array
                $slider_data['image'] = base_url('uploads/images/slider/' . $file_name);
            } else {
                // If file upload fails, return an error response
                $response = [
                    'status' => false,
                    'message' => 'Failed to upload image for slider ' . ($i + 1)
                ];
                return $this->response($response, REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            // Use the existing image if no new image is uploaded
            $slider_data['image'] = $existing_image;
        }

        // Add this slider's data to the array
        $slider_images_array[] = $slider_data;
    }

    // Encode the updated slider images data back to JSON format
    $updated_slider_images_json = json_encode($slider_images_array);

    // Save the updated slider images to the frontend settings
    $this->admin_model->update_table('frontend_settings', ['value' => $updated_slider_images_json], ['setting_key' => 'slider_images']);

    if (db()->affectedRows() > 0) {
        // If the update was successful
        $response = [
            'status' => true,
            'message' => 'Sliders updated successfully.'
        ];
    } else {
        // If no changes were made or the update failed
        $response = [
            'status' => false,
            'message' => 'Failed to update sliders or no changes were made.'
        ];
    }

    // Return the response
    $this->response($response, REST_Controller::HTTP_OK);
}








public function create_noticeboard_post() {
    $data = json_decode($this->request->getRawInput(), true);

    // Validate the required fields
    if (empty($data['notice_title']) || empty($data['date']) || empty($data['notice']) || empty($data['school_id'])) {
        $response = array('status' => false, 'message' => 'All fields (notice_title, date, notice, and school_id) are required');
        return $this->response->setJSON($response);
    }

    // CSRF protection
    $data[csrf_token()] = csrf_hash();

    // Use the date provided by the user in the required format
    $formattedDate = date('m/d/Y H:i:s', strtotime($data['date'])); // Adjust format if necessary

    // Check if there's an existing session for the same day
    $existingSessionQuery = $this->admin_model->get_by_query("
        SELECT session 
        FROM noticeboard 
        WHERE DATE_FORMAT(STR_TO_DATE(date, '%m/%d/%Y %H:%i:%s'), '%m/%d/%Y') = ?
        AND school_id = ?
        LIMIT 1
    ", [date('m/d/Y', strtotime($formattedDate)), $data['school_id']]);

    $existingSession = !empty($existingSessionQuery) ? $existingSessionQuery[0] : null;
    $session = !empty($existingSession) ? $existingSession['session'] : (isset($data['session']) ? $data['session'] : 1);

    // Prepare data for insertion
    $noticeData = array(
        'notice_title' => $data['notice_title'],
        'date' => $formattedDate,  // Use the formatted date from user input
        'notice' => $data['notice'],
        'show_on_website' => isset($data['show_on_website']) ? $data['show_on_website'] : 0,
        'school_id' => $data['school_id'],
        'session' => $session
    );

    // Check if there is an image file
    if (isset($_FILES['notice_photo']) && $_FILES['notice_photo']['error'] == 0) {
        $config['upload_path'] = './uploads/notices/';
        $config['allowed_types'] = 'jpg|png|jpeg';
        $config['file_name'] = time() . '_' . $_FILES['notice_photo']['name'];

        // TODO: Replace with Config\Services::upload(null, config);

        if ($this->upload->do_upload('notice_photo')) {
            $uploadData = $this->upload->data();
            $noticeData['image'] = $uploadData['file_name'];
        } else {
            $response = array('status' => false, 'message' => $this->upload->display_errors());
            return $this->response->setJSON($response);
        }
    }

    // Insert notice data into the database
    $this->admin_model->insert_table('noticeboard', $noticeData);

    if (db()->affectedRows() > 0) {
        $response = array('status' => true, 'message' => 'Notice created successfully');
    } else {
        $response = array('status' => false, 'message' => 'Failed to create notice');
    }

    return $this->response->setJSON($response);
}


public function noticeboard_get($id = null) {
    if ($id) {
        $notice = $this->admin_model->get_where('noticeboard', array('id' => $id));
    } else {
        $notice = $this->admin_model->get_result('noticeboard');
    }

    if ($notice) {
        $response = array('status' => true, 'data' => $notice);
    } else {
        $response = array('status' => false, 'message' => 'No notice found');
    }

    return $this->response->setJSON($response);
}


public function fetch_all_notices_get() {
    // Fetch all records from the noticeboard table
    $notices = $this->admin_model->get_result('noticeboard');

    // Check if any notices were found
    if ($notices) {
        $response = array('status' => true, 'data' => $notices);
    } else {
        $response = array('status' => false, 'message' => 'No notices found');
    }

    // Return the response in JSON format
    return $this->response->setJSON($response);
}


public function update_noticeboard_post() {
    $data = json_decode($this->request->getRawInput(), true);

    // Validate required fields
    if (empty($data['notice_id']) || empty($data['notice_title']) || empty($data['date']) || empty($data['notice']) || empty($data['school_id'])) {
        $response = array('status' => false, 'message' => 'All fields (notice_id, notice_title, date, notice, and school_id) are required');
        return $this->response->setJSON($response);
    }

    // CSRF protection
    $data[csrf_token()] = csrf_hash();

    // Format the provided date
    $formattedDate = date('m/d/Y H:i:s', strtotime($data['date']));

    // Check if notice exists
    $existingNotice = $this->admin_model->get_where('noticeboard', array('id' => $data['notice_id'], 'school_id' => $data['school_id']));
    if (!$existingNotice) {
        $response = array('status' => false, 'message' => 'Notice not found');
        return $this->response->setJSON($response);
    }

    // Prepare data for update
    $noticeData = array(
        'notice_title' => $data['notice_title'],
        'date' => $formattedDate,
        'notice' => $data['notice'],
        'show_on_website' => isset($data['show_on_website']) ? $data['show_on_website'] : $existingNotice['show_on_website'],
        'school_id' => $data['school_id'],
        'session' => isset($data['session']) ? $data['session'] : $existingNotice['session']
    );

    // Check if there is a new image file
    if (isset($_FILES['notice_photo']) && $_FILES['notice_photo']['error'] == 0) {
        $config['upload_path'] = './uploads/notices/';
        $config['allowed_types'] = 'jpg|png|jpeg';
        $config['file_name'] = time() . '_' . $_FILES['notice_photo']['name'];

        // TODO: Replace with Config\Services::upload(null, config);

        if ($this->upload->do_upload('notice_photo')) {
            // Delete the old image if it exists
            if (!empty($existingNotice['image']) && file_exists('./uploads/notices/' . $existingNotice['image'])) {
                unlink('./uploads/notices/' . $existingNotice['image']);
            }

            $uploadData = $this->upload->data();
            $noticeData['image'] = $uploadData['file_name'];
        } else {
            $response = array('status' => false, 'message' => $this->upload->display_errors());
            return $this->response->setJSON($response);
        }
    }

    // Update notice data in the database
    $this->admin_model->update_table('noticeboard', $noticeData, ['id' => $data['notice_id']]);

    if (db()->affectedRows() > 0) {
        $response = array('status' => true, 'message' => 'Notice updated successfully');
    } else {
        $response = array('status' => false, 'message' => 'Failed to update notice or no changes detected');
    }

    return $this->response->setJSON($response);
}


public function noticeboard_delete_post($id) {
    $deleted = $this->admin_model->delete('noticeboard', ['id' => $id]);

    if ($deleted) {
        $response = array('status' => true, 'message' => 'Notice deleted successfully');
    } else {
        $response = array('status' => false, 'message' => 'Failed to delete notice');
    }

    return $this->response->setJSON($response);
}

public function filter_notices_get($year = null, $month = null, $day = null) {
    $conditions = [];
    $params = [];
    
    // Check if all date components are provided for daily filtering
    if ($year && $month && $day) {
        $conditions[] = "DATE_FORMAT(STR_TO_DATE(date, '%m/%d/%Y %H:%i:%s'), '%Y-%m-%d') = ?";
        $params[] = "$year-$month-$day";
    } 
    // Check if only year and month are provided for monthly filtering
    elseif ($year && $month) {
        $conditions[] = "YEAR(STR_TO_DATE(date, '%m/%d/%Y %H:%i:%s')) = ?";
        $params[] = $year;
        $conditions[] = "MONTH(STR_TO_DATE(date, '%m/%d/%Y %H:%i:%s')) = ?";
        $params[] = $month;
    } 
    // Check if only year is provided for yearly filtering
    elseif ($year) {
        $conditions[] = "YEAR(STR_TO_DATE(date, '%m/%d/%Y %H:%i:%s')) = ?";
        $params[] = $year;
    } 
    // If no date components are provided, return an error
    else {
        $response = array('status' => false, 'message' => 'Please specify at least a year for filtering');
        return $this->response->setJSON($response);
    }

    // Execute the query
    $sql = "SELECT * FROM noticeboard";
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    $notices = $this->admin_model->get_by_query($sql, $params);

    // Prepare the response
    if ($notices) {
        $response = array('status' => true, 'data' => $notices);
    } else {
        $response = array('status' => false, 'message' => 'No notices found for the specified date range');
    }

    return $this->response->setJSON($response);
}

public function unique_notice_days_get() {
    // Use a raw SQL query to retrieve dates in the specified format
    $days = $this->admin_model->get_by_query("
        SELECT DISTINCT DATE_FORMAT(date, '%d/%m/%Y 00:00:1') AS day
        FROM noticeboard
        WHERE date IS NOT NULL AND date <> '0000-00-00 00:00:00'
        ORDER BY day ASC
    ", []);

    // Prepare the response
    if (!empty($days)) {
        // Filter out any null or incorrectly parsed days
        $filteredDays = array_filter($days, function ($day) {
            return !is_null($day['day']) && $day['day'] !== '';
        });
        
        $response = array('status' => true, 'data' => array_values($filteredDays));
    } else {
        $response = array('status' => false, 'message' => 'No unique days found in notices');
    }

    // Return the response in JSON format
    return $this->response->setJSON($response);
}
























public function session_get() {
    $sessions = $this->crud_model->get_session();
    $response = [
        'status' => true,
        'data' => $sessions
    ];
    return $this->response->setJSON($response);
}




 public function students_for_promotion_post() {
    $input = json_decode($this->request->getRawInput(), true);
    log_message('debug', 'Received input (raw): ' . json_encode($input));

    // Retrieve the input parameters
    $session_from = $input['session_from'] ?? $this->request->getPost('session_from');
    $session_to = $input['session_to'] ?? $this->request->getPost('session_to');
    $class_id_from = $input['class_id_from'] ?? $this->request->getPost('class_id_from');
    $class_id_to = $input['class_id_to'] ?? $this->request->getPost('class_id_to');

    // Validate that all required fields are provided
    if (!$session_from || !$session_to || !$class_id_from || !$class_id_to) {
        $response = ['status' => false, 'message' => 'All fields (session_from, session_to, class_id_from, class_id_to) are required'];
        return $this->response->setJSON($response);
    }

    // Fetch students based on the input parameters
    $students = $this->admin_model->get_where_result('enrols', [
        'session' => $session_from,
        'class_id' => $class_id_from
    ]);

    foreach ($students as &$student) {
        $student_details = $this->user_model->get_student_details_by_id('student', $student['student_id']);
        $student['details'] = $student_details;
    }

    // Prepare and send response
    $response = ['status' => true, 'data' => $students];
    return $this->response->setJSON($response);
}

public function classes_promote_get() {
    // Fetch classes from the database
    $classes = $this->crud_model->get_classes();

    // Check if classes data is available
    if (!empty($classes)) {
        $response = [
            'status' => true,
            'data' => $classes
        ];
    } else {
        $response = [
            'status' => false,
            'message' => 'No classes found'
        ];
    }

    // Send JSON response
    return $this->response->setJSON($response);
}




public function promote_student_post() {
    $student_id = $this->request->getPost('student_id');
    $session_to = $this->request->getPost('session_to');
    $class_id_to = $this->request->getPost('class_id_to');

    if (!$student_id || !$session_to || !$class_id_to) {
        $response = ['status' => false, 'message' => 'All fields are required'];
        return $this->response->setJSON($response);
    }

    $update_data = [
        'session' => $session_to,
        'class_id' => $class_id_to
    ];
    $this->admin_model->update_table('enrols', $update_data, ['student_id' => $student_id]);

    $response = [
        'status' => db()->affectedRows() > 0,
        'message' => db()->affectedRows() > 0 ? 'Student promoted successfully' : 'Failed to promote student'
    ];
    return $this->response->setJSON($response);
}




 public function all_quizzes_get() {
    $quizzes = $this->admin_model->get_where_result('lesson', ['lesson_type' => 'quiz']);

    if (empty($quizzes)) {
        $response = ['status' => false, 'message' => 'No quizzes found'];
        return $this->response->setJSON($response);
    }

    $quiz_data = array_map(function($quiz) {
        return [
            'id' => $quiz['id'],
            'title' => $quiz['title']
        ];
    }, $quizzes);

    $response = ['status' => true, 'data' => $quiz_data];
    return $this->response->setJSON($response);
}


/* public function quiz_result_get() {
    $class_id = $this->request->getGet('class_id');
    $quiz_id = $this->request->getGet('quiz_id');

    if (empty($class_id) || empty($quiz_id)) {
        $response = ['status' => false, 'message' => 'Both class_id and quiz_id are required'];
        return $this->response->setJSON($response);
    }

    $enrolled_students = $this->admin_model->get_where_result('enrols', ['class_id' => $class_id]);

    if (empty($enrolled_students)) {
        $response = ['status' => false, 'message' => 'No students found for the given class'];
        return $this->response->setJSON($response);
    }

    $quiz_results = [];
    foreach ($enrolled_students as $enrol) {
        $student = $this->admin_model->get_row('users', ['id' => $enrol['student_id']]);

        if (!$student) {
            continue;
        }

        $quiz = $this->admin_model->get_row('quiz_responses', [
            'user_id' => $enrol['student_id'],
            'quiz_id' => $quiz_id
        ]);

        if ($quiz) {
            $quiz_results[] = [
                'student_id' => $enrol['student_id'],
                'student_name' => $student['name'] ?? 'Unknown',
                'quiz' => [
                    'quiz_id' => $quiz['quiz_id'],
                    'question_id' => $quiz['question_id'],
                    'submitted_answers' => $quiz['submitted_answers'],
                    'correct_answers' => $quiz['correct_answers'],
                    'submitted_answer_status' => $quiz['submitted_answer_status'],
                    'date_submitted' => $quiz['date_submitted'],
                ],
            ];
        }
    }

    if (empty($quiz_results)) {
        $response = ['status' => false, 'message' => 'No quiz results found for the specified class and quiz'];
        return $this->response->setJSON($response);
    }

    $response = ['status' => true, 'data' => $quiz_results];
    return $this->response->setJSON($response);
}

 */

 public function quiz_result_get() {
    $class_id = $this->request->getGet('class_id');
    $quiz_id = $this->request->getGet('quiz_id');

    if (empty($class_id) || empty($quiz_id)) {
        $response = ['status' => false, 'message' => 'Both class_id and quiz_id are required'];
        return $this->response->setJSON($response);
    }

    // Fetch enrolled students for the specified class
    $enrolled_students = $this->admin_model->get_where_result('enrols', ['class_id' => $class_id]);

    if (empty($enrolled_students)) {
        $response = ['status' => false, 'message' => 'No students found for the given class'];
        return $this->response->setJSON($response);
    }

    $quiz_results = [];
    foreach ($enrolled_students as $enrol) {
        $student = $this->admin_model->get_row('users', ['id' => $enrol['student_id']]);

        if (!$student) {
            continue;
        }

        // Check if the student has a response for the given quiz
        $quiz = $this->admin_model->get_row('quiz_responses', [
            'user_id' => $enrol['student_id'],
            'quiz_id' => $quiz_id
        ]);

        if ($quiz) {
            // Add quiz result including pass/fail status
            $is_passed = $quiz['submitted_answer_status'] == 1; // Adjust this condition based on your pass/fail logic

            $quiz_results[] = [
                'student_id' => $enrol['student_id'],
                'student_name' => $student['name'] ?? 'Unknown',
                'status' => $is_passed ? 'Passed' : 'Failed',
                'quiz' => [
                    'quiz_id' => $quiz['quiz_id'],
                    'question_id' => $quiz['question_id'],
                    'submitted_answers' => $quiz['submitted_answers'],
                    'correct_answers' => $quiz['correct_answers'],
                    'submitted_answer_status' => $quiz['submitted_answer_status'],
                    'date_submitted' => $quiz['date_submitted'],
                ],
            ];
        } else {
            // Add students who did not attempt the quiz
            $quiz_results[] = [
                'student_id' => $enrol['student_id'],
                'student_name' => $student['name'] ?? 'Unknown',
                'status' => 'Not Attempted',
                'quiz' => null, // No quiz data since it wasn't attempted
            ];
        }
    }

    if (empty($quiz_results)) {
        $response = ['status' => false, 'message' => 'No quiz results found for the specified class and quiz'];
        return $this->response->setJSON($response);
    }

    $response = ['status' => true, 'data' => $quiz_results];
    return $this->response->setJSON($response);
}





public function quizzes_by_class_post() {
    // Collect and sanitize input
    $class_id = $this->request->getPost('class_id');
    
    // Validate input
    if (!$class_id) {
        $response = ['status' => false, 'message' => 'class_id is required'];
        return $this->response->setJSON($response);
    }

    // Adjusted column name based on database structure
    $quizzes = $this->admin_model->get_where_result('quiz_responses', ['quiz_class_id' => $class_id]);

    // Prepare response
    $response = [
        'status' => true,
        'data' => $quizzes,
    ];
    return $this->response->setJSON($response);
}

/* public function filter_attendance_post() {
    // Decode the raw JSON input
    $input_data = json_decode(file_get_contents('php://input'), true);
    
    $month = isset($input_data['month']) ? $input_data['month'] : null;
    $year = isset($input_data['year']) ? $input_data['year'] : null;
    $class_id = isset($input_data['class_id']) ? $input_data['class_id'] : null;
    $section_id = isset($input_data['section_id']) ? $input_data['section_id'] : null;
    
    // Log received data for debugging
    log_message('debug', 'Received Data - year: ' . var_export($year, true));
    log_message('debug', 'Received Data - month: ' . var_export($month, true));
    log_message('debug', 'Received Data - class_id: ' . var_export($class_id, true));
    log_message('debug', 'Received Data - section_id: ' . var_export($section_id, true));

    // Check for required fields
    if (!$month || !$year || !$class_id || !$section_id) {
        log_message('error', 'Missing required fields. year: ' . var_export($year, true) . 
            ', month: ' . var_export($month, true) . 
            ', class_id: ' . var_export($class_id, true) . 
            ', section_id: ' . var_export($section_id, true));

        $response = [
            'status' => false,
            'message' => 'All fields are required. Please provide year, month, class_id, and section_id.',
            'data' => [],
            'received_data' => [
                'year' => $year,
                'month' => $month,
                'class_id' => $class_id,
                'section_id' => $section_id
            ]
        ];
        return $this->response->setJSON($response);
    }

    // Ensure month is converted to a full name if it's numeric
    if (is_numeric($month)) {
        $month = date("F", strtotime("2024-{$month}-10")); // Convert month number to full month name
    }

    // Convert month and year to start and end timestamps
    $start_date = strtotime("01-$month-$year");
    if ($start_date === false) {
        log_message('error', 'Failed to parse start date.');
        return $this->response->setJSON(['status' => false, 'message' => 'Invalid date format for start date.']);
    }

    $end_date = strtotime("last day of $month $year");
    if ($end_date === false) {
        log_message('error', 'Failed to parse end date.');
        return $this->response->setJSON(['status' => false, 'message' => 'Invalid date format for end date.']);
    }

    log_message('debug', 'Calculated Start Date: ' . date('Y-m-d', $start_date) . ' | End Date: ' . date('Y-m-d', $end_date));

    // Fetch attendance records within the specified month
    $attendance = $this->admin_model->get_by_query("SELECT * FROM daily_attendances WHERE class_id = ? AND section_id = ? AND timestamp >= ? AND timestamp <= ?", [$class_id, $section_id, $start_date, $end_date]);

    // Log attendance data for debugging
    if (!empty($attendance)) {
        log_message('debug', 'Attendance records found: ' . count($attendance));
    } else {
        log_message('debug', 'No attendance records found for the given criteria.');
    }

    // Prepare response with attendance data
    $response = [
        'status' => true,
        'data' => $attendance
    ];
    
    log_message('debug', 'Response: ' . json_encode($response));
    return $this->response->setJSON($response);
} */

public function filter_attendance_post() {
    // Decode the raw JSON input
    $input_data = json_decode(file_get_contents('php://input'), true);
    
    $month = isset($input_data['month']) ? $input_data['month'] : null;
    $year = isset($input_data['year']) ? $input_data['year'] : null;
    $class_id = isset($input_data['class_id']) ? $input_data['class_id'] : null;
    $section_id = isset($input_data['section_id']) ? $input_data['section_id'] : null;
    
    // Log received data for debugging
    log_message('debug', 'Received Data - year: ' . var_export($year, true));
    log_message('debug', 'Received Data - month: ' . var_export($month, true));
    log_message('debug', 'Received Data - class_id: ' . var_export($class_id, true));
    log_message('debug', 'Received Data - section_id: ' . var_export($section_id, true));

    // Check for required fields
    if (!$month || !$year || !$class_id || !$section_id) {
        $response = [
            'status' => false,
            'message' => 'All fields are required. Please provide year, month, class_id, and section_id.',
            'data' => [],
            'received_data' => [
                'year' => $year,
                'month' => $month,
                'class_id' => $class_id,
                'section_id' => $section_id
            ]
        ];
        return $this->response->setJSON($response);
    }

    // Ensure month is converted to a full name if it's numeric
    if (is_numeric($month)) {
        $month = date("F", strtotime("2024-{$month}-10")); // Convert month number to full month name
    }

    // Convert month and year to start and end timestamps
    $start_date = strtotime("01-$month-$year");
    if ($start_date === false) {
        log_message('error', 'Failed to parse start date.');
        return $this->response->setJSON(['status' => false, 'message' => 'Invalid date format for start date.']);
    }

    $end_date = strtotime("last day of $month $year");
    if ($end_date === false) {
        log_message('error', 'Failed to parse end date.');
        return $this->response->setJSON(['status' => false, 'message' => 'Invalid date format for end date.']);
    }

    log_message('debug', 'Calculated Start Date: ' . date('Y-m-d', $start_date) . ' | End Date: ' . date('Y-m-d', $end_date));

    // Fetch attendance records with student name from users table
    $attendance = $this->admin_model->get_by_query([
        'select' => 'daily_attendances.*, users.name as student_name',
        'from' => 'daily_attendances',
        'join' => [['users', 'daily_attendances.student_id = users.id', 'left']],
        'where' => [
            'daily_attendances.class_id' => $class_id,
            'daily_attendances.section_id' => $section_id,
            'daily_attendances.timestamp >=' => $start_date,
            'daily_attendances.timestamp <=' => $end_date
        ]
    ]);

    // Log attendance data for debugging
    if (!empty($attendance)) {
        log_message('debug', 'Attendance records found: ' . count($attendance));
    } else {
        log_message('debug', 'No attendance records found for the given criteria.');
    }

    // Prepare response with attendance data including student name
    $response = [
        'status' => true,
        'data' => array_map(function ($record) {
            return [
                'Student Name' => $record['student_name'],
                'Student ID' => $record['student_id'],
                'Status' => $record['status'] == '1' ? 'Present' : 'Absent',
                'Date' => date('Y-m-d', $record['timestamp']),
                'Class ID' => $record['class_id'],
                'Section ID' => $record['section_id']
            ];
        }, $attendance)
    ];
    
    log_message('debug', 'Response: ' . json_encode($response));
    return $this->response->setJSON($response);
}

public function update_attendance_status_post() {
    // Decode the raw JSON input
    $input_data = json_decode(file_get_contents('php://input'), true);

    $attendance_id = isset($input_data['attendance_id']) ? $input_data['attendance_id'] : null;
    $new_status = isset($input_data['new_status']) ? $input_data['new_status'] : null;

    // Log received data for debugging
    log_message('debug', 'Received Data - attendance_id: ' . var_export($attendance_id, true));
    log_message('debug', 'Received Data - new_status: ' . var_export($new_status, true));

    // Check for required fields
    if (!$attendance_id || $new_status === null) {
        $response = [
            'status' => false,
            'message' => 'Both attendance_id and new_status are required.',
            'received_data' => [
                'attendance_id' => $attendance_id,
                'new_status' => $new_status
            ]
        ];
        return $this->response->setJSON($response);
    }

    // Ensure new_status is either '0' (Absent) or '1' (Present)
    if (!in_array($new_status, ['0', '1'])) {
        return $this->response->setJSON(['status' => false, 'message' => 'Invalid new_status. It should be 0 (Absent) or 1 (Present).']);
    }

    // Update the attendance status
    $this->admin_model->update_table('daily_attendances', ['status' => $new_status], ['id' => $attendance_id]);

    if (db()->affectedRows() > 0) {
        $response = [
            'status' => true,
            'message' => 'Attendance status updated successfully.',
            'updated_data' => [
                'attendance_id' => $attendance_id,
                'new_status' => $new_status == '1' ? 'Present' : 'Absent'
            ]
        ];
    } else {
        $response = [
            'status' => false,
            'message' => 'No record found or status unchanged. Please check the attendance_id.',
            'received_data' => [
                'attendance_id' => $attendance_id,
                'new_status' => $new_status
            ]
        ];
    }

    log_message('debug', 'Response: ' . json_encode($response));
    return $this->response->setJSON($response);
}
public function toggle_attendance_status_post() {
    // Decode the raw JSON input
    $input_data = json_decode(file_get_contents('php://input'), true);

    $attendance_id = isset($input_data['attendance_id']) ? $input_data['attendance_id'] : null;

    // Log received data for debugging
    log_message('debug', 'Received Data - attendance_id: ' . var_export($attendance_id, true));

    // Check for required fields
    if (!$attendance_id) {
        $response = [
            'status' => false,
            'message' => 'Attendance_id is required.',
            'received_data' => [
                'attendance_id' => $attendance_id
            ]
        ];
        return $this->response->setJSON($response);
    }

    // Retrieve current status if new_status is not provided
    $current_record = $this->admin_model->get_row('daily_attendances', ['id' => $attendance_id]);

    if ($current_record) {
        $current_status = $current_record['status'];
        // Toggle status: '1' (Present) to '0' (Absent) and vice versa
        $new_status = $current_status == '1' ? '0' : '1';
    } else {
        return $this->response->setJSON(['status' => false, 'message' => 'No record found with the given attendance_id.']);
    }

    // Update the attendance status
    $this->admin_model->update_table('daily_attendances', ['status' => $new_status], ['id' => $attendance_id]);

    if (db()->affectedRows() > 0) {
        $response = [
            'status' => true,
            'message' => 'Attendance status toggled successfully.',
            'updated_data' => [
                'attendance_id' => $attendance_id,
                'new_status' => $new_status == '1' ? 'Present' : 'Absent'
            ]
        ];
    } else {
        $response = [
            'status' => false,
            'message' => 'No record found or status unchanged. Please check the attendance_id.',
            'received_data' => [
                'attendance_id' => $attendance_id
            ]
        ];
    }

    log_message('debug', 'Response: ' . json_encode($response));
    return $this->response->setJSON($response);
}



public function student_list_get() {
    $class_id = $this->request->getGet('class_id');
    $section_id = $this->request->getGet('section_id');

    if (!$class_id || !$section_id) {
        $response = ['status' => false, 'message' => 'class_id and section_id are required'];
        return $this->response->setJSON($response);
    }

    // Fetch students enrolled in the selected class and section
    $students = $this->db_helper_model->get('enrols', [
        'class_id' => $class_id,
        'section_id' => $section_id,
        'school_id' => school_id(),
        'session' => active_session()
    ], 'result_array');

    $response = ['status' => true, 'data' => $students];
    return $this->response->setJSON($response);
}

public function create_attendance_post() {
    // Decode the JSON payload manually
    $input = json_decode($this->request->getRawInput(), true);

    // Extract data from the decoded JSON
    $class_id = $input['class_id'] ?? null;
    $section_id = $input['section_id'] ?? null;
    $attendance_date = $input['attendance_date'] ?? null;
    $attendance_data = $input['attendance_data'] ?? null;

    // Debug logs to confirm received data
    log_message('debug', 'Received class_id: ' . var_export($class_id, true));
    log_message('debug', 'Received section_id: ' . var_export($section_id, true));
    log_message('debug', 'Received attendance_date: ' . var_export($attendance_date, true));
    log_message('debug', 'Received attendance_data: ' . var_export($attendance_data, true));

    // Check if any required field is missing
    if (!$class_id || !$section_id || !$attendance_date || !$attendance_data) {
        $response = [
            'status' => false,
            'message' => 'All fields are required',
            'received_data' => [
                'class_id' => $class_id,
                'section_id' => $section_id,
                'attendance_date' => $attendance_date,
                'attendance_data' => $attendance_data,
            ]
        ];
        return $this->response->setJSON($response);
    }

    $timestamp = strtotime($attendance_date);

    foreach ($attendance_data as $data) {
        $this->admin_model->replace('daily_attendances', [
            'class_id' => $class_id,
            'section_id' => $section_id,
            'student_id' => $data['student_id'],
            'status' => $data['status'],
            'timestamp' => $timestamp,
            'school_id' => school_id(),
            'session_id' => active_session()
        ]);
    }

    $response = ['status' => true, 'message' => 'Attendance marked successfully'];
    return $this->response->setJSON($response);
}



public function bulk_attendance_update_post() {
    $class_id = $this->request->getPost('class_id');
    $section_id = $this->request->getPost('section_id');
    $attendance_date = $this->request->getPost('attendance_date');
    $status = $this->request->getPost('status'); // 1 for present, 0 for absent

    if (!$class_id || !$section_id || !$attendance_date || $status === null) {
        $response = ['status' => false, 'message' => 'All fields are required'];
        return $this->response->setJSON($response);
    }

    $timestamp = strtotime($attendance_date);

    // Fetch all students in the selected class and section
    $students = $this->db_helper_model->get('enrols', [
        'class_id' => $class_id,
        'section_id' => $section_id,
        'school_id' => school_id(),
        'session' => active_session()
    ], 'result_array');

    foreach ($students as $student) {
        $this->admin_model->replace('daily_attendances', [
            'class_id' => $class_id,
            'section_id' => $section_id,
            'student_id' => $student['student_id'],
            'status' => $status,
            'timestamp' => $timestamp,
            'school_id' => school_id(),
            'session_id' => active_session()
        ]);
    }

    $response = ['status' => true, 'message' => 'All students marked successfully'];
    return $this->response->setJSON($response);
}

public function monthly_attendance_summary_get() {
    $class_id = $this->request->getGet('class_id');
    $section_id = $this->request->getGet('section_id');
    $month = $this->request->getGet('month');
    $year = $this->request->getGet('year');

    if (!$class_id || !$section_id || !$month || !$year) {
        $response = ['status' => false, 'message' => 'All fields are required'];
        return $this->response->setJSON($response);
    }

    $start_date = strtotime("01-$month-$year");
    $end_date = strtotime("last day of $month $year");

    $attendance_records = $this->admin_model->get_by_query("SELECT * FROM daily_attendances WHERE class_id = ? AND section_id = ? AND timestamp >= ? AND timestamp <= ?", [$class_id, $section_id, $start_date, $end_date]);

    $response = ['status' => true, 'data' => $attendance_records];
    return $this->response->setJSON($response);
}

















// Controller: Session Management

// Get list of sessions
public function list_get() {
    $sessions = $this->admin_model->get_result('sessions');
    $response = ['status' => true, 'data' => $sessions];
    return $this->response->setJSON($response);
}

 public function create_post() {
    // Decode JSON input if necessary
    $postData = json_decode(file_get_contents('php://input'), true);
    log_message('debug', 'Decoded POST data: ' . json_encode($postData));

    $session_title = isset($postData['session_title']) ? $postData['session_title'] : null;

    if (empty($session_title)) {
        log_message('error', 'Missing required field: session_title');
        $response = ['status' => false, 'message' => 'Session title is required'];
        return $this->response->setJSON($response);
    }

    $data = [
        'name' => $session_title,
        'status' => 0 // Default inactive
    ];

    // Insert data into the sessions table
    $insert_id = $this->admin_model->insert_table('sessions', $data);
    if ($insert_id) {
        log_message('debug', 'Session created successfully with data: ' . json_encode($data));
        $response = ['status' => true, 'message' => 'Session created successfully'];
    } else {
        log_message('error', 'Failed to insert session data into the database');
        $response = ['status' => false, 'message' => 'Failed to create session'];
    }

    return $this->response->setJSON($response);
}

public function update_post($id) {
    // Decode raw input if JSON payloads are expected
    $postData = json_decode(file_get_contents('php://input'), true);
    log_message('debug', 'Decoded POST data: ' . json_encode($postData));

    // Extract session_title from decoded data
    $session_title = isset($postData['session_title']) ? $postData['session_title'] : null;

    // Validate session_title
    if (empty($session_title)) {
        log_message('error', 'Missing session_title for update');
        $response = ['status' => false, 'message' => 'Session title is required'];
        return $this->response->setJSON($response);
    }

    // Prepare update data
    $data = ['name' => $session_title];

    // Execute the update query
    if ($this->admin_model->update_table('sessions', $data, ['id' => $id])) {
        log_message('debug', 'Session updated successfully with data: ' . json_encode($data));
        $response = ['status' => true, 'message' => 'Session updated successfully'];
    } else {
        log_message('error', 'Failed to update session data in the database');
        $response = ['status' => false, 'message' => 'Failed to update session'];
    }

    return $this->response->setJSON($response);
}


// Delete session
public function delete_post($id) {
    $session = $this->db_helper_model->get_by_id('sessions', $id);

    if ($session && $session['status'] != 1) { // Ensure not active
        $deleted = $this->admin_model->delete('sessions', ['id' => $id]);
        $response = ['status' => true, 'message' => 'Session deleted successfully'];
    } else {
        log_message('error', 'Cannot delete active or non-existent session');
        $response = ['status' => false, 'message' => 'Cannot delete active or non-existent session'];
    }
    return $this->response->setJSON($response);
}

// Activate session
public function activate_get($id) {
    // Deactivate all other sessions
    $this->admin_model->update_table('sessions', ['status' => 0], ['status' => 1]);

    // Activate the selected session
    $this->admin_model->update_table('sessions', ['status' => 1], ['id' => $id]);

    $response = ['status' => true, 'message' => 'Session activated successfully'];
    return $this->response->setJSON($response);
}

// Deactivate session
public function deactivate_post($id) {
    $session = $this->db_helper_model->get_by_id('sessions', $id);
    if ($session && $session['status'] == 1) {
        $this->admin_model->update_table('sessions', ['status' => 0], ['id' => $id]);

        $response = ['status' => true, 'message' => 'Session deactivated successfully'];
    } else {
        log_message('error', 'Session is already inactive or does not exist');
        $response = ['status' => false, 'message' => 'Session is already inactive or does not exist'];
    }
    return $this->response->setJSON($response);
}














 // 1. Get all accountants
 public function accountant_users_get() {
    $school_id = $this->request->getGet('school_id');

    if (!$school_id) {
        $response = ['status' => false, 'message' => 'School ID is required'];
        echo json_encode($response);
        return;
    }

    $accountants = $this->admin_model->get_where_result('users', [
        'role' => 'accountant',
        'school_id' => $school_id
    ]);

    if ($accountants) {
        $response = ['status' => true, 'data' => $accountants];
    } else {
        $response = ['status' => false, 'message' => 'No accountants found for this school ID'];
    }

    echo json_encode($response);
}

// 2. Create an accountant
public function create_accountant_post() {
    $data = json_decode($this->request->getRawInput(), true);

    // Validate required fields
    if (empty($data['name']) || empty($data['email']) || empty($data['password']) || empty($data['phone']) || empty($data['gender']) || empty($data['address'])) {
        $response = ['status' => false, 'message' => 'Name, email, password, phone, gender, and address are required'];
        echo json_encode($response);
        return;
    }

    // Set the role and ensure school_id is present
    $data['role'] = 'accountant';
    if (empty($data['school_id'])) {
        $response = ['status' => false, 'message' => 'School ID is required'];
        echo json_encode($response);
        return;
    }

    // Insert the new accountant into the users table
    $this->admin_model->insert_table('users', $data);
    if (db()->affectedRows() > 0) {
        $response = ['status' => true, 'message' => 'Accountant created successfully'];
    } else {
        $response = ['status' => false, 'message' => 'Failed to create accountant'];
    }

    echo json_encode($response);
}

// 3. Update an accountant
public function update_accountant_post($id) {
    $data = json_decode($this->request->getRawInput(), true);

    // Validate required fields
    if (empty($data['name']) || empty($data['email']) || empty($data['phone']) || empty($data['gender']) || empty($data['address'])) {
        $response = ['status' => false, 'message' => 'Name, email, phone, gender, and address are required'];
        echo json_encode($response);
        return;
    }

    // Update the accountant record in the database
    $this->admin_model->update_table('users', $data, ['id' => $id]);

    // Check if the update was successful
    if (db()->affectedRows() > 0) {
        $response = ['status' => true, 'message' => 'Accountant updated successfully'];
    } else {
        $response = ['status' => false, 'message' => 'Failed to update accountant or no changes made'];
    }

    echo json_encode($response);
}


// 4. Delete an accountant
public function delete_accountant_post($id) {
    $deleted = $this->admin_model->delete('users', ['id' => $id]);

    if ($deleted) {
        $response = ['status' => true, 'message' => 'Accountant deleted successfully'];
    } else {
        $response = ['status' => false, 'message' => 'Failed to delete accountant or accountant not found'];
    }

    echo json_encode($response);
}










public function all_librarians_get() {
    $school_id = $this->request->getGet('school_id');

    if (!$school_id) {
        $response = ['status' => false, 'message' => 'School ID is required'];
        echo json_encode($response);
        return;
    }

    $librarians = $this->admin_model->get_where_result('users', [
        'role' => 'librarian',
        'school_id' => $school_id
    ]);

    if ($librarians) {
        $response = ['status' => true, 'data' => $librarians];
    } else {
        $response = ['status' => false, 'message' => 'No librarians found for this school ID'];
    }

    echo json_encode($response);
}

public function create_librarian_post() {
    $data = json_decode($this->request->getRawInput(), true);

    if (empty($data['name']) || empty($data['email']) || empty($data['password']) || empty($data['phone']) || empty($data['gender']) || empty($data['address'])) {
        $response = ['status' => false, 'message' => 'Name, email, password, phone, gender, and address are required'];
        echo json_encode($response);
        return;
    }

    $data['role'] = 'librarian';

    if (empty($data['school_id'])) {
        $response = ['status' => false, 'message' => 'School ID is required'];
        echo json_encode($response);
        return;
    }

    $this->admin_model->insert_table('users', $data);
    if (db()->affectedRows() > 0) {
        $response = ['status' => true, 'message' => 'Librarian created successfully'];
    } else {
        $response = ['status' => false, 'message' => 'Failed to create librarian'];
    }

    echo json_encode($response);
}



public function update_librarian_post($id) {
    $data = json_decode($this->request->getRawInput(), true);

    if (empty($data['name']) || empty($data['email'])) {
        $response = ['status' => false, 'message' => 'Name and email are required'];
        echo json_encode($response);
        return;
    }

    $this->admin_model->update_table('users', $data, ['id' => $id]);

    if (db()->affectedRows() > 0) {
        $response = ['status' => true, 'message' => 'Librarian updated successfully'];
    } else {
        $response = ['status' => false, 'message' => 'Failed to update librarian or no changes made'];
    }

    echo json_encode($response);
}


public function delete_librarian_post($id) {
    $deleted = $this->admin_model->delete('users', ['id' => $id]);

    if ($deleted) {
        $response = ['status' => true, 'message' => 'Librarian deleted successfully'];
    } else {
        $response = ['status' => false, 'message' => 'Failed to delete librarian or librarian not found'];
    }

    echo json_encode($response);
}



































public function download_csv_get() {
    // Set the filename for the CSV file
    $filename = 'student_list.csv';

    // Set CSV headers
    $header = array("Student ID", "Name", "Class", "Section", "Email", "Phone");
    
    // Retrieve data from the database (modify this query as per your requirements)
   $school_id = session()->get('school_id'); // Assuming school_id is stored in session
    $students = $this->admin_model->get_where_result('students', ['school_id' => $school_id]);

    // Open a memory stream for the CSV output
    $fp = fopen('php://output', 'w');
    
    // Set the headers to download the file
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Write the header to the CSV file
    fputcsv($fp, $header);

    // Write the student data to the CSV file
    foreach ($students as $student) {
        // Modify the row as per your data structure
        $row = array(
            $student['id'], // Replace 'id' with your actual ID column if different
            $student['full_name'], // Replace with the actual name column if different
            $this->getClassName($student['class_id']),
            $this->getSectionName($student['section_id']),
            $student['email'],
            $student['phone']
        );
        fputcsv($fp, $row);
    }

    // Close the file pointer
    fclose($fp);

    // Exit to prevent any other output from corrupting the CSV file
    exit;
}

private function getClassName($class_id) {
    return get_class_name($class_id);
}

private function getSectionName($section_id) {
    return get_section_name($section_id);
}

public function syllabus_operations($operation, $class_id = null, $section_id = null, $syllabus_id = null) {
    switch ($operation) {
        case 'get':
            $this->get_syllabus_by_class_section($class_id, $section_id);
            break;

        case 'create':
            $this->create_syllabus();
            break;

        case 'delete':
            $this->delete_syllabus($syllabus_id);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid operation']);
            break;
    }
}

public function syllabus_by_class_section_get($class_id, $section_id) {
    $school_id = school_id();
    $session_id = active_session();
    
    $syllabuses = $this->admin_model->get_by_query([
        'select' => 'syllabuses.*, subjects.name as subject_name',
        'from' => 'syllabuses',
        'join' => [['subjects', 'syllabuses.subject_id = subjects.id', 'left']],
        'where' => [
            'syllabuses.class_id' => $class_id,
            'syllabuses.section_id' => $section_id,
            'syllabuses.school_id' => $school_id,
            'syllabuses.session_id' => $session_id
        ]
    ]);

    if (!empty($syllabuses)) {
        echo json_encode($syllabuses);
    } else {
        echo json_encode(['status' => 'empty', 'message' => 'No syllabuses found.']);
    }
}

// Function to add a new syllabus
public function create_syllabus_post() {
    $data = array(
        'title' => $this->request->getPost('title'),
        'class_id' => $this->request->getPost('class_id'),
        'section_id' => $this->request->getPost('section_id'),
        'subject_id' => $this->request->getPost('subject_id'),
        'file' => $this->upload_syllabus_file(), // Handle file upload
        'school_id' => school_id(),
        'session_id' => active_session()
    );

    $this->admin_model->insert_table('syllabuses', $data);
    echo json_encode(['status' => 'success', 'message' => 'Syllabus created successfully.']);
}

private function upload_syllabus_file() {
    if (!empty($_FILES['syllabus_file']['name'])) {
        $config['upload_path'] = './uploads/syllabus/';
        $config['allowed_types'] = 'pdf|doc|docx';
        $config['file_name'] = time() . '_' . $_FILES['syllabus_file']['name'];
        
        // TODO: Replace with Config\Services::upload(null, config);

        if ($this->upload->do_upload('syllabus_file')) {
            return $this->upload->data('file_name');
        } else {
            // Log the error and provide more debugging information
            log_message('error', 'File upload error: ' . $this->upload->display_errors());
            echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
            exit; // Exit to prevent further processing
        }
    }
    return null; // Return null if no file was uploaded  
}


// Function to delete a syllabus by ID
private function delete_syllabus_post($syllabus_id) {
    $deleted = $this->admin_model->delete('syllabuses', ['id' => $syllabus_id]);
    echo json_encode(['status' => 'success', 'message' => 'Syllabus deleted successfully.']);
}

































public function book_issues_get() {
    $date_from = $this->request->getGet('date_from');
    $date_to = $this->request->getGet('date_to');

    $where = [
        'book_issues.session' => $this->active_session,
        'book_issues.school_id' => $this->school_id
    ];

    if ($date_from && $date_to) {
        $date_from_timestamp = strtotime($date_from);
        $date_to_timestamp = strtotime($date_to);

        if ($date_from_timestamp !== false && $date_to_timestamp !== false) {
            $where['book_issues.issue_date >='] = $date_from_timestamp;
            $where['book_issues.issue_date <='] = $date_to_timestamp;
        }
    }

    $result = $this->admin_model->get_by_query([
        'select' => 'book_issues.*, books.name AS book_name, users.name AS student_name',
        'from' => 'book_issues',
        'join' => [
            ['books', 'book_issues.book_id = books.id', 'left'],
            ['students', 'book_issues.student_id = students.id', 'left'],
            ['users', 'students.user_id = users.id', 'left']
        ],
        'where' => $where
    ]);

    $response = [
        'status' => true,
        'data' => $result
    ];
    return $this->response->setJSON($response);
}

// Create a new book issue
public function create_book_issue_post() {
    $input_data = json_decode($this->request->getRawInput(), true);

    // Validate the required fields
    if (empty($input_data['book_id']) || empty($input_data['class_id']) || empty($input_data['student_id']) || empty($input_data['issue_date'])) {
        $response = ['status' => false, 'message' => 'Missing required fields'];
        return $this->response->setStatusCode(400)->setJSON($response);
        return;
    }

    // Assign data directly from input (timestamps expected)
    $data['book_id'] = htmlspecialchars($input_data['book_id']);
    $data['class_id'] = htmlspecialchars($input_data['class_id']);
    $data['student_id'] = htmlspecialchars($input_data['student_id']);
    $data['issue_date'] = intval($input_data['issue_date']); // Use as-is (assumed to be a Unix timestamp)
    $data['school_id'] = isset($input_data['school_id']) ? htmlspecialchars($input_data['school_id']) : $this->school_id;
    $data['session'] = isset($input_data['session']) ? htmlspecialchars($input_data['session']) : $this->active_session;
    $data['created_at'] = isset($input_data['created_at']) ? intval($input_data['created_at']) : time();
    $data['updated_at'] = isset($input_data['updated_at']) ? intval($input_data['updated_at']) : null;

    // Insert the data into the database
    $this->admin_model->insert_table('book_issues', $data);

    if (db()->affectedRows() > 0) {
        $response = ['status' => true, 'message' => 'Book issue created successfully'];
    } else {
        $response = ['status' => false, 'message' => 'Failed to create book issue'];
    }
    return $this->response->setJSON($response);
}


// Update an existing book issue
public function update_book_issue_post($id) {
    $input_data = json_decode($this->request->getRawInput(), true);

    $data['book_id'] = htmlspecialchars($input_data['book_id']);
    $data['class_id'] = htmlspecialchars($input_data['class_id']);
    $data['student_id'] = htmlspecialchars($input_data['student_id']);
    $data['issue_date'] = strtotime($input_data['issue_date']);
    $data['school_id'] = $this->school_id;
    $data['session'] = $this->active_session;

    $this->admin_model->update_table('book_issues', $data, ['id' => $id]);

    if (db()->affectedRows() > 0) {
        $response = ['status' => true, 'message' => 'Book issue updated successfully'];
    } else {
        $response = ['status' => false, 'message' => 'Failed to update book issue'];
    }
    return $this->response->setJSON($response);
}

// Return a book issue
public function return_book_issue_post($id) {
    $data['status'] = 1;

    $this->admin_model->update_table('book_issues', $data, ['id' => $id]);

    if (db()->affectedRows() > 0) {
        $response = ['status' => true, 'message' => 'Book issue returned successfully'];
    } else {
        $response = ['status' => false, 'message' => 'Failed to return book issue'];
    }
    return $this->response->setJSON($response);
}

// Delete a book issue
public function delete_book_issue_delete($id) {
    $deleted = $this->admin_model->delete('book_issues', ['id' => $id]);

    if ($deleted) {
        $response = ['status' => true, 'message' => 'Book issue deleted successfully'];
    } else {
        $response = ['status' => false, 'message' => 'Failed to delete book issue'];
    }
    return $this->response->setJSON($response);
}

public function books_by_school_get($school_id = null) {
    if (!$school_id) {
        $response = [
            'status' => false,
            'message' => 'School ID is required'
        ];
        return $this->response->setStatusCode(400)->setJSON($response);
        return;
    }

    // Select only `id` and `name` columns from the books table
    $books = $this->admin_model->get_where_result('books', ['school_id' => $school_id]);

    if (!empty($books)) {
        $response = [
            'status' => true,
            'data' => $query
        ];
    } else {
        $response = [
            'status' => false,
            'message' => 'No books found for this school'
        ];
    }

    // Output the JSON response
    return $this->response->setJSON($response);
}


public function classes_by_school_get($school_id = null) {
    if (!$school_id) {
        $response = [
            'status' => false,
            'message' => 'School ID is required'
        ];
        return $this->response->setStatusCode(400)->setJSON($response);
        return;
    }

    // Query to fetch classes based on school_id
    $classes = $this->admin_model->get_where_result('classes', ['school_id' => $school_id]);

    if (!empty($classes)) {
        $response = [
            'status' => true,
            'data' => $query
        ];
    } else {
        $response = [
            'status' => false,
            'message' => 'No classes found for this school'
        ];
    }

    // Output the JSON response
    return $this->response->setJSON($response);
}

public function students_by_school_get($school_id = null) {
    if (!$school_id) {
        $response = [
            'status' => false,
            'message' => 'School ID is required'
        ];
        return $this->response->setStatusCode(400)->setJSON($response);
        return;
    }

    // Query to fetch students based on school_id
    $students = $this->admin_model->get_by_query([
        'select' => 'students.*, users.name AS student_name',
        'from' => 'students',
        'join' => [['users', 'students.user_id = users.id', 'left']],
        'where' => ['students.school_id' => $school_id]
    ]);

    if (!empty($students)) {
        $response = [
            'status' => true,
            'data' => $students
        ];
    } else {
        $response = [
            'status' => false,
            'message' => 'No students found for this school'
        ];
    }

    // Output the JSON response
    return $this->response->setJSON($response);
}
























public function create_addon_post() {
    $input_data = json_decode($this->request->getRawInput(), true);

    // Validate required fields
    if (empty($input_data['name']) || empty($input_data['unique_identifier']) || empty($input_data['version'])) {
        $response = ['status' => false, 'message' => 'Missing required fields'];
        return $this->response->setStatusCode(400)->setJSON($response);
        return;
    }

    // Prepare data for insertion
    $data['name'] = htmlspecialchars($input_data['name']);
    $data['unique_identifier'] = htmlspecialchars($input_data['unique_identifier']);
    $data['version'] = htmlspecialchars($input_data['version']);
    $data['status'] = isset($input_data['status']) ? intval($input_data['status']) : 0; // Default to inactive

    // Insert into the database
    $this->admin_model->insert_table('addons', $data);

    if (db()->affectedRows() > 0) {
        $response = ['status' => true, 'message' => 'Addon created successfully'];
    } else {
        $response = ['status' => false, 'message' => 'Failed to create addon'];
    }
    return $this->response->setJSON($response);
}
public function addon_get($unique_identifier = "") {
    if ($unique_identifier != "") {
        $addons = $this->db_helper_model->get('addons', ['unique_identifier' => $unique_identifier], 'result_array');
    } else {
        $addons = $this->admin_model->get_result('addons');
    }

    $response = [
        'status' => true,
        'data' => $addons
    ];
    return $this->response->setJSON($response);
}
public function remove_addon_post($id) {
    // Check if the addon exists
    $addon = $this->db_helper_model->get_by_id('addons', $id);

    if (!$addon) {
        $response = ['status' => false, 'message' => 'Addon not found'];
        return $this->response->setStatusCode(404)->setJSON($response);
        return;
    }

    // Delete the addon
    $deleted = $this->admin_model->delete('addons', ['id' => $id]);

    if ($deleted) {
        $response = ['status' => true, 'message' => 'Addon removed successfully'];
    } else {
        $response = ['status' => false, 'message' => 'Failed to remove addon'];
    }
    return $this->response->setJSON($response);
}



















public function not_approved_schools_get() {
    $limit = $this->request->getGet('limit') ? (int)$this->request->getGet('limit') : 3;
    $offset = $this->request->getGet('offset') ? (int)$this->request->getGet('offset') : 0;

    $schools = $this->admin_model->get_by_query([
        'select' => 'schools.*, users.email',
        'from' => 'schools',
        'join' => [['users', 'users.school_id = schools.id', 'left']],
        'where' => ['schools.status' => 0],
        'limit' => $limit,
        'offset' => $offset
    ]);

    $total = $this->admin_model->count('schools', ['status' => 0]);

    if (!empty($schools)) {
        $data = array_map(function ($school) {
            $school['id'] = (int)$school['id'];
            return $school;
        }, $schools);

        $response = [
            'status' => true,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'remaining' => max($total - ($offset + $limit), 0)
            ]
        ];
    } else {
        $response = [
            'status' => false,
            'message' => 'No unapproved schools found',
            'pagination' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'remaining' => max($total - ($offset + $limit), 0)
            ]
        ];
    }

    // Output the JSON response
    return $this->response->setJSON($response);
}



public function approve_school_post($school_id) {
    // Check if the school exists
    $school = $this->admin_model->get_row('schools', ['id' => $school_id]);

    if ($school) {
        // Update status to 1 (approved)
        $this->admin_model->update_table('schools', ['status' => 1], ['id' => $school_id]);

        if (db()->affectedRows() > 0) {
            $response = [
                'status' => true,
                'message' => 'School approved successfully'
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Failed to approve school'
            ];
        }
    } else {
        $response = [
            'status' => false,
            'message' => 'School not found'
        ];
    }

    // Output the JSON response
    return $this->response->setJSON($response);
}

    // Function to delete a school
    public function del_school_post($school_id) {
        // Check if the school exists
        $school = $this->admin_model->get_row('schools', ['id' => $school_id]);

        if ($school) {
            // Delete the school record
            $deleted = $this->admin_model->delete('schools', ['id' => $school_id]);

            if ($deleted) {
                $response = [
                    'status' => true,
                    'message' => 'School deleted successfully'
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Failed to delete school'
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'School not found'
            ];
        }

        // Output the JSON response
        return $this->response->setJSON($response);
    }


    public function count_not_approved_schools_get() {
        // Query to count schools with status 0
        $count = $this->admin_model->count('schools', ['status' => 0]);

        if ($count > 0) {
            $response = [
                'status' => true,
                'count' => $count,
                'message' => "Number of unapproved schools: $count"
            ];
        } else {
            $response = [
                'status' => false,
                'count' => 0,
                'message' => 'No unapproved schools found'
            ];
        }

        // Output the JSON response
        return $this->response->setJSON($response);
    }


    public function students_by_class_and_section_get($class_id, $section_id) {
        // Ensure the request method is GET
        if ($this->request->getServer('REQUEST_METHOD') !== 'GET') {
            $this->output
                 ->set_status_header(405)
                 ->set_output(json_encode(['message' => 'Method not allowed']));
            return;
        }
    
        // Validate the input
        if (empty($class_id) || empty($section_id)) {
            $this->output
                 ->set_status_header(400)
                 ->set_output(json_encode(['message' => 'Class ID and Section ID are required']));
            return;
        }
    
        // Get page parameter for pagination (default to 1 if not provided)
        $page = $this->request->getGet('page') ? (int) $this->request->getGet('page') : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
    
        $students = $this->admin_model->get_by_query([
            'select' => 'users.*',
            'from' => 'enrols',
            'join' => [
                ['students', 'students.id = enrols.student_id'],
                ['users', 'users.id = students.user_id']
            ],
            'where' => [
                'enrols.class_id' => $class_id,
                'enrols.section_id' => $section_id
            ],
            'limit' => $limit,
            'offset' => $offset
        ]);
    
        if (!empty($students)) {
            $this->output
                 ->set_status_header(200)
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                     'status' => 'success',
                     'page' => $page,
                     'students' => $students
                 ]));
        } else {
            $this->output
                 ->set_status_header(404)
                 ->set_output(json_encode(['status' => 'error', 'message' => 'No students found for the given class and section IDs']));
        }
    }
    
    
    
    
    
    



























































public function add_lesson_youtube_post() {
    log_message('info', 'Starting add_lesson_youtube_post function.');

    // Get the JSON input and decode it
    $input_data = json_decode(file_get_contents("php://input"), true);
    $youtube_url = isset($input_data['youtube_url']) ? $input_data['youtube_url'] : '';

    log_message('info', 'Received YouTube URL: ' . $youtube_url);

    if (empty($youtube_url)) {
        $response = ['status' => 'error', 'message' => 'URL YouTube manquante'];
        log_message('error', 'YouTube URL is missing.');
        return $this->response->setJSON($response);
        return;
    }

    // Extract video ID from URL
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtube_url, $match);
    $video_id = isset($match[1]) ? $match[1] : null;
    log_message('info', 'Extracted Video ID: ' . $video_id);

    if (!$video_id) {
        $response = ['status' => 'error', 'message' => 'URL YouTube invalide'];
        log_message('error', 'Invalid YouTube URL provided.');
        return $this->response->setJSON($response);
        return;
    }

    // Retrieve YouTube API key from the settings table
    $settings = $this->admin_model->get_row('settings', ['id' => 1]);
    $api_key = $settings ? $settings['youtube_api_key'] : null;
    log_message('info', 'Retrieved YouTube API key.');

    $youtube_api_url = "https://www.googleapis.com/youtube/v3/videos?id={$video_id}&key={$api_key}&part=snippet,contentDetails";
    log_message('info', 'YouTube API URL: ' . $youtube_api_url);

    $video_data = file_get_contents($youtube_api_url);
    if ($video_data === false) {
        $response = ['status' => 'error', 'message' => 'Erreur de connexion à l\'API YouTube'];
        log_message('error', 'Failed to connect to the YouTube API.');
        return $this->response->setJSON($response);
        return;
    }

    $video_data = json_decode($video_data, true);
    if ($video_data === null || empty($video_data['items'])) {
        $response = ['status' => 'error', 'message' => 'Aucune donnée trouvée pour cet ID de vidéo'];
        log_message('error', 'No items found in YouTube API response.');
        return $this->response->setJSON($response);
        return;
    }

    $video_info = $video_data['items'][0]['snippet'];
    $content_details = $video_data['items'][0]['contentDetails'];
    $duration = $this->convert_iso8601_duration($content_details['duration']);
    log_message('info', 'Converted video duration: ' . $duration);

    $lesson_data = [
        'title' => isset($input_data['title']) ? $input_data['title'] : $video_info['title'],
        'summary' => isset($input_data['summary']) ? $input_data['summary'] : $video_info['description'],
        'course_id' => isset($input_data['course_id']) ? $input_data['course_id'] : null,
        'section_id' => isset($input_data['section_id']) ? $input_data['section_id'] : null,
        'duration' => $duration,
        'video_type' => 'youtube',
        'video_url' => $youtube_url,
        'lesson_type' => 'video',
        'date_added' => date('Y-m-d H:i:s'),
        'last_modified' => date('Y-m-d H:i:s')
    ];
    log_message('info', 'Prepared lesson data: ' . json_encode($lesson_data));

    $insert_result = $this->admin_model->insert_table('lesson', $lesson_data);
    $lesson_id = db()->insertID();

    if ($insert_result && $lesson_id) {
        log_message('info', 'Lesson added successfully with ID: ' . $lesson_id);
        $response = [
            'status' => 'success',
            'message' => 'Leçon ajoutée avec succès',
            'lesson_id' => $lesson_id,
            'duration' => $duration  // Include the duration in the response
        ];
    } else {
        log_message('error', 'Failed to insert lesson into the database.');
        $response = ['status' => 'error', 'message' => 'Erreur lors de l\'ajout de la leçon'];
    }

    return $this->response->setJSON($response);
    log_message('info', 'Response sent to client.');
}

private function convert_iso8601_duration($iso_duration) {
    try {
        $interval = new DateInterval($iso_duration);
        return sprintf('%02d:%02d:%02d', $interval->h, $interval->i, $interval->s);
    } catch (Exception $e) {
        log_message('error', 'Error in convert_iso8601_duration: ' . $e->getMessage());
        return '00:00:00';
    }
}

public function get_youtube_video_duration_post() {
    log_message('info', 'Starting get_youtube_video_duration_post function.');

    // Get the JSON input and decode it
    $input_data = json_decode(file_get_contents("php://input"), true);
    $youtube_url = isset($input_data['youtube_url']) ? $input_data['youtube_url'] : '';

    log_message('info', 'Received YouTube URL: ' . $youtube_url);

    if (empty($youtube_url)) {
        $response = ['status' => 'error', 'message' => 'YouTube URL is missing'];
        return $this->response->setJSON($response);
        return;
    }

    // Extract video ID from URL
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtube_url, $match);
    $video_id = isset($match[1]) ? $match[1] : null;

    if (!$video_id) {
        $response = ['status' => 'error', 'message' => 'Invalid YouTube URL'];
        return $this->response->setJSON($response);
        return;
    }

    // Retrieve YouTube API key from the settings table
    $settings = $this->admin_model->get_row('settings', ['id' => 1]);
    $api_key = $settings ? $settings['youtube_api_key'] : null;

    $youtube_api_url = "https://www.googleapis.com/youtube/v3/videos?id={$video_id}&key={$api_key}&part=contentDetails";

    $video_data = file_get_contents($youtube_api_url);
    if ($video_data === false) {
        $response = ['status' => 'error', 'message' => 'Failed to connect to YouTube API'];
        return $this->response->setJSON($response);
        return;
    }

    $video_data = json_decode($video_data, true);
    if ($video_data === null || empty($video_data['items'])) {
        $response = ['status' => 'error', 'message' => 'No data found for this video ID'];
        return $this->response->setJSON($response);
        return;
    }

    $content_details = $video_data['items'][0]['contentDetails'];
    $duration = $this->convert_iso8601_duration($content_details['duration']);

    $response = [
        'status' => 'success',
        'duration' => $duration
    ];
    return $this->response->setJSON($response);
}



public function display_youtube_get() {
    log_message('info', 'Starting display_youtube_get function.');

    // Get YouTube URL from GET parameters
    $youtube_url = $this->request->getGet('youtube_url');
    log_message('info', 'Received YouTube URL: ' . $youtube_url);

    if (empty($youtube_url)) {
        $response = ['status' => 'error', 'message' => 'YouTube URL is missing'];
        log_message('error', 'YouTube URL is missing.');
        return $this->response->setJSON($response);
        return;
    }

    // Extract video ID from URL
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtube_url, $match);
    $video_id = isset($match[1]) ? $match[1] : null;

    if (!$video_id) {
        $response = ['status' => 'error', 'message' => 'Invalid YouTube URL'];
        log_message('error', 'Invalid YouTube URL provided.');
        return $this->response->setJSON($response);
        return;
    }

    // Retrieve YouTube API key from the settings table
    $settings = $this->admin_model->get_row('settings', ['id' => 1]);
    $api_key = $settings ? $settings['youtube_api_key'] : null;
    log_message('info', 'Retrieved YouTube API key.');

    $youtube_api_url = "https://www.googleapis.com/youtube/v3/videos?id={$video_id}&key={$api_key}&part=snippet,contentDetails";
    log_message('info', 'YouTube API URL: ' . $youtube_api_url);

    // Fetch video data from YouTube API
    $video_data = file_get_contents($youtube_api_url);
    if ($video_data === false) {
        $response = ['status' => 'error', 'message' => 'Failed to connect to YouTube API'];
        log_message('error', 'Failed to connect to YouTube API.');
        return $this->response->setJSON($response);
        return;
    }

    $video_data = json_decode($video_data, true);
    if ($video_data === null || empty($video_data['items'])) {
        $response = ['status' => 'error', 'message' => 'No data found for this video ID'];
        log_message('error', 'No items found in YouTube API response.');
        return $this->response->setJSON($response);
        return;
    }

    $video_info = $video_data['items'][0]['snippet'];
    $content_details = $video_data['items'][0]['contentDetails'];
    $duration = $this->convert_iso8601_duration($content_details['duration']);
    log_message('info', 'Converted video duration: ' . $duration);

    // Prepare the response data
    $response = [
        'status' => 'success',
        'title' => $video_info['title'],
        'description' => $video_info['description'],
        'duration' => $duration,
        'publishedAt' => $video_info['publishedAt'],
        'thumbnail' => $video_info['thumbnails']['high']['url']
    ];

    log_message('info', 'Video information retrieved and ready for output.');
    return $this->response->setJSON($response);
}




public function update_lesson_youtube_post() {
    log_message('info', 'Starting update_lesson_youtube_post function.');

    // Decode the JSON input
    $input_data = json_decode(file_get_contents("php://input"), true);
    $lesson_id = $input_data['lesson_id'] ?? null;
    $youtube_url = $input_data['youtube_url'] ?? null;

    if (!$lesson_id) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Lesson ID is required']);
        log_message('error', 'Lesson ID is missing.');
        return;
    }

    // Check if the lesson exists
    $lesson = $this->db_helper_model->get('lesson', ['id' => $lesson_id], 'row');
    if (!$lesson) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Lesson not found']);
        log_message('error', 'Lesson not found with ID: ' . $lesson_id);
        return;
    }

    // Prepare data for update
    $update_data = [];
    
    // Process YouTube URL and fetch video details if provided
    if ($youtube_url) {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtube_url, $match);
        $video_id = $match[1] ?? null;

        if (!$video_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid YouTube URL']);
            log_message('error', 'Invalid YouTube URL provided.');
            return;
        }

        // Fetch API key and video details from YouTube
        $settings = $this->admin_model->get_row('settings', ['id' => 1]);
        $api_key = $settings ? $settings['youtube_api_key'] : null;
        $youtube_api_url = "https://www.googleapis.com/youtube/v3/videos?id={$video_id}&key={$api_key}&part=snippet,contentDetails";
        
        $video_data = @file_get_contents($youtube_api_url);
        if ($video_data === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to connect to YouTube API']);
            log_message('error', 'Failed to connect to YouTube API.');
            return;
        }

        $video_data = json_decode($video_data, true);
        if ($video_data === null || empty($video_data['items'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No data found for this video ID']);
            log_message('error', 'No items found in YouTube API response.');
            return;
        }

        $video_info = $video_data['items'][0]['snippet'];
        $content_details = $video_data['items'][0]['contentDetails'];
        $duration = $this->convert_iso8601_duration($content_details['duration']);
        
        // Populate update data with video information
        $update_data['video_url'] = $youtube_url;
        $update_data['title'] = $input_data['title'] ?? $video_info['title'];
        $update_data['summary'] = $input_data['summary'] ?? $video_info['description'];
        $update_data['duration'] = $duration;
    }

    // Populate update data with other fields if provided
    if (isset($input_data['title'])) {
        $update_data['title'] = $input_data['title'];
    }
    if (isset($input_data['summary'])) {
        $update_data['summary'] = $input_data['summary'];
    }
    if (isset($input_data['course_id'])) {
        $update_data['course_id'] = $input_data['course_id'];
    }
    if (isset($input_data['section_id'])) {
        $update_data['section_id'] = $input_data['section_id'];
    }
    $update_data['last_modified'] = date('Y-m-d H:i:s');

    // Perform update and handle response
    $update_result = $this->admin_model->update_table('lesson', $update_data, ['id' => $lesson_id]);

    if ($update_result) {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Lesson updated successfully',
            'lesson_id' => $lesson_id,
            'duration' => $update_data['duration'] ?? $lesson->duration
        ]);
        log_message('info', 'Lesson updated successfully with ID: ' . $lesson_id);
    } else {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Error updating lesson']);
        log_message('error', 'Failed to update lesson in the database.');
    }
    log_message('info', 'Response sent to client.');
}

public function get_lesson_details_post() {
    // Retrieve lesson_id from POST data
    $input_data = json_decode(file_get_contents("php://input"), true);
    $lesson_id = $input_data['lesson_id'] ?? null;

    if (empty($lesson_id)) {
        $response = ['status' => 'error', 'message' => 'Lesson ID is required'];
        return $this->response->setJSON($response);
        return;
    }

    // Query the database to retrieve lesson details
    $lesson = $this->db_helper_model->get_by_id('lesson', $lesson_id);

    if ($lesson) {
        $response = [
            'status' => 'success',
            'lesson' => $lesson,
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Lesson not found',
        ];
    }

    return $this->response->setJSON($response);
}



public function delete_lesson_post() {
    log_message('info', 'Starting delete_lesson_post function.');

    // Decode JSON input to get the lesson ID
    $input_data = json_decode(file_get_contents("php://input"), true);
    $lesson_id = $input_data['lesson_id'] ?? null;

    // Check for missing lesson ID
    if (empty($lesson_id)) {
        $response = ['status' => 'error', 'message' => 'Lesson ID is required'];
        log_message('error', 'Lesson ID is missing.');
        return $this->response->setJSON($response);
        return;
    }

    // Verify that the lesson exists
    $lesson = $this->db_helper_model->get('lesson', ['id' => $lesson_id], 'row');
    if (!$lesson) {
        $response = ['status' => 'error', 'message' => 'Lesson not found'];
        log_message('error', 'Lesson not found with ID: ' . $lesson_id);
        return $this->response->setJSON($response);
        return;
    }

    // Proceed to delete the lesson
    $deleted = $this->admin_model->delete('lesson', ['id' => $lesson_id]);

    // Response based on the deletion result
    if ($deleted) {
        $response = [
            'status' => 'success',
            'message' => 'Lesson deleted successfully',
            'lesson_id' => $lesson_id
        ];
        log_message('info', 'Lesson deleted successfully with ID: ' . $lesson_id);
    } else {
        $response = ['status' => 'error', 'message' => 'Error deleting lesson'];
        log_message('error', 'Failed to delete lesson with ID: ' . $lesson_id);
    }

    // Send response to client
    return $this->response->setJSON($response);
    log_message('info', 'Response sent to client.');
}

public function lesson_video_type_post() {
    // Decode JSON input
    $input_data = json_decode(file_get_contents("php://input"), true);
    $lesson_id = $input_data['lesson_id'] ?? null;

    // Check if lesson ID is provided
    if (empty($lesson_id)) {
        $response = ['status' => 'error', 'message' => 'Lesson ID is required'];
        return $this->response->setJSON($response);
        return;
    }

    // Retrieve lesson details including section_id and section title
    $lesson = $this->admin_model->get_by_query([
        'select' => 'lesson.id, lesson.lesson_type, lesson.video_type, lesson.video_url, lesson.section_id, course_section.title AS section_title',
        'from' => 'lesson',
        'join' => [['course_section', 'course_section.id = lesson.section_id', 'left']],
        'where' => ['lesson.id' => $lesson_id],
        'return' => 'row_array'
    ]);

    // Check if the lesson exists
    if ($lesson) {
        $response = [
            'status' => 'success',
            'lesson' => $lesson
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Lesson not found'
        ];
    }

    // Send response
    return $this->response->setJSON($response);
}


public function update_lesson_device_post() {
    // TODO: Replace with service: Config\Services::form_validation();

    // Set validation rules
    $this->form_validation->set_rules('lesson_id', 'Lesson ID', 'required');
    $this->form_validation->set_rules('course_id', 'Course ID', 'required');
    $this->form_validation->set_rules('title', 'Title', 'required');
    $this->form_validation->set_rules('section_id', 'Section ID', 'required');
    $this->form_validation->set_rules('lesson_type', 'Lesson Type', 'required');

    if ($this->form_validation->run() == FALSE) {
        $response = [
            'status' => 'error',
            'message' => strip_tags(validation_errors())
        ];
        return $this->response->setJSON($response);
    }

    // Retrieve lesson ID and check if it exists
    $lesson_id = $this->request->getPost('lesson_id');
    $lesson = $this->db_helper_model->get_by_id('lesson', $lesson_id);

    if (!$lesson) {
        $response = ['status' => 'error', 'message' => 'Lesson not found'];
        return $this->response->setJSON($response);
    }

    // Prepare lesson data for update
    $data = [
        'course_id' => $this->request->getPost('course_id'),
        'title' => $this->request->getPost('title'),
        'section_id' => $this->request->getPost('section_id'),
        'summary' => $this->request->getPost('summary'),
        'lesson_type' => $this->request->getPost('lesson_type'),
        'last_modified' => time(),
        'attachment_type' => null
    ];

    // Handle video types
    if ($data['lesson_type'] == 'video') {
        $lesson_provider = $this->request->getPost('lesson_provider');
        if ($lesson_provider == 'youtube' || $lesson_provider == 'vimeo') {
            $data['video_url'] = $this->request->getPost('video_url');
            $data['duration'] = $this->request->getPost('duration');
            $data['video_type'] = $lesson_provider;
        } elseif ($lesson_provider == 'mydevice') {
            $data['video_type'] = 'mydevice';
            // Attempt to upload video file if a new file is provided
            if (isset($_FILES['userfileMe'])) {
                $video_file_name = $this->upload_file('userfileMe', 'uploads/videos/');
                if ($video_file_name) {
                    $data['video_upload'] = $video_file_name;
                } else {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Video upload failed']);
                }
            } else {
                // Retain the existing video upload if no new file is provided
                $data['video_upload'] = $lesson['video_upload'];
            }
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid lesson provider']);
        }
    } else {
        // Handle other types with an attachment
        if (isset($_FILES['attachment'])) {
            $attachment_file_name = $this->upload_file('attachment', 'uploads/lesson_files/');
            if ($attachment_file_name) {
                $data['attachment'] = $attachment_file_name;
            }
        } else {
            // Retain the existing attachment if no new file is provided
            $data['attachment'] = $lesson['attachment'];
        }
    }

    // Update the database
    $this->admin_model->update_table('lesson', $data, ['id' => $lesson_id]);

    $response = ['status' => 'success', 'message' => 'Lesson updated successfully'];
    return $this->response->setJSON($response);
}





public function add_lesson_with_attachment_post() {
    log_message('info', 'Starting add_lesson_with_attachment_post function.');

    // Retrieve POST data
    $title = $this->request->getPost('title');
    $course_id = $this->request->getPost('course_id');
    $section_id = $this->request->getPost('section_id');
    $lesson_type = $this->request->getPost('lesson_type');
    $summary = $this->request->getPost('summary');
    $attachment_type = $this->request->getPost('attachment_type'); // e.g., pdf, image, document, text

    // Validate required fields
    if (empty($title) || empty($course_id) || empty($section_id) || empty($lesson_type)) {
        $response = ['status' => 'error', 'message' => 'Missing required fields'];
        log_message('error', 'Missing required fields.');
        return $this->response->setJSON($response);
        return;
    }

    // Handle file upload if applicable
    $attachment_file_name = null;
    if (isset($_FILES['attachment'])) {
        $upload_path = 'uploads/lesson_files/';
        $allowed_types = $this->get_allowed_file_types($attachment_type);

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = implode('|', $allowed_types);
        $config['encrypt_name'] = true;

        // TODO: Replace with Config\Services::upload(null, config);

        if ($this->upload->do_upload('attachment')) {
            $upload_data = $this->upload->data();
            $attachment_file_name = $upload_data['file_name'];
            log_message('info', 'File uploaded successfully: ' . $attachment_file_name);
        } else {
            $response = ['status' => 'error', 'message' => $this->upload->display_errors()];
            log_message('error', 'File upload error: ' . $this->upload->display_errors());
            return $this->response->setJSON($response);
            return;
        }
    }

    // Prepare lesson data for insertion
    $lesson_data = [
        'title' => $title,
        'course_id' => $course_id,
        'section_id' => $section_id,
        'lesson_type' => $lesson_type,
        'attachment' => $attachment_file_name,
        'attachment_type' => $attachment_type,
        'summary' => $summary,
        'date_added' => date('Y-m-d H:i:s'),
        'last_modified' => date('Y-m-d H:i:s'),
    ];

    // Insert lesson data into the database
    $insert_result = $this->admin_model->insert_table('lesson', $lesson_data);
    $lesson_id = db()->insertID();

    if ($insert_result && $lesson_id) {
        $response = [
            'status' => 'success',
            'message' => 'Lesson added successfully',
            'lesson_id' => $lesson_id,
        ];
        log_message('info', 'Lesson added successfully with ID: ' . $lesson_id);
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to add lesson'];
        log_message('error', 'Failed to insert lesson into the database.');
    }

    return $this->response->setJSON($response);
}

private function get_allowed_file_types($attachment_type) {
    switch ($attachment_type) {
        case 'pdf':
            return ['pdf'];
        case 'image':
            return ['jpg', 'jpeg', 'png'];
        case 'document':
            return ['doc', 'docx', 'txt'];
        case 'text':
            return ['txt'];
        default:
            return [];
    }
}












public function update_lesson_with_attachment_post() {
    log_message('info', 'Starting update_lesson_with_attachment_post function.');

    // Retrieve POST data
    $lesson_id = $this->request->getPost('lesson_id');
    $title = $this->request->getPost('title');
    $course_id = $this->request->getPost('course_id');
    $section_id = $this->request->getPost('section_id');
    $lesson_type = $this->request->getPost('lesson_type');
    $summary = $this->request->getPost('summary');
    $attachment_type = $this->request->getPost('attachment_type'); // e.g., pdf, image, document, text

    // Validate required fields
    if (empty($lesson_id) || empty($title) || empty($course_id) || empty($section_id) || empty($lesson_type)) {
        $response = ['status' => 'error', 'message' => 'Missing required fields'];
        log_message('error', 'Missing required fields.');
        return $this->response->setJSON($response);
        return;
    }

    // Check if lesson exists
    $lesson = $this->db_helper_model->get('lesson', ['id' => $lesson_id], 'row');
    if (!$lesson) {
        $response = ['status' => 'error', 'message' => 'Lesson not found'];
        log_message('error', 'Lesson not found with ID: ' . $lesson_id);
        return $this->response->setJSON($response);
        return;
    }

    // Handle file upload if applicable
    $attachment_file_name = $lesson->attachment; // Retain existing file if no new upload
    if (isset($_FILES['attachment'])) {
        $upload_path = 'uploads/lesson_files/';
        $allowed_types = $this->get_allowed_file_types($attachment_type);

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = implode('|', $allowed_types);
        $config['encrypt_name'] = true;

        // TODO: Replace with Config\Services::upload(null, config);

        if ($this->upload->do_upload('attachment')) {
            // Delete the previous file if it exists
            if (!empty($lesson->attachment) && file_exists($upload_path . $lesson->attachment)) {
                unlink($upload_path . $lesson->attachment);
            }

            $upload_data = $this->upload->data();
            $attachment_file_name = $upload_data['file_name'];
            log_message('info', 'File uploaded successfully: ' . $attachment_file_name);
        } else {
            $response = ['status' => 'error', 'message' => $this->upload->display_errors()];
            log_message('error', 'File upload error: ' . $this->upload->display_errors());
            return $this->response->setJSON($response);
            return;
        }
    }

    // Prepare lesson data for update
    $lesson_data = [
        'title' => $title,
        'course_id' => $course_id,
        'section_id' => $section_id,
        'lesson_type' => $lesson_type,
        'attachment' => $attachment_file_name,
        'attachment_type' => $attachment_type,
        'summary' => $summary,
        'last_modified' => date('Y-m-d H:i:s'),
    ];

    // Update lesson data in the database
    $update_result = $this->admin_model->update_table('lesson', $lesson_data, ['id' => $lesson_id]);

    if ($update_result) {
        $response = [
            'status' => 'success',
            'message' => 'Lesson updated successfully',
            'lesson_id' => $lesson_id,
        ];
        log_message('info', 'Lesson updated successfully with ID: ' . $lesson_id);
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to update lesson'];
        log_message('error', 'Failed to update lesson in the database.');
    }

    return $this->response->setJSON($response);
}




}