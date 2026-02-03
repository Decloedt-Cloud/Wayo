<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

// =====================================================
// LANGUAGE PREFIX CONFIGURATION
// =====================================================
// Supported language codes mapped to language names
$supported_langs = array(
    'fr' => 'french',
    'en' => 'english',
    'ar' => 'arabic',
    'es' => 'spanish',
    'nl' => 'dutch'
);

// Detect language prefix from URL (handles subdirectory installations)
$lang_prefix = '';
$lang_segment = '';
if (isset($_SERVER['REQUEST_URI'])) {
    $request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Get the script path to determine base directory
    $script_name = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $base_dir = rtrim(dirname($script_name), '/');
    
    // Remove base directory from request URI to get relative path
    $relative_path = $request_uri;
    if (!empty($base_dir) && strpos($request_uri, $base_dir) === 0) {
        $relative_path = substr($request_uri, strlen($base_dir));
    }
    
    // Parse the relative path
    $uri_parts = explode('/', trim($relative_path, '/'));
    
    // Check if first segment is a language code
    if (!empty($uri_parts[0]) && array_key_exists($uri_parts[0], $supported_langs)) {
        $lang_prefix = $uri_parts[0];
        $lang_segment = $uri_parts[0] . '/';
        
        // Store detected language in a constant for later use
        if (!defined('DETECTED_LANG_CODE')) {
            define('DETECTED_LANG_CODE', $lang_prefix);
            define('DETECTED_LANG_NAME', $supported_langs[$lang_prefix]);
        }
    }
}

// =====================================================
// LANGUAGE-PREFIXED FRONTEND ROUTES
// =====================================================
foreach ($supported_langs as $code => $lang_name) {
    // Home routes with language prefix
    $route[$code] = 'home/index/' . $code;
    $route[$code . '/home'] = 'home/index/' . $code;
    
    // Communities
    $route[$code . '/communities'] = 'home/communities/' . $code;
    $route[$code . '/communities/(.+)'] = 'home/communities/' . $code . '/$1';
    
    // Tutorial / How it works
    $route[$code . '/tutorial'] = 'home/tutorial/' . $code;
    $route[$code . '/getting_started'] = 'home/tutorial/' . $code;
    
    // Help Center / FAQ
    $route[$code . '/help-center'] = 'home/faq/' . $code;
    $route[$code . '/faq'] = 'home/faq/' . $code;
    
    // Contact / Support
    $route[$code . '/contact'] = 'home/contact/' . $code;
    $route[$code . '/support'] = 'home/contact/' . $code;
    $route[$code . '/support/send'] = 'home/contact/send/' . $code;
    
    // About
    $route[$code . '/about'] = 'home/about/' . $code;
    
    // Affiliation
    $route[$code . '/affiliation'] = 'home/affiliation/' . $code;
    
    // Terms and Privacy
    $route[$code . '/terms'] = 'home/terms_conditions/' . $code;
    $route[$code . '/terms_conditions'] = 'home/terms_conditions/' . $code;
    $route[$code . '/privacy_policy'] = 'home/privacy_policy/' . $code;
    
    // Community details
    $route[$code . '/community_details'] = 'home/community_details/' . $code;
    $route[$code . '/community_details/(.+)'] = 'home/community_details/$1/' . $code;
    
    // Trends / Articles
    $route[$code . '/trends'] = 'articles/index/' . $code;
    $route[$code . '/trends/category/(:any)'] = 'articles/category/$1/' . $code;
    $route[$code . '/trends/tag/(:any)'] = 'articles/tag/$1/' . $code;
    $route[$code . '/trends/page/(:any)'] = 'articles/page/$1/' . $code;
    $route[$code . '/trends/(:any)'] = 'articles/show/$1/' . $code;
    
    // Teachers
    $route[$code . '/teachers'] = 'home/teachers/' . $code;
    $route[$code . '/teachers/(.+)'] = 'home/teachers/$1/' . $code;
    
    // Events
    $route[$code . '/events'] = 'home/events/' . $code;
    $route[$code . '/events/(.+)'] = 'home/events/$1/' . $code;
    
    // Gallery
    $route[$code . '/gallery'] = 'home/gallery/' . $code;
    $route[$code . '/gallery/(.+)'] = 'home/gallery/$1/' . $code;
    $route[$code . '/gallery_view/(:any)'] = 'home/gallery_view/$1/' . $code;
    $route[$code . '/gallery_view/(:any)/(.+)'] = 'home/gallery_view/$1/$2/' . $code;
    
    // Noticeboard
    $route[$code . '/noticeboard'] = 'home/noticeboard/' . $code;
    $route[$code . '/noticeboard/(.+)'] = 'home/noticeboard/$1/' . $code;
    $route[$code . '/notice_details/(:any)'] = 'home/notice_details/$1/' . $code;
    
    // Admission routes with language prefix
    $route[$code . '/join/community'] = 'admission/online_admission/' . $code;
    $route[$code . '/join/community/(.+)'] = 'admission/online_admission/$1/' . $code;
    $route[$code . '/join/member'] = 'admission/online_admission_student/' . $code;
    $route[$code . '/join/member/(.+)'] = 'admission/online_admission_student/$1/' . $code;
    
    // Webinaire
    $route[$code . '/webinaire'] = 'home/webinaire/' . $code;
}

