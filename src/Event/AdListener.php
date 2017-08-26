<?php

namespace fevm\Classified\Event;

use fevm\Classified\Model\Ad;
use Pagekit\Event\EventSubscriberInterface;

class AdListener implements EventSubscriberInterface
{

    public function onRoleDelete($event, $role)
    {
        Ad::removeRole($role);
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'model.role.deleted' => 'onRoleDelete'
        ];
    }
}
