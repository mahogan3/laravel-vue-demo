<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * The next status forward in the lifecycle, or null once terminal
     * (Completed/Cancelled).
     */
    public function next(): ?self
    {
        return match ($this) {
            self::Pending => self::Processing,
            self::Processing => self::Shipped,
            self::Shipped => self::Completed,
            self::Completed, self::Cancelled => null,
        };
    }

    /**
     * Statuses this order can move to from here: the next status forward,
     * plus Cancelled (always available until the order is terminal).
     *
     * @return self[]
     */
    public function availableTransitions(): array
    {
        if (in_array($this, [self::Completed, self::Cancelled], true)) {
            return [];
        }

        return array_filter([$this->next(), self::Cancelled]);
    }
}
