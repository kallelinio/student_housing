<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publier un logement</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #0052FF; --bg: #F8FAFF; --white: #ffffff; --border: #E2E8F0; --text: #333; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: var(--bg); margin: 0; padding-bottom: 50px; }
        
        .blue-header { background: var(--primary); color: white; padding: 30px 20px 60px; text-align: center; }
        .blue-header h1 { margin: 0; font-size: 22px; }

        .container { max-width: 550px; margin: -40px auto 0; padding: 0 15px; }
        .white-card { background: var(--white); border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }

        label { display: block; margin-top: 15px; font-weight: 600; font-size: 14px; color: var(--text); }
        input, select, textarea { 
            width: 100%; padding: 12px; margin-top: 6px; border: 1px solid var(--border); 
            border-radius: 10px; font-size: 14px; box-sizing: border-box; background: #FBFCFE;
        }
        textarea { resize: none; height: 80px; }

        /* Grid للأشياء الصغيرة */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

        /* ستايل الـ Checkboxes */
        .amenities-grid { 
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px; 
            background: #F0F4FF; padding: 15px; border-radius: 12px; margin-top: 10px;
        }
        .check-item { display: flex; align-items: center; gap: 8px; font-size: 13px; }
        .check-item input { width: auto; margin: 0; }

        .btn-submit { 
            width: 100%; padding: 15px; margin-top: 25px; border: none; 
            background: var(--primary); color: white; font-size: 16px; 
            font-weight: 700; border-radius: 12px; cursor: pointer; transition: 0.3s;
        }
        .btn-submit:hover { opacity: 0.9; transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="blue-header">
    <h1>Publier une annonce</h1>
    <p>Attirez les meilleurs étudiants vers votre logement</p>
</div>

<div class="container">
    <div class="white-card">
        <form action="proprietaire_add_annonce.php" method="POST" enctype="multipart/form-data">
            
            <label><i class="fas fa-heading"></i> Titre de l'annonce</label>
            <input type="text" name="titre" placeholder="Ex: Studio S+1 Meublé à l'Ariana" required>

            <label><i class="fas fa-align-left"></i> Description</label>
            <textarea name="description" placeholder="Détails (étage, voisinage, conditions...)" required></textarea>

            <div class="form-grid">
                <div>
                    <label>Prix (DT/mois)</label>
                    <input type="number" name="prix" min="0" required>
                </div>
                <div>
                    <label>Caution (Garantie)</label>
                    <input type="number" name="caution" placeholder="0">
                </div>
            </div>

            <div class="form-grid">
                <div>
                    <label>Type logement</label>
                    <select name="type_logement">
                        <option>Studio</option>
                        <option>Appartement</option>
                        <option>Chambre</option>
                        <option>Foyer</option>
                    </select>
                </div>
                <div>
                    <label>Nombre chambres</label>
                    <input type="number" name="nb_chambres" value="1">
                </div>
            </div>

            <label>Localisation (Ville / Quartier)</label>
            <input type="text" name="localisation" placeholder="Cité El Ghazala, Tunis" required>

            <label>Distance de la faculté (Min à pied)</label>
            <input type="text" name="distance_fac" placeholder="Ex: 5 min à pied">

            <div class="form-grid">
                <div>
                    <label>Colocation</label>
                    <select name="colocation">
                        <option value="Non">Non</option>
                        <option value="Oui">Oui</option>
                    </select>
                </div>
                <div>
                    <label>Genre Admis</label>
                    <select name="genre_admis">
                        <option value="Filles">Filles</option>
                        <option value="Garçons">Garçons</option>
                    </select>
                </div>
            </div>

            <label>Équipements inclus</label>
            <div class="amenities-grid">
                <div class="check-item"><input type="checkbox" name="wifi" value="1"> WiFi</div>
                <div class="check-item"><input type="checkbox" name="meuble" value="1" checked> Meublé</div>
                <div class="check-item"><input type="checkbox" name="chauffage" value="1"> Chauffage</div>
                <div class="check-item"><input type="checkbox" name="disponibilite" value="1" checked> Disponible</div>
            </div>

            <label>Photos du logement (Plusieurs photos)</label>
            <input type="file" name="images[]" multiple accept="image/*" required>

            <button type="submit" class="btn-submit">Publier l'annonce</button>
        </form>
    </div>
</div>

</body>
</html>