<?php

namespace App\Enums;

enum MpesaStatusEnum: string
{
    // STK Push Statuses
    case REQUEST_SENT = 'Request Sent';
    case PAID = 'Paid';
    case FAILED = 'Failed';
    
    // Detailed Failure Reasons
    case CANCELLED_BY_USER = 'Cancelled by User';
    case WRONG_PIN = 'Wrong PIN';
    case INSUFFICIENT_BALANCE = 'Insufficient Balance';
    case TIMEOUT = 'Timeout';
    case USER_CANCELLED = 'User Cancelled';
    case SYSTEM_ERROR = 'System Error';
    case NETWORK_ERROR = 'Network Error';
    case INVALID_AMOUNT = 'Invalid Amount';
    case INVALID_PHONE = 'Invalid Phone Number';
    case SERVICE_UNAVAILABLE = 'Service Unavailable';
    case ACCOUNT_SUSPENDED = 'Account Suspended';
    case DAILY_LIMIT_EXCEEDED = 'Daily Limit Exceeded';
    case TRANSACTION_LIMIT_EXCEEDED = 'Transaction Limit Exceeded';
    
    // C2B Statuses
    case C2B_SUCCESS = 'C2B Success';
    case C2B_FAILED = 'C2B Failed';
    case C2B_PENDING = 'C2B Pending';
    
    // Processing Statuses
    case PROCESSING = 'Processing';
    case PENDING_CONFIRMATION = 'Pending Confirmation';
    case REVERSED = 'Reversed';
    case REFUNDED = 'Refunded';
    
    /**
     * Get all STK Push statuses
     */
    public static function getStkStatuses(): array
    {
        return [
            self::REQUEST_SENT,
            self::PAID,
            self::FAILED,
            self::CANCELLED_BY_USER,
            self::WRONG_PIN,
            self::INSUFFICIENT_BALANCE,
            self::TIMEOUT,
            self::USER_CANCELLED,
            self::SYSTEM_ERROR,
            self::NETWORK_ERROR,
            self::INVALID_AMOUNT,
            self::INVALID_PHONE,
            self::SERVICE_UNAVAILABLE,
            self::ACCOUNT_SUSPENDED,
            self::DAILY_LIMIT_EXCEEDED,
            self::TRANSACTION_LIMIT_EXCEEDED,
        ];
    }
    
    /**
     * Get all C2B statuses
     */
    public static function getC2bStatuses(): array
    {
        return [
            self::C2B_SUCCESS,
            self::C2B_FAILED,
            self::C2B_PENDING,
        ];
    }
    
    /**
     * Get all processing statuses
     */
    public static function getProcessingStatuses(): array
    {
        return [
            self::PROCESSING,
            self::PENDING_CONFIRMATION,
            self::REVERSED,
            self::REFUNDED,
        ];
    }
    
    /**
     * Check if status indicates success
     */
    public function isSuccess(): bool
    {
        return in_array($this, [
            self::PAID,
            self::C2B_SUCCESS,
        ]);
    }
    
    /**
     * Check if status indicates failure
     */
    public function isFailure(): bool
    {
        return in_array($this, [
            self::FAILED,
            self::CANCELLED_BY_USER,
            self::WRONG_PIN,
            self::INSUFFICIENT_BALANCE,
            self::TIMEOUT,
            self::USER_CANCELLED,
            self::SYSTEM_ERROR,
            self::NETWORK_ERROR,
            self::INVALID_AMOUNT,
            self::INVALID_PHONE,
            self::SERVICE_UNAVAILABLE,
            self::ACCOUNT_SUSPENDED,
            self::DAILY_LIMIT_EXCEEDED,
            self::TRANSACTION_LIMIT_EXCEEDED,
            self::C2B_FAILED,
        ]);
    }
    
    /**
     * Check if status indicates pending/processing
     */
    public function isPending(): bool
    {
        return in_array($this, [
            self::REQUEST_SENT,
            self::PROCESSING,
            self::PENDING_CONFIRMATION,
            self::C2B_PENDING,
        ]);
    }
    
    /**
     * Get status color for UI
     */
    public function getColor(): string
    {
        return match($this) {
            self::PAID, self::C2B_SUCCESS => 'success',
            self::FAILED, self::CANCELLED_BY_USER, self::WRONG_PIN, 
            self::INSUFFICIENT_BALANCE, self::TIMEOUT, self::USER_CANCELLED,
            self::SYSTEM_ERROR, self::NETWORK_ERROR, self::INVALID_AMOUNT,
            self::INVALID_PHONE, self::SERVICE_UNAVAILABLE, self::ACCOUNT_SUSPENDED,
            self::DAILY_LIMIT_EXCEEDED, self::TRANSACTION_LIMIT_EXCEEDED, self::C2B_FAILED => 'danger',
            self::REQUEST_SENT, self::PROCESSING, self::PENDING_CONFIRMATION, self::C2B_PENDING => 'warning',
            self::REVERSED, self::REFUNDED => 'info',
        };
    }
    
