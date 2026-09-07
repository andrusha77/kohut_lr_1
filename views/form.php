<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Бронювання консультації</title>

    <link rel="stylesheet" href="/style.css">
</head>

<body>

<main class="container">

    <h1>Бронювання консультації</h1>

    <p>
        Варіант: 6
    </p>

    <p>
        K:
        <?= e(number_format($coefficientPercent / 100, 2)) ?>
    </p>

    <p>
        Максимальна кількість записів історії:
        <?= e($historyLimit) ?>
    </p>

    <?php if (isset($_GET['created'])): ?>

        <div class="success">
            Консультацію успішно заброньовано.
        </div>

    <?php endif; ?>


    <form method="post" action="/">

        <!-- ІМ'Я -->

        <div class="form-group">

            <label for="clientName">
                Ім’я клієнта
            </label>

            <input
                id="clientName"
                type="text"
                name="clientName"
                value="<?= e($old['clientName']) ?>"
                required
            >

            <?php if (isset($errors['clientName'])): ?>

                <p class="error">
                    <?= e($errors['clientName']) ?>
                </p>

            <?php endif; ?>

        </div>


        <!-- ТРИВАЛІСТЬ -->

        <div class="form-group">

            <label for="duration">
                Тривалість, хв
            </label>

            <input
                id="duration"
                type="number"
                name="duration"
                min="30"
                max="180"
                step="15"
                value="<?= e($old['duration']) ?>"
                required
            >

            <small>
                Від 30 до 180 хвилин, крок 15.
            </small>

            <?php if (isset($errors['duration'])): ?>

                <p class="error">
                    <?= e($errors['duration']) ?>
                </p>

            <?php endif; ?>

        </div>


        <!-- ТИП -->

        <div class="form-group">

            <label for="type">
                Тип консультації
            </label>

            <select
                id="type"
                name="type"
                required
            >

                <option value="">
                    Оберіть тип
                </option>

                <option
                    value="study"
                    <?= $old['type'] === 'study'
                        ? 'selected'
                        : '' ?>
                >
                    Навчання — 8 грн/хв
                </option>

                <option
                    value="career"
                    <?= $old['type'] === 'career'
                        ? 'selected'
                        : '' ?>
                >
                    Кар'єра — 10 грн/хв
                </option>

                <option
                    value="project"
                    <?= $old['type'] === 'project'
                        ? 'selected'
                        : '' ?>
                >
                    Проєкт — 14 грн/хв
                </option>

            </select>

            <?php if (isset($errors['type'])): ?>

                <p class="error">
                    <?= e($errors['type']) ?>
                </p>

            <?php endif; ?>

        </div>


        <!-- ДАТА -->

        <div class="form-group">

            <label for="date">
                Дата консультації
            </label>

            <input
                id="date"
                type="date"
                name="date"
                value="<?= e($old['date']) ?>"
                required
            >

            <?php if (isset($errors['date'])): ?>

                <p class="error">
                    <?= e($errors['date']) ?>
                </p>

            <?php endif; ?>

        </div>


        <!-- ЗАПИТАННЯ -->

        <div class="form-group">

            <label for="question">
                Запитання
            </label>

            <textarea
                id="question"
                name="question"
                rows="5"
                required
            ><?= e($old['question']) ?></textarea>

            <small>
                Не менше 10 символів.
            </small>

            <?php if (isset($errors['question'])): ?>

                <p class="error">
                    <?= e($errors['question']) ?>
                </p>

            <?php endif; ?>

        </div>


        <button type="submit">
            Забронювати консультацію
        </button>

    </form>


    <?php

    require __DIR__ . '/history.php';

    ?>

</main>

</body>

</html>