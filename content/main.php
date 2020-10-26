<!DOCTYPE html>

<html>
    <head>
        <!-- Meta -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

        <!-- Title -->
        <title>Huella UPM</title>
        <meta name="apple-mobile-web-app-title" content="Huella UPM">
        
        <!-- Theme Colors -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="theme-color" content="#5f4da7">
        <meta name="msapplication-navbutton-color" content="#5f4da7">
        <meta name="apple-mobile-web-app-status-bar-style" content="#5f4da7">
        
        <!-- Icons -->
        <link rel="shortcut icon" href="/img/favicon.png">
        <link rel="icon" type="image/png" href="/img/favicon.png">
        <link rel="apple-touch-icon-precomposed" href="/img/favicon.png">

        <!-- Styles -->
        <link rel="stylesheet" href="/css/skeuos.css">
        <link rel="stylesheet" href="/content/css/main.css">

        <!-- Scripts -->
        <script src="/content/js/main.js"></script>
    </head>
    <body>
        <main>
            <{ content }>
        </main>
        <div id="loading-animation">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
        <nav>
            <{ link-template }>
                <div style="--icon: url(/content/img/<{ link-text }>.svg)"></div>
            <{ /link-template }>
        </nav>
    </body>
</html>
