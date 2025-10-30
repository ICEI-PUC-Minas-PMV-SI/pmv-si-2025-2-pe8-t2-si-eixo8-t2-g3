<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'name', 'cnpj', 'email', 'phone',
        'street', 'complement', 'city', 'district', 'state', 'cep',
        'machines', 'codigo_ibge', 'latitude', 'longitude',
    ];

    /**
     * Casts garantem que os tipos de dados sejam corretos.
     * O Eloquent converterá para float ao salvar e ao ler.
     */
    protected $casts = [
        'codigo_ibge' => 'integer',
        'latitude'    => 'float',
        'longitude'   => 'float',
    ];

    // Os mutadores setLatitudeAttribute e setLongitudeAttribute podem ser removidos
    // pois o $casts já faz o trabalho de conversão de tipo.

    public function setCnpjAttribute($value)
    {
        $this->attributes['cnpj'] = preg_replace('/\D/', '', (string) $value);
    }

    public function setCepAttribute($value)
    {
        $this->attributes['cep'] = preg_replace('/\D/', '', (string) $value);
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = preg_replace('/\D/', '', (string) $value);
    }
    
    // ... restante do model ...
}