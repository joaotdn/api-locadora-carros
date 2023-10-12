<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Marca::paginate(10), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $marca = new Marca();
        $request->validate($marca->rules(), $marca->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        $marca = $marca->create([
            'nome' => $request->get('nome'),
            'imagem' => $imagem_urn
        ]);
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marca = Marca::find($id);
        if ($marca === null) {
            return response()->json(['msg' => 'Marca não encontrada.'], 404);
        }
        return response()->json($marca, 200);
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
        $marca = new Marca();
        $marca = $marca->find($id);

        if($request->method() === 'PATCH') {
            $regrasDinamicas = array();
            foreach($marca->rules() as $input => $regra) {
                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }
            $request->validate($regrasDinamicas, $marca->feedback());
        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }

        if($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        $marca->update([
            'nome' => $request->get('nome'),
            'imagem' => $imagem_urn
        ]);
        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marca = Marca::find($id);

        if ($marca == null) {
            return response()->json(['msg' => 'Impossível deletar a marca.'], 404);
        }

        Storage::disk('public')->delete($marca->imagem);

        $deleted = $marca;
        $marca->delete();
        return response()->json($deleted, 200);
    }
}
