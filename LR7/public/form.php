<?php
require '../.core/modules.php';
require '../templates/header.php';

// Получаем список типов товаров
$types = getOptionGroups($pdo); // предполагается, что функция getOptionGroups теперь работает с types
?>

<div class="container mt-5 mb-5">
    <div class="container">
        <div class="row">
            <p class="col py-2">
                <a href="/" class="text-decoration-none">Домашняя страница</a>
                > <a href="products.php" class="text-decoration-none">Список товаров</a>
                > <b>Добавление товара</b>
            </p>
        </div>
    </div>
    <h1>Добавление товара</h1>
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <div class="form-group mb-3">
            <label for="name">Название товара*</label>
            <input type="text" name="name" class="form-control" placeholder="Введите название товара" required>
        </div>
        <div class="form-group mb-3">
            <label for="id_type">Тип товара*</label>
            <select name="id_type" class="form-control" required>
                <option value="" selected>Выберите тип товара</option>
                <?php foreach ($types as $type): ?>
                    <option value="<?= htmlspecialchars($type['id']) ?>"><?= htmlspecialchars($type['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group mb-3">
            <label for="description">Описание</label>
            <textarea name="description" class="form-control" placeholder="Введите описание" rows="3"></textarea>
        </div>
        <div class="form-group mb-3">
            <label for="cost">Цена*</label>
            <input type="number" name="cost" class="form-control" placeholder="Введите цену" required>
        </div>
        <div class="form-group mb-4">
            <label for="img">Фото товара</label>
            <input type="file" name="img" class="form-control" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary w-100">Добавить</button>
    </form>
</div>

<?php require '../templates/footer.php'; ?>

