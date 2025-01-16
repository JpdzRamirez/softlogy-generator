<?php
namespace App\Repositories;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;
use App\Repositories\UserRepository;
use App\Models\GlpiUserEntiti;
use App\Models\GlpiUserLocation;
use App\Models\GlpiUserTitle;
use App\Models\User;

use Exception;

class GlpiQueryRepository
{

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;        
    }
    public function getUserEmailById(int $userId)
    {
        return DB::connection('mysql_second')
            ->table('glpi_useremails')
            ->select('id', 'email')
            ->where('users_id', $userId)
            ->first();
    }

    public function setTokenUserSession($user, $email){

        /*
            **************************
                🍫Local Session token
            **************************
         */
        // Buscar usuario local por nombre
        $localUser = $this->userRepository->findByName($user->name);

        // Si existe el usuario
        if ($localUser) {
            // Preparar los datos a actualizar solo si hay cambios
            $updatedData = array_filter([
                'email' => $email ?? "{$user->name}@example.com",
                'realname' => $user->realname !== $localUser->realname ? $user->realname : null,
                'firstname' => $user->firstname !== $localUser->firstname ? $user->firstname : null,
                'phone' => $user->phone !== $localUser->phone ? $user->phone : null,
                'mobile' => $user->mobile !== $localUser->mobile ? $user->mobile : null,
                'is_active' => $user->is_active !== $localUser->is_active ? $user->is_active : null,
                'picture' => $user->picture !== $localUser->picture ? $user->picture : null,
            ], fn($value) => $value !== null); // Filtrar solo valores no nulos

            if (!empty($updatedData)) {
                $this->userRepository->update($localUser->id, $updatedData);                
            }
        } else {
            $hashedPassword=Hash::make(Str::random(12));
            $user->password = $hashedPassword;

            $data=[
                'name' => $user->name,
                'realname' => $user->realname,
                'firstname' => $user->firstname,
                'email' => $email ?? "{$user->name}@softlogydummy.com",
                'password' => $user->password, // Asegúrate de que esté encriptada si es necesario
                'phone' => $user->phone,
                'mobile' => $user->mobile,
                'is_active' => $user->is_active,
                'picture' => $user->picture
            ];
            // Crear usuario local si no existe
            $localUser =$this->userRepository->create($data);
        }

        // Generar un token de acceso
        $token = $localUser->createToken('authToken')->plainTextToken;

        return [
            'user' => $localUser,
            'token' => $token,
        ];
    }
}