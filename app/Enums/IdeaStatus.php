<?php

declare(strict_types=1);

namespace App\Enums;

enum IdeaStatus: int
{
    case All = 0;
    case Open = 1;
    case Considering = 2;
    case InProgress = 3;
    case Implemented = 4;
    case Closed = 5;

    /**
     * Get all statuses except 'All'
     */
    public static function getActiveStatuses(): array
    {
        return array_filter(self::cases(), fn($status) => self::All !== $status);
    }

    /**
     * Get statuses as key-value array for select options
     */
    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($status) => [$status->value => $status->getName()])
            ->toArray();
    }

    /**
     * Get active statuses as key-value array (excluding 'All')
     */
    public static function getActiveOptions(): array
    {
        return collect(self::getActiveStatuses())
            ->mapWithKeys(fn($status) => [$status->value => $status->getName()])
            ->toArray();
    }

    /**
     * Get the display name for the status
     */
    public function getName(): string
    {
        return match ($this) {
            self::All => 'All Ideas',
            self::Open => 'Open',
            self::Considering => 'Considering',
            self::InProgress => 'In Progress',
            self::Implemented => 'Implemented',
            self::Closed => 'Closed',
        };
    }

    /**
     * Get the CSS class for styling
     */
    public function getClass(): string
    {
        return match ($this) {
            self::All => 'bg-gray-100 text-gray-800',
            self::Open => 'bg-blue-100 text-blue-800',
            self::Considering => 'bg-yellow-100 text-yellow-800',
            self::InProgress => 'bg-orange-100 text-orange-800',
            self::Implemented => 'bg-green-100 text-green-800',
            self::Closed => 'bg-red-100 text-red-800',
        };
    }

    /**
     * Get the badge color for UI
     */
    public function getBadgeColor(): string
    {
        return match ($this) {
            self::All => 'gray',
            self::Open => 'blue',
            self::Considering => 'yellow',
            self::InProgress => 'orange',
            self::Implemented => 'green',
            self::Closed => 'red',
        };
    }

    /**
     * Get icon for the status
     */
    public function getIcon(): string
    {
        return match ($this) {
            self::All => 'list',
            self::Open => 'lightbulb',
            self::Considering => 'clock',
            self::InProgress => 'cog',
            self::Implemented => 'check-circle',
            self::Closed => 'x-circle',
        };
    }

    /**
     * Check if status is active (not closed)
     */
    public function isActive(): bool
    {
        return ! in_array($this, [self::Closed, self::Implemented]);
    }

    /**
     * Check if status can be transitioned to another status
     */
    public function canTransitionTo(IdeaStatus $status): bool
    {
        return match ($this) {
            self::Open => in_array($status, [self::Considering, self::InProgress, self::Closed]),
            self::Considering => in_array($status, [self::Open, self::InProgress, self::Closed]),
            self::InProgress => in_array($status, [self::Considering, self::Implemented, self::Closed]),
            self::Implemented => self::Closed === $status,
            self::Closed => false, // Closed ideas cannot be reopened
            self::All => false, // All is not a real status
        };
    }

    /**
     * Get next possible statuses
     */
    public function getNextStatuses(): array
    {
        return array_filter(
            self::getActiveStatuses(),
            fn($status) => $this->canTransitionTo($status),
        );
    }
}
