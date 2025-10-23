<?php

require 'conexion.php'; 

// 1) Obtener 'q' (mismo name que en tu formulario)
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// 2) Preparar y ejecutar la consulta segura (LIKE)
$results = [];
if ($q !== '') {
    $sql = "SELECT id_zapatilla, Marca, Precio, CantDisponibles FROM ZAPATILLAS WHERE Marca LIKE :busq";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['busq' => "%$q%"]);
    $results = $stmt->fetchAll();
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Resultados de búsqueda - <?= htmlspecialchars($q) ?></title>
  <link rel="stylesheet" href="estilos.css">
    <style>
    .cards-container { display:flex; gap:20px; justify-content:center; flex-wrap:wrap; padding:20px; }
    .card { position:relative; width:44%; height:245px; background:#f2f2f2; border-radius:10px; display:flex; align-items:center; justify-content:center; overflow:hidden; box-shadow:0 0 0 5px #ffffff80; margin:10px; }
    .card img { width:100%; height:auto; max-height:245px; object-fit:contain; }
    .card__content { padding:18px; text-align:left; width:100%; box-sizing:border-box; }
    .card__title { margin:0 0 8px; font-size:20px; font-weight:700; color:#333; }
    .card__description { color:#666; font-size:14px; margin:6px 0; }
    .card__price, .card__stock { font-weight:600; margin:6px 0; }
  </style>
</head>
<body>
  <header>
    <a href="index.html" style="color: inherit; text-decoration: none;"><h1>Mi E-commerce</h1></a>
    <nav>
      <form action="buscar.php" method="get">
        <input type="text" name="q" placeholder="Buscar..." value="<?= htmlspecialchars($q) ?>" required>
        <button type="submit">Buscar</button>
      </form>
      <a href="index.html">Volver</a>
    </nav>
  </header>

  <main>
    <h2>Resultados para: <em><?= htmlspecialchars($q) ?: '—' ?></em></h2>

    <?php if ($q === ''): ?>
      <p>Ingresá un término (por ejemplo: <strong>Nike</strong>, <strong>Adidas</strong>, <strong>Puma</strong>).</p>
    <?php else: ?>

      <?php if (count($results) === 0): ?>
        <p>No se encontraron resultados para <strong><?= htmlspecialchars($q) ?></strong>.</p>
      <?php else: ?>
        <div class="cards-container">
          <?php foreach ($results as $r): 
            // Normalizar campos
            $marca = htmlspecialchars($r['Marca']);
            // Formateo con separador local
            $precio = number_format($r['Precio'], 2, ',', '.');
            $stock = (int)$r['CantDisponibles'];

            $img = '';
            $lower = mb_strtolower($r['Marca'], 'UTF-8');
            if (strpos($lower, 'nike') !== false) $img = 'z-nike.png';
            elseif (strpos($lower, 'adidas') !== false) $img = 'z-adidas.png';
            elseif (strpos($lower, 'puma') !== false) $img = 'z-puma.png';
            else $img = 'placeholder.png';
          ?>
            <div class="card">
              <img src="<?= htmlspecialchars($img) ?>" alt="Zapatillas <?= $marca ?>">
              <div class="card__content">
                <p class="card__title"><?= $marca ?></p>
                <p class="card__description">Descripción breve de <?= $marca ?>.</p>
                <p class="card__price">Precio: $<?= $precio ?></p>
                <p class="card__stock">Stock: <?= $stock ?> unidades</p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    <?php endif; ?>
  </main>
</body>
</html>