$route['superadmin/community_list'] = 'superadmin/school';
$route['superadmin/community_list/(:any)'] = 'superadmin/school/$1';

$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;

// Routes for home masking
$route['getting_started'] = 'home/tutorial';
$route['help-center'] = 'home/faq';
$route['communities'] = 'home/communities';
$route['communities/(.+)'] = 'home/communities/$1';

// Trends Routes (formerly Blog)
$route['trends'] = 'articles/index';
$route['trends/category/(:any)'] = 'articles/category/$1';
$route['trends/tag/(:any)'] = 'articles/tag/$1';
$route['trends/page/(:any)'] = 'articles/page/$1';
$route['trends/(:any)'] = 'articles/show/$1';

$route['tutorial'] = 'home/tutorial';
$route['contact'] = 'home/contact';
$route['support'] = 'home/contact';
$route['support/send'] = 'home/contact/send';
$route['about'] = 'home/about';
$route['faq'] = 'home/faq';
$route['affiliation'] = 'home/affiliation';
$route['terms'] = 'home/terms_conditions';
$route['terms_conditions'] = 'home/terms_conditions';
$route['privacy_policy'] = 'home/privacy_policy';
$route['community_details'] = 'home/community_details';
$route['community_details/(.+)'] = 'home/community_details/$1';

// Routes for admission masking
$route['join/community'] = 'admission/online_admission';
$route['join/community/(.+)'] = 'admission/online_admission/$1';
$route['join/member'] = 'admission/online_admission_student';
$route['join/member/(.+)'] = 'admission/online_admission_student/$1';

// Routes for app rewriter
$route['app/join_school'] = 'student/join_school';
$route['app/join_school/(.+)'] = 'student/join_school/$1';



// API Routes
$route['api/login'] = 'api/Admin/login';
$route['api/menu'] = 'api/Admin/menu';


$route['api/edit'] = 'api/Admin/editProfile';

$route['api/editPassword'] = 'api/Admin/updatePassword';


$route['api/getDashboard'] = 'api/Admin/get_dashboard_data';


$route['api/getexpenses/(:num)'] = 'api/Admin/expense/$1';


$route['api/getinvoices/(:num)'] = 'api/Admin/invoices/$1';


$route['api/onlineadmission'] = 'api/Admin/onlineadmission';
$route['api/onlineadmissionList/(:num)'] = 'api/Admin/onlineadmissionList/$1';
$route['api/onlineadmissionDelete/(:num)'] = 'api/Admin/onlineadmissionDelete/$1';
$route['api/onlineadmissionListApproved/(:num)'] = 'api/Admin/onlineadmissionListApproved/$1';
$route['api/onlineadmissionListApprovedAndDesapproved/(:num)'] = 'api/Admin/onlineadmissionListApprovedAndDesapproved/$1';
$route['api/approveOnlineAdmissions/(:num)']['PUT'] = 'api/Admin/approveOnlineAdmissions/$1';
$route['api/deactivate/(:num)']['PUT'] = 'api/Admin/deactivate/$1';
$route['api/activate/(:num)']['PUT'] = 'api/Admin/activate/$1';
$route['api/onlineadmissionEdit/(:num)']['PUT'] = 'api/Admin/onlineadmissionEdit/$1';
$route['api/onlineadmission'] = 'api/Admin/onlineadmission';
$route['api/fetchStudentsByName/(:num)/(:any)'] = 'api/Admin/fetchStudentsByName/$1/$2';

$route['api/school/create']= 'api/Admin/online_admission_school'; 
$route['api/school/update'] = 'api/Admin/update_school';
$route['api/school/delete/(:num)'] = 'api/Admin/delete_school/$1'; 
$route['api/schools'] = 'api/Admin/schools';
$route['api/categoriesByName']= 'api/Admin/categoriesByName'; 
$route['api/schools_by_category']= 'api/Admin/schools_by_category'; 
$route['api/search_schools']= 'api/Admin/search_schools'; 



$route['api/create_admin']= 'api/Admin/create_admin'; 
$route['api/get_admin']= 'api/Admin/all_admins';
$route['api/fetch_admins']= 'api/Admin/fetch_admins_by_name';
$route['api/schoolsName']= 'api/Admin/all_school_names';
$route['api/editAdmin/(:num)']= 'api/Admin/edit_admin/$1';
$route['api/deleteAdmin/(:num)']= 'api/Admin/Deladmin/$1';

