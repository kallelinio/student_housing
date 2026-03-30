<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Plateforme Logement</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #0052FF;
            --secondary: #28a745;
            --bg: #F8FAFF;
            --white: #ffffff;
            --text-dark: #1A202C;
            --text-muted: #718096;
            --border: #E2E8F0;
        }

        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; padding-bottom: 50px; color: var(--text-dark); }

        /* الاختيارات الفوقانية */
        .choice-container {
            display: flex; justify-content: center; gap: 30px; padding: 50px 20px; flex-wrap: wrap;
        }
        .role-card {
            background: var(--white); padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            width: 300px; text-align: center; transition: 0.3s; border: 2px solid transparent; cursor: pointer;
        }
        .role-card:hover { transform: translateY(-10px); border-color: var(--primary); }
        .role-card i { font-size: 40px; color: var(--primary); margin-bottom: 15px; }
        .role-card h2 { font-size: 18px; margin: 10px 0; }
        .role-card p { font-size: 13px; color: var(--text-muted); line-height: 1.6; }

        /* الفورم */
        .form-wrapper {
            max-width: 500px; margin: 0 auto; background: var(--white); padding: 40px;
            border-radius: 25px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .hidden { display: none; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 7px; }
        .form-group input, .form-group select {
            width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 12px;
            font-size: 14px; background: #FBFCFE; box-sizing: border-box;
        }

        /* خانات خاصة */
        .special-fields {
            background: #F0F4FF; padding: 20px; border-radius: 15px; margin: 20px 0;
            border: 1px dashed var(--primary);
        }

        .btn-submit {
            width: 100%; padding: 16px; background: var(--secondary); color: white;
            border: none; border-radius: 12px; font-size: 16px; font-weight: 700;
            cursor: pointer; transition: 0.3s; margin-top: 10px;
        }
        .btn-submit:hover { opacity: 0.9; transform: scale(1.01); }
    </style>
</head>
<body>

<div class="choice-container" id="selection-area">
    <div class="role-card" onclick="showForm('Etudiant')">
        <i class="fas fa-user-graduate"></i>
        <h2>Je suis Étudiant</h2>
        <p>Je cherche un logement proche de ma faculté.</p>
    </div>

    <div class="role-card" onclick="showForm('Proprietaire')">
        <i class="fas fa-home"></i>
        <h2>Je suis Propriétaire</h2>
        <p>Je souhaite proposer mes logements aux étudiants.</p>
    </div>
</div>

<div id="registerForm" class="form-wrapper hidden">
    <h2 style="text-align:center; margin-bottom:25px;">Créer votre compte</h2>
    <form action="signup_process.php" method="POST" enctype="multipart/form-data">
        
        <input type="hidden" name="type_compte" id="type_compte">

        <div class="form-group">
            <label>Nom complet</label>
            <input type="text" name="nom" placeholder="Ahmed Ben Ali" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="nom@email.com" required>
        </div>

        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="mot_de_passe" required>
        </div>

        <div class="form-group">
            <label>Téléphone</label>
            <input type="text" name="telephone" placeholder="216 -- --- ---" required>
        </div>

        <div class="form-group">
            <label>Genre</label>
            <select name="genre" required>
                <option value="Homme">Homme</option>
                <option value="Femme">Femme</option>
            </select>
        </div>

        <div id="etudiant_fields" class="special-fields hidden">
            <div class="form-group">
                <label>Votre Faculté</label>
                <input type="text" name="faculte" placeholder="Ex: FST, IHEC...">
            </div>
        </div>

        <div id="proprietaire_fields" class="special-fields hidden">
            <div class="form-group">
                <label>Numéro CIN</label>
                <input type="text" name="cin_number" placeholder="8 chiffres">
            </div>
            <div class="form-group">
                <label>Photo CIN (Recto/Verso)</label>
                <input type="file" name="cin_image">
            </div>
        </div>

        <button type="submit" class="btn-submit">S'inscrire maintenant</button>
        <p style="text-align:center; font-size:12px; margin-top:15px;">
            Déjà inscrit? <a href="login.php" style="color:var(--primary);">Se connecter</a>
        </p>
    </form>
</div>

<script>
function showForm(role) {
    document.getElementById('selection-area').style.display = 'none';
    const form = document.getElementById('registerForm');
    const typeInput = document.getElementById('type_compte');
    
    typeInput.value = role;
    form.classList.remove('hidden');

    if(role === 'Etudiant') {
        document.getElementById('etudiant_fields').classList.remove('hidden');
        document.getElementById('etudiant_fields').querySelector('input').required = true;
    } else {
        document.getElementById('proprietaire_fields').classList.remove('hidden');
        document.getElementById('proprietaire_fields').querySelector('input').required = true;
    }
}
</script>

</body>
</html>