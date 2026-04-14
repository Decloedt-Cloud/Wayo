<?php

helper('ci4_compat_helper');

/*
*  @author   : Creativeitem
*  date      : November, 2019
*  Ekattor School Management System With Addons
*  http://codecanyon.net/user/Creativeitem
*  http://support.creativeitem.com
*/

if ( ! function_exists('get_video_extension')){
    // Checks if a video is youtube, vimeo or any other
    function get_video_extension($url) {
        if (strpos($url, '.mp4') > 0) {
            return 'mp4';
        } elseif (strpos($url, '.webm') > 0) {
            return 'webm';
        } else {
            return 'unknown';
        }
    }
}

if ( ! function_exists('lesson_progress')){
    function lesson_progress($lesson_id = "", $user_id = "") {
        $session = \Config\Services::session();
        if ($user_id == "") {
            $user_id = $session->get('user_id');
        }
        $userModel = model('User_model');
        if (!$userModel) {
            return 0;
        }
        $watch_history = (string) $userModel->get_user_details($user_id, 'watch_history');
        $watch_history_array = json_decode($watch_history, true);
        if (!is_array($watch_history_array)) {
            return 0;
        }
        for ($i = 0; $i < count($watch_history_array); $i++) {
          $watch_history_for_each_lesson = $watch_history_array[$i];
          if ($watch_history_for_each_lesson['lesson_id'] == $lesson_id) {
              return $watch_history_for_each_lesson['progress'];
          }
        }
        return 0;
    }
}

// Human readable time
if ( ! function_exists('readable_time_for_humans')){
    function readable_time_for_humans($duration) {
        if ($duration) {
            $duration_array = explode(':', $duration);
            $hour   = $duration_array[0];
            $minute = $duration_array[1];
            $second = $duration_array[2];
            if ($hour > 0) {
                $duration = $hour.' '.get_phrase('hr').' '.$minute.' '.get_phrase('min');
            }elseif ($minute > 0) {
                if ($second > 0) {
                    $duration = ($minute+1).' '.get_phrase('min');
                }else{
                    $duration = $minute.' '.get_phrase('min');
                }
            }elseif ($second > 0){
                $duration = $second.' '.get_phrase('sec');
            }else {
                $duration = '00:00';
            }
        }else {
            $duration = '00:00';
        }
        return $duration;
    }
}

if ( ! function_exists('course_progress')){
    function course_progress($course_id = "", $user_id = "") {
        $session = \Config\Services::session();
        if ($user_id == "") {
            $user_id = $session->get('user_id');
        }
        $userModel = model('User_model');
        $lmsModel = model('App\\Models\\addons\\Lms_model');
        if (!$userModel || !$lmsModel) {
            return 0;
        }
        $watch_history = $userModel->get_user_details($user_id, 'watch_history');

        $completed_lessons_ids = array();
        $lesson_completed = 0;

        $watch_history_array = json_decode($watch_history, true);
        if (!is_array($watch_history_array)) {
            $watch_history_array = [];
        }
        $lessons_for_that_course = $lmsModel->get_lessons('course', $course_id);
        $lesson_rows = [];
        if (is_object($lessons_for_that_course)) {
            if (method_exists($lessons_for_that_course, 'result_array')) {
                $lesson_rows = $lessons_for_that_course->result_array();
            } elseif (method_exists($lessons_for_that_course, 'getResultArray')) {
                $lesson_rows = $lessons_for_that_course->getResultArray();
            }
        } elseif (is_array($lessons_for_that_course)) {
            $lesson_rows = $lessons_for_that_course;
        }
        $total_number_of_lessons = count($lesson_rows);
        for ($i = 0; $i < count($watch_history_array); $i++) {
          $watch_history_for_each_lesson = $watch_history_array[$i];
          if ($watch_history_for_each_lesson['progress'] == 1) {
              array_push($completed_lessons_ids, $watch_history_for_each_lesson['lesson_id']);
          }
        }

        foreach ($lesson_rows as $row) {
          $lesson_id = is_array($row) ? ($row['id'] ?? null) : ($row->id ?? null);
          if ($lesson_id !== null && in_array($lesson_id, $completed_lessons_ids)) {
              $lesson_completed++;
          }
        }
        if($total_number_of_lessons == 0){
            $total_number_of_lessons = 1;
        }

        // calculate the percantage of progress
        $course_progress = ($lesson_completed / $total_number_of_lessons) * 100;
        return $course_progress;
    }
}