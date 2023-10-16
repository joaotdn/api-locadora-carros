<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{

    public function __construct(Modelo $modelo) {
        $this->modelo = $modelo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo);

        if ($request->has('attrs_marca')) {
            $filter = 'marca:id,'.$request->attrs_marca;
            $modeloRepository->selectRelatedsAttrs($filter);
        } else {
            $modeloRepository->selectRelatedsAttrs('marca');
        }

        if($request->has('filtro')) {
            $modeloRepository->filter($request->filtro);
        }

        if ($request->has('attrs')) {
            $modeloRepository->selectAttrs($request->attrs);
        }

        return response()->json($modeloRepository->getResults(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $modelo = new Modelo();
        $request->validate($modelo->rules());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');


        $modelo = $modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs,
        ]);

        return response()->json($modelo, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $modelo = new Modelo();
        $modelo = $modelo->with('marca')->find($id);
        if ($modelo === null) {
            return response()->json(['error' => 'Modelo não encontrado'], 404);
        }
        return response()->json($modelo, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $modelo = new Modelo();
        $modelo = $modelo->find($id);

        if ($request->method() === 'PATCH') {
            $regrasDinamicas = array();
            foreach ($modelo->rules() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas);
        } else {
            $request->validate($modelo->rules());
        }

        if ($request->file('imagem')) {
            Storage::disk('public')->delete($modelo->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo->fill($request->all());
        $modelo->imagem = $imagem_urn;
        $modelo->save();

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $modelo = new Modelo();
        $modelo = $modelo->find($id);

        if ($modelo == null) {
            return response()->json(['msg' => 'Impossível deletar o modelo.'], 404);
        }

        Storage::disk('public')->delete($modelo->imagem);

        $deleted = $modelo;
        $modelo->delete();
        return response()->json($deleted, 200);
    }
}
