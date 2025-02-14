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


    /**
     * A fresh builder instance should contain a blank product object, which is
     * used in further assembly.
     */
    public function __construct(GlpiUser $model)
    {
        $this->model = $model;

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

    
    public function setTokenUserSession($user, $expiration, $plataform,UserRepository $userRepository): array
    {

        /*
            **************************
                🍫Local Session token
            **************************
         */
        // Buscar usuario local por nombre
        $localUser = $userRepository->findByName($user->name);

        try {
            // Si existe el usuario
            if ($localUser) {
                // Preparar los datos a actualizar solo si hay cambios
                $updatedData = array_filter([
                    'email' => $user->email->email ?? "{$user->name}@example.com", // Corregido el acceso a email
                    'realname' => $user->realname !== $localUser->realname ? $user->realname : null,
                    'firstname' => $user->firstname !== $localUser->firstname ? $user->firstname : null,
                    'phone' => $user->phone !== $localUser->phone ? $user->phone : null,
                    'mobile' => $user->mobile !== $localUser->mobile ? $user->mobile : null,
                    'entiti' => ($user->entiti?->name ?? null) !== ($localUser->entiti?->name ?? null) ? $user->entiti?->name : null,
                    'entities_id' => ($user->entiti?->id ?? null) !== ($localUser->entiti?->id ?? null) ? $user->entiti?->id : null,
                    'title' => ($user->title?->name ?? null) !== ($localUser->title?->name ?? null) ? $user->title?->name : null,
                    'location' => ($user->location?->name ?? null) !== ($localUser->location?->name ?? null) ? $user->location?->name : null,
                    'location_id' => ($user->locations_id ?? null) !== ($localUser->location_id ?? null) ? $user->locations_id : null,
                    'glpi_id' => $user->id !== $localUser->glpi_id ? $user->id : null,
                    'profile' => ($user->profile?->name ?? null) !== ($localUser->profile?->name ?? null) ? $user->profile?->name : null,
                    'profile_id' => ($user->profile?->id ?? null) !== ($localUser->profile?->id ?? null) ? $user->profile?->id : null,
                    'is_active' => $user->is_active !== $localUser->is_active ? $user->is_active : null,                    
                    'picture' => $user->picture !== $localUser->picture ? $user->picture : null,
                ], fn($value) => $value !== null); // Filtrar solo valores no nulos

                if (!empty($updatedData)) {
                    $userRepository->update($localUser->id, $updatedData);
                }
            } else {
                $hashedPassword = Hash::make(Str::random(12));
                $user->password = $hashedPassword;

                $data = [
                    'name' => $user->name,
                    'realname' => !empty($user->realname) ? $user->name : null,
                    'firstname' => !empty($user->firstname) ? $user->firstname : null, 
                    'email' => optional($user->email)->email ?? "{$user->name}@softlogydummy.com", 
                    'password' => $user->password,
                    'phone' => !empty($user->phone) ? $user->phone : null,
                    'mobile' => !empty($user->mobile) ? $user->mobile : null,
                    'entity' => optional($user->entiti)->name, 
                    'entities_id' => optional($user->entiti)->id, 
                    'title' =>  optional($user->title)->name ?? 'Usuario Estandar', 
                    'location' => optional($user->location)->name ?? '-', 
                    'location_id' => $user->locations_id ?? null, 
                    'glpi_id' => $user->id,
                    'profile' => optional($user->profile)->name, 
                    'profile_id' => optional($user->profile)->id, 
                    'is_active' => $user->is_active,
                    'picture' => $user->picture ?? null,
                ];
                
                // Crear usuario local si no existe
                $localUser = $userRepository->create($data);
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
