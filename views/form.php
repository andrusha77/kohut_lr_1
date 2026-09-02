<form method="post" action="/">
  <label>
    Назва
    <input name="title" value="<?= e($old['title']) ?>">
  </label>
  <?php if (isset($errors['title'])): ?>
    <p class="error"><?= e($errors['title']) ?></p>
  <?php endif; ?>

  <!-- TODO: quantity, category, date, comment -->
  <button type="submit">Зберегти заявку</button>
</form>
