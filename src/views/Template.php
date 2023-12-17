<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="LAMP template project" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css" />
    <link rel="stylesheet" href="<?= ROOT ?>/styles/global.css" />

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🐞</text></svg>" />
    <?= $template_tags ?>
    <title><?= $template_title ?></title>
</head>

<body>
    <nav class="container-fluid">
        <ul>
            <li>
                <a href="<?= ROOT ?>/" class="contrast"><strong>lamp</strong></a>
            </li>
        </ul>
        <ul>
            <li><a href="<?= ROOT ?>/shop" class="secondary">Shop</a></li>
            <li><a href="<?= ROOT ?>/login" class="secondary">Login</a></li>

            <li>
                <details role="list" dir="rtl">
                    <summary aria-haspopup="listbox" role="link" class="secondary">Theme</summary>
                    <ul role="listbox">
                        <li><a href="#" data-theme-switcher="auto">Auto</a></li>
                        <li><a href="#" data-theme-switcher="light">Light</a></li>
                        <li><a href="#" data-theme-switcher="dark">Dark</a></li>
                    </ul>
                </details>
            </li>
        </ul>
    </nav>

    <?= $template_content ?>

    <footer class="container-fluid">
        <small>Built by <a href="https://github.com/creme332" class="secondary">creme332</a> •
            <a href="https://github.com/creme332/skeleton/" class="secondary">Source code</a></small>
    </footer>

    <!-- Minimal theme switcher -->
    <script src="js/minimal-theme-switcher.js"></script>
</body>

</html>