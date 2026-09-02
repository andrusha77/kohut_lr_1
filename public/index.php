<?php

declare(strict_types=1);

use App\SubmissionProcessor;

require dirname(__DIR__) . '/vendor/autoload.php';

// -------------------------------------------------
// ДОПОМІЖНА ФУНКЦІЯ ДЛЯ БЕЗПЕЧНОГО HTML
// -------------------------------------------------

function e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// -------------------------------------------------
// ІНДИВІДУАЛЬНІ ПАРАМЕТРИ
// -------------------------------------------------

$studentNumber = 1; // !!! ЗАМІНИ НА СВІЙ НОМЕР У ЖУРНАЛІ
$variantNumber = 6;

$coefficientPercent = 100 + 5 * ($studentNumber % 5);

$historyLimit = 3 + ($studentNumber % 4);

// -------------------------------------------------
// SESSION
// -------------------------------------------------

session_start();

// Обов'язковий HTTP-заголовок
header(
    "X-Student-Variant: {$studentNumber}-{$variantNumber}"
);

// -------------------------------------------------
// ТИПИ КОНСУЛЬТАЦІЙ І СТАВКИ
// -------------------------------------------------

$rates = [
    'study' => 8,
    'career' => 10,
    'project' => 14,
];

$allowedTypes = array_keys($rates);

// -------------------------------------------------
// PROCESSOR
// -------------------------------------------------

$processor = new SubmissionProcessor(
    $rates,
    $coefficientPercent
);

// -------------------------------------------------
// ПОЧАТКОВІ ЗНАЧЕННЯ
// -------------------------------------------------

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$errors = [];

$old = [
    'clientName' => '',
    'duration' => '',
    'type' => '',
    'date' => '',
    'question' => '',
];

// -------------------------------------------------
// POST
// -------------------------------------------------

if ($method === 'POST') {

    $result = $processor->process($_POST);

    $errors = $result['errors'];
    $old = $result['old'];

    // Якщо все добре
    if ($result['submission'] !== null) {

        $history = $_SESSION['submissions'] ?? [];

        $history[] = $result['submission']->toArray();

        // Зберігаємо тільки останні L заявок
        $_SESSION['submissions'] = array_slice(
            $history,
            -$historyLimit
        );

        // Post / Redirect / Get
        header('Location: /?created=1', true, 303);

        exit;
    }
}

// -------------------------------------------------
// ІСТОРІЯ
// -------------------------------------------------

$history = $_SESSION['submissions'] ?? [];

// -------------------------------------------------
// GET-ФІЛЬТР ЗА ТИПОМ КОНСУЛЬТАЦІЇ
// -------------------------------------------------

$typeFilter = trim((string) ($_GET['type'] ?? ''));

if (
    $typeFilter !== '' &&
    in_array($typeFilter, $allowedTypes, true)
) {
    $history = array_values(
        array_filter(
            $history,
            static fn(array $row): bool =>
                $row['type'] === $typeFilter
        )
    );
}

// -------------------------------------------------
// VIEW
// -------------------------------------------------

require dirname(__DIR__) . '/views/form.php';