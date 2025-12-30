<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

if (!function_exists('activity_log')) {
    /**
     * Log user activity
     * 
     * @param string $action The action performed (e.g., 'LOGIN', 'CREATED', 'UPDATED', 'DELETED')
     * @param string $entityType The type of entity affected (e.g., 'USER', 'PURCHASE_REQUEST')
     * @param int|null $entityId The ID of the entity affected
     * @param array $details Additional details about the activity
     * @return ActivityLog|null
     */
    function activity_log(string $action, string $entityType, ?int $entityId = null, array $details = []): ?ActivityLog
    {
        try {
            return ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'details' => $details,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            // Log the error but don't break the application
            logger()->error('Failed to create activity log: ' . $e->getMessage());
            return null;
        }
    }
}
