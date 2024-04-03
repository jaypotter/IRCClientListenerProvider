<?php

declare(strict_types=1);

namespace Potter\Resource\IRC;

use \Potter\Event\Listener\AbstractListenerProvider;
use \Potter\Event\EventInterface;

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
                    $event->getEmitter()->handleConnection();
                }];
                break;
            case 'onReceive':
                return [function (EventInterface $event) {
                    $event->getEmitter()->handleMessage();
                }];
                break;
            case 'onPing':
                return [function (EventInterface $event) {
                    $event->getEmitter()->handlePing();
                }];
                break;
            case 'onPrivateMessage':
                return [function (EventInterface $event) {
                    $event->getEmitter()->handlePrivateMessage();
                }];
                break;
            case 'onMessageOfTheDay':
                return [function (EventInterface $event) {
                    $event->getEmitter()->handleMessageOfTheDay();
                }];
                break;
        }
        return [];
    }
    
    public function hasListenersForEvent(object $event): bool
    {
        if (!($event instanceof EventInterface)) {
            return false;
        }
        return in_array($event->getId(), [
           'onConnection', 'onReceive', 'onPing', 'onMessageOfTheDay', 'onPrivateMessage'
        ]);
    }
}