<?php 

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository {
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function selectRelatedsAttrs($attrs)
    {
        $this->model = $this->model->with($attrs);
    }

    public function filter($filtro)
    {
        $filtros = explode(';', $filtro);
        foreach ($filtros as $key => $condicao) {
            $c = explode(':', $condicao);
            $this->model = $this->model->where($c[0], $c[1], $c[2]);
        }
    }

    public function selectAttrs($attrs) {
        $this->model = $this->model->selectRaw($attrs);
    }

    public function getResults() {
        return $this->model->paginate(10);
    }
}