<?php

use App\Helpers\OrganizationHelper;

if (!function_exists('org_trans')) {
    /**
     * Translate text based on organization language
     */
    function org_trans($key, $params = [])
    {
        return OrganizationHelper::trans($key, $params);
    }
}

if (!function_exists('org_date')) {
    /**
     * Format date according to organization settings
     */
    function org_date($date, $includeTime = false)
    {
        return OrganizationHelper::formatDate($date, $includeTime);
    }
}

if (!function_exists('org_time')) {
    /**
     * Format time according to organization settings
     */
    function org_time($time)
    {
        return OrganizationHelper::formatTime($time);
    }
}

if (!function_exists('org_setting')) {
    /**
     * Get organization setting
     */
    function org_setting($key, $default = null)
    {
        return OrganizationHelper::getSetting($key, $default);
    }
}