$route['api/teachers']= 'api/Admin/all_teacher';
$route['api/departments']= 'api/Admin/department';
$route['api/teachers/search'] = 'api/Admin/search';
$route['api/teachers/Create'] = 'api/Admin/create_teacher';
$route['api/teachers/edit/(:num)'] = 'api/Admin/edit_teacher/$1';
$route['api/teachers/delete/(:num)'] = 'api/Admin/delete_teacher/$1';
$route['api/teachersById/(:num)'] = 'api/Admin/teacher_by_id/$1';
$route['api/teacherPermission']= 'api/Admin/add_teacher_permission';
$route['api/classes']= 'api/Admin/classes';
$route['api/teachers_by_class/(:num)'] = 'api/Admin/teachers_by_class/$1';
$route['api/assign_teacher_permission_to_class']= 'api/Admin/assign_teacher_permission_to_class';
$route['api/get_class_id_by_name']= 'api/Admin/get_class_id_by_name';






$route['api/GetPaymentSettings/(:num)'] = 'api/Admin/payment_settings/$1';
$route['api/CreateClass'] = 'api/Admin/class_create';
$route['api/GetClass'] = 'api/Admin/class';
$route['api/UpdateClass'] = 'api/Admin/class_update';
$route['api/DeleteClass'] = 'api/Admin/class_del';
$route['api/SubmitQuizResponse'] = 'api/Admin/submit_quiz_responses';

$route['api/Instructor'] = 'api/Admin/all_teacher_names';
$route['api/subjects'] = 'api/Admin/subjects_names';
$route['api/CreateCourse']  = 'api/Admin/create_course';
$route['api/StatusCourse'] = 'api/Admin/course_status_counts';
$route['api/courses_by_class/(:any)'] = 'api/Admin/courses_by_class/$1';
$route['api/coursesByTeacher/(:any)'] = 'api/Admin/courses_by_user/$1';
$route['api/CoursesByStatus/(:any)'] = 'api/Admin/courses_by_status/$1';
$route['api/AllCourses'] = 'api/Admin/all_courses';
$route['api/LessonsAndSections/(:num)'] = 'api/Admin/lessons_and_sections/$1';
$route['api/Course/ChangeStatus/(:num)'] = 'api/Admin/change_course_status/$1';
$route['api/EditCourse/(:num)']  = 'api/Admin/edit_course/$1';
$route['api/GetCourse/(:num)']  = 'api/Admin/course_details/$1';
$route['api/DeleteCourse/(:num)']  = 'api/Admin/delete_course/$1';
$route['api/course/(:num)/add_section'] = 'api/Admin/add_course_section/$1';
$route['api/course/(:num)/sections'] = 'api/Admin/sections/$1';
$route['api/section/(:num)/quizzes'] = 'api/Admin/quizzes_by_section/$1';
$route['api/course/(:num)/quizzes'] = 'api/Admin/quizzes_by_course/$1';
$route['api/quizzes/course/(:num)'] = 'api/Admin/quizzes/$1';
$route['api/quizzes/section/(:num)'] = 'api/Admin/quizzes/null/$1';
$route['api/course/(:num)/section/(:num)/edit'] = 'api/Admin/sections_title/$1/$2';
$route['api/course/(:num)/section/(:num)/delete'] = 'api/Admin/sections_del/$1/$2';
$route['api/update_quiz_order/(:num)'] = 'api/Admin/update_quiz_order/$1';
$route['api/quizzes/AddQuiz'] = 'api/Admin/add_quiz';
$route['api/quizzes/UpdateQuiz'] = 'api/Admin/update_quiz';
$route['api/quizzes/delete'] = 'api/Admin/delete_quiz';
$route['api/quizzes/AddQuestion'] = 'api/Admin/add_question';
$route['api/quizzes/GetQuestions/(:num)'] = 'api/Admin/get_quiz_questions/$1';
$route['api/quizzes/EditQuestions'] = 'api/Admin/edit_question';
$route['api/quizzes/DeleteQuestions'] = 'api/Admin/delete_question';
$route['api/lesson/create'] = 'api/Admin/add_lesson';
$route['api/AllLessons/(:num)'] = 'api/Admin/all_lesson_types/$1';
$route['api/AffectUserToSchool']='api/Admin/associate_user_with_school';
$route['api/GetStudentsList']='api/Admin/get_students_list';
$route['api/ApproveStudent']='api/Admin/approve_student';
$route['api/GetSectionsList'] = 'api/Admin/section';
$route['api/DeleteStudent'] = 'api/Admin/delete_student';
$route['api/NumberStudentOnlineAdmission'] = 'api/Admin/count_student_online_admission';
$route['api/GetStudentIdByUserId'] = 'api/Admin/get_student_id';
$route['api/GetSectionsForClass'] = 'api/Admin/sections_for_class';
$route['api/GetUserName/(:num)']='api/Admin/user_name_by_student_id/$1';





