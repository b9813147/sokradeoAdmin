<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static DEFAULT
 * @method static static ANNOUNCE
 * @method static static EVENT
 * @method static static REVIEW
 * @method static static LICENSE
 * @method static static JOIN
 */
final class NotificationMessageType extends Enum
{
    /**
     *  通知類型
     */
    const DEFAULT = 0;
    const ANNOUNCE = 1;
    const EVENT = 2;
    const REVIEW = 3;
    const LICENSE = 4;
    const JOIN = 5;

    /**
     * @param int $type
     * @return bool
     */
    public static function check(int $type): bool
    {
        switch ($type) {
            case NotificationMessageType::DEFAULT:
            case NotificationMessageType::ANNOUNCE;
            case NotificationMessageType::EVENT;
            case NotificationMessageType::REVIEW;
            case NotificationMessageType::LICENSE;
            case NotificationMessageType::JOIN;
                return true;
            default:
                return false;
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function list(): \Illuminate\Support\Collection
    {
        return collect([
            ['type' => 'Default', 'value' => NotificationMessageType::DEFAULT, 'text' => __('notification.Default')],
            ['type' => 'Announce', 'value' => NotificationMessageType::ANNOUNCE, 'text' => __('notification.Announce')],
            ['type' => 'Event', 'value' => NotificationMessageType::EVENT, 'text' => __('notification.Event')],
            ['type' => 'Review', 'value' => NotificationMessageType::REVIEW, 'text' => __('notification.Review')],
            ['type' => 'License', 'value' => NotificationMessageType::LICENSE, 'text' => __('notification.License')],
            ['type' => 'Join', 'value' => NotificationMessageType::JOIN, 'text' => __('notification.Join')]
        ]);

    }
}
