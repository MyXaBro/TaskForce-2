<?php

namespace app\src\logic\actions;

/**
 * Класс отвечает за завершение действия
 * Завершить это задание может только исполнитель (текущий пользователь)
 */
class CompleteAction extends AbstractAction
{

    public static function getLabel():string
    {
        return "Завершить";
    }

    public static function getInternalName():string
    {
        return "act_complete";
    }

    public static function checkRights($userId, $performerId, $clientId):bool
    {
        return $performerId == $userId;
    }
}