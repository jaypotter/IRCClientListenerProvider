<?php

declare(strict_types=1);

namespace Potter\Socket\IRC\Client;

use \Potter\Event\Listener\AbstractListenerProvider;

use Potter\Event\EventInterface;

final class IRCClientListenerProvider extends AbstractListenerProvider
{
    public function getListenersForEvent(object $event): iterable
    {
        if (!($event instanceof EventInterface)) {
            return [];
        }
        switch ($event->getId()) {
            case 'onConnection':
                return [function (EventInterface $event) {
                    $emitter = $event->getEmitter();
                    $emitter->sendPassword();
                    $emitter->sendNick();
                    $emitter->sendUser();
                }];
        }
    }
    
    public function hasListenersForEvent(object $event): bool
    {
        if (!($event instanceof EventInterface)) {
            return false;
        }
        
        return true;
    }
}