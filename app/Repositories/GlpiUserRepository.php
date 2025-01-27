<?php

namespace App\Repositories;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\GlpiUser;

use App\Repositories\UserRepository;

use Exception;




class GlpiUserRepository
{
    protected $model;

    protected $userRepository;

    /**
     * A fresh builder instance should contain a blank product object, which is
     * used in further assembly.
     */
    public function __construct(GlpiUser $model,UserRepository $userRepository)
    {
        $this->model = $model;
        $this->userRepository=$userRepository;
    }
    public function reset(): void
    {
        $this->model = new GlpiUser();
    }
    /**
     * All production steps work with the same product instance.
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $user = $this->model->findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        $presentation = $this->model->findOrFail($id);
        $presentation->delete();
    }

    /**
     * Obtiene todos los usuarios.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Obtiene solo ciertas columnas y carga relaciones
     * @param array $columns
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function only(array $columns = ['*'], array $relations = [])
    {
        $query = $this->model->select($columns);

        // Cargar las relaciones si existen
        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->get();
    }

    
    public function setTokenUserSession($user, $expiration, $plataform): array
    {

        /*
            **************************
                🍫Local Session token
            **************************
         */
        // Buscar usuario local por nombre
        $localUser = $this->userRepository->findByName($user->name);

        try {
            // Si existe el usuario
            if ($localUser) {
                // Preparar los datos a actualizar solo si hay cambios
                $updatedData = array_filter([
                    'email' => $user->email->email ?? "{$user->name}@example.com",
                    'realname' => $user->realname !== $localUser->realname ? $user->realname : null,
                    'firstname' => $user->firstname !== $localUser->firstname ? $user->firstname : null,
                    'phone' => $user->phone !== $localUser->phone ? $user->phone : null,
                    'mobile' => $user->mobile !== $localUser->mobile ? $user->mobile : null,
                    'entiti' => $user->entiti->name !== $localUser->entiti ? $user->entiti->name : null,
                    'title' => $user->title->name !== $localUser->title ? $user->title->name : null,
                    'location' => $user->location->name !== $localUser->location ? $user->location->name : null,
                    'glpi_id' => $user->id !== $localUser->glpi_id ? $user->id : null,
                    'profile_id' => $user->profiles_id !== $localUser->profile_id ? $user->profiles_id : null,
                    'is_active' => $user->is_active !== $localUser->is_active ? $user->is_active : null,
                    'picture' => $user->picture !== $localUser->picture ? $user->picture : null,
                ], fn($value) => $value !== null); // Filtrar solo valores no nulos

                if (!empty($updatedData)) {
                    $this->update($localUser->id, $updatedData);
                }
            } else {
                $hashedPassword = Hash::make(Str::random(12));
                $user->password = $hashedPassword;

                $data = [
                    'name' => $user->name,
                    'realname' => $user->realname ? $user->name : null ,
                    'firstname' => $user->firstname ? $user->fistname : null,
                    'email' => $user->email->email ?? "{$user->name}@softlogydummy.com",
                    'password' => $user->password, // Asegúrate de que esté encriptada si es necesario
                    'phone' => $user->phone ? $user->phone : null,
                    'mobile' => $user->mobile ? $user->mobile : null,
                    'entiti' => $user->entiti->name,
                    'title' => $user->title->name ?? 'Sin título',
                    'location' => $user->location->name ?? 'Sin ubicación',
                    'glpi_id' => $user->id,
                    'profile_id' => $user->id,
                    'is_active' => $user->is_active,
                    'picture' => $user->picture ?? null,
                ];
                // Crear usuario local si no existe
                $localUser = $this->userRepository->create($data);
            }

            if ($plataform == "WEB") {
                Auth::login($localUser, $expiration);
                return [
                    'user'=>$localUser
                ];
            } else {
                // Generar un token de acceso para API
                $token = $localUser->createToken('auth_token', [], $expiration)->plainTextToken;

                if (!$token) {
                    throw new Exception("Failed to generate access token for user {$localUser->id}");
                }
                return [
                    'user' => $localUser,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ];
            }
        } catch (Exception $e) {
            // Relanzar la excepción
            throw $e;
        }
    }
}
