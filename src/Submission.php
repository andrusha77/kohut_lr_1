<?php

declare(strict_types=1);

namespace App;

final readonly class Submission
{
    public function __construct(
        public string $clientName,
        public int $duration,
        public string $type,
        public string $date,
        public string $question,
        public float $cost,
    ) {
    }

    public function toArray(): array
    {
        return [
            'clientName' => $this->clientName,
            'duration' => $this->duration,
            'type' => $this->type,
            'date' => $this->date,
            'question' => $this->question,
            'cost' => $this->cost,
        ];
    }
}