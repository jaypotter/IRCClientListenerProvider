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
            case 'onReceiveMessageOfTheDay':
                return [function (EventInterface $event) {
                    $event->getEmitter()->handleMessageOfTheDay();
                }];
                break;
            case 'onCompleteMessageOfTheDay':
                return [function (EventInterface $event) {
                    echo $event->getEmitter->getMessageOfTheDay();
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
        
        return true;
    }
}