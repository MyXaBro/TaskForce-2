<?php
namespace app\src\logic;

use app\src\exception\StatusActionException;
use app\src\logic\actions\AbstractAction;
use app\src\logic\actions\CancelAction;
use app\src\logic\actions\CompleteAction;
use app\src\logic\actions\DenyAction;
use app\src\logic\actions\ResponseAction;
use DateTime;

class AvailableActions
{
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'proceed';
    const STATUS_CANCEL = 'cancel';
    const STATUS_COMPLETE = 'complete';
    const STATUS_EXPIRED = 'expired';

    const ROLE_PERFORMER = 'Исполнитель';
    const ROLE_CLIENT = 'Заказчик';

    private ?int $performerId;
    private ?int $clientId;

    private ?string $status;

    /**
     * AvailableActionsStrategy constructor.
     * @param string $status
     * @param int|null $performerId
     * @param int $clientId
     */
    public function __construct(string $status, ?int $performerId, int $clientId)
    {
        $this->setStatus($status);

        $this->performerId = $performerId;
        $this->clientId = $clientId;
    }

    public function setFinishDate(DateTime $dt) {
        $curDate = new DateTime();

        if ($dt > $curDate) {
            $this->finishDate = $dt;
        }
    }

    /**
     * Метод получения действий для текущего статуса
     * @param string $role
     * @param int $id
     * @return array
     */
    public function getAvailableActions(string $role, int $id):array
    {
        //Передаём имя статуса и получаем набор действий, который соответствует этому статусу
        $statusActions = $this->statusAllowedActions()[$this->status];
        //Получаем набор действий для роли, либо закзчик, либо исполнитель
        $roleActions = $this->roleAllowedActions()[$role];
        //Создаём пересечение действий для текущей роли и текущего статуса
        $allowedActions = array_intersect($statusActions, $roleActions);
        //Проходим по массиву и для каждых доступных действий применяем метод проверки действий (bool)
        $allowedActions = array_filter($allowedActions, function ($action) use ($id) {
            return $action::checkRights($id, $this->performerId, $this->clientId);
        });

        return array_values($allowedActions);
    }

    /**
     * Возвращает следующий статус
     * @param AbstractAction $action
     * @return null|string
     */
    public function getNextStatus(AbstractAction $action):?string
    {
            $map = [
                CompleteAction::class => self::STATUS_COMPLETE,
                CancelAction::class => self::STATUS_CANCEL,
                DenyAction::class => self::STATUS_CANCEL,
                ResponseAction::class => null,
                AbstractAction::class => null
            ];

        return $map[get_class($action)];
    }

    /**
     * Устанавливает статус без возврата значений
     * @param string $status
     * @return void
     */
public function setStatus(string $status):void
    {
        $availableStatuses = [
            self::STATUS_NEW,
            self::STATUS_IN_PROGRESS,
            self::STATUS_CANCEL,
            self::STATUS_COMPLETE,
            self::STATUS_EXPIRED];

        if (!in_array($status, $availableStatuses)) {
           throw new StatusActionException("Неизвестный статус: $status");
        }
        $this->status = $status;
    }

    /**
     * Проверяет роль пользователя
     * @param string $role
     * @return void
     */
    public function checkRole(string $role):void
    {
        $availableRoles = [
            self::ROLE_PERFORMER,
            self::ROLE_CLIENT
        ];

        if(!in_array($role, $availableRoles)){
            throw new StatusActionException("Неизвестная роль: $role");
        }
    }

    /**
     * Возвращает действия, доступные для каждой роли
     * @return array
     */
    private function roleAllowedActions():array
    {
         return [
             self::ROLE_CLIENT => [CancelAction::class, CompleteAction::class],
             self::ROLE_PERFORMER => [ResponseAction::class, DenyAction::class]
        ];
    }

    /**
     * Возвращает действия, доступные для каждого статуса
     * @return array
     */
    private function statusAllowedActions():array
    {
        return [
            self::STATUS_CANCEL => [],
            self::STATUS_COMPLETE => [],
            self::STATUS_IN_PROGRESS => [DenyAction::class, CompleteAction::class],
            self::STATUS_NEW => [CancelAction::class, ResponseAction::class],
            self::STATUS_EXPIRED => []
        ];
    }

    private function getStatusMap():array
    {
       return [
            self::STATUS_NEW => [self::STATUS_EXPIRED, self::STATUS_CANCEL],
            self::STATUS_IN_PROGRESS => [self::STATUS_CANCEL, self::STATUS_COMPLETE],
            self::STATUS_CANCEL => [],
            self::STATUS_COMPLETE => [],
            self::STATUS_EXPIRED => [self::STATUS_CANCEL]
        ];
    }

}
