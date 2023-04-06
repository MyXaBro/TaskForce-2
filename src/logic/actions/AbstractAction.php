<?php

namespace app\src\logic\actions;

abstract class AbstractAction
{
    //метод возвращает название на русском языке
    abstract public static function getLabel():string;
    //метод возвращает внутреннее имя класса
    abstract public static function getInternalName():string;
    //метод для проверки возможности применения текущему пользователю, для текущего исполнителя и для текущего задания
    abstract public static function checkRights(int $userId, ?int $performerId, ?int $clientId):bool;
}

