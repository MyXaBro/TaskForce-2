<?php

namespace app\src\logic\actions;

/**
 * Класс для отказа от задания
 */
class DenyAction extends AbstractAction
{
    public static function getLabel():string
    {
        return "Отказаться";
    }

    public static function getInternalName():string
    {
        return "act_deny";
    }

    public static function checkRights($userId, $performerId, $clientId):bool
    {
        return $userId == $performerId;
    }
}