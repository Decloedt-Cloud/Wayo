<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class LmsService
{
    private $db;
    private $session;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->session = service('session');
    }

    public function getVideoExtension(string $url): string
    {
        if (strpos($url, '.mp4') > 0) {
            return 'mp4';
        } elseif (strpos($url, '.webm') > 0) {
            return 'webm';
        }
        return 'unknown';
    }

    public function getLessonProgress(int $lessonId, ?int $userId = null): int
    {
        $userId = $userId ?? (int) session()->get('user_id');
        
        if (!$userId) {
            return 0;
        }

        $user = db()->table('users')
            ->where('id', $userId)
            ->get()
            ->getRow();

        if (!$user || empty($user->watch_history)) {
            return 0;
        }

        $watchHistory = json_decode($user->watch_history, true) ?? [];
        
        foreach ($watchHistory as $entry) {
            if ($entry['lesson_id'] == $lessonId) {
                return $entry['progress'];
            }
        }
        
        return 0;
    }

    public function getCourseProgress(int $courseId, ?int $userId = null): float
    {
        $userId = $userId ?? (int) session()->get('user_id');
        
        if (!$userId) {
            return 0.0;
        }

        $user = db()->table('users')
            ->where('id', $userId)
            ->get()
            ->getRow();

        if (!$user || empty($user->watch_history)) {
            return 0.0;
        }

        $watchHistory = json_decode($user->watch_history, true) ?? [];
        
        $completedLessons = [];
        foreach ($watchHistory as $entry) {
            if ($entry['progress'] == 1) {
                $completedLessons[] = $entry['lesson_id'];
            }
        }

        $totalLessons = db()->table('lesson')
            ->where('course_id', $courseId)
            ->countAllResults();

        if ($totalLessons === 0) {
            return 0.0;
        }

        return (count($completedLessons) / $totalLessons) * 100;
    }

    public function readableTimeForHumans(string $duration): string
    {
        if (!$duration) {
            return '00:00';
        }

        $parts = explode(':', $duration);
        
        if (count($parts) !== 3) {
            return $duration;
        }

        [$hours, $minutes, $seconds] = $parts;

        if ($hours > 0) {
            return $hours . ' hr ' . $minutes . ' min';
        } elseif ($minutes > 0) {
            return ($seconds > 0 ? $minutes + 1 : $minutes) . ' min';
        } elseif ($seconds > 0) {
            return $seconds . ' sec';
        }

        return '00:00';
    }
}
