<?php

namespace Crm\SubscriptionsModule\Events;

use Crm\UsersModule\User\IUserGetter;
use League\Event\AbstractEvent;
use Nette\Database\Table\ActiveRow;

class SubscriptionStartsEvent extends AbstractEvent implements SubscriptionEventInterface, IUserGetter
{
    /** @var ActiveRow */
    private $subscription;

    public function __construct(ActiveRow $subscription)
    {
        $this->subscription = $subscription;
    }

    public function getSubscription(): ActiveRow
    {
        return $this->subscription;
    }

    public function getUserId(): int
    {
        return $this->subscription->user_id;
    }
}