$route['api/CreateSubject'] = 'api/Admin/create_subject';
$route['api/GetClassBySchoolId/(:num)'] = 'api/Admin/get_classes_by_school_id/$1';
$route['api/GetSubjectsBySchoolId/(:num)'] = 'api/Admin/get_subjects_by_school_id/$1';
$route['api/GetCorrectAnswers/(:num)'] = 'api/Admin/all_quiz_responses/$1 ';



$route['api/CreateEvent'] = 'api/Admin/create_event';
$route['api/GetEvent/(:num)']= 'api/Admin/events_by_school_id/$1';
$route['api/DeleteEvent/(:num)']= 'api/Admin/delete_event/$1';
$route['api/EditEvent/(:num)']= 'api/Admin/edit_event/$1';




$route['api/GetExams/(:num)/(:num)'] = 'api/Admin/exams_by_school_id/$1/$2';
$route['api/CreateExams'] = 'api/Admin/create_exam';
$route['api/EditExams'] = 'api/Admin/edit_exam';
$route['api/DeleteExams/(:num)'] = 'api/Admin/delete_exam/$1';



$route['api/GetBooks/(:num)/(:num)'] = 'api/Admin/books_by_school_id/$1/$2';
$route['api/CreateBook'] = 'api/Admin/create_book';
$route['api/EditBook'] = 'api/Admin/edit_book';
$route['api/DeleteBook/(:num)'] = 'api/Admin/delete_book/$1';



$route['api/GetGrades/(:num)/(:num)'] = 'api/Admin/grades_by_school_id/$1/$2';
$route['api/CreateGrade'] = 'api/Admin/create_grade';
$route['api/EditGrade'] = 'api/Admin/edit_grade';
$route['api/DeleteGrade/(:num)'] = 'api/Admin/delete_grade/$1';



$route['api/CreateDepartment'] = 'api/Admin/create_department';
$route['api/GetDepartments/(:num)']= 'api/Admin/departments_by_school_id/$1';
$route['api/UpdateDepartment'] = 'api/Admin/update_department';
$route['api/DeleteDepartment'] = 'api/Admin/delete_department';


$route['api/getexpenses/(:num)'] = 'api/Admin/expense/$1';
$route['api/CreateExpenseCategory'] = 'api/Admin/create_expense_category';
$route['api/EditExpenseCategory'] = 'api/Admin/edit_expense_category';
$route['api/DeleteExpenseCategory'] = 'api/Admin/delete_expense_category';
$route['api/GetExpenseCategories/(:num)/(:num)/(:num)'] = 'api/Admin/expense_categories/$1/$2/$3';
$route['api/GetExpenses/(:num)'] = 'api/Admin/get_expenses/$1';
$route['api/CreateExpense'] = 'api/Admin/create_expense';
$route['api/EditExpense'] = 'api/Admin/edit_expense';
$route['api/DeleteExpense'] = 'api/Admin/delete_expense';


$route['api/GetSessions'] = 'api/Admin/sessions';
$route['api/CreateSession'] = 'api/Admin/create_session';
$route['api/EditSession'] = 'api/Admin/edit_session';
$route['api/DeleteSession/(:num)'] = 'api/Admin/delete_session/$1';



$route['api/createInvoice'] = 'api/Admin/create_invoice';
$route['api/PaidInvoice'] = 'api/Admin/paid_invoice';
$route['api/getInvoiceStatus'] = 'api/Admin/invoice_status';




$route['api/AddMarks'] = 'api/Admin/add_marks';
$route['api/FilterExams/(:num)'] = 'api/Admin/filter_exams_by_school_id/$1';
$route['api/FilterStudents'] = 'api/Admin/filter_student';
$route['api/GetSectionsByClassId/(:num)'] = 'api/Admin/get_sections_by_class_id/$1';
$route['api/GetSubjectIdByName/(:any)'] = 'api/Admin/get_subject_id_by_name/$1';
$route['api/UpdateMarks'] = 'api/Admin/update_marks';

$route['api/CreateClassRoom'] = 'api/Admin/add_class_room';
$route['api/UpdateClassRoom/(:num)'] = 'api/Admin/update_class_room/$1';
$route['api/DeleteClassRoom/(:num)'] = 'api/Admin/delete_class_room/$1';
$route['api/GetClassRoom/(:num)'] = 'api/Admin/get_class_room/$1';


$route['api/CreateRoutine'] = 'api/Admin/create_routine';
$route['api/UpdateRoutine/(:num)'] = 'api/Admin/update_routine/$1';
$route['api/DeleteRoutine/(:num)'] = 'api/Admin/delete_routine/$1';
$route['api/GetRoutine/(:num)'] = 'api/Admin/routines_by_school_id/$1';
$route['api/GetRoutineByClassAndSection/(:num)/(:num)'] = 'api/Admin/routines_by_class_and_section/$1/$2';



