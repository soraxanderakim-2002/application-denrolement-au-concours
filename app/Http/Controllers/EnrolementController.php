<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidat;
use App\Models\Document; // si tu utilises aussi Document



class EnrolementController extends Controller
{
    public function formulaire()
    {
                /**Cette méthode renvoie la vue du formulaire d’inscription.

view('candidat.inscription') cherche le fichier resources/views/candidat/inscription.blade.php.

Cette méthode est appelée quand l’utilisateur veut accéder au formulaire d’enregistrement. */
        return view('candidat.inscription');
    }

    public function enregistrer(Request $request)
    {


/**$request->validate() vérifie que les champs remplis par l’utilisateur respectent certaines règles :

required → le champ est obligatoire.

email → doit être une adresse email valide.

unique:candidats → l’email ne doit pas déjà exister dans la table candidats.

min:6 → le mot de passe doit avoir au moins 6 caractères.

mimes:pdf,jpeg,png,jpg → le fichier doit être de l’un de ces formats.

Si la validation échoue, Laravel renvoie automatiquement à la page précédente avec les erreurs. */
        // Validation
        $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required|email|unique:candidats',
            'telephone' => 'required',
            'password' => 'required|min:6',
            'cni' => 'required|mimes:pdf,jpeg,png,jpg',
            'diplome' => 'required|mimes:pdf,jpeg,png,jpg',
        ]);
/**On génère un identifiant unique pour le candidat :

time() → timestamp actuel.

rand(100, 999) → un nombre aléatoire à 3 chiffres.

Exemple : CAND-173562312345-456. */
        // Génération du numéro de candidature
        $numero = 'CAND-' . time() . rand(100, 999);
/**On utilise le modèle Candidat pour créer une nouvelle entrée dans la table candidats.

bcrypt() sert à hasher le mot de passe avant de le stocker (sécurité !). */
        // Création du candidat
        $candidat = Candidat::create([
            'numero_candidature' => $numero,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => bcrypt($request->password),
        ]);
/**Les fichiers téléchargés par l’utilisateur sont enregistrés dans le dossier storage/app/public :

documents/cni → pour la carte d’identité.

documents/diplomes → pour le diplôme.

$request->cni->store() retourne le chemin du fichier qui sera stocké en base. */
        // Upload des documents
        $cni = $request->cni->store('documents/cni', 'public');
        $diplome = $request->diplome->store('documents/diplomes', 'public');
/**On crée un enregistrement dans la table documents lié au candidat (candidat_id).

La relation un-à-un Candidat → Document est respectée. */
        Document::create([
            'candidat_id' => $candidat->id,
            'cni' => $cni,
            'diplome' => $diplome,
        ]);
/**On renvoie une vue de confirmation candidat.confirmation.

La variable $candidat est passée à la vue grâce à compact(). */
        return view('candidat.confirmation', compact('candidat'));
    }
}

