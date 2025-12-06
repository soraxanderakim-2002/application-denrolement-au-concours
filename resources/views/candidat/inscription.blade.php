<h2>Enrôlement du candidat</h2>

<form action="/enrolement" method="POST" enctype="multipart/form-data">
    @csrf

    <label>Nom :</label>
    <input type="text" name="nom"><br><br>

    <label>Prénom :</label>
    <input type="text" name="prenom"><br><br>

    <label>Email :</label>
    <input type="email" name="email"><br><br>

    <label>Téléphone :</label>
    <input type="text" name="telephone"><br><br>

    <label>Mot de passe :</label>
    <input type="password" name="password"><br><br>

    <label>Carte d'identité (PDF/JPG) :</label>
    <input type="file" name="cni"><br><br>

    <label>Diplôme (PDF/JPG) :</label>
    <input type="file" name="diplome"><br><br>

    <button type="submit">S’enrôler</button>
</form>
