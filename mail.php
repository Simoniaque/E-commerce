<?php

require 'vendor/autoload.php';

use \Mailjet\Resources;

function SendAccountCreatedEmail($emailAddress, $name, $urlVerif)
{

    $subject = 'Bienvenue chez New Vet !';
    $textPart = 'Votre compte a bien été créé';
    $htmlPart = '<div style=\'font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; max-width: 600px; margin: 0 auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);\'>
                    <div style=\'background-color: #212529; color: #ffffff; padding: 10px; text-align: center; border-radius: 2px;\'>
                        <h1 style=\'margin: 0;\'>Bienvenue chez New Vet</h1>
                    </div>
                    <div style=\'margin: 20px 0; background-color: #ffffff; padding: 20px; border-radius: 2px;\'>
                        <p>Bonjour ' . $name . ',</p>
                        <p>Merci de vous être inscrit sur notre site web. Pour valider votre adresse mail, veuillez cliquer sur le lien suivant : <a href='.$urlVerif.'>' . $urlVerif . '</a></p>
                        <br/>
                        <p>Si vous avez des questions, n\'hésitez pas à nous contacter à l\'adresse mail suivante : '.MAILJET_SENDER_EMAIL.'.</p>
                        <p>Merci et à bientôt !</p>
                        <p>Cordialement,<br>L\'équipe de New Vet</p>
                    </div>
                    <div style=\'text-align: center; margin-top: 20px; color: #777777;\'>
                        <p>&copy; 2024 New Vet. Tous droits réservés.</p>
                    </div>
                </div>';
    return SendMail($emailAddress, $name, $subject, $textPart, $htmlPart);
}


function SendAccountDeleteEmail($emailAddress, $name)
{
    $subject = 'Compte supprimé !';
    $textPart = 'Votre compte a bien été supprimé';
    $htmlPart = '<div style=\'font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; max-width: 600px; margin: 0 auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);\'>
                    <div style=\'background-color: #212529; color: #ffffff; padding: 10px; text-align: center; border-radius: 2px;\'>
                        <h1 style=\'margin: 0;\'>A bientôt</h1>
                    </div>
                    <div style=\'margin: 20px 0; background-color: #ffffff; padding: 20px; border-radius: 2px;\'>
                        <p>Bonjour ' . $name . ',</p>
                        <p>Nous sommes désolés de vous voir partir.</p>
                        <p>Vous pouvez recréer un compte quand vous voudrez à l\'adresse suivante : ' . WEBSITE_URL . 'signup.php
                        <br/>
                        <p>Si vous avez des questions, n\'hésitez pas à nous contacter à l\'adresse mail suivante : '.MAILJET_SENDER_EMAIL.'. </p>
                        <p>Merci et à bientôt !</p>
                        <p>Cordialement,<br>L\'équipe de New Vet</p>
                    </div>
                    <div style=\'text-align: center; margin-top: 20px; color: #777777;\'>
                        <p>&copy; 2024 New Vet. Tous droits réservés.</p>
                    </div>
                </div>';
    return SendMail($emailAddress, $name, $subject, $textPart, $htmlPart);
}

function SendMail($emailAddress, $name, $subject, $textPart, $htmlPart)
{

    $mj = new \Mailjet\Client(MAILJET_API_KEY, MAIJET_API_SECRET_KEY, true, ['version' => 'v3.1']);
    $senderName = 'New Vet';

    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => MAILJET_SENDER_EMAIL,
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

function SendMailResetPassword($emailAddress, $name, $urlResetPassword)
{

    $subject = 'Réinitialisation de votre mot de passe';
    $textPart = 'Mot de passe oublié';
    $htmlPart = '<div style=\'font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; max-width: 600px; margin: 0 auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);\'>
                    <div style=\'background-color: #212529; color: #ffffff; padding: 10px; text-align: center; border-radius: 2px;\'>
                        <h1 style=\'margin: 0;\'>Réinitialisation mot de passe</h1>
                    </div>
                    <div style=\'margin: 20px 0; background-color: #ffffff; padding: 20px; border-radius: 2px;\'>
                        <p>Bonjour ' . $name . ',</p>
                        <p>Vous avez effectué une demande pour réinitialiser votre mot de passe.</p>
                        <p>Cliquez sur le lien suivant pour réinitialiser votre mot de passe : <a href=' . $urlResetPassword . '>' . $urlResetPassword . '</p>
                        <br/>
                        <p>Si vous avez des questions, n\'hésitez pas à nous contacter à l\'adresse mail suivante : '.MAILJET_SENDER_EMAIL.'.</p>
                        <p>Merci et à bientôt !</p>
                        <p>Cordialement,<br>L\'équipe de New Vet</p>
                    </div>
                    <div style=\'text-align: center; margin-top: 20px; color: #777777;\'>
                        <p>&copy; 2024 New Vet. Tous droits réservés.</p>
                    </div>
                </div>';
    return SendMail($emailAddress, $name, $subject, $textPart, $htmlPart);
}

function SendMailVerifyAccount($emailAddress, $name, $urlVerif)
{


    $subject = 'Vérification de votre adresse mail';
    $textPart = 'Vérification de votre adresse mail';
    $htmlPart = '<div style=\'font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; max-width: 600px; margin: 0 auto; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);\'>
                        <div style=\'background-color: #212529; color: #ffffff; padding: 10px; text-align: center; border-radius: 2px;\'>
                            <h1 style=\'margin: 0;\'>Vérification de votre adresse mail</h1>
                        </div>
                        <div style=\'margin: 20px 0; background-color: #ffffff; padding: 20px; border-radius: 2px;\'>
                            <p>Bonjour ' . $name . ',</p>
                            <p>Merci de vous être inscrit sur notre site web. Pour valider votre adresse mail, veuillez cliquer sur le lien suivant : <a href='.$urlVerif.'>' . $urlVerif . '</a></p>
                            <br/>
                            <p>Si vous avez des questions, n\'hésitez pas à nous contacter à l\'adresse mail suivante : '.MAILJET_SENDER_EMAIL.'</p>
                            <p>Merci et à bientôt !</p>
                            <p>Cordialement,<br>L\'équipe de New Vet</p>
                        </div>
                        <div style=\'text-align: center; margin-top: 20px; color: #777777;\'>
                            <p>&copy; 2024 New Vet. Tous droits réservés.</p>
                        </div>
                    </div>';
    return SendMail($emailAddress, $name, $subject, $textPart, $htmlPart);
}
