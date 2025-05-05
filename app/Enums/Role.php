<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'Admin';
    case MANAGER = 'Manager';
    case RECON_MANAGER = 'Recon Manager';
    case VENDOR_MANAGER = 'Vendor';
    case SALES_TEAM = 'Sales Team';
    case TRANSPORTER = 'Transporter';
    case SALES_MANAGER = 'Sales Manager';
    case ONSITE_VENDOR = 'On-Site Vendor';
    case OFFSITE_VENDOR = 'Off-Site Vendor';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::MANAGER => 'Manager',
            self::RECON_MANAGER => 'Recon Manager',
            self::TRANSPORTER => 'Transporter',
            self::VENDOR_MANAGER => 'Vendor',
            self::SALES_MANAGER => 'Sales Manager',
            self::SALES_TEAM => 'Sales Team',
            self::ONSITE_VENDOR => 'On-Site Vendor',
            self::OFFSITE_VENDOR => 'Off-Site Vendor',
        };
    }

    public function canEnterCosts(): bool
    {
        return match($this) {
            self::ADMIN, self::MANAGER, self::ONSITE_VENDOR => true,
            default => false,
        };
    }

    public function canApproveEstimates(): bool
    {
        return match($this) {
            self::ADMIN, self::SALES_MANAGER => true,
            default => false,
        };
    }

    public function isVendor(): bool
    {
        return match($this) {
            self::ONSITE_VENDOR, self::OFFSITE_VENDOR => true,
            default => false,
        };
    }
} 