$route['api/GetInvoice'] = 'api/Admin/invoice_by_date_range';
$route['api/invoices/parent'] = 'api/Admin/get_invoice_by_parent_id';
$route['api/invoices/CreateSingleInvoice'] = 'api/Admin/create_single_invoice';
$route['api/invoices/CreateMassInvoice'] = 'api/Admin/create_mass_invoice';
$route['api/invoices/update/(:num)'] = 'api/Admin/update_invoice/$1';
$route['api/invoices/delete/(:num)'] = 'api/Admin/delete_invoice/$1';
$route['api/GetStudentFreeFilter'] = 'api/Admin/invoices_by_filter';
$route['api/Getclasses']= 'api/Admin/classe';
















$route['api/GetAppropriateCourses'] = 'api/Admin/get_appropriate_courses';


//Partie Quiz (stocker les reponses dans la table question_quiz)
$route['api/SubmitQuiz'] = 'api/Admin/submit_quiz';
$route['api/checkProgress/(:num)/(:num)'] = 'api/Admin/check_progress/$1/$2';

// Forget Password
$route['api/VerifyEmail'] = 'api/Admin/send_reset_link_api';
$route['api/ResendCode'] =  'api/Admin/resend_code_api';
$route['api/VerifyCode'] =  'api/Admin/verify_code_api';
$route['api/GetUserIdByEmail'] = 'api/Admin/getUserIdByEmail';
$route['api/UpdatePassword'] = 'api/Admin/update_Password';

//register 
$route['api/Register']  = 'api/Admin/register';












$route['api/SetSmtpSettings'] = 'api/Admin/set_smtp_settings';
$route['api/GetSmtpSettings'] = 'api/Admin/get_smtp_settings';
$route['api/GetAllSmtpSettings'] = 'api/Admin/get_alls_smtp_settings';



$route['api/SetPaymentSettings'] = 'api/Admin/set_payment_settings';

$route['api/UpdatePaypalSettings'] = 'api/Admin/update_paypal_settings';
$route['api/UpdateSystemCurrency'] = 'api/Admin/update_system_currency';
$route['api/UpdateStripeSettings'] = 'api/Admin/update_stripe_settings';
$route['api/GetSchoolSettings/(:num)'] = 'api/Admin/school_settings/$1';
$route['api/UpdateSchoolSettings/(:num)'] = 'api/Admin/school_settings_update/$1';
$route['api/GetSystemSettings/(:num)'] = 'api/Admin/system_settings/$1';
$route['api/UpdateSystemSettings/(:num)'] = 'api/Admin/update_system_settings/$1';


$route['api/GetSystemLogo/(:num)'] = "api/Admin/system_logo/$1";
$route['api/UpdateSystemLogo/(:num)'] = "api/Admin/update_system_logo/$1";


$route['api/GetLanguage/(:num)'] = 'api/Admin/language/$1';
$route['api/GetSelectedLanguage/(:num)'] = 'api/Admin/selected_language/$1';
$route['api/AddLanguage/(:num)'] = 'api/Admin/add_language/$1';
$route['api/UpdateLanguage/(:num)'] = 'api/Admin/update_language/$1';
$route['api/GetPhrases/(:any)'] = 'api/Admin/phrases/$1';
$route['api/GetWebsiteSettings/(:num)'] = 'api/Admin/website_settings/$1';
$route['api/GetGeneralSettings'] = 'api/Admin/general_settings';
$route['api/GetOthersSettings'] = 'api/Admin/other_settings';
$route['api/UpdateOthersSettings'] = 'api/Admin/other_settings_update';

$route['api/GetTermsAndConditionsSettings'] = 'api/Admin/terms_and_conditions_settings';
$route['api/UpdateTermsAndConditionsSettings'] = 'api/Admin/upterms_and_conditions_settings';

$route['api/GetPrivacyPolicySettings'] = 'api/Admin/privacy_policy_settings';
$route['api/UpdatePrivacyPolicySettings'] = 'api/Admin/privacy_policy_settings_update';

$route['api/CreateGallery'] = 'api/Admin/create_gallery';
$route['api/GetGalleries'] = 'api/Admin/galleries';
$route['api/GetGalleryImage'] = 'api/Admin/gallery_images_by_id';
$route['api/DeleteGallery'] = 'api/Admin/gallery_by_id';
$route['api/DeleteGalleryImage'] = 'api/Admin/gallery_image_by_id';

$route['api/GetHomePageSlider'] = 'api/Admin/homepage_slider';
$route['api/UpdateSliders'] = 'api/Admin/update_sliders';

















