<?php

use App\Services\LmsService;
use Config\Database;

helper('ci4_compat_helper');

if (!function_exists('get_video_extension')) {
    function get_video_extension(string $url): string
    {
        if (class_exists('\\CodeIgniter\\CodeIgniter')) {
            $service = service('lmsService');
            return $service->getVideoExtension($url);
        }
        
        if (strpos($url, '.mp4') > 0) {
            return 'mp4';
        } elseif (strpos($url, '.webm') > 0) {
            return 'webm';
        }
        return 'unknown';
    }
}

if (!function_exists('lesson_progress')) {
    function lesson_progress($lesson_id = "", $user_id = ""): int
    {
        if (class_exists('\\CodeIgniter\\CodeIgniter')) {
            $service = service('lmsService');
            return $service->getLessonProgress((int) $lesson_id, $user_id ? (int) $user_id : null);
        }
        
        $session = \Config\Services::session();
        if ($user_id == "") {
            $user_id = $session->get('user_id');
        }
        
        $db = Database::connect();
        $user_details = $db->where('id', $user_id)->get('users')->getRowArray();
        $watch_history_array = json_decode($user_details['watch_history'] ?? '[]', true);
        
        foreach ($watch_history_array as $entry) {
            if ($entry['lesson_id'] == $lesson_id) {
                return $entry['progress'];
            }
        }
        return 0;
    }
}

if (!function_exists('readable_time_for_humans')) {
    function readable_time_for_humans($duration): string
    {
        if (class_exists('\\CodeIgniter\\CodeIgniter')) {
            $service = service('lmsService');
            return $service->readableTimeForHumans($duration);
        }
        
        if ($duration) {
            $duration_array = explode(':', $duration);
            $hour = $duration_array[0];
            $minute = $duration_array[1];
            $second = $duration_array[2];
            if ($hour > 0) {
                return $hour . ' hr ' . $minute . ' min';
            } elseif ($minute > 0) {
                return ($second > 0 ? $minute + 1 : $minute) . ' min';
            } elseif ($second > 0) {
                return $second . ' sec';
            }
        }
        return '00:00';
    }
}

if (!function_exists('course_progress')) {
    function course_progress($course_id = "", $user_id = ""): float
    {
        if (class_exists('\\CodeIgniter\\CodeIgniter')) {
            $service = service('lmsService');
            return $service->getCourseProgress((int) $course_id, $user_id ? (int) $user_id : null);
        }
        
        $session = \Config\Services::session();
        $db = Database::connect();
        
        if ($user_id == "") {
            $user_id = $session->get('user_id');
        }
        
        $user_details = $db->where('id', $user_id)->get('users')->getRowArray();
        $watch_history = $user_details['watch_history'] ?? '[]';
        $watch_history_array = json_decode($watch_history, true);
        
        $completed_lessons_ids = [];
        foreach ($watch_history_array as $entry) {
            if ($entry['progress'] == 1) {
                $completed_lessons_ids[] = $entry['lesson_id'];
            }
        }

        $lessons_for_that_course = wrap_result($db->where('course_id', $course_id)->get('lessons'));
        $total_number_of_lessons = $lessons_for_that_course->num_rows();

        if ($total_number_of_lessons == 0) {
            return 0;
        }

        return (count($completed_lessons_ids) / $total_number_of_lessons) * 100;
    }
}
