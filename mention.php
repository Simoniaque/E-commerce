<?php
session_start();

include("config.php");
include("functions.php");

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>New Vet</title>
        <link rel="icon" type="image/x-icon" href="assets/img/logo-black.png" />
    
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
        <link rel="stylesheet" href="assets/css/style.css" />
    
        <script src="assets/js/addToCart.js"></script>
    </head>
<body>
<?php include "header.php"; ?>

   
    <main style="padding: 40px; padding-bottom: 100px;" >

    <h2>Mentions Légales</h2>
        
            <h3>1. Éditeur du site</h3>
            <p>Le site Newvet est édité par Newvet, [Forme juridique] au capital de [Montant du capital], immatriculée au Registre du Commerce et des Sociétés de Toulouse sous le numéro [Numéro d'immatriculation].</p>
            <p><strong>Siège social :</strong> 50 Rue de Limayrac, 31500 Toulouse, France</p>
            <p><strong>Téléphone :</strong> +33 1 01 01 01 01</p>
            <p><strong>Email :</strong> <?php echo MAILJET_SENDER_EMAIL;?></p>
        

        
            <h3>2. Directeur de la publication</h3>
            <p>Le directeur de la publication est [Nom du directeur de la publication].</p>
        

        
            <h3>3. Hébergement</h3>
            <p>Le site est hébergé par alwaysdata, dont le siège social est situé à 91 Rue du Faubourg Saint-Honoré, 75008 Paris.</p>
        

        
            <h3>4. Propriété intellectuelle</h3>
            <p>L'ensemble des contenus (textes, images, logos) présents sur ce site est protégé par les lois en vigueur sur la propriété intellectuelle et est la propriété de Newvet.</p>
        

        
            <h3>5. Données personnelles</h3>
            <p>Les données collectées sur ce site sont traitées conformément à notre <a href="politique-de-confidentialite.html">Politique de Confidentialité</a>. Vous disposez d'un droit d'accès, de rectification, et de suppression de vos données personnelles.</p>
        

        
            <h3>6. Cookies</h3>
            <p>Ce site utilise des cookies pour améliorer l'expérience utilisateur. Vous pouvez gérer vos préférences en matière de cookies via les paramètres de votre navigateur.</p>
        

        
            <h3>7. Litiges</h3>
            <p>En cas de litige, et après échec de toute tentative de résolution amiable, les tribunaux de Toulouse seront seuls compétents pour connaître du litige.</p>
        
    </main>
    <?php include "footer.php"; ?>
</body>
</html>