// Noticeboard Routes under Admin Controller
$route['api/notices/create'] = 'api/Admin/create_noticeboard'; // Create a new notice
$route['api/noticeboard/get/(:num)'] = 'api/Admin/noticeboard/$1'; // Fetch a single notice by ID
$route['api/allnoticeboard'] = 'api/Admin/fetch_all_notices'; // Fetch all notices
$route['api/notices/update/(:num)'] = 'api/Admin/update_noticeboard/$1'; // Update a notice by ID
$route['api/notices/delete/(:num)'] = 'api/Admin/noticeboard_delete/$1'; // Delete a notice by ID
$route['api/notices/filter/(:num)'] = 'api/Admin/filter_notices/$1';

// Route for filtering by year and month
$route['api/notices/filter/(:num)/(:num)'] = 'api/Admin/filter_notices/$1/$2';

// Route for filtering by year, month, and day
$route['api/notices/filter/(:num)/(:num)/(:num)'] = 'api/Admin/filter_notices/$1/$2/$3';
$route['api/GetDays'] = 'api/Admin/unique_notice_days';





$route['api/sessions'] = 'api/Admin/session';
$route['api/StudentsForPromotion'] = 'api/Admin/students_for_promotion';
$route['api/GetPromotedClasses'] = 'api/Admin/classes_promote';
$route['api/PromoteStudent'] = 'api/Admin/promote_student';



$route['api/GetQuizResult'] = 'api/Admin/quiz_result';
$route['api/GetQuizByClass'] = 'api/Admin/quizzes_by_class';
$route['api/GetQuizzesNames'] = 'api/Admin/all_quizzes';





// Route for filtering attendance
$route['api/attendance/filter'] = 'api/Admin/filter_attendance';
$route['api/attendance/student_list'] = 'api/Admin/student_list';
$route['api/attendance/CreateAttendance'] = 'api/Admin/create_attendance';
$route['api/attendance/bulk_update'] = 'api/Admin/bulk_attendance_update';
$route['api/attendance/monthly_summary'] = 'api/Admin/monthly_attendance_summary';
$route['api/attendance/UpdateAttendanceStatus'] = 'api/Admin/update_attendance_status';
$route['api/attendance/ToggleAttendanceStatus'] = 'api/Admin/toggle_attendance_status';






















//Route of Session Manager 
$route['api/GetSessionManager/list'] = 'api/Admin/list';
$route['api/CreateSessionManager/create'] = 'api/Admin/create';
$route['api/UpdateSessionManager/(:num)'] = 'api/Admin/update/$1';
$route['api/DeleteSessionManager/delete/(:num)'] = 'api/Admin/delete/$1';
$route['api/ActivateSessionManager/activate/(:num)'] = 'api/Admin/activate/$1';
$route['api/DesactivateSessionManager/deactivate/(:num)'] = 'api/Admin/deactivate/$1';





//Route of Accountant 
$route['api/GetAccountant'] = 'api/Admin/accountant_users';
$route['api/CreateAccountant/create'] = 'api/Admin/create_accountant';
$route['api/UpdateAccountant/update/(:num)'] = 'api/Admin/update_accountant/$1';
$route['api/DeleteAccountant/delete/(:num)'] = 'api/Admin/delete_accountant/$1';










$route['api/GetLibrarians'] = 'api/Admin/all_librarians';
$route['api/CreateLibrarian/create'] = 'api/Admin/create_librarian';
$route['api/UpdateLibrarian/update/(:num)'] = 'api/Admin/update_librarian/$1';
$route['api/DeleteLibrarian/delete/(:num)'] = 'api/Admin/delete_librarian/$1';















$route['api/student/DownloadCsv'] = 'api/Admin/download_csv';




















// Route for syllabus operations
$route['syllabus/(:any)'] = 'api/Admin/syllabus_operations/$1'; // Handles create, delete operations
$route['syllabus/(:any)/(:num)/(:num)'] = 'api/Admin/syllabus_operations/$1/$2/$3'; // Handles get operation
$route['api/GetSyllabus/(:any)/(:num)'] = 'api/Admin/syllabus_by_class_section/$1/$2'; // Handles delete operation by ID
$route['api/CreateSyllabus'] = 'api/Admin/create_syllabus';
$route['api/DeleteSyllabus'] = 'api/Admin/delete_syllabus';


















$route['api/GetBookIssue'] = 'api/Admin/book_issues';
$route['api/CreateBookIssue'] = 'api/Admin/create_book_issue';
$route['api/EditBookIssue/(:num)'] = 'api/Admin/update_book_issue/$1';
$route['api/ReturnBookIssue/(:num)'] = 'api/Admin/return_book_issue/$1';
$route['api/DeleteBookIssue/(:num)'] = 'api/Admin/delete_book_issue/$1';
$route['api/GetBooksBySchool/(:num)'] = 'api/Admin/books_by_school/$1';
$route['api/ClassesBySchool/(:num)'] = 'api/Admin/classes_by_school/$1';
$route['api/StudentsBySchool/(:num)'] = 'api/Admin/students_by_school/$1';



















