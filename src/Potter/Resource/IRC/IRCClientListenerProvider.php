<?php

declare(strict_types=1);

namespace Potter\Resource\IRC;

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
            case 'onPrivateMessage':
                return [function (EventInterface $event) {
                    echo 'RECEIVED PRIVATE MESSAGE FROM ' . $event->getEmitter()->getLastPrivateMessageSender() . PHP_EOL;
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
        $message = $emitter->getLastMessage();
        if (!str_contains($message, ' :')) {
            return;
        }
        $split = explode(' :', $message, 2);
        $left = $split[0];
        $right = $split[1];
        $eventDispatcher = $emitter->getEventDispatcher();
        if ($left === "PING") {
            $this->pingToken = $right;
            $eventDispatcher->dispatch(new Event('onPing', $emitter));
            return;
        }
        $leftSide = explode(' ', $left);
        if ($leftSide[1] === "PRIVMSG") {
            $sender = substr($leftSide[0], 1, strpos($leftSide[0], '!') - 2);
            $emitter->receivePrivateMessage($sender, $right);
            $eventDispatcher->dispatch(new Event('onPrivateMessage', $emitter));
        }
    }
}