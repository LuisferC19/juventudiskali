<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($titulo_pagina ?? APP_NAME) ?> — <?= APP_NAME ?></title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

<!-- CSS principal del sistema -->
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/iskalli.css">
</head>
<body<?= !empty($body_class) ? ' class="'.htmlspecialchars($body_class).'"' : '' ?>>