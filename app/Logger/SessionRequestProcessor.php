<?php

namespace App\Logger;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionRequestProcessor
{
    private $session;
    private $sessionId;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    // this method is called for each log record; optimize it to not hurt performance
    public function __invoke(array $record)
    {
        if (!$this->session->isStarted()) {
            return $record;
        }

        if (!$this->sessionId) {
            $this->sessionId = substr($this->session->getId(), 0, 8) ?: '????????';
        }

        $record['extra']['token'] = $this->sessionId . '-' . substr(uniqid('', true), -8);

        return $record;
    }
}
