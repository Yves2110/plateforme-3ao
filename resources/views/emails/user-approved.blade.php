<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Votre inscription a été approuvée</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2D6A4F; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; margin: 20px 0; }
        .credentials { background: #e8f5e9; padding: 15px; border-left: 4px solid #2D6A4F; margin: 20px 0; }
        .button { display: inline-block; background: #2D6A4F; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; }
        .warning { background: #fff3cd; padding: 10px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenue sur la Plateforme 3AO !</h1>
        </div>
        
        <div class="content">
            <p>Bonjour {{ $user->name }},</p>
            
            <p>Nous avons le plaisir de vous informer que votre inscription sur la <strong>Plateforme Agroécologique 3AO</strong> a été <strong>approuvée</strong> par notre équipe.</p>
            
            <p>Vous pouvez maintenant accéder à tous les contenus et fonctionnalités de la plateforme.</p>
            
            <div class="credentials">
                <h3>Vos identifiants de connexion :</h3>
                <p><strong>Email :</strong> {{ $user->email }}</p>
                <p><strong>Mot de passe :</strong> {{ $password }}</p>
            </div>
            
            <div class="warning">
                <strong>Important :</strong> Pour des raisons de sécurité, nous vous recommandons de changer votre mot de passe dès votre première connexion.
            </div>
            
            <center>
                <a href="{{ route('login') }}" class="button">Se connecter</a>
            </center>
            
            <p>Si vous rencontrez des difficultés pour vous connecter, n'hésitez pas à contacter notre équipe d'assistance.</p>
        </div>
        
        <div class="footer">
            <p>Cordialement,<br>L'équipe de la Plateforme Agroécologique 3AO</p>
            <p style="font-size: 11px; color: #999;">Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>
