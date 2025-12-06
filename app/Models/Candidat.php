<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidat extends Model
{
    //
    protected $fillable = [
        'numero_candidature',
        'nom',
        'prenom',
        'email',
        'telephone',
        'password',
    ];

    public function documents()
    {
        return $this->hasOne(Document::class);
    }
}
