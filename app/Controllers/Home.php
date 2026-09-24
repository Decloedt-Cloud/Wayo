<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Home extends BaseController
{
    protected $theme = 'ultimate';
    protected $active_school_id;
    protected $models = ['Settings_model', 'User_model', 'Frontend_model'];
    
    protected $supported_lang_codes = [
        'fr' => 'french',
        'en' => 'english',
        'ar' => 'arabic',
        'es' => 'spanish',
        'nl' => 'dutch'
    ];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        helper(['url', 'file', 'ci4_compat']);
    }

    public function index($lang_code = null)
    {
        $page_data['page_name'] = 'home';
        $page_data['page_title'] = 'Home';
        
        $page_data['theme'] = $this->theme;
        
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function about($lang_code = null)
    {
        $page_data['page_name'] = 'about';
        $page_data['page_title'] = 'About Us';
        
        $page_data['theme'] = $this->theme;
        
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function contact($lang_code = null)
    {
        $page_data['page_name'] = 'contact';
        $page_data['page_title'] = 'Contact Us';
        
        $page_data['theme'] = $this->theme;
        
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function gallery($lang_code = null)
    {
        $page_data['page_name'] = 'gallery';
        $page_data['page_title'] = 'Gallery';
        
        $page_data['theme'] = $this->theme;
        
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function noticeboard($lang_code = null)
    {
        $page_data['page_name'] = 'noticeboard';
        $page_data['page_title'] = 'Noticeboard';
        
        $page_data['theme'] = $this->theme;
        
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function faq($lang_code = null)
    {
        $page_data['page_name'] = 'faq';
        $page_data['page_title'] = 'FAQ';
        
        $page_data['theme'] = $this->theme;
        
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function teachers($lang_code = null)
    {
        $page_data['page_name'] = 'teacher';
        $page_data['page_title'] = 'Teachers';
        $page_data['theme'] = $this->theme;
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function events($lang_code = null)
    {
        $page_data['page_name'] = 'event';
        $page_data['page_title'] = 'Events';
        $page_data['theme'] = $this->theme;
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function gallery_view($id = null, $lang_code = null)
    {
        $page_data['page_name'] = 'gallery_view';
        $page_data['page_title'] = 'Gallery View';
        $page_data['gallery_id'] = $id;
        $page_data['theme'] = $this->theme;
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function notice_details($id = null, $lang_code = null)
    {
        $page_data['page_name'] = 'notice_details';
        $page_data['page_title'] = 'Notice Details';
        $page_data['notice_id'] = $id;
        $page_data['theme'] = $this->theme;
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function tutorial($lang_code = null)
    {
        $page_data['page_name'] = 'tutorial';
        $page_data['page_title'] = 'Tutorial';
        $page_data['theme'] = $this->theme;
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function webinaire($lang_code = null)
    {
        $page_data['page_name'] = 'webinaire';
        $page_data['page_title'] = 'Webinaire';
        $page_data['theme'] = $this->theme;
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function affiliation($lang_code = null)
    {
        $page_data['page_name'] = 'affiliation';
        $page_data['page_title'] = 'Affiliation';
        $page_data['theme'] = $this->theme;
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function privacy_policy($lang_code = null)
    {
        $page_data['page_name'] = 'privacy_policy';
        $page_data['page_title'] = 'Privacy Policy';
        $page_data['theme'] = $this->theme;
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function terms_conditions($lang_code = null)
    {
        $page_data['page_name'] = 'terms_conditions';
        $page_data['page_title'] = 'Terms & Conditions';
        $page_data['theme'] = $this->theme;
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    /**
     * A community is public only when it is approved and not deleted.
     */
    private function visibleCommunitiesBuilder()
    {
        return $this->db->table('schools')
            ->where('status', 1)
            ->where('Etat', 1);
    }

    private function communityIsPublic(array $school): bool
    {
        return (int) ($school['status'] ?? 0) === 1
            && (int) ($school['Etat'] ?? 0) === 1;
    }

    private function publicCommunityStats(): array
    {
        $communities = (clone $this->visibleCommunitiesBuilder())->countAllResults();
        $members = (int) $this->db->table('students')
            ->join('schools', 'schools.id = students.school_id')
            ->where('students.status', 1)
            ->where('schools.status', 1)
            ->where('schools.Etat', 1)
            ->countAllResults();

        return [
            'visible_communities_count' => $communities,
            'visible_members_count' => $members,
        ];
    }

    public function communities($lang_code = null)
    {
        $perPage = 8;
        $page = 1;
        $category = null;
        $pageFromPath = null;
        $queryParams = $this->request->getGet();
        $segments = $this->request->getUri()->getSegments();
        $normalizedSegments = $segments;

        $communitiesPos = array_search('communities', $segments, true);
        if ($communitiesPos !== false) {
            $next = $segments[$communitiesPos + 1] ?? null;
            $next2 = $segments[$communitiesPos + 2] ?? null;

            if ($next !== null) {
                if (is_numeric($next)) {
                    $pageFromPath = max(1, (int) $next);
                    // Legacy format: /communities/{page}
                    array_splice($normalizedSegments, $communitiesPos + 1, 1);
                } else {
                    $category = str_replace('_', ' ', urldecode((string) $next));
                    $category = str_replace(' and ', ' & ', $category);
                    $category = trim($category);
                    $category = mb_substr($category, 0, 100, 'UTF-8');
                    $category = preg_replace('/[^a-zA-Z0-9 &_\-\p{L}\p{N}]/u', '', $category);
                    if ($next2 !== null && is_numeric($next2)) {
                        $pageFromPath = max(1, (int) $next2);
                        // Legacy format: /communities/{category}/{page}
                        array_splice($normalizedSegments, $communitiesPos + 2, 1);
                    }
                }
            }
        }

        // Support query-string pagination (/communities?page=2) used by pager links.
        $pageFromQuery = max(1, (int) ($this->request->getGet('page') ?? 1));
        $page = $pageFromQuery;
        if ($pageFromPath !== null && $pageFromQuery === 1) {
            $page = $pageFromPath;
        }

        // Canonicalize old segment-based pagination to query format.
        if ($pageFromPath !== null) {
            $canonicalPath = implode('/', $normalizedSegments);
            unset($queryParams['page']);
            if ($page > 1) {
                $queryParams['page'] = $page;
            }
            $canonicalUrl = site_url($canonicalPath);
            if (!empty($queryParams)) {
                $canonicalUrl .= '?' . http_build_query($queryParams);
            }
            return redirect()->to($canonicalUrl);
        }

        $builder = $this->visibleCommunitiesBuilder();

        $validCategory = false;
        if (!empty($category)) {
            $validCategory = $this->db->table('categories')->where('name', $category)->countAllResults() > 0;
            if ($validCategory) {
                $builder->where('category', $category);
            }
        }

        $totalRows = (clone $builder)->countAllResults();
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        $offset = ($page - 1) * $perPage;
        $schools = $builder
            ->orderBy('id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $schoolIds = array_values(array_filter(array_map(static function (array $school): int {
            return (int) ($school['id'] ?? 0);
        }, $schools)));

        $studentCountsBySchool = [];
        $classesCountsBySchool = [];
        if (!empty($schoolIds)) {
            $studentRows = $this->db->table('students')
                ->select('school_id, COUNT(*) as total')
                ->where('status', 1)
                ->whereIn('school_id', $schoolIds)
                ->groupBy('school_id')
                ->get()
                ->getResultArray();
            foreach ($studentRows as $row) {
                $studentCountsBySchool[(int) $row['school_id']] = (int) $row['total'];
            }

            $classRows = $this->db->table('classes')
                ->select('school_id, COUNT(*) as total')
                ->where('statut', 'active')
                ->whereIn('school_id', $schoolIds)
                ->groupBy('school_id')
                ->get()
                ->getResultArray();
            foreach ($classRows as $row) {
                $classesCountsBySchool[(int) $row['school_id']] = (int) $row['total'];
            }
        }

        foreach ($schools as &$school) {
            $schoolId = (int) ($school['id'] ?? 0);
            $school['members_count'] = $studentCountsBySchool[$schoolId] ?? 0;
            $school['classes_count'] = $classesCountsBySchool[$schoolId] ?? 0;
        }
        unset($school);

        $page_data['schools'] = $schools;
        $page_data['total_rows'] = $totalRows;
        $page_data['links'] = service('pager')->makeLinks($page, $perPage, $totalRows);
        $page_data['categories'] = $this->frontend_model->get_categories();
        $page_data['selected_category'] = $category;
        if (!empty($category) && $validCategory && empty($schools)) {
            $page_data['no_courses_found'] = get_phrase('0_communities_found_in_category');
        }

        // SEO pagination links (canonical / prev / next).
        $baseCanonicalUrl = current_url();
        $seoQuery = $queryParams;
        unset($seoQuery['page']);

        $canonicalQuery = $seoQuery;
        if ($page > 1) {
            $canonicalQuery['page'] = $page;
        }
        $page_data['canonical_url'] = $baseCanonicalUrl . (!empty($canonicalQuery) ? ('?' . http_build_query($canonicalQuery)) : '');

        $page_data['prev_url'] = null;
        if ($page > 1) {
            $prevQuery = $seoQuery;
            if ($page - 1 > 1) {
                $prevQuery['page'] = $page - 1;
            }
            $page_data['prev_url'] = $baseCanonicalUrl . (!empty($prevQuery) ? ('?' . http_build_query($prevQuery)) : '');
        }

        $page_data['next_url'] = null;
        if ($page < $totalPages) {
            $nextQuery = $seoQuery;
            $nextQuery['page'] = $page + 1;
            $page_data['next_url'] = $baseCanonicalUrl . '?' . http_build_query($nextQuery);
        }

        $page_data['page_name'] = 'communities';
        $page_data['page_title'] = 'Communities';
        $page_data['theme'] = $this->theme;
        $page_data = array_merge($page_data, $this->publicCommunityStats());
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function community_details($id = null, $lang_code = null)
    {
        $schoolId = is_numeric($id) ? (int) $id : 0;
        if ($schoolId <= 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $school = $this->db->table('schools')->where('id', $schoolId)->get()->getRowArray();
        if (!$school || !$this->communityIsPublic($school)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $userId = (int) (session()->get('user_id') ?? 0);
        $studentId = 0;
        if ($userId > 0) {
            $student = $this->db->table('students')
                ->where('user_id', $userId)
                ->where('school_id', $schoolId)
                ->get()
                ->getRowArray();
            $studentId = (int) ($student['id'] ?? 0);
        }

        $crudModel = $this->crud_model ?? model('Crud_model');

        $courseStudentsCount = $this->db->table('students')
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->countAllResults();
        $classesCount = is_object($crudModel) && method_exists($crudModel, 'get_school_classes_count')
            ? (int) $crudModel->get_school_classes_count($schoolId)
            : (int) $this->db->table('classes')->where('school_id', $schoolId)->where('statut', 'active')->countAllResults();
        // Use direct CI4 query here because legacy User_model::get_school_teachers_count uses CI3 db calls.
        $teachersCount = (int) $this->db->table('teachers')
            ->where('school_id', $schoolId)
            ->countAllResults();
        $classes = is_object($crudModel) && method_exists($crudModel, 'get_school_classes')
            ? $crudModel->get_school_classes($schoolId)
            : $this->db->table('classes')->where('school_id', $schoolId)->where('statut', 'active')->get()->getResultArray();

        $school['course_students_count'] = $courseStudentsCount;
        $school['classes_count'] = $classesCount;
        $school['teachers_count'] = $teachersCount;

        $page_data['school'] = $school;
        $page_data['school_id'] = $schoolId;
        $page_data['community_id'] = $schoolId;
        $page_data['student_id'] = $studentId;
        $page_data['classes'] = is_array($classes) ? $classes : [];
        $page_data['course_students_count'] = $courseStudentsCount;
        $page_data['classes_count'] = $classesCount;
        $page_data['teachers_count'] = $teachersCount;
        $page_data['settings_data'] = $this->db->table('settings_school')
            ->where('school_id', $schoolId)
            ->get()
            ->getRowArray();

        $page_data['page_name'] = 'community_details';
        $page_data['page_title'] = 'Community Details';
        $page_data['theme'] = $this->theme;
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function online_admission_school($lang_code = null)
    {
        $page_data['page_name'] = 'online_admission';
        $page_data['page_title'] = 'Online Admission';
        $page_data['theme'] = $this->theme;
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function onlineadmission($lang_code = null)
    {
        $page_data['page_name'] = 'online_admission';
        $page_data['page_title'] = 'Online Admission';
        $page_data['theme'] = $this->theme;
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function help_center($lang_code = null)
    {
        $page_data['page_name'] = 'help_center';
        $page_data['page_title'] = 'Help Center';
        $page_data['theme'] = $this->theme;
        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function dropdown_guest_lang()
    {
        $current_lang = session()->get('language') ?? 'french';
        
        $languages = [
            'french' => 'Français',
            'english' => 'English',
            'dutch' => 'Nederlands',
            'arabic' => 'العربية',
            'spanish' => 'Español'
        ];
        
        $html = '';
        foreach ($languages as $code => $name) {
            $html .= '<a href="#" onclick="setGuestLanguage(\'' . $code . '\'); return false;" class="dropdown-item ' . ($current_lang == $code ? 'active' : '') . '">';
            $html .= $name . '</a>';
        }
        
        return $this->response->setBody($html);
    }

    public function set_guest_language()
    {
        $language = $this->request->getPost('language');
        if ($language) {
            session()->set('language', $language);
        }
        return $this->response->setJSON(['success' => true]);
    }

    public function get_user_roles()
    {
        $user_id = session()->get('user_id');
        if (!$user_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Not logged in']);
        }

        if (session()->get('superadmin_login') == 1) {
            return $this->response->setJSON([
                'status' => 'success',
                'roles' => [],
                'current_role' => 'superadmin'
            ]);
        }

        $roles = $this->db->table('user_schools us')
            ->select('us.role')
            ->where('us.user_id', $user_id)
            ->groupBy('us.role')
            ->get()->getResultArray();

        $result = [];
        foreach ($roles as $r) {
            $role_key = strtolower($r['role']);
            $label = $role_key === 'teacher' ? get_phrase('Mentor') : ucfirst($role_key);
            if ($role_key === 'student') $label = get_phrase('member');

            if ($role_key === 'student') {
                $communities = $this->db->table('user_schools us')
                    ->select('s.id as school_id, s.name as community_name, st.status')
                    ->join('schools s', 's.id = us.school_id', 'inner')
                    ->join('students st', 'st.school_id = us.school_id AND st.user_id = us.user_id', 'inner')
                    ->where('us.user_id', $user_id)
                    ->where('us.role', $r['role'])
                    ->get()->getResultArray();
            } else {
                $communities = $this->db->table('user_schools us')
                    ->select('s.id as school_id, s.name as community_name')
                    ->join('schools s', 's.id = us.school_id', 'inner')
                    ->where('us.user_id', $user_id)
                    ->where('us.role', $r['role'])
                    ->get()->getResultArray();
            }

            if (count($communities) > 0) {
                $result[] = [
                    'role' => $role_key,
                    'label' => $label,
                    'count' => count($communities),
                    'communities' => $communities
                ];
            }
        }

        $current_role = strtolower(session()->get('role') ?? 'student');

        return $this->response->setJSON([
            'status' => 'success',
            'roles' => $result,
            'current_role' => $current_role
        ]);
    }

    public function alumni_event()
    {
        if (addon_status('alumni')) {
            $page_data['page_name'] = 'alumni_event';
            $page_data['page_title'] = get_phrase('alumni_event');
            $page_data['theme'] = $this->theme;
            echo view('frontend/' . $this->theme . '/index', $page_data);
        } else {
            return redirect()->to('/');
        }
    }

    public function alumni_gallery()
    {
        if (addon_status('alumni')) {
            $page_data['page_name'] = 'alumni_gallery';
            $page_data['page_title'] = get_phrase('alumni_gallery');
            $page_data['theme'] = $this->theme;
            echo view('frontend/' . $this->theme . '/index', $page_data);
        } else {
            return redirect()->to('/');
        }
    }

    public function alumni_gallery_view($gallery_id = '')
    {
        if (addon_status('alumni')) {
            $count_images = $this->db->table('alumni_gallery_photos')->where('gallery_id', $gallery_id)->countAllResults();
            
            $page_data['gallery_id'] = $gallery_id;
            $page_data['page_name'] = 'alumni_gallery_view';
            $page_data['page_title'] = get_phrase('alumni_gallery');
            $page_data['theme'] = $this->theme;
            echo view('frontend/' . $this->theme . '/index', $page_data);
        } else {
            return redirect()->to('/');
        }
    }

    public function communities_search($param1 = null)
    {
        $input = trim((string) $this->request->getGet('search'));
        $input = mb_substr($input, 0, 100, 'UTF-8');
        $input = preg_replace('/[^\p{L}\p{N}\s\-_&]/u', '', $input);
        $perPage = 8;
        $page = 1;
        $pageFromQuery = max(1, (int) ($this->request->getGet('page') ?? 1));
        $pageFromPath = null;

        if ($param1 !== null && is_numeric($param1)) {
            $pageFromPath = max(1, (int) $param1);
        }

        $page = $pageFromQuery;
        if ($pageFromPath !== null && $pageFromQuery === 1) {
            $page = $pageFromPath;
        }

        // Canonicalize old /communities_search/{page} to /communities_search?page={page}
        if ($pageFromPath !== null) {
            $queryParams = $this->request->getGet();
            unset($queryParams['page']);
            if ($page > 1) {
                $queryParams['page'] = $page;
            }
            $canonicalUrl = site_url('home/communities_search');
            if (!empty($queryParams)) {
                $canonicalUrl .= '?' . http_build_query($queryParams);
            }
            return redirect()->to($canonicalUrl);
        }

        $applySearchFilters = static function ($builder, string $searchTerm) {
            if ($searchTerm === '') {
                return $builder;
            }
            return $builder->groupStart()
                ->like('name', $searchTerm)
                ->orLike('description', $searchTerm)
                ->orLike('category', $searchTerm)
                ->groupEnd();
        };

        $countBuilder = $this->visibleCommunitiesBuilder();
        $countBuilder = $applySearchFilters($countBuilder, $input);
        $totalRows = (int) $countBuilder->countAllResults();

        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        $offset = ($page - 1) * $perPage;

        $listBuilder = $this->visibleCommunitiesBuilder();
        $listBuilder = $applySearchFilters($listBuilder, $input);

        $schools = $listBuilder
            ->orderBy('id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $schoolIds = array_values(array_filter(array_map(static function (array $school): int {
            return (int) ($school['id'] ?? 0);
        }, $schools)));

        $studentCountsBySchool = [];
        $classesCountsBySchool = [];
        if (!empty($schoolIds)) {
            $studentRows = $this->db->table('students')
                ->select('school_id, COUNT(*) as total')
                ->where('status', 1)
                ->whereIn('school_id', $schoolIds)
                ->groupBy('school_id')
                ->get()
                ->getResultArray();
            foreach ($studentRows as $row) {
                $studentCountsBySchool[(int) $row['school_id']] = (int) $row['total'];
            }

            $classRows = $this->db->table('classes')
                ->select('school_id, COUNT(*) as total')
                ->where('statut', 'active')
                ->whereIn('school_id', $schoolIds)
                ->groupBy('school_id')
                ->get()
                ->getResultArray();
            foreach ($classRows as $row) {
                $classesCountsBySchool[(int) $row['school_id']] = (int) $row['total'];
            }
        }

        foreach ($schools as &$school) {
            $schoolId = (int) ($school['id'] ?? 0);
            $school['members_count'] = $studentCountsBySchool[$schoolId] ?? 0;
            $school['classes_count'] = $classesCountsBySchool[$schoolId] ?? 0;
        }
        unset($school);

        $page_data['schools'] = $schools;
        $page_data['total_rows'] = $totalRows;
        $page_data['links'] = service('pager')->makeLinks($page, $perPage, $totalRows);
        $page_data['categories'] = $this->frontend_model->get_categories();
        $page_data['page_name'] = 'communities';
        $page_data['page_title'] = get_phrase('communities');
        $page_data['theme'] = $this->theme;
        $page_data = array_merge($page_data, $this->publicCommunityStats());
        if ($input !== '') {
            $page_data['input_search'] = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
            if (empty($schools)) {
                $page_data['no_courses_found'] = get_phrase('0_communities_found_for_search') . ' "' . $input . '"';
            }
        }

        // SEO pagination links (canonical / prev / next) for search results.
        $baseCanonicalUrl = site_url('home/communities_search');
        $seoQuery = [];
        if ($input !== '') {
            $seoQuery['search'] = $input;
        }

        $canonicalQuery = $seoQuery;
        if ($page > 1) {
            $canonicalQuery['page'] = $page;
        }
        $page_data['canonical_url'] = $baseCanonicalUrl . (!empty($canonicalQuery) ? ('?' . http_build_query($canonicalQuery)) : '');

        $page_data['prev_url'] = null;
        if ($page > 1) {
            $prevQuery = $seoQuery;
            if ($page - 1 > 1) {
                $prevQuery['page'] = $page - 1;
            }
            $page_data['prev_url'] = $baseCanonicalUrl . (!empty($prevQuery) ? ('?' . http_build_query($prevQuery)) : '');
        }

        $page_data['next_url'] = null;
        if ($page < $totalPages) {
            $nextQuery = $seoQuery;
            $nextQuery['page'] = $page + 1;
            $page_data['next_url'] = $baseCanonicalUrl . '?' . http_build_query($nextQuery);
        }

        if ($this->request->isAJAX()) {
            return view('frontend/' . $this->theme . '/partials/communities_grid', $page_data);
        }

        echo view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function active_school_id_for_frontend($active_school_id = "")
    {
        if (addon_status('multi-school') && $active_school_id > 0) {
            session()->set('active_school_id', $active_school_id);
        } else {
            $active_school_id = get_settings('school_id');
            session()->set('active_school_id', $active_school_id);
        }
    }

    public function check_student_status_ajax($school_id)
    {
        $user_id = session()->get('user_id');
        $user_type = session()->get('user_type');
        $admin_login = session()->get('admin_login');
        $teacher_login = session()->get('teacher_login');

        if (!$user_id) {
            return $this->response->setJSON(['status' => null]);
        }
        
        $user_role_in_school = $this->db->table('user_schools')
            ->where('user_id', $user_id)
            ->where('school_id', $school_id)
            ->get()->getRow();
        
        if ($user_role_in_school) {
            $role_lower = strtolower($user_role_in_school->role);
            
            if ($role_lower === 'admin') {
                return $this->response->setJSON(['status' => 1, 'user_role' => 'admin', 'role_in_this_school' => 'admin']);
            }
            
            if ($role_lower === 'teacher') {
                return $this->response->setJSON(['status' => 1, 'user_role' => 'teacher', 'role_in_this_school' => 'teacher']);
            }
        }
        
        $student_record = $this->db->table('students')
            ->where('user_id', $user_id)
            ->where('school_id', $school_id)
            ->get()->getRow();
        
        if ($student_record) {
            if ($student_record->status == 1) {
                return $this->response->setJSON(['status' => 1, 'user_role' => 'student', 'role_in_this_school' => 'student']);
            } else {
                $current_role = $admin_login ? 'admin' : ($teacher_login ? 'teacher' : 'student');
                return $this->response->setJSON(['status' => 0, 'user_role' => $current_role]);
            }
        }
        
        if ($admin_login || $teacher_login) {
            $user_role = $admin_login ? 'admin' : 'teacher';
            return $this->response->setJSON(['status' => 2, 'user_role' => $user_role]);
        } else if ($user_type == "student") {
            $status = $this->user_model->check_student_status($school_id);
            return $this->response->setJSON(['status' => $status]);
        } else {
            return $this->response->setJSON(['status' => null]);
        }
    }

    public function dropdown_guest()
    {
        $languages = $this->settings_model->get_all_languages();
        $current_language = function_exists('get_user_language') ? get_user_language() : 'english';
        
        $html = '';
        foreach ($languages as $language) {
            $html .= '<a class="dropdown-item' . ($current_language == $language ? ' active' : '') . '" href="#" onclick="setGuestLanguage(\'' . $language . '\')">' . ucfirst(get_phrase($language)) . '</a>';
        }
        
        return $this->response->setBody($html);
    }

    public function get_user_communities()
    {
        $user_id = session()->get('user_id');
        if (!$user_id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'not_logged_in',
                'data' => [],
            ]);
        }

        $normalizeRole = static function (string $role): string {
            $role = trim($role);
            if ($role === '') {
                return '';
            }

            // Handle serialized/json-like legacy values such as ["student"].
            if (($role[0] === '[' || $role[0] === '{')) {
                $decoded = json_decode($role, true);
                if (is_array($decoded)) {
                    $first = reset($decoded);
                    if (is_string($first) && trim($first) !== '') {
                        $role = $first;
                    }
                }
            }

            $role = strtolower(trim((string) preg_replace('/[\[\]"\']+/', '', $role)));
            if ($role === '') {
                return '';
            }

            if (str_contains($role, 'superadmin')) {
                return 'superadmin';
            }
            if (str_contains($role, 'teacher') || str_contains($role, 'mentor')) {
                return 'teacher';
            }
            if (str_contains($role, 'student') || str_contains($role, 'member')) {
                return 'student';
            }
            if (str_contains($role, 'parent')) {
                return 'parent';
            }
            if (str_contains($role, 'admin')) {
                return 'admin';
            }
            if (str_contains($role, 'accountant')) {
                return 'accountant';
            }
            if (str_contains($role, 'librarian')) {
                return 'librarian';
            }
            if (str_contains($role, 'driver')) {
                return 'driver';
            }

            return $role;
        };

        $current_role = $normalizeRole((string) (session()->get('role') ?: session()->get('user_type')));
        $current_school_id = (int) (session()->get('active_school_id') ?: session()->get('school_id'));

        if (session()->get('superadmin_login') == 1 && $current_role === 'superadmin') {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [],
                'count' => 0
            ]);
        }

        $all_communities = $this->db->table('user_schools us')
            ->select('us.school_id, s.name as community_name, us.role')
            ->join('schools s', 's.id = us.school_id', 'inner')
            ->where('us.user_id', $user_id)
            ->where('us.school_id IS NOT NULL', null, false)
            ->where('s.id IS NOT NULL', null, false)
            ->get()->getResultArray();

        $communities = [];

        foreach ($all_communities as $community) {
            $role_lower = $normalizeRole((string) ($community['role'] ?? ''));
            $community['role'] = $role_lower;
            
            if ($role_lower === 'student') {
                $student_entry = $this->db->table('students')
                    ->where('user_id', $user_id)
                    ->where('school_id', $community['school_id'])
                    ->get()->getRowArray();

                $community['is_active'] = (
                    (int) $community['school_id'] === (int) $current_school_id &&
                    $role_lower === strtolower($current_role)
                );
                $community['status'] = isset($student_entry['status']) ? (int) $student_entry['status'] : 0;
                $communities[] = $community;
            } else {
                $community['is_active'] = (
                    (int) $community['school_id'] === (int) $current_school_id &&
                    $role_lower === strtolower($current_role)
                );
                $communities[] = $community;
            }
        }

        // Fallback for migrated users missing user_schools rows:
        // build a single active community from session/users.school_id.
        if (empty($communities)) {
            $user_row = $this->db->table('users')
                ->select('school_id, role')
                ->where('id', $user_id)
                ->get()->getRowArray();

            $fallback_school_id = (int) ($current_school_id ?: ($user_row['school_id'] ?? 0));
            $fallback_role = $normalizeRole((string) ($current_role ?: ($user_row['role'] ?? '')));

            if ($fallback_school_id > 0 && $fallback_role !== '') {
                $school_row = $this->db->table('schools')
                    ->select('id, name')
                    ->where('id', $fallback_school_id)
                    ->get()->getRowArray();

                if (!empty($school_row)) {
                    $fallback = [
                        'school_id' => (int) $school_row['id'],
                        'community_name' => (string) $school_row['name'],
                        'role' => $fallback_role,
                        'is_active' => true,
                    ];

                    if ($fallback_role === 'student') {
                        $student_entry = $this->db->table('students')
                            ->select('status')
                            ->where('user_id', $user_id)
                            ->where('school_id', $fallback_school_id)
                            ->get()->getRowArray();
                        $fallback['status'] = isset($student_entry['status']) ? (int) $student_entry['status'] : 0;
                    }

                    $communities[] = $fallback;
                    $current_school_id = $fallback_school_id;
                    $current_role = $fallback_role;
                }
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $communities,
            'active_school_id' => $current_school_id,
            'active_role' => $current_role
        ]);
    }

    public function get_student_communities()
    {
        $user_id = session()->get('user_id');
        
        if (!$user_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'not_logged_in']);
        }

        $student_communities = $this->db->table('user_schools us')
            ->select('us.school_id, s.name as community_name')
            ->join('schools s', 's.id = us.school_id', 'inner')
            ->join('students st', 'st.school_id = us.school_id AND st.user_id = us.user_id', 'inner')
            ->where('us.user_id', $user_id)
            ->where('us.role', 'student')
            ->where('us.school_id IS NOT NULL', null, false)
            ->where('st.status', 1)
            ->get()->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $student_communities,
            'count' => count($student_communities)
        ]);
    }

    public function switch_to_member_account()
    {
        $user_id = session()->get('user_id');
        $active_school_id = session()->get('active_school_id');

        if (!$user_id || !$active_school_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'invalid_session']);
        }

        $this->db->table('users')->where('id', $user_id)->update([
            'role' => 'student',
            'school_id' => NULL
        ]);

        session()->set([
            'user_type' => 'student',
            'role' => 'student',
            'student_login' => true,
            'active_school_id' => NULL,
            'school_id' => NULL,
            'admin_login' => false,
            'teacher_login' => false,
            'superadmin_login' => false,
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'redirect_url' => site_url('app/dashboard')
        ]);
    }

    public function switch_community_role()
    {
        $user_id = session()->get('user_id');
        $school_id = $this->request->getPost('school_id');
        $role = $this->request->getPost('role');

        if (!$user_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'session_expired']);
        }

        if (session()->get('superadmin_login') == 1) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access_denied_for_superadmin']);
        }

        if (!$school_id || !$role) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'missing_data']);
        }

        $exists = $this->db->table('user_schools')
            ->where('user_id', $user_id)
            ->where('school_id', $school_id)
            ->where('role', $role)
            ->countAllResults();

        if (!$exists) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access_denied']);
        }

        $this->db->table('users')->where('id', $user_id)->update([
            'role' => $role,
            'school_id' => $school_id
        ]);

        session()->set([
            'active_school_id' => $school_id,
            'role' => $role,
            'user_type' => $role,
            'school_id' => $school_id,
            'superadmin_login' => ($role === 'superadmin'),
            'student_login' => ($role === 'student'),
            'admin_login' => ($role === 'admin'),
            'teacher_login' => ($role === 'teacher'),
            'accountant_login' => ($role === 'accountant'),
            'librarian_login' => ($role === 'librarian'),
            'driver_login' => ($role === 'driver'),
            'parent_login' => ($role === 'parent' || $role === 'parents'),
        ]);

        $redirect_url = site_url('app/dashboard');
        if ($role === 'student') {
            $student_status = $this->db->table('students')->where('user_id', $user_id)->where('school_id', $school_id)->get()->getRow('status');
            if (isset($student_status) && $student_status != 1) {
                $redirect_url = site_url('app/invoice');
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'redirect_url' => $redirect_url
        ]);
    }

    public function check_community_name_exists()
    {
        $user_id = session()->get('user_id');
        if (!$user_id) {
            return $this->response->setJSON(['exists' => false]);
        }

        $school_name = trim($this->request->getPost('school_name') ?? '');

        if (empty($school_name)) {
            return $this->response->setJSON(['exists' => false]);
        }

        $exists = $this->db->table('schools')->where('name', $school_name)->countAllResults() > 0;

        return $this->response->setJSON([
            'exists' => $exists,
            'message' => $exists ? get_phrase('this_community_name_already_exists.') : get_phrase('name_available')
        ]);
    }

    public function get_communities_by_role()
    {
        $user_id = session()->get('user_id');
        $role = $this->request->getPost('role');

        if (!$user_id || !$role) {
            return $this->response->setJSON(['status' => 'error']);
        }

        if (session()->get('superadmin_login') == 1) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access_denied_for_superadmin']);
        }

        $role_key = strtolower($role);
        
        if ($role_key === 'student') {
            $communities = $this->db->table('user_schools us')
                ->select('s.id as school_id, s.name as community_name, us.role, st.status')
                ->join('schools s', 's.id = us.school_id')
                ->join('students st', 'st.school_id = us.school_id AND st.user_id = us.user_id', 'inner')
                ->where('us.user_id', $user_id)
                ->where('us.role', ucfirst($role))
                ->get()->getResultArray();
        } else {
            $communities = $this->db->table('user_schools us')
                ->select('s.id as school_id, s.name as community_name, us.role')
                ->join('schools s', 's.id = us.school_id')
                ->where('us.user_id', $user_id)
                ->where('us.role', ucfirst($role))
                ->get()->getResultArray();
        }

        foreach ($communities as &$c) {
            $c['role_label'] = $c['role'] === 'teacher' ? get_phrase('Mentor') : ucfirst($c['role']);
            if ($c['role'] === 'student') $c['role_label'] = get_phrase('member');
        }

        return $this->response->setJSON([
            'status' => 'success',
            'communities' => $communities
        ]);
    }

    public function switch_community_role_front()
    {
        $user_id = session()->get('user_id');
        $school_id = $this->request->getPost('school_id');
        $role = $this->request->getPost('role');

        if (!$user_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'session_expired']);
        }

        if (session()->get('superadmin_login') == 1) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access_denied_for_superadmin']);
        }

        if (!$school_id || !$role) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'missing_data']);
        }

        $exists = $this->db->table('user_schools')
            ->where('user_id', $user_id)
            ->where('school_id', $school_id)
            ->where('role', $role)
            ->countAllResults();

        if (!$exists) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access_denied']);
        }

        $this->db->table('users')->where('id', $user_id)->update([
            'role' => $role,
            'school_id' => $school_id
        ]);

        session()->set([
            'active_school_id' => $school_id,
            'role' => $role,
            'user_type' => $role,
            'school_id' => $school_id,
            'superadmin_login' => ($role === 'superadmin'),
            'student_login' => ($role === 'student'),
            'admin_login' => ($role === 'admin'),
            'teacher_login' => ($role === 'teacher'),
            'accountant_login' => ($role === 'accountant'),
            'librarian_login' => ($role === 'librarian'),
            'driver_login' => ($role === 'driver'),
            'parent_login' => ($role === 'parent' || $role === 'parents'),
        ]);

        $redirect_url = site_url('app/dashboard');
        if ($role === 'student') {
            $student_status = $this->db->table('students')->where('user_id', $user_id)->where('school_id', $school_id)->get()->getRow('status');
            if (isset($student_status) && $student_status != 1) {
                $redirect_url = site_url('app/invoice');
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'redirect_url' => $redirect_url
        ]);
    }

    public function switch_to_member_account_front()
    {
        $user_id = session()->get('user_id');
        $active_school_id = session()->get('active_school_id');

        if (!$user_id || !$active_school_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'invalid_session']);
        }

        if (session()->get('superadmin_login') == 1) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Access_denied_for_superadmin']);
        }

        $this->db->table('users')->where('id', $user_id)->update([
            'role' => 'student',
            'school_id' => NULL
        ]);

        session()->set([
            'user_type' => 'student',
            'role' => 'student',
            'student_login' => true,
            'active_school_id' => NULL,
            'school_id' => NULL,
            'admin_login' => false,
            'teacher_login' => false,
            'superadmin_login' => false,
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'redirect_url' => site_url('app/dashboard')
        ]);
    }

    public function verify_email($token = '')
    {
        if (empty($token)) {
            return redirect()->to('/login')->with('error', 'Invalid verification link');
        }

        $user = $this->db->table('users')
            ->where('email_verification_token', $token)
            ->get()
            ->getRowArray();

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Invalid verification token');
        }

        if ($user['email_verification_expires'] < date('Y-m-d H:i:s')) {
            return redirect()->to('/login')->with('error', 'Verification link has expired. Please request a new one.');
        }

        if ($user['status'] != 3) {
            return redirect()->to('/login')->with('info', 'Email already verified');
        }

        $this->db->table('users')
            ->where('id', $user['id'])
            ->update([
                'status' => 1,
                'email_verification_token' => null,
                'email_verification_expires' => null
            ]);

        log_message('info', 'Email verified for user ID: ' . $user['id']);

        return redirect()->to('/login')->with('success', 'Email verified successfully. You can now login.');
    }
}
