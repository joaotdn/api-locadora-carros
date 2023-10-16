<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use App\Http\Requests\StoreCarroRequest;
use App\Http\Requests\UpdateCarroRequest;
use App\Repositories\CarroRepository;
use Illuminate\Http\Request;

class CarroController extends Controller
{

    public function __construct(Carro $carro) {
        $this->carro = $carro;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $carroRepository = new CarroRepository($this->carro);
        if ($request->has('attrs_modelo')) {
            $attrs_modelo = 'modelo:id'.$request->attrs_modelo;
            $carroRepository->selectRelatedsAttrs($attrs_modelo);
        } else {
            $carroRepository->selectRelatedsAttrs('modelo');
        }

        if ($request->has('filtro')) {
            $carroRepository->filter($request->filtro);
        }

        if ($request->has('attrs')) {
            $carroRepository->selectAttrs($request->attrs);
        }

        return response()->json($carroRepository->getResults(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCarroRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->carro->rules());
        
        $carro = $this->carro->create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km
        ]);

        return response()->json($carro, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $carro = $this->carro->with('modelo')->find($id);
        if ($carro === null) {
            return response()->json(['error' => 'O carro não existe'], 404);
        }

        return response()->json($carro, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCarroRequest  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $carro = $this->carro->find($id);

        if ($carro === null) {
            return response()->json(['error' => 'Impossivel atualizar o carro'], 404);
        }

        if($request->method() === 'PATCH') {
            $regrasDinamicas = array();
            foreach($carro->rules() as $input => $regra) {
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas);
        } else {
            $request->validate($carro->rules());
        }

        
        $carro->fill($request->all());
        $carro->save();

        return response()->json($carro, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $carro = $this->carro->find($id);
        if ($carro === null) {
            return response()->json(['error' => 'Impossivel deletar o carro'], 404);
        }

        $deleted = $carro;
        $carro->delete();
        return response()->json($deleted, 200);
    }
}
