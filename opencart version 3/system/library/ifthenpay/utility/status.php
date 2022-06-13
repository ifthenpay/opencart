<?php

declare(strict_types=1);

namespace Ifthenpay\Utility;

class Status {
    
    private $statusSucess = "6dfcbb0428e4f89c";
    private $statusError = "101737ba0aa2e7c5";
    private $statusCancel = "d4d26126c0f39bf2";

    public function getTokenStatus(string $token): string
    {
        switch ($token) {
            case $this->statusSucess:
                return 'success';
            case $this->statusCancel:
                return 'cancel';
            case $this->statusError:
                return 'error';
            default:
                return '';
        }
    }

    /**
     * Get the value of statusSucess
     */ 
    public function getStatusSucess(): string
    {
        return $this->statusSucess;
    }

    /**
     * Get the value of statusError
     */ 
    public function getStatusError(): string
    {
        return $this->statusError;
    }

    /**
     * Get the value of statusCancel
     */ 
    public function getStatusCancel(): string
    {
        return $this->statusCancel;
    }
}