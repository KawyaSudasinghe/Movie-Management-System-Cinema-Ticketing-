<?php

// Use a default page title when a page does not provide one.
$pageTitle = $pageTitle ?? 'CineVault Movie Management System';

// Set the relative path from the current page back to the project root.
$rootPath = $rootPath ?? '';

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= e($pageTitle) ?></title>

    <link
        rel="stylesheet"
        href="<?= e($rootPath) ?>css/style.css"
    >

</head>

<body>

<header class="site-header">

    <div class="container nav-wrap">

        <a
            class="brand"
            href="<?= e($rootPath) ?>index.php"
        >
            <span class="brand-icon">▶</span>
            CineVault
        </a>

        <nav class="main-nav">

            <a href="<?= e($rootPath) ?>index.php">Movies</a>

            <a href="<?= e($rootPath) ?>admin/login.php">Admin</a>

        </nav>

    </div>

</header>

<main>
