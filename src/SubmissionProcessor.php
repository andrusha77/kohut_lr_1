<?php

declare(strict_types=1);

namespace App;

final class SubmissionProcessor
{
    public function __construct(
        private readonly array $rates,
        private readonly int $coefficientPercent,
    ) {
    }

    public function process(array $input): array
    {
        $errors = [];

        // -------------------------------------------------
        // НОРМАЛІЗАЦІЯ
        // -------------------------------------------------

        $old = [
            'clientName' => trim((string) ($input['clientName'] ?? '')),
            'duration' => trim((string) ($input['duration'] ?? '')),
            'type' => trim((string) ($input['type'] ?? '')),
            'date' => trim((string) ($input['date'] ?? '')),
            'question' => trim((string) ($input['question'] ?? '')),
        ];

        // -------------------------------------------------
        // 1. ІМ'Я КЛІЄНТА
        // -------------------------------------------------

        if ($old['clientName'] === '') {
            $errors['clientName'] = 'Введіть ім’я клієнта.';
        } elseif (strlen($old['clientName']) > 80) {
            $errors['clientName'] = 'Ім’я не повинно перевищувати 80 байтів.';
        }

        // -------------------------------------------------
        // 2. ТРИВАЛІСТЬ
        // -------------------------------------------------

        $duration = filter_var(
            $input['duration'] ?? null,
            FILTER_VALIDATE_INT
        );

        if ($duration === false || $duration === null) {

            $errors['duration'] = 'Тривалість повинна бути цілим числом.';

        } elseif ($duration < 30 || $duration > 180) {

            $errors['duration'] =
                'Тривалість повинна бути від 30 до 180 хвилин.';

        } elseif ($duration % 15 !== 0) {

            $errors['duration'] =
                'Тривалість повинна бути кратною 15 хвилинам.';
        }

        // -------------------------------------------------
        // 3. ТИП КОНСУЛЬТАЦІЇ
        // -------------------------------------------------

        if (!array_key_exists($old['type'], $this->rates)) {
            $errors['type'] = 'Оберіть допустимий тип консультації.';
        }

        // -------------------------------------------------
        // 4. ДАТА
        // -------------------------------------------------

        $date = \DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $old['date']
        );

        if (
            $date === false ||
            $date->format('Y-m-d') !== $old['date']
        ) {
            $errors['date'] = 'Введіть коректну дату.';
        }

        // -------------------------------------------------
        // 5. ЗАПИТАННЯ
        // -------------------------------------------------

        if ($old['question'] === '') {

            $errors['question'] = 'Введіть запитання.';

        } elseif (strlen($old['question']) < 10) {

            $errors['question'] =
                'Запитання повинно містити не менше 10 байтів.';
        }

        // -------------------------------------------------
        // ЯКЩО Є ПОМИЛКИ
        // -------------------------------------------------

        if ($errors !== []) {
            return [
                'submission' => null,
                'errors' => $errors,
                'old' => $old,
            ];
        }

        // -------------------------------------------------
        // РОЗРАХУНОК ВАРТОСТІ
        // -------------------------------------------------

        $rate = $this->rates[$old['type']];

        $cost = $duration * $rate;

        // Знижка 8%, якщо консультація більше 120 хв.
        if ($duration > 120) {
            $cost *= 0.92;
        }

        // K
        $coefficient = $this->coefficientPercent / 100;

        $cost *= $coefficient;

        $cost = round($cost, 2);

        // -------------------------------------------------
        // СТВОРЕННЯ ОБ'ЄКТА
        // -------------------------------------------------

        $submission = new Submission(
            clientName: $old['clientName'],
            duration: $duration,
            type: $old['type'],
            date: $old['date'],
            question: $old['question'],
            cost: $cost,
        );

        return [
            'submission' => $submission,
            'errors' => [],
            'old' => $old,
        ];
    }
}