    /**
     * Get status icon for UI
     */
    public function getIcon(): string
    {
        return match($this) {
            self::PAID, self::C2B_SUCCESS => 'ni-check-circle',
            self::FAILED, self::CANCELLED_BY_USER, self::WRONG_PIN, 
            self::INSUFFICIENT_BALANCE, self::TIMEOUT, self::USER_CANCELLED,
            self::SYSTEM_ERROR, self::NETWORK_ERROR, self::INVALID_AMOUNT,
            self::INVALID_PHONE, self::SERVICE_UNAVAILABLE, self::ACCOUNT_SUSPENDED,
            self::DAILY_LIMIT_EXCEEDED, self::TRANSACTION_LIMIT_EXCEEDED, self::C2B_FAILED => 'ni-cross-circle',
            self::REQUEST_SENT, self::PROCESSING, self::PENDING_CONFIRMATION, self::C2B_PENDING => 'ni-clock',
            self::REVERSED, self::REFUNDED => 'ni-refresh',
        };
    }
    
    /**
     * Get human-readable description
     */
    public function getDescription(): string
    {
        return match($this) {
            self::REQUEST_SENT => 'Payment request sent to customer',
            self::PAID => 'Payment completed successfully',
            self::FAILED => 'Payment failed due to unknown error',
            self::CANCELLED_BY_USER => 'Customer cancelled the payment',
            self::WRONG_PIN => 'Customer entered wrong PIN',
            self::INSUFFICIENT_BALANCE => 'Customer has insufficient balance',
            self::TIMEOUT => 'Payment request timed out',
            self::USER_CANCELLED => 'User cancelled the transaction',
            self::SYSTEM_ERROR => 'System error occurred',
            self::NETWORK_ERROR => 'Network error occurred',
            self::INVALID_AMOUNT => 'Invalid payment amount',
            self::INVALID_PHONE => 'Invalid phone number',
            self::SERVICE_UNAVAILABLE => 'M-PESA service unavailable',
            self::ACCOUNT_SUSPENDED => 'Customer account is suspended',
            self::DAILY_LIMIT_EXCEEDED => 'Daily transaction limit exceeded',
            self::TRANSACTION_LIMIT_EXCEEDED => 'Transaction limit exceeded',
            self::C2B_SUCCESS => 'C2B payment successful',
            self::C2B_FAILED => 'C2B payment failed',
            self::C2B_PENDING => 'C2B payment pending',
            self::PROCESSING => 'Payment is being processed',
            self::PENDING_CONFIRMATION => 'Waiting for confirmation',
            self::REVERSED => 'Payment was reversed',
            self::REFUNDED => 'Payment was refunded',
        };
    }
    
    /**
     * Map M-PESA ResultCode to detailed status
     */
    public static function fromResultCode(int $resultCode, string $resultDesc = ''): self
    {
        return match($resultCode) {
            0 => self::PAID,
            1 => self::CANCELLED_BY_USER,
            2 => self::INSUFFICIENT_BALANCE,
            3 => self::WRONG_PIN,
            4 => self::TIMEOUT,
            5 => self::SYSTEM_ERROR,
            6 => self::NETWORK_ERROR,
            7 => self::INVALID_AMOUNT,
            8 => self::INVALID_PHONE,
            9 => self::SERVICE_UNAVAILABLE,
            10 => self::ACCOUNT_SUSPENDED,
            11 => self::DAILY_LIMIT_EXCEEDED,
            12 => self::TRANSACTION_LIMIT_EXCEEDED,
            default => self::FAILED,
        };
    }
    
    /**
     * Map M-PESA ResultDesc to detailed status
     */
    public static function fromResultDesc(string $resultDesc): self
    {
        $desc = strtolower($resultDesc);
        
        return match(true) {
            str_contains($desc, 'cancelled') || str_contains($desc, 'cancel') => self::CANCELLED_BY_USER,
            str_contains($desc, 'wrong pin') || str_contains($desc, 'invalid pin') => self::WRONG_PIN,
            str_contains($desc, 'insufficient') || str_contains($desc, 'low balance') => self::INSUFFICIENT_BALANCE,
            str_contains($desc, 'timeout') || str_contains($desc, 'timed out') => self::TIMEOUT,
            str_contains($desc, 'system error') || str_contains($desc, 'internal error') => self::SYSTEM_ERROR,
            str_contains($desc, 'network') || str_contains($desc, 'connection') => self::NETWORK_ERROR,
            str_contains($desc, 'invalid amount') || str_contains($desc, 'amount') => self::INVALID_AMOUNT,
            str_contains($desc, 'invalid phone') || str_contains($desc, 'phone number') => self::INVALID_PHONE,
            str_contains($desc, 'service unavailable') || str_contains($desc, 'unavailable') => self::SERVICE_UNAVAILABLE,
            str_contains($desc, 'suspended') || str_contains($desc, 'blocked') => self::ACCOUNT_SUSPENDED,
            str_contains($desc, 'daily limit') || str_contains($desc, 'limit exceeded') => self::DAILY_LIMIT_EXCEEDED,
            str_contains($desc, 'transaction limit') => self::TRANSACTION_LIMIT_EXCEEDED,
            default => self::FAILED,
        };
    }
}

