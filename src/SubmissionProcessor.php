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


        $old = [
            'clientName' => trim((string) ($input['clientName'] ?? '')),
            'duration' => trim((string) ($input['duration'] ?? '')),
            'type' => trim((string) ($input['type'] ?? '')),
            'date' => trim((string) ($input['date'] ?? '')),
            'question' => trim((string) ($input['question'] ?? '')),
        ];



        if ($old['clientName'] === '') {
            $errors['clientName'] = 'Введіть ім’я клієнта.';
        } elseif (strlen($old['clientName']) > 80) {
            $errors['clientName'] = 'Ім’я не повинно перевищувати 80 символів.';
        }


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

        

        if (!array_key_exists($old['type'], $this->rates)) {
            $errors['type'] = 'Оберіть допустимий тип консультації.';
        }



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


        if ($old['question'] === '') {

            $errors['question'] = 'Введіть запитання.';

        } elseif (strlen($old['question']) < 10) {

            $errors['question'] =
                'Запитання повинно містити не менше 10 байтів.';
        }


        if ($errors !== []) {
            return [
                'submission' => null,
                'errors' => $errors,
                'old' => $old,
            ];
        }

=

        $rate = $this->rates[$old['type']];

        $cost = $duration * $rate;

        if ($duration > 120) {
            $cost *= 0.92;
        }

=        $coefficient = $this->coefficientPercent / 100;

        $cost *= $coefficient;

        $cost = round($cost, 2);



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