<?php

namespace app\src\logic\actions;

/**
 * Класс реализует отклик на задание
 */
class ResponseAction extends AbstractAction
{
    public static function getLabel():string
    {
        return "Откликнуться";
    }

    public static function getInternalName():string
    {
        return "act_response";
    }

    public static function checkRights($userId, $performerId, $clientId):bool
    {
        return $userId !== $performerId;
    }
}