$route['api/GetAddons'] = 'api/Admin/addon';
$route['api/CreateAddon'] = 'api/Admin/create_addon';
$route['api/RemoveAddon/(:num)'] = 'api/Admin/remove_addon/$1';










$route['api/SchoolsNotApproved'] = 'api/Admin/not_approved_schools';
$route['api/ApprovedSchool/(:num)'] = 'api/Admin/approve_school/$1';
$route['api/DelSchool/(:num)'] = 'api/Admin/del_school/$1';
$route['api/NumberSchoolsNotApproved'] = 'api/Admin/count_not_approved_schools';
$route['api/FilterStudents/(:num)/(:num)'] = 'api/Admin/students_by_class_and_section/$1/$2';





$route['addons/exams/exam_questions/(:num)/(:any)/(:num)'] = 'courses/exam_questions/$1/$2/$3';
$route['addons/exams/exam_questions/(:num)'] = 'courses/exam_questions/$1';
$route['addons/exams/ajax_sort_question'] = 'courses/ajax_sort_question';















$route['api/AddLessonYoutubeVideo'] = 'api/Admin/add_lesson_youtube';
$route['api/GetYoutubeVideoDuration'] = 'api/Admin/get_youtube_video_duration';
$route['api/DisplayYoutubeVideo'] = 'api/Admin/display_youtube';
$route['api/UpdateLessonYoutubeVideo'] = 'api/Admin/update_lesson_youtube';
$route['api/GetLessonDetails'] = 'api/Admin/get_lesson_details';
$route['api/DeleteLesson'] = 'api/Admin/delete_lesson';

// Export routes
$route['export/url'] = 'admin/export/url';
$route['export/(:any)'] = 'admin/export/$1';
$route['export/(:any)/(:num)'] = 'admin/export/$1/$2';
$route['export/(:any)/(:num)/(:num)'] = 'admin/export/$1/$2/$3';
$route['export/(:any)/(:num)/(:num)/(:any)'] = 'admin/export/$1/$2/$3/$4';
$route['export/(:any)/(:num)/(:num)/(:any)/(:any)'] = 'admin/export/$1/$2/$3/$4/$5';
$route['api/GetLessonVideoType'] = 'api/Admin/lesson_video_type';
$route['api/UpdateLessonDevice'] = 'api/Admin/update_lesson_device';
$route['api/AddOthersLessons'] = 'api/Admin/add_lesson_with_attachment';
$route['api/UpdateOthersLessons'] = 'api/Admin/update_lesson_with_attachment';


$route['meeting/create'] = 'meeting/create';
$route['meeting/join'] = 'meeting/join';


$route['test-meeting/start'] = 'TestMeeting/start';
$route['test-meeting/join'] = 'TestMeeting/joinAsAttendee';


$route['bigbluebutton'] = 'bigbluebutton/index';

// $route['bigbluebutton/create'] = 'BigBlueButtonController/create';
$route['bigbluebutton/join/(:any)/(:any)/(:any)'] = 'BigBlueButtonController/join/$1/$2/$3';

// $route['bigbluebutton/create'] = 'BigBlueButton/create_meeting';
$route['bigbluebutton/create/(:num)'] = 'bigbluebutton/create_meeting/$1';
$route['bigbluebutton/start_meeting/(:num)'] = 'bigbluebutton/start_meeting/$1';

$route['bigbluebutton/start'] = 'BigBlueButton/join_meeting';
$route['bigbluebutton/get_active_meetings'] = 'Bigbluebutton/get_active_meetings';
$route['bigbluebutton/check_active_meetings'] = 'BigBlueButton/check_active_meetings';

$route['bigbluebutton/get_meetings'] = 'BigBlueButton/get_meetings';
$route['bigbluebutton/is_meeting_running/(:any)'] = 'BigBlueButton/is_meeting_running/$1';


$route['bigbluebutton/join_room/(:any)'] = 'BigBlueButtonController/join_room/$1';

$route['bigbluebutton/create_room'] = 'BigBlueButton/create_room';
// $route['bigbluebutton/delete_room'] = 'BigBlueButton/delete_room';
$route['bigbluebutton/webhook'] = 'bigbluebutton/webhook';

$route['meeting_states'] = 'bigbluebutton/meeting_states';

// =====================================================
// FX RATES API ROUTES
// =====================================================
$route['api/fx'] = 'api/FxRates/index';
$route['api/fx/today'] = 'api/FxRates/today';
$route['api/fx/latest'] = 'api/FxRates/latest';
$route['api/fx/date/(:any)'] = 'api/FxRates/date/$1';
$route['api/fx/range'] = 'api/FxRates/range';
$route['api/fx/convert'] = 'api/FxRates/convert';
$route['api/fx/health'] = 'api/FxRates/health';

