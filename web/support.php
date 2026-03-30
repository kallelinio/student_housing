<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aide & Support</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #0052FF;
            --bg: #F8FAFF;
            --white: #ffffff;
            --text-dark: #1A202C;
            --border: #E2E8F0;
        }

        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; color: var(--text-dark); padding-bottom: 50px; }
        
        .blue-header { background: var(--primary); color: white; padding: 40px 20px 80px; text-align: center; }
        
        .container { max-width: 700px; margin: -50px auto 0; padding: 0 15px; }

        .support-card {
            background: var(--white); border-radius: 20px; padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); margin-bottom: 30px;
        }

        h3 { margin-top: 0; color: var(--primary); display: flex; align-items: center; gap: 10px; }

        /* ستايل الأسئلة المتكررة FAQ */
        .faq-item { border-bottom: 1px solid var(--border); padding: 15px 0; cursor: pointer; }
        .faq-question { display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 15px; }
        .faq-answer { display: none; padding-top: 10px; font-size: 14px; color: #4A5568; line-height: 1.6; }
        .faq-item.active .faq-answer { display: block; }
        .faq-item.active i { transform: rotate(180deg); color: var(--primary); }

        /* فورم الاتصال */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px; }
        .form-group input, .form-group textarea {
            width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 10px;
            font-size: 14px; background: #FBFCFE; box-sizing: border-box;
        }
        textarea { height: 100px; resize: none; }

        .btn-send {
            width: 100%; padding: 14px; background: var(--primary); color: white;
            border: none; border-radius: 12px; font-size: 15px; font-weight: 700;
            cursor: pointer; transition: 0.3s;
        }
        .btn-send:hover { opacity: 0.9; }

        .contact-info { display: flex; gap: 20px; margin-top: 20px; font-size: 14px; color: #4A5568; }
        .contact-info i { color: var(--primary); }
    </style>
</head>
<body>

<div class="blue-header">
    <h1>Centre d'Aide</h1>
    <p>Comment pouvons-nous vous aider aujourd'hui ?</p>
</div>

<div class="container">
    <div class="support-card">
        <h3><i class="fas fa-question-circle"></i> Questions Fréquentes</h3>
        
        <div class="faq-item" onclick="toggleFaq(this)">
            <div class="faq-question">Comment réserver un logement ? <i class="fas fa-chevron-down"></i></div>
            <div class="faq-answer">Une fois que vous avez trouvé une annonce qui vous plaît, vous pouvez voir le numéro du propriétaire et le contacter directement pour organiser une visite.</div>
        </div>

        <div class="faq-item" onclick="toggleFaq(this)">
            <div class="faq-question">Pourquoi mon annonce est-elle "En attente" ? <i class="fas fa-chevron-down"></i></div>
            <div class="faq-answer">Toutes les annonces sont vérifiées par notre équipe d'administration pour garantir la sécurité des étudiants. Cela prend généralement moins de 24h.</div>
        </div>

        <div class="faq-item" onclick="toggleFaq(this)">
            <div class="faq-question">Mon compte propriétaire n'est pas encore vérifié ? <i class="fas fa-chevron-down"></i></div>
            <div class="faq-answer">L'administrateur doit vérifier votre CIN. Assurez-vous d'avoir téléchargé une photo claire de votre carte d'identité dans votre profil.</div>
        </div>
    </div>

    <div class="support-card">
        <h3><i class="fas fa-envelope"></i> Contactez-nous</h3>
        <form action="send_support.php" method="POST">
            <div class="form-group">
                <label>Sujet</label>
                <input type="text" name="sujet" placeholder="Ex: Problème de connexion" required>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea name="message" placeholder="Décrivez votre problème en détail..." required></textarea>
            </div>
            <button type="submit" class="btn-send">Envoyer le message</button>
        </form>

        <div class="contact-info">
            <span><i class="fas fa-phone-alt"></i> +216 71 000 000</span>
            <span><i class="fas fa-at"></i> support@logement.tn</span>
        </div>
    </div>
</div>

<script>
function toggleFaq(element) {
    element.classList.toggle('active');
}
</script>

</body>
</html>