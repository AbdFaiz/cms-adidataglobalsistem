<?php

if (!function_exists('canResetPassword')) {
    function canResetPassword($currentUser, $targetUser)
    {
        $hierarchy = [
            'Supervisor' => 5,
            'Leader' => 4,
            'Admin Tracking' => 3,
            'Admin Officer' => 2,
            'Customer Service' => 1,
        ];

        $currentRoles = $currentUser->roles
            ->pluck('name')
            ->map(fn($r) => $hierarchy[$r] ?? 0)
            ->max();
        $targetRoles = $targetUser->roles
            ->pluck('name')
            ->map(fn($r) => $hierarchy[$r] ?? 0)
            ->max();

        return $currentRoles > $targetRoles;
    }
}
