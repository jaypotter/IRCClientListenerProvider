<?php

declare(strict_types=1);

namespace Potter\Socket\IRC\Client;

use \Potter\Event\Listener\AbstractListenerProvider;
use \Potter\Event\{EventInterface, Event};

final class IRCClientListenerProvider extends AbstractListenerProvider
{
    private string $pingToken;
    
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
                    $emitter->sendNickname();
                    $emitter->sendUsername();
                }];
                break;
            case 'onReceive':
                return [function (EventInterface $event) {
                    $this->onReceive($event);
                }];
                break;
            case 'onPing':
                return [function (EventInterface $event) {
                    $event->getEmitter()->pong($this->pingToken);
                }];
                break;
        }
    }
    
    public function hasListenersForEvent(object $event): bool
    {
        if (!($event instanceof EventInterface)) {
            return false;
        }
        
        return true;
    }
    
    private function onReceive(EventInterface $event): void
    {
        $emitter = $event->getEmitter();
        $message = $emitter->getLastSocketMessage();
        if (!str_contains($message, ' :')) {
            return;
        }
        $split = explode(' :', $message, 2);
        echo $message . PHP_EOL;
        $left = $split[0];
        $right = $split[1];
        if ($left === "PING") {
            $this->pingToken = $right;
            $emitter->getEventDispatcher()->dispatch(new Event('onPing', $emitter));
        }
    }
}