<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an activity.
     */
    public static function log(string $type, string $description, $subject = null, array $properties = []): Activity
    {
        $activity = Activity::create([
            'type' => $type,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->getKey() : null,
            'causer_type' => Auth::check() ? get_class(Auth::user()) : null,
            'causer_id' => Auth::check() ? Auth::id() : null,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);

        return $activity;
    }

    /**
     * Log user login.
     */
    public static function login($user): Activity
    {
        return static::log('login', 'User logged in', $user, [
            'email' => $user?->email,
            'role' => $user?->role,
        ]);
    }

    /**
     * Log user logout.
     */
    public static function logout($user): Activity
    {
        return static::log('logout', 'User logged out', $user, [
            'email' => $user?->email,
            'role' => $user?->role,
        ]);
    }

    /**
     * Log crop creation.
     */
    public static function cropCreated($crop): Activity
    {
        return static::log('crop_created', 'Crop created', $crop, [
            'name' => $crop->name,
            'category' => $crop->category,
            'price_per_kg' => $crop->price_per_kg,
            'quantity' => $crop->quantity,
            'region' => $crop->region,
        ]);
    }

    /**
     * Log crop update.
     */
    public static function cropUpdated($crop, $changes): Activity
    {
        return static::log('crop_updated', 'Crop updated', $crop, [
            'name' => $crop->name,
            'changes' => $changes,
        ]);
    }

    /**
     * Log crop deletion.
     */
    public static function cropDeleted($crop): Activity
    {
        return static::log('crop_deleted', 'Crop deleted', $crop, [
            'name' => $crop->name,
            'category' => $crop->category,
        ]);
    }

    /**
     * Log order creation.
     */
    public static function orderCreated($order): Activity
    {
        return static::log('order_created', 'Order created', $order, [
            'order_number' => $order->order_number,
            'total_amount' => $order->total_amount,
            'buyer_name' => $order->buyer->name,
        ]);
    }

    /**
     * Log order status update.
     */
    public static function orderStatusUpdated($order, $oldStatus, $newStatus): Activity
    {
        return static::log('order_status_updated', 'Order status updated', $order, [
            'order_number' => $order->order_number,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);
    }

    /**
     * Log user registration.
     */
    public static function userRegistered($user): Activity
    {
        return static::log('user_registered', 'User registered', $user, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }

    /**
     * Log user profile update.
     */
    public static function profileUpdated($user, $changes): Activity
    {
        return static::log('profile_updated', 'Profile updated', $user, [
            'name' => $user->name,
            'changes' => $changes,
        ]);
    }

    /**
     * Log admin action.
     */
    public static function adminAction(string $action, string $description, $subject = null, array $properties = []): Activity
    {
        return static::log('admin_action', $description, $subject, array_merge([
            'action' => $action,
        ], $properties));
    }
}
