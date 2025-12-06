<?php

use App\Models\post;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Http\Controllers\EnrolementController;

Route::get('/enrolement', [EnrolementController::class, 'formulaire']);
Route::post('/enrolement', [EnrolementController::class, 'enregistrer']);


Route::get('/', function () {
    return view('welcome');
});

Route::get('/blog', function(Request $request){
   $post = post::create([
    'title'=>'mon nouveau titre',
    'slug'=>'mon-nouveau-titre',
    'content'=>'nouveau contenu',
   ]);

   return $post;
});

Route::get('/blog/{slug}-{id}', function(string $slug, string $id){
    return [
        "slug" => $slug,
        "id" => $id
    ];
});

Route::get('/enrolement', [EnrolementController::class, 'formulaire']);
/**Route::get définit une route qui répond aux requêtes HTTP GET (typiquement quand tu ouvres une page dans le navigateur).

/enrolement → c’est l’URL que l’utilisateur va taper ou sur laquelle il va cliquer pour accéder à la page.

[EnrolementController::class, 'formulaire'] → indique que la méthode formulaire du contrôleur EnrolementController sera exécutée quand cette URL sera visitée.

En pratique :

L’utilisateur va sur http://ton-site.test/enrolement.

Laravel appelle EnrolementController->formulaire().

La méthode retourne la vue du formulaire d’inscription. */
Route::post('/enrolement', [EnrolementController::class, 'enregistrer']);
/**Route::post définit une route qui répond aux requêtes HTTP POST (typiquement pour soumettre des formulaires).

L’URL est la même /enrolement, mais la méthode HTTP change (POST au lieu de GET).

[EnrolementController::class, 'enregistrer'] → Laravel va appeler la méthode enregistrer du contrôleur pour traiter les données envoyées par le formulaire.

En pratique :

L’utilisateur remplit le formulaire et clique sur « Soumettre ».

Le formulaire envoie les données via POST vers /enrolement.

Laravel exécute EnrolementController->enregistrer().

Les données sont validées, le candidat et ses documents sont enregistrés, puis la vue de confirmation est affichée. */
