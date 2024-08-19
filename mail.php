<?php

require 'vendor/autoload.php';

use \Mailjet\Resources;

function sendAccountCreatedEmail($emailAddress, $name)
{
    $url = 'localhost/E-Commerce/verifyaccount.php';

    $subject = 'Bienvenue chez New Vet !';
    $textPart = 'Votre compte a bien été créé';
    $htmlPart = '<div style=\'font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; max-width: 600px; margin: 0 auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);\'>
                    <div style=\'background-color: #212529; color: #ffffff; padding: 10px; text-align: center; border-radius: 2px;\'>
                        <h1 style=\'margin: 0;\'>Bienvenue chez New Vet</h1>
                    </div>
                    <div style=\'margin: 20px 0; background-color: #ffffff; padding: 20px; border-radius: 2px;\'>
                        <p>Bonjour '.$name.',</p>
                        <p>Merci de vous être inscrit sur notre site web. Pour valider votre adresse mail, veuillez cliquer sur le lien suivant : '.$url.'</p>
                        <br/>
                        <p>Si vous avez des questions, n\'hésitez pas à nous contacter à l\'adresse mail suivante : contact.newvet@gmail.com.</p>
                        <p>Merci et à bientôt !</p>
                        <p>Cordialement,<br>L\'équipe de New Vet</p>
                    </div>
                    <div style=\'text-align: center; margin-top: 20px; color: #777777;\'>
                        <p>&copy; 2024 New Vet. Tous droits réservés.</p>
                    </div>
                </div>';
    return sendMail($emailAddress, $name, $subject, $textPart, $htmlPart);
}


function sendAccountDeleteEmail($emailAddress, $name)
{
    $subject = 'Compte supprimé !';
    $textPart = 'Votre compte a bien été supprimé';
    $htmlPart = '<div style=\'font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; max-width: 600px; margin: 0 auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);\'>
                    <div style=\'background-color: #212529; color: #ffffff; padding: 10px; text-align: center; border-radius: 2px;\'>
                        <h1 style=\'margin: 0;\'>A bientôt</h1>
                    </div>
                    <div style=\'margin: 20px 0; background-color: #ffffff; padding: 20px; border-radius: 2px;\'>
                        <p>Bonjour '.$name.',</p>
                        <p>Nous sommes désolés de vous voir partir.</p>
                        <p>Vous pouvez recréer un compte quand vous voudrez à l\'adresse suivante : localhost/E-Commerce/singup.php
                        <br/>
                        <p>Si vous avez des questions, n\'hésitez pas à nous contacter à l\'adresse mail suivante : contact.newvet@gmail.com.</p>
                        <p>Merci et à bientôt !</p>
                        <p>Cordialement,<br>L\'équipe de New Vet</p>
                    </div>
                    <div style=\'text-align: center; margin-top: 20px; color: #777777;\'>
                        <p>&copy; 2024 New Vet. Tous droits réservés.</p>
                    </div>
                </div>';
    return sendMail($emailAddress, $name, $subject, $textPart, $htmlPart);
}

function sendMail($emailAddress, $name, $subject, $textPart, $htmlPart)
{
    $apikey = '0bc323f63d691610b12559e414c49398';
    $apisecret = '4b5e4da435d031796507a324c034a7cd';

    $mj = new \Mailjet\Client($apikey, $apisecret, true, ['version' => 'v3.1']);
    $sender = 'contact.newvet@gmail.com';
    $senderName = 'New Vet';

    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => "$sender",
                    'Name' => "$senderName"
                ],
                'To' => [
                    [
                        'Email' => "$emailAddress",
                        'Name' => "$name"
                    ]
                ],
                'Subject' => "$subject",
                'TextPart' => "$textPart",
                'HTMLPart' => "$htmlPart"
            ]
        ]
    ];


    $response = $mj->post(Resources::$Email, ['body' => $body]);

    if ($response->success()) {
        return true;
    } else {
        return false;
    }
}

function sendMailResetPassword($emailAddress, $name){

    $url = 'localhost/E-Commerce/singup.php';

    $subject = 'Réinitialisation de votre mot de passe';
    $textPart = 'Mot de passe oublié';
    $htmlPart = '<div style=\'font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; max-width: 600px; margin: 0 auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);\'>
                    <div style=\'background-color: #212529; color: #ffffff; padding: 10px; text-align: center; border-radius: 2px;\'>
                        <h1 style=\'margin: 0;\'>Réinitialisation mot de passe</h1>
                    </div>
                    <div style=\'margin: 20px 0; background-color: #ffffff; padding: 20px; border-radius: 2px;\'>
                        <p>Bonjour '.$name.',</p>
                        <p>Vous avez effectué une demande pour réinitialiser votre mot de passe.</p>
                        <p>Cliquez sur le lien suivant pour réinitialiser votre mot de passe : '.$url.'</p>
                        <br/>
                        <p>Si vous avez des questions, n\'hésitez pas à nous contacter à l\'adresse mail suivante : contact.newvet@gmail.com.</p>
                        <p>Merci et à bientôt !</p>
                        <p>Cordialement,<br>L\'équipe de New Vet</p>
                    </div>
                    <div style=\'text-align: center; margin-top: 20px; color: #777777;\'>
                        <p>&copy; 2024 New Vet. Tous droits réservés.</p>
                    </div>
                </div>';
    return sendMail($emailAddress, $name, $subject, $textPart, $htmlPart);
}

function sendMailVerifyAccount($emailAddress, $name){
    
        $url = 'localhost/E-Commerce/verifyaccount.php';
    
        $subject = 'Vérification de votre adresse mail';
        $textPart = 'Vérification de votre adresse mail';
        $htmlPart = '<div style=\'font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; max-width: 600px; margin: 0 auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);\'>
                        <div style=\'background-color: #212529; color: #ffffff; padding: 10px; text-align: center; border-radius: 2px;\'>
                            <h1 style=\'margin: 0;\'>Vérification de votre adresse mail</h1>
                        </div>
                        <div style=\'margin: 20px 0; background-color: #ffffff; padding: 20px; border-radius: 2px;\'>
                            <p>Bonjour '.$name.',</p>
                            <p>Merci de vous être inscrit sur notre site web. Pour valider votre adresse mail, veuillez cliquer sur le lien suivant : '.$url.'</p>
                            <br/>
                            <p>Si vous avez des questions, n\'hésitez pas à nous contacter à l\'adresse mail suivante : contact.newvet@gmail.com.</p>
                            <p>Merci et à bientôt !</p>
                            <p>Cordialement,<br>L\'équipe de New Vet</p>
                        </div>
                        <div style=\'text-align: center; margin-top: 20px; color: #777777;\'>
                            <p>&copy; 2024 New Vet. Tous droits réservés.</p>
                        </div>
                    </div>';
        return sendMail($emailAddress, $name, $subject, $textPart, $htmlPart);
}
