<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'imagem'];

    public function rules() {
        return [
            'nome' => 'required|unique:marcas,nome,'. $this->id .'|min:3',
            'imagem' => 'required|file|mimes:png,jpg,jpeg,webp',
        ];
    }

    public function feedback() {
        return [
            'required' => 'O campo :attribute é requerido',
            'nome.unique' => 'O nome da marca já existe',
            'nome.min' => 'O nome deve conter no mínimo 3 caracteres',
            'imagem.mimes' => 'Formato de arquivo inválido'
        ];
    }

    public function modelos() {
        return $this->hasMany('App\Models\Modelo');
    }
}
