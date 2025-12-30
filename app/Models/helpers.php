<?php

use App\Models\ActivityLog;

if (!function_exists('activity_log')) {
    /**
     * Log user activity
     */
    function activity_log($action, $entity_type, $entity_id, $details = null, $ip_address = null)
    {
        if (auth()->check()) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'entity_type' => $entity_type,
                'entity_id' => $entity_id,
                'details' => $details,
                'ip_address' => $ip_address ?? request()->ip(),
            ]);
        }
    }
}