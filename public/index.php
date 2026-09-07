<?php

declare(strict_types=1);

use App\SubmissionProcessor;

require dirname(__DIR__) . '/vendor/autoload.php';



function e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}



$studentNumber = 6; 
$variantNumber = 6;

$coefficientPercent = 100 + 5 * ($studentNumber % 5);

$historyLimit = 3 + ($studentNumber % 4);



session_start();

header(
    "X-Student-Variant: {$studentNumber}-{$variantNumber}"
);


$rates = [
    'study' => 8,
    'career' => 10,
    'project' => 14,
];

$allowedTypes = array_keys($rates);



$processor = new SubmissionProcessor(
    $rates,
    $coefficientPercent
);



$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$errors = [];

$old = [
    'clientName' => '',
    'duration' => '',
    'type' => '',
    'date' => '',
    'question' => '',
];



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


$history = $_SESSION['submissions'] ?? [];



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

require dirname(__DIR__) . '/views/form.php';