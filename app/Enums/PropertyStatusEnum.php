<?php

namespace App\Enums;

enum PropertyStatusEnum: string
{
    case AVAILABLE = 'available';
    case OCCUPIED = 'occupied';
    case MAINTENANCE = 'maintenance';
    case RENOVATION = 'renovation';
    case PENDING_APPROVAL = 'pending_approval';
    case SUSPENDED = 'suspended';
    case ARCHIVED = 'archived';
    case VACANT = 'vacant';
    case MULTI_UNIT = 'multi_unit';
    case INACTIVE = 'inactive';

    /**
     * Get human-readable label for the status
     */
    public function getLabel(): string
    {
        return match($this) {
            self::AVAILABLE => 'Available',
            self::OCCUPIED => 'Occupied',
            self::MAINTENANCE => 'Under Maintenance',
            self::RENOVATION => 'Under Renovation',
            self::PENDING_APPROVAL => 'Pending Approval',
            self::SUSPENDED => 'Suspended',
            self::ARCHIVED => 'Archived',
            self::VACANT => 'Vacant',
            self::MULTI_UNIT => 'Multi-Unit',
            self::INACTIVE => 'Inactive',
        };
    }

    /**
     * Get color class for UI display
     */
    public function getColorClass(): string
    {
        return match($this) {
            self::AVAILABLE => 'success',
            self::OCCUPIED => 'primary',
            self::MAINTENANCE => 'warning',
            self::RENOVATION => 'info',
            self::PENDING_APPROVAL => 'secondary',
            self::SUSPENDED => 'danger',
            self::ARCHIVED => 'dark',
            self::VACANT => 'light',
            self::MULTI_UNIT => 'primary',
            self::INACTIVE => 'secondary',
        };
    }

    /**
     * Get icon for UI display
     */
    public function getIcon(): string
    {
        return match($this) {
            self::AVAILABLE => 'ni ni-check-circle',
            self::OCCUPIED => 'ni ni-home',
            self::MAINTENANCE => 'ni ni-tools',
            self::RENOVATION => 'ni ni-hammer',
            self::PENDING_APPROVAL => 'ni ni-clock',
            self::SUSPENDED => 'ni ni-ban',
            self::ARCHIVED => 'ni ni-archive',
            self::VACANT => 'ni ni-home-alt',
            self::MULTI_UNIT => 'ni ni-building',
            self::INACTIVE => 'ni ni-pause',
        };
    }

    /**
     * Get all active statuses (not archived or suspended)
     */
    public static function getActiveStatuses(): array
    {
        return [
            self::AVAILABLE,
            self::OCCUPIED,
            self::MAINTENANCE,
            self::RENOVATION,
            self::PENDING_APPROVAL,
            self::VACANT,
            self::MULTI_UNIT,
            self::INACTIVE,
        ];
    }

    /**
     * Get all available statuses for rent
     */
    public static function getRentableStatuses(): array
    {
        return [
            self::AVAILABLE,
            self::VACANT,
            self::MULTI_UNIT,
        ];
    }

    /**
     * Check if status allows occupancy
     */
    public function allowsOccupancy(): bool
    {
        return in_array($this, [
            self::AVAILABLE,
            self::OCCUPIED,
            self::MULTI_UNIT,
        ]);
    }

    /**
     * Check if status requires maintenance attention
     */
    public function requiresMaintenance(): bool
    {
        return in_array($this, [
            self::MAINTENANCE,
            self::RENOVATION,
        ]);
    }
}