<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Filters Configuration
 * 
 * CI4 Migration Template - Based on CI3 hooks.php and filters
 * Copy to app/Config/Filters.php when migrating to CI4
 */
class Filters extends BaseConfig
{
    /**
     * Aliases for filter classes
     */
    public array $aliases = [
        'csrf'      => \CodeIgniter\Filters\CSRF::class,
        'toolbar'   => \CodeIgniter\Filters\DebugToolbar::class,
        'adminauth' => \App\Filters\AdminAuthFilter::class,
        'teacherauth' => \App\Filters\TeacherAuthFilter::class,
        'studentauth' => \App\Filters\StudentAuthFilter::class,
        'parentauth' => \App\Filters\ParentAuthFilter::class,
        'language'  => \App\Filters\LanguageFilter::class,
        'cors'      => \App\Filters\CorsFilter::class,
    ];

    /**
     * URI filters - apply to specific URI patterns
     */
    public array $filters = [
        'csrf' => ['before' => ['admin/*', 'student/*', 'teacher/*', 'api/*']],
        'language' => ['before' => ['*']],
    ];

    /**
     * Filter configuration for methods that need special handling
     */
    public function __construct()
    {
        parent::__construct();
        
        // Load CI3 autoload config for reference
        $autoload_file = APPPATH . 'config/autoload.php';
        if (file_exists($autoload_file)) {
            include $autoload_file;
            
            // Convert CI3 libraries to CI4 filters
            if (isset($autoload) && isset($autoload['libraries'])) {
                // Libraries that should become filters
            }
        }
    }
}
