<?php

namespace app\src\logic\actions;

use app\src\logic\actions\AbstractAction;

/**
 * Класс отвечает за действие отмены
 * Отменить задание может только тот пользователь, который его создал
 */
class CancelAction extends AbstractAction
{

    public static function getLabel():string
    {
      return "Отменить";
    }

    public static function getInternalName():string
    {
        return "act_cancel";
    }

    public static function checkRights($userId, $performerId, $clientId):bool
    {
        return $userId == $clientId;
    }
}