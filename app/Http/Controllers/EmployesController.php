<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employe;
use App\Models\Gridbox;

class EmployesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $params = $request->all();

            #establezco los campos a mostrar
            $params["select"] = [
                ["field" => "employes.id"],
                ["field" => "name", "conditions" => "employes.name"],
                ["field" => "lastname", "conditions" => "employes.lastname"],
                ["field" => "position", "conditions" => "employes.position"],
                ["field" => "cedula", "conditions" => "employes.cedula"],
                ["field" => "data_admission", "conditions" => "employes.data_admission"],
                ["field" => "address", "conditions" => "employes.address"],
                ["field" => "city", "conditions" => "employes.city"],
                ["field" => "province", "conditions" => "employes.province"],
                ["field" => "nacionality", "conditions" => "employes.nacionality"],
                ["field" => "phone", "conditions" => "employes.phone"],
                ["field" => "genere", "conditions" => "employes.genere"],
                ["field" => "date_brith", "conditions" => "employes.date_brith"],
                ["field" => "employes.created_at"],
                ["field" => "employes.updated_at"]
            ];
            
            # Obteniendo la lista
            $employes = Gridbox::pagination("employes", $params, false, $request);
            return response()->json($employes);
        } catch(\Exception $e) {
            \Log::info("Error  ({$e->getCode()}):  {$e->getMessage()}  in {$e->getFile()} line {$e->getLine()}");
            return \Response::json([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 422);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
           $new_employe = new Employe();

           $new_employe->name = $request->name;
           $new_employe->lastname = $request->lastname;
           $new_employe->position = $request->position;
           $new_employe->cedula = $request->cedula;
           $new_employe->data_admission = $request->data_admission;
           $new_employe->address = $request->address;
           $new_employe->city = $request->city;
           $new_employe->province = $request->province;
           $new_employe->nacionality = $request->nacionality;
           $new_employe->phone = $request->phone;
           $new_employe->genere = $request->genere;
           $new_employe->date_brith = $request->date_brith;

           $new_employe->save();

           return response()->json('Registrado Correctamente', 201);

        } catch(\Exception $e) {
            \Log::info("Error  ({$e->getCode()}):  {$e->getMessage()}  in {$e->getFile()} line {$e->getLine()}");
            return \Response::json([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $employe = Employe::findOrFail($id);
            return response()->json($employe);

        } catch(\Exception $e) {
            \Log::info("Error  ({$e->getCode()}):  {$e->getMessage()}  in {$e->getFile()} line {$e->getLine()}");
            return \Response::json([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 422);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            $employe = Employe::findOrFail($id);
            
            # actualizo los datos

            $employe->name = $request->name;
            $employe->lastname = $request->lastname;
            $employe->position = $request->position;
            $employe->cedula = $request->cedula;
            $employe->data_admission = $request->data_admission;
            $employe->address = $request->address;
            $employe->city = $request->city;
            $employe->province = $request->province;
            $employe->nacionality = $request->nacionality;
            $employe->phone = $request->phone;
            $employe->genere = $request->genere;
            $employe->date_brith = $request->date_brith;

            $employe->save();

            return response()->json('Actualizado Correctamente', 202);

        } catch(\Exception $e) {
            \Log::info("Error  ({$e->getCode()}):  {$e->getMessage()}  in {$e->getFile()} line {$e->getLine()}");
            return \Response::json([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $employe = Employe::findOrFail($id);
            $employe->delete();
            return response()->json(null, 204);

        } catch(\Exception $e) {
            \Log::info("Error  ({$e->getCode()}):  {$e->getMessage()}  in {$e->getFile()} line {$e->getLine()}");
            return \Response::json([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], 422);
        }
    }
}
