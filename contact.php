<?php
session_start();
include_once("config.php");
include_once("functions.php");
include_once("mail.php");
include_once("API/usersRequests.php");


// Initialiser des variables pour les messages de statut
$contactMessage = '';
$contactSuccess = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $name = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $object = "Formulaire de contact";
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    // Vérifier que les champs requis sont remplis
    if ($name && $email && $subject && $message) {
        // Préparer les parties du message
        $textPart = "Nom : $name\nEmail : $email\nTéléphone : $phone\n\nMessage :\n$message";
        $htmlPart = "<p><strong>Nom :</strong> $name</p>
                     <p><strong>Email :</strong> $email</p>
                     <p><strong>Sujet :</strong> $subject</p>
                     <p><strong>Téléphone :</strong> $phone</p>
                     <p><strong>Message :</strong></p><p>$message</p>";

        // Adresse e-mail de l'expéditeur
        $toEmail = 'contact.newvet@gmail.com';  // L'adresse de contact de votre marque

        // Envoyer l'e-mail
        $emailSent = sendMail($toEmail, $name, $object, $textPart, $htmlPart);

        if ($emailSent) {
            $contactSuccess = 'Votre message a été envoyé avec succès !';

            //Envoie un mail à la personne pour dire que son message a bien été envoyé
            $subjectClient = 'Message envoyé avec succès';
            $textPartClient = "Merci de nous avoir contactés";
            $htmlPartClient = '<div style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; max-width: 600px; margin: 0 auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                    <div style="background-color: #212529; color: #ffffff; padding: 10px; text-align: center; border-radius: 2px;">
                        <h1 style="margin: 0;">Confirmation de Réception</h1>
                    </div>
                    <div style="margin: 20px 0; background-color: #ffffff; padding: 20px; border-radius: 2px;">
                        <p>Bonjour ' . $name . ',</p>
                        <p>Nous avons bien reçu votre message et nous vous remercions pour votre prise de contact.</p>
                        <p>Voici les détails que vous avez envoyés :</p>
                        <ul>
                            <li><strong>Nom :</strong> ' . $name . '</li>
                            <li><strong>Email :</strong> ' . $email . '</li>
                            <li><strong>Téléphone :</strong> ' . $phone . '</li>
                            <li><strong>Sujet :</strong> ' . $subject . '</li>
                            <li><strong>Message :</strong><br>' . nl2br($message) . '</li>
                        </ul>
                        <br/>
                        <p>Notre équipe examinera votre message et vous répondra dans les plus brefs délais.</p>
                        <p>Si vous avez des questions supplémentaires, n\'hésitez pas à nous contacter à l\'adresse suivante : <a href="mailto:contact.newvet@gmail.com" style="color: #007bff; text-decoration: none;">contact.newvet@gmail.com</a>.</p>
                        <p>Merci et à bientôt !</p>
                        <p>Cordialement,<br>L\'équipe de New Vet</p>
                    </div>
                    <div style="text-align: center; margin-top: 20px; color: #777777;">
                        <p>&copy; 2024 New Vet. Tous droits réservés.</p>
                    </div>
                </div>';
                
        $emailSent = sendMail($email, $name, $subjectClient, $textPartClient, $htmlPartClient);

        AddMessage($pdo, $name, $email, $phone, $subject, $message);

        } else {
            $contactMessage = 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer.';
        }
    } else {
        $contactMessage = 'Veuillez remplir tous les champs requis.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Contact</title>
    <link rel="icon" type="image/x-icon" href="assets/img/logo-black.png" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.4/utilities/bsb-overlay/bsb-overlay.css">
    <link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.4/utilities/background/background.css">
    <link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.4/utilities/margin/margin.css">
    <link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.4/utilities/padding/padding.css">

    <link rel="stylesheet" href="assets/css/style.css" />
</head>

<body>
    <main>
        <?php include "header.php"; ?>
        <!-- Contact 6 - Bootstrap Brain Component -->
        <section class="py-3 py-md-5 py-xl-8">
            <div class="container">
                <div class="row justify-content-md-center">
                    <div class="col-12 col-md-10 col-lg-8 col-xl-7 col-xxl-6">
                        <h2 class="mb-4 display-5 text-center">Contactez-nous</h2>
                        <p class="text-secondary mb-5 text-center lead fs-4">N'hésitez pas à nous contacter, notre équipe se fera un plaisir de vous répondre dans les plus brefs délais.</p>
                        <?php if ($contactMessage): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $contactMessage ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if ($contactSuccess): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= $contactSuccess ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <hr class="w-50 mx-auto mb-5 mb-xl-9 border-dark-subtle">
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="card border border-dark rounded shadow-sm overflow-hidden">
                            <div class="card-body p-0">
                                <div class="row gy-3 gy-md-4 gy-lg-0">
                                    <div class="col-12 col-lg-6 bsb-overlay background-position-center background-size-cover" style="--bsb-overlay-opacity: 0.8; ">
                                        <div class="row align-items-lg-center justify-content-center h-100">
                                            <div class="col-11 col-xl-10">
                                                <div class="contact-info-wrapper py-4 py-xl-5">
                                                    <h2 class="h1 mb-3 text-light">Nos informations</h2>
                                                    <div class="d-flex mb-4 mb-xxl-5">
                                                        <div class="me-4">
                                                            <h1><i class="bi bi-geo text-white"></i></h1>
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-3 text-light">Adresse</h4>
                                                            <address class="mb-0 text-light opacity-75">50 Rue de Limayrac, 31500 Toulouse, France</address>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4 mb-xxl-5">
                                                        <div class="col-12 col-xxl-6">
                                                            <div class="d-flex mb-4 mb-xxl-0">
                                                                <div class="me-4">
                                                                    <h1><i class="bi bi-telephone text-white"></i></h1>
                                                                </div>
                                                                <div>
                                                                    <h4 class="mb-3 text-light">Téléphone</h4>
                                                                    <p class="mb-0">
                                                                        <a class="link-light link-opacity-75 link-opacity-100-hover text-decoration-none" href="tel:+15057922430">+33 1 01 01 01 01</a>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-xxl-6">
                                                            <div class="d-flex mb-0">
                                                                <div class="me-4">
                                                                    <h1><i class="bi bi-envelope text-white"></i></h1>
                                                                </div>
                                                                <div>
                                                                    <h4 class="mb-3 text-light">Mail</h4>
                                                                    <p class="mb-0">
                                                                        <a class="link-light link-opacity-75 link-opacity-100-hover text-decoration-none" href="mailto:contact.newvet@gmail.com">contact.newvet@gmail.com</a>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex">
                                                        <div class="me-4">
                                                            <h1><i class="bi bi-clock text-white"></i></h1>
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-3 text-light">Horaire d'ouverture</h4>
                                                            <div class="d-flex mb-1">
                                                                <p class="text-light fw-bold mb-0 me-5">Lundi - Vendredi</p>
                                                                <p class="text-light opacity-75 mb-0">10h - 19h</p>
                                                            </div>
                                                            <div class="d-flex">
                                                                <p class="text-light fw-bold mb-0 me-5">Samedi - Dimanche</p>
                                                                <p class="text-light opacity-75 mb-0">9h - 18h</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <div class="row align-items-lg-center h-100">
                                            <div class="col-12">
                                                <form action="contact.php" method="POST">
                                                    <div class="row gy-4 gy-xl-5 p-4 p-xl-5">
                                                        <div class="col-12">
                                                            <label for="fullname" class="form-label">Nom Complet <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="fullname" name="fullname" value="" required>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label for="email" class="form-label">Adresse Mail <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <input type="email" class="form-control" id="email" name="email" value="" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label for="phone" class="form-label">Numéro de téléphone</label>
                                                            <div class="input-group">
                                                                <input type="tel" class="form-control" id="phone" name="phone" value="">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label for="subject" class="form-label">Sujet <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" id="subject" name="subject" value="" required>
                                                        </div>
                                                        <div class="col-12">
                                                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="d-grid">
                                                                <button class="btn btn-dark btn-lg" type="submit">Envoyer message</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="push"></div>
    </main>

    <?php include "footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
