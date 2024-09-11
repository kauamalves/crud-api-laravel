<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index():JsonResponse
    {
        $users = User::orderBy('id', 'DESC')->paginate(2);

        // Retorna os usuários como uma resposta JSON
        return response()->json([
            'status' => true,
            'users' => $users,
     ], 200);
    }

    public function show(User $user) : JsonResponse
    {
        return response()->json([
            'status' => true,
            'users' => $user,
     ], 200);
    }

    public function store(UserRequest $request){
        // Inicia a transação
        DB::beginTransaction();

        try{

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password
            ]);

            // Operação concluída com êxito
            DB::commit();

            // Cadastra os usuários e passa uma mensagem de êxito
            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Usuário cadastrado com sucesso',
         ], 201);

        } catch(Exception $e){
            // Operação não concluida com sucesso

            DB::rollBack();

            // Retorna um erro com uma mensagem 400
            return response()->json([
                'status' => false,
                'message' => 'Usuário não cadastrado',
         ], 400);
        }
    }

    public function update(UserRequest $request, User $user):JsonResponse
    {

        // Inicia a transação com o banco de dados
        DB::beginTransaction();

        try{

            // Editar o registro no banco de dados
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password
            ]);

            DB::commit();

            // Edita os usuários e passa uma mensagem de êxito
            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Usuário editado com sucesso',
         ], 201);

        } catch(Exception $e) {
            
            // Operação não é concluida
            DB::rollBack();

            // Retorna um erro com uma mensagem 400
            return response()->json([
                'status' => false,
                'message' => 'Usuário não editado',
         ], 400);
        }

        // Mensagem de usuário editado com sucesso
        return response()->json([
            'status' => true,
            'user' => $request,
            'message' => 'Usuário editado com sucesso',
     ], 200);
    }

    public function destroy(User $user):JsonResponse{
        try{
            // Apagar o registro do banco de dados
            $user->delete();

            // Retorna uma mensagem de sucesso
            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Usuário deletado com sucesso',
         ], 201);

        } catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Usuário não deletado :(',
         ], 200);
        }
    }
}
