<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    //
    protected $fillable = [
        'candidat_id',
        'cni',
        'diplome',
    ];

    public function candidat()
    {
        return $this->belongsTo(Candidat::class);
    }
}
