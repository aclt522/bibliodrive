    <?php
    session_start();
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Bienvenue dans la Bibliodrive</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>

    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Contenu principal -->
    <div class="container-fluid mt-4">
        <div class="row justify-content-center">

            <!-- Carousel centré -->
            <main class="col-lg-9 col-12 mb-3">
                <div class="d-flex justify-content-center">
                    <?php include 'Carousel.php'; ?>
                </div>
            </main>

        </div> <!-- fin row -->
    </div> <!-- fin container-fluid -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php include 'cookies_RGPD.php'; ?>

    </body>
    </html>
