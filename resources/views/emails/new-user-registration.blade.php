<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nouvelle inscription en attente</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2D6A4F; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; margin: 20px 0; }
        .button { display: inline-block; background: #2D6A4F; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nouvelle inscription en attente</h1>
        </div>
        
        <div class="content">
            <p>Bonjour,</p>
            
            <p>Un nouvel utilisateur s'est inscrit sur la <strong>Plateforme Agroécologique 3AO</strong> et est en attente de validation.</p>
            
            <h3>Informations de l'utilisateur :</h3>
            <ul>
                <li><strong>Nom :</strong> {{ $user->name }}</li>
                <li><strong>Email :</strong> {{ $user->email }}</li>
                @if($user->organization)
                    <li><strong>Organisation :</strong> {{ $user->organization }}</li>
                @endif
                @if($user->registration_reason)
                    <li><strong>Motif de la demande :</strong> {{ $user->registration_reason }}</li>
                @endif
                <li><strong>Date d'inscription :</strong> {{ $user->created_at->format('d/m/Y à H:i') }}</li>
            </ul>
            
            <p>Connectez-vous au panneau d'administration pour approuver ou refuser cette inscription.</p>
            
            <center>
                <a href="{{ route('admin.users.pending') }}" class="button">Voir les inscriptions en attente</a>
            </center>
        </div>
        
        <div class="footer">
            <p>Cet email a été envoyé automatiquement par la Plateforme Agroécologique 3AO.</p>
        </div>
    </div>
</body>
</html>
