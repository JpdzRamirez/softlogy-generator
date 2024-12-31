<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\User;

class GlpiQueryRepository
{

    public function getUserEmailById(int $userId)
    {
        return DB::connection('mysql_second')
            ->table('glpi_useremails')
            ->select('id', 'email')
            ->where('users_id', $userId)
            ->first();
    }

    public function setTokenUserSession($user, $userEmail){
        // Buscar usuario local por nombre
        $localUser = User::where('name', $user->name)->first();

        if ($localUser) {
            // Preparar los datos a actualizar solo si hay cambios
            $updatedData = array_filter([
                'email' => $userEmail ?? "{$user->name}@example.com",
                'realname' => $user->realname !== $localUser->realname ? $user->realname : null,
                'firstname' => $user->firstname !== $localUser->firstname ? $user->firstname : null,
            ]);

            if (!empty($updatedData)) {
                $localUser->update($updatedData);
            }
        } else {
            // Crear usuario local si no existe
            $localUser = User::create([
                'name' => $user->name,
                'email' => $userEmail ?? "{$user->name}@softlogydummy.com",
                'password' => $user->password, // Asegúrate de que esté encriptada si es necesario
                'realname' => $user->realname,
                'firstname' => $user->firstname,
            ]);
        }

        // Generar un token de acceso
        $token = $localUser->createToken('authToken')->plainTextToken;

        return [
            'user' => $localUser,
            'token' => $token,
        ];
    }
}