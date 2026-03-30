<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de Confidentialité</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #0052FF;
            --bg: #F8FAFF;
            --white: #ffffff;
            --text-dark: #2D3748;
            --text-light: #718096;
            --border: #E2E8F0;
        }

        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; color: var(--text-dark); line-height: 1.6; }
        
        .blue-header { background: var(--primary); color: white; padding: 40px 20px 80px; text-align: center; }
        
        .container { max-width: 800px; margin: -50px auto 50px; padding: 0 20px; }

        .policy-card {
            background: var(--white); border-radius: 20px; padding: 40px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }

        h2 { color: var(--primary); border-bottom: 2px solid var(--bg); padding-bottom: 10px; margin-top: 30px; font-size: 20px; }
        h2 i { margin-right: 10px; }

        p { font-size: 15px; color: var(--text-dark); margin-bottom: 15px; }

        ul { padding-left: 20px; }
        ul li { margin-bottom: 10px; font-size: 14px; color: var(--text-dark); }

        .last-update { font-style: italic; color: var(--text-light); font-size: 13px; margin-bottom: 30px; }

        .btn-back {
            display: inline-block; margin-top: 20px; color: var(--primary);
            text-decoration: none; font-weight: 600; font-size: 14px;
        }
        .btn-back:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="blue-header">
    <h1>Confidentialité</h1>
    <p>Votre sécurité et la protection de vos données sont nos priorités</p>
</div>

<div class="container">
    <div class="policy-card">
        <p class="last-update">Dernière mise à jour : Mars 2026</p>

        <h2><i class="fas fa-user-shield"></i> 1. Collecte des données</h2>
        <p>Nous collectons les informations nécessaires pour assurer le bon fonctionnement de la plateforme :</p>
        <ul>
            <li><strong>Étudiants :</strong> Nom, email, téléphone, genre et établissement universitaire.</li>
            <li><strong>Propriétaires :</strong> Nom, email, téléphone, numéro de CIN et justificatifs d'identité.</li>
        </ul>

        <h2><i class="fas fa-eye"></i> 2. Utilisation des données</h2>
        <p>Vos données sont utilisées exclusivement pour :</p>
        <ul>
            <li>Mettre en relation les étudiants et les propriétaires.</li>
            <li>Vérifier l'authenticité des comptes propriétaires (Sécurité).</li>
            <li>Améliorer l'expérience utilisateur et le support technique.</li>
        </ul>

        <h2><i class="fas fa-lock"></i> 3. Protection et Sécurité</h2>
        <p>Toutes les données sensibles, notamment les mots de passe, sont cryptées à l'aide d'algorithmes de hachage sécurisés. L'accès aux documents d'identité (CIN) est strictement réservé à l'administrateur du site pour validation.</p>

        <h2><i class="fas fa-share-alt"></i> 4. Partage des informations</h2>
        <p>Nous ne vendons ni ne louons vos données personnelles à des tiers. Les informations de contact (téléphone) ne sont visibles que par les utilisateurs enregistrés souhaitant conclure une location.</p>

        <h2><i class="fas fa-user-check"></i> 5. Vos droits</h2>
        <p>Conformément à la loi, vous disposez d'un droit d'accès, de modification et de suppression de vos données personnelles depuis votre espace <strong>Profil</strong>. Vous pouvez également demander la fermeture de votre compte à tout moment.</p>

        <hr style="border: 0; border-top: 1px solid var(--border); margin: 30px 0;">
        
        <p style="text-align: center; font-size: 13px; color: var(--text-light);">
            Pour toute question concernant notre politique de confidentialité, contactez-nous via la page <a href="support.php" style="color: var(--primary);">Support</a>.
        </p>

        <a href="javascript:history.back()" class="btn-back"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
</div>

</body>
</html>