// FX RATES CRON ROUTES
$route['cron/fx_fetch_daily'] = 'Cron/fx_fetch_daily';
$route['cron/fx_health'] = 'Cron/fx_health';
$route['cron/fx_cleanup'] = 'Cron/fx_cleanup';
$route['cron/fx_clear_cache'] = 'Cron/fx_clear_cache';
$route['cron/fx_test_api'] = 'Cron/fx_test_api';


$route['payment/community/(:num)'] = 'student/payment/community/$1';
$route['app/online_admission'] = 'student/online_admission';
$route['app/online_admission/(:any)'] = 'student/online_admission/$1';
$route['app/payment'] = 'student/payment';
$route['app/payment/(:any)'] = 'admin/payment/$1';
$route['app/payment/(:any)/(:any)'] = 'admin/payment/$1/$2';

// FIX: Route for app/join_school
$route['app/join_school'] = 'student/join_school';
$route['app/join_school/(:any)'] = 'student/join_school/$1';
$route['app/courses/(:num)'] = 'student/courses/$1';
/*
| -------------------------------------------------------------------------
| CUSTOM ROUTE FOR APP URL MASKING
| -------------------------------------------------------------------------
*/
if (isset($_SERVER['REQUEST_URI']) && (strpos($_SERVER['REQUEST_URI'], '/app') !== false)) {
    
    $role_route = '';
    $cookie_name = 'ci_session';
    
    if (isset($_COOKIE[$cookie_name])) {
        if (!isset($db)) {
             include(APPPATH . 'config/database.php');
        }
        
        $dsn_db = $db['default'];
        
        try {
            $driver = ($dsn_db['dbdriver'] == 'mysqli') ? 'mysql' : $dsn_db['dbdriver'];
            $dsn = $driver . ':host=' . $dsn_db['hostname'] . ';dbname=' . $dsn_db['database'] . ';charset=' . $dsn_db['char_set'];
            $pdo = new PDO($dsn, $dsn_db['username'], $dsn_db['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $sess_id = $_COOKIE[$cookie_name];
            
            $stmt = $pdo->prepare("SELECT data FROM ci_sessions WHERE id = ?");
            $stmt->execute([$sess_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                 $data = $row['data'];
                 if (is_resource($data)) {
                     $data = stream_get_contents($data);
                 }
                 
                 // Try to extract the active 'role' from session data
                 // Matches: role|s:5:"admin"; or "role";s:5:"admin";
                 if (preg_match('/(?:^|[;|}])"?role"?[|;]s:\d+:"([^"]+)"/', $data, $matches)) {
                     $role_route = $matches[1];
                 }
                 // Fallback to legacy checks if role is not found (unlikely but safe)
                 elseif (strpos($data, 'admin_login|b:1') !== false) {
                     $role_route = 'admin';
                 } elseif (strpos($data, 'teacher_login|b:1') !== false) {
                     $role_route = 'teacher';
                 } elseif (strpos($data, 'student_login|b:1') !== false) {
                     $role_route = 'student';
                 } elseif (strpos($data, 'superadmin_login|b:1') !== false) {
                     $role_route = 'superadmin';
                 }
            }
        } catch (Exception $e) {
            // Silence
        }
    }
    
    if ($role_route) {
        $route['app/member'] = $role_route . '/student';
        $route['app/member/(.+)'] = $role_route . '/student/$1';
        $route['app/mentor'] = $role_route . '/teacher';
        $route['app/mentor/(.+)'] = $role_route . '/teacher/$1';
        $route['app/certifications'] = $role_route . '/exam';
        $route['app/certifications/(.+)'] = $role_route . '/exam/$1';
        $route['app/announcements'] = $role_route . '/event_calendar';
        $route['app/announcements/(.+)'] = $role_route . '/event_calendar/$1';
        $route['app/community_settings'] = $role_route . '/school_settings';
        $route['app/community_settings/(.+)'] = $role_route . '/school_settings/$1';
        $route['app/community_list'] = $role_route . '/school';
        $route['app/community_list/(.+)'] = $role_route . '/school/$1';
        $route['app/courses'] = 'addons/courses';
        $route['app/courses/(:num)'] = 'student/manage_class/courses/$1';
        $route['app/courses/(.+)'] = 'addons/courses/$1';
        $route['app/lessons'] = 'addons/lessons';
        $route['app/lessons/(.+)'] = 'addons/lessons/$1';
        $route['app/payment/(:num)'] = 'student/payment/classe/$1';
        $route['app'] = $role_route . '/dashboard';
        $route['app/(.+)'] = $role_route . '/$1';
    } else {
        $route['app'] = 'login';
        $route['app/(.+)'] = 'login';
    }
}