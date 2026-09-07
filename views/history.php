<section class="history">

    <h2>Історія бронювань</h2>


=
    <form method="get" action="/" class="filter">

        <label for="filterType">
            Фільтр за типом:
        </label>

        <select id="filterType" name="type">

            <option value="">
                Усі
            </option>

            <option
                value="study"
                <?= $typeFilter === 'study'
                    ? 'selected'
                    : '' ?>
            >
                Навчання
            </option>

            <option
                value="career"
                <?= $typeFilter === 'career'
                    ? 'selected'
                    : '' ?>
            >
                Кар'єра
            </option>

            <option
                value="project"
                <?= $typeFilter === 'project'
                    ? 'selected'
                    : '' ?>
            >
                Проєкт
            </option>

        </select>

        <button type="submit">
            Фільтрувати
        </button>

        <a href="/">
            Показати всі
        </a>

    </form>


    <?php if ($history === []): ?>

        <p>
            Історія порожня.
        </p>

    <?php else: ?>

        <div class="table-wrapper">

            <table>

                <thead>

                <tr>
                    <th>Клієнт</th>
                    <th>Тривалість</th>
                    <th>Тип</th>
                    <th>Дата</th>
                    <th>Запитання</th>
                    <th>Вартість</th>
                </tr>

                </thead>

                <tbody>

                <?php foreach ($history as $row): ?>

                    <tr>

                        <td>
                            <?= e($row['clientName']) ?>
                        </td>

                        <td>
                            <?= e($row['duration']) ?>
                            хв
                        </td>

                        <td>
                            <?= e($row['type']) ?>
                        </td>

                        <td>
                            <?= e($row['date']) ?>
                        </td>

                        <td>
                            <?= e($row['question']) ?>
                        </td>

                        <td>
                            <?= e(
                                number_format(
                                    (float) $row['cost'],
                                    2,
                                    '.',
                                    ''
                                )
                            ) ?>
                            грн
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</section>