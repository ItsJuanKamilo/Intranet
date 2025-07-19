<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Admin\Report;
use App\Models\Role;
use App\Models\User;
use App\Rules\ValidRut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class UserController extends Controller
{

    public function profile()
    {
        return view('profile.show'); // Asegúrate de crear esta vista en resources/views/profile/show.blade.php
    }
    public function show()
    {
        // Por ejemplo, usando el usuario autenticado
        $user = auth()->user();
        return view('users.show', compact('user'));
    }


    /**
     * Lista todos los usuarios activos.
     */
    public function index()
    {
        $users = User::withTrashed()->orderBy('deleted_at', 'asc')->get(); // Mostrar inactivos y activos
        return view('users.index', compact('users'));
    }


    /**
     * Lista solo usuarios desactivados.
     */
    public function trashed()
    {
        $users = User::onlyTrashed()->get(); // Muestra solo desactivados
        return view('users.trashed', compact('users'));
    }

    /**
     * Desactiva un usuario (Soft Delete).
     */
    public function deactivate($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->delete(); // Soft delete
        return redirect()->route('admin.users.index')->with('success', 'Usuario desactivado correctamente.');
    }

    public function restore($rut)
    {
        $user = User::withTrashed()->where('rut', $rut)->firstOrFail();

        $user->restore();

        return redirect()->route('admin.users.index')->with('success', 'Usuario reactivado correctamente.');
    }





    /**
     * Elimina permanentemente un usuario.
     */
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete(); // Elimina definitivamente

        return redirect()->route('users.index')->with('success', 'Usuario eliminado permanentemente.');
    }

    /**
     * Crea un nuevo usuario.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Almacena un usuario en la base de datos.
     */
    public function store(Request $request)
    {
        // **1️⃣ Limpiar el RUT**
        $rutLimpio = preg_replace('/[\.\-\s]/', '', $request->rut);
        $rutSoloNumeros = substr($rutLimpio, 0, -1);
        $dv = strtoupper(substr($rutLimpio, -1));

        // **2️⃣ Validar los datos**
        $validated = $request->validate([
            'rut' => [
                'required',
                'string',
                'max:12',
                new ValidRut(),
            ],
            'name' => 'required|string|max:255',
            'second_name' => 'nullable|string|max:255',
            'surname_1' => 'required|string|max:255',
            'surname_2' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|digits:9',
            'annex' => 'nullable|integer|max:99999',
            'local' => 'nullable|string|max:255',
            'date_admission' => 'nullable|date',
            'date_birthday' => 'nullable|date',
            'role_id' => 'required|exists:roles,id',
            'role_description' => 'nullable|string|max:255',
            'profile_photo_path' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // **Nuevos campos**
            'gender' => ['nullable', 'string', 'max:50'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'personal_phone' => 'nullable|digits:9',
            'personal_email' => ['nullable', 'email', 'max:255'],
            'personal_address_region' => ['nullable', 'string', 'max:100'],
            'personal_address_city' => ['nullable', 'string', 'max:100'],
            'personal_address_street' => ['nullable', 'string', 'max:255'],
            'personal_address_apartment' => ['nullable', 'string', 'max:100'],
            'health_insurance' => ['nullable', 'string', 'max:100'],
            'pension_fund' => ['nullable', 'string', 'max:100'],
            'salary_bank_account' => 'nullable|string|regex:/^\d+$/|max:20',
            'account_type' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'emergency_phone'           => ['nullable'],
            'professional_social_networks' => ['nullable', 'string', 'max:500'],
            'hobbies_interests' => ['nullable', 'string', 'max:500'],
        ],[
            'rut.required' => 'El RUT es obligatorio.',
            'rut.max' => 'El RUT no puede tener más de 12 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'phone.digits' => 'El teléfono debe tener exactamente 9 dígitos.',
            'phone.regex' => 'El teléfono debe comenzar con 9 y tener 9 dígitos.',
            'role_id.required' => 'Debe seleccionar un rol.',
            'role_id.exists' => 'El rol seleccionado no es válido.',
            'profile_photo_path.mimes' => 'El archivo debe ser una imagen en formato JPG, JPEG o PNG.',
            'profile_photo_path.image' => 'El archivo debe ser una imagen válida.',
            'profile_photo_path.max' => 'La imagen no debe superar los 2MB.',
            'mimes' => 'Debe ser un archivo de tipo: :values.',

        ]);

        // **3️⃣ Verificar si el RUT ya existe en la base de datos**
        $existeUsuario = User::where('rut', (int) $rutSoloNumeros)->exists();
        if ($existeUsuario) {
            return redirect()->back()->withErrors(['rut' => 'El RUT ya está registrado en el sistema.'])->withInput();

        }

        // **4️⃣ Aplicar capitalización a los campos de texto**
        foreach ([
                     'name',
                     'second_name',
                     'surname_1',
                     'surname_2',
                     'role_description',
                     'personal_address_street',
                     'emergency_contact',
                     'hobbies_interests'
                 ] as $field) {
            if (!empty($validated[$field])) {
                $validated[$field] = Str::title($validated[$field]);
            }
        }

        // **4️⃣ Almacenar el usuario**
        $validated['rut'] = (int) $rutSoloNumeros;
        $validated['dv'] = $dv;
        $validated['password'] = Hash::make($validated['password']);



        foreach (['phone', 'personal_phone'] as $field) {
            if (!empty($validated[$field])) {
                $validated[$field] = '56' . $validated[$field];
            }
        }


        // Carga de imagen
        $userName = Str::slug($validated['name'] . ' ' . $validated['surname_1']);
        $dateTimeStr = date('Ymd_His');

        if ($request->filled('cropped_image')) {
            $imageData = $request->input('cropped_image');

            // Detectar el formato: soporta PNG y JPEG
            if (strpos($imageData, 'data:image/png;base64,') === 0) {
                $image = str_replace('data:image/png;base64,', '', $imageData);
                $extension = 'png';
            } elseif (strpos($imageData, 'data:image/jpeg;base64,') === 0) {
                $image = str_replace('data:image/jpeg;base64,', '', $imageData);
                $extension = 'jpg';
            } else {
                throw new \Exception('Formato de imagen no soportado.');
            }

            // Reemplazar espacios y decodificar
            $image = str_replace(' ', '+', $image);
            $imageBinary = base64_decode($image);
            if ($imageBinary === false || strlen($imageBinary) == 0) {
                throw new \Exception('Error al decodificar la imagen en Base64 o imagen vacía.');
            }

            // Generar el nombre de archivo personalizado: "api/avatars/{userName}_{dateTimeStr}.{extension}"
            $filename = 'api/avatars/' . $userName . '_' . $dateTimeStr . '.' . $extension;

            try {
                // Subir la imagen a S3 con permisos públicos
                $result = Storage::put($filename, $imageBinary, ['public']);
                if (!$result) {
                    throw new \Exception('La función put() devolvió false.');
                }
            } catch (\Exception $e) {
                \Log::error('Error al guardar la imagen en S3: ' . $e->getMessage(), [
                    'filename' => $filename,
                    'bucket' => config('filesystems.disks.s3.bucket'),
                ]);
                throw new \Exception('Error al guardar la imagen en S3: ' . $e->getMessage());
            }

            $validated['profile_photo_path'] = $filename;
        }
        // Caso de imagen subida normalmente (archivo)
        elseif ($request->hasFile('profile_photo_path')) {
            $extension = $request->file('profile_photo_path')->getClientOriginalExtension();
            $customName = $userName . '_' . $dateTimeStr . '.' . $extension;
            $path = $request->file('profile_photo_path')->storeAs('api/avatars', $customName, 's3');
            $validated['profile_photo_path'] = $path;
        }


        $user = User::create($validated);

        // Asigna el rol utilizando Spatie (esto actualizará la relación many-to-many)
        $role = Role::findOrFail($validated['role_id']);
        $user->assignRole($role->name);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente.');
    }



    /**
     * Muestra el formulario para editar un usuario.
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        // Formatear el RUT con puntos y guion
        $formattedRut = number_format($user->rut, 0, '', '.') . '-' . strtoupper($user->dv);

        // Verificar si el teléfono tiene el prefijo "56" y eliminarlo si está presente
        $phoneWithoutPrefix = (!empty($user->phone) && substr($user->phone, 0, 2) === '56')
            ? substr($user->phone, 2)
            : ($user->phone ?? ''); // Si no tiene teléfono, se asigna cadena vacía

        return view('users.edit', compact('user', 'roles', 'formattedRut', 'phoneWithoutPrefix'));
    }


    /**
     * Actualiza la información de un usuario.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'second_name' => 'nullable|string|max:255',
            'surname_1' => 'required|string|max:255',
            'surname_2' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|integer|digits:9',
            'annex' => 'nullable|integer',
            'local' => 'nullable|string|max:255',
            'date_admission' => 'nullable|date',
            'date_birthday' => 'nullable|date',
            'role_id' => 'required|exists:roles,id',
            'role_description' => 'nullable|string|max:255',
            'profile_photo_path' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // **Nuevos campos**
            'gender' => ['nullable', 'string', 'max:50'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'personal_phone' => 'nullable|integer|digits:9',
            'personal_email' => ['nullable', 'email', 'max:255'],
            'personal_address_region' => ['nullable', 'string', 'max:100'],
            'personal_address_city' => ['nullable', 'string', 'max:100'],
            'personal_address_street' => ['nullable', 'string', 'max:255'],
            'personal_address_apartment' => ['nullable', 'string', 'max:100'],
            'health_insurance' => ['nullable', 'string', 'max:100'],
            'pension_fund' => ['nullable', 'string', 'max:100'],
            'salary_bank_account' => 'nullable|string|regex:/^\d+$/|max:20',
            'account_type' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'emergency_phone'           => ['nullable'],
            'professional_social_networks' => ['nullable', 'string', 'max:500'],
            'hobbies_interests' => ['nullable', 'string', 'max:500'],

            $messages = [
                'name.required' => 'El campo Nombre es obligatorio.',
                'surname_1.required' => 'El campo Apellido es obligatorio.',
                'email.required' => 'El campo Correo Electrónico es obligatorio.',
                'email.email' => 'Debe ingresar un correo electrónico válido.',
                'email.unique' => 'El correo electrónico ya está registrado.',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                'password.confirmed' => 'Las contraseñas no coinciden.',
                'phone.digits' => 'El teléfono debe tener exactamente 9 dígitos.',
                'personal_phone.digits' => 'El teléfono personal debe tener exactamente 9 dígitos.',
                'emergency_phone.regex' => 'El teléfono de emergencia debe estar en formato +56XXXXXXXXX.',
                'mimes' => 'Debe ser un archivo de tipo: :values.',
            ]
        ];

        if ($request->email !== $user->email) {
            $rules['email'] .= '|unique:users,email';
        }

        $validated = $request->validate($rules, $messages);

        // Capitalización
        $validated['name'] = Str::title($validated['name']);
        $validated['second_name'] = isset($validated['second_name']) ? Str::title($validated['second_name']) : null;
        $validated['surname_1'] = Str::title($validated['surname_1']);
        $validated['surname_2'] = isset($validated['surname_2']) ? Str::title($validated['surname_2']) : null;
        $validated['role_description'] = isset($validated['role_description']) ? Str::title($validated['role_description']) : null;
        $validated['personal_address_street'] = isset($validated['personal_address_street']) ? Str::title($validated['personal_address_street']) : null;
        $validated['emergency_contact'] = isset($validated['emergency_contact']) ? Str::title($validated['emergency_contact']) : null;
        $validated['hobbies_interests'] = isset($validated['hobbies_interests']) ? Str::title($validated['hobbies_interests']) : null;



        if ($request->filled('password')) {
            // Hashear la nueva contraseña
            $validated['password'] = Hash::make($validated['password']);

            // Limpiar el RUT eliminando puntos y guiones
            $rutClean = preg_replace('/[^0-9Kk]/', '', $request->input('rut')); // Solo permite números y 'K'

            // Verificar si el RUT tiene un dígito verificador (DV) al final
            // Si lo tiene, se lo eliminamos porque en la base de datos solo está almacenado el RUT sin el DV.
            $rutSinDv = substr($rutClean, 0, -1);  // Eliminar el último dígito (DV)

            // DEBUG: Verificar si el RUT sin DV coincide con lo esperado
            \Log::info("Buscando usuario con RUT sin DV: " . $rutSinDv);

            // Buscar al usuario en la base de datos usando solo el RUT sin el DV
            $user = User::where('rut', $rutSinDv)->first();

            if (!$user) {
                \Log::error("Usuario NO encontrado con RUT sin DV: " . $rutSinDv);
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            // Guardar la nueva contraseña en la base de datos
            $user->update(['password' => $validated['password']]);

            // Eliminar TODAS las sesiones activas del usuario editado en la base de datos
            DB::table('sessions')->where('user_id', $user->rut)->delete();
        } else {
            unset($validated['password']);
        }







        foreach (['phone', 'personal_phone'] as $field) {
            if (!empty($validated[$field])) {
                $validated[$field] = '56' . $validated[$field];
            }
        }

        // Carga de imagen
        $userName = Str::slug($user->name . ' ' . $user->surname_1);
        $dateTimeStr = date('Ymd_His');

        if ($request->filled('cropped_image')) {
            $imageData = $request->input('cropped_image');

            // Detectar el formato: soporta PNG y JPEG
            if (strpos($imageData, 'data:image/png;base64,') === 0) {
                $image = str_replace('data:image/png;base64,', '', $imageData);
                $extension = 'png';
            } elseif (strpos($imageData, 'data:image/jpeg;base64,') === 0) {
                $image = str_replace('data:image/jpeg;base64,', '', $imageData);
                $extension = 'jpg';
            } else {
                throw new \Exception('Formato de imagen no soportado.');
            }

            // Reemplazar espacios y decodificar
            $image = str_replace(' ', '+', $image);
            $imageBinary = base64_decode($image);
            if ($imageBinary === false || strlen($imageBinary) == 0) {
                throw new \Exception('Error al decodificar la imagen en Base64 o imagen vacía.');
            }

            // Generar el nombre de archivo personalizado: "api/avatars/{userName}_{dateTimeStr}.{extension}"
            $filename = 'api/avatars/' . $userName . '_' . $dateTimeStr . '.' . $extension;

            try {
                // Subir la imagen a S3 con permisos públicos
                $result = Storage::put($filename, $imageBinary, ['public']);
                if (!$result) {
                    throw new \Exception('La función put() devolvió false.');
                }
            } catch (\Exception $e) {
                \Log::error('Error al guardar la imagen en S3: ' . $e->getMessage(), [
                    'filename' => $filename,
                    'bucket' => config('filesystems.disks.s3.bucket'),
                ]);
                throw new \Exception('Error al guardar la imagen en S3: ' . $e->getMessage());
            }

            $validated['profile_photo_path'] = $filename;
        }
        // Caso de imagen subida normalmente (archivo)
        elseif ($request->hasFile('profile_photo_path')) {
            $extension = $request->file('profile_photo_path')->getClientOriginalExtension();
            $customName = $userName . '_' . $dateTimeStr . '.' . $extension;
            $path = $request->file('profile_photo_path')->storeAs('api/avatars', $customName, 's3');
            $validated['profile_photo_path'] = $path;
        }




        $user->update($validated);

        // Sincronizar el rol usando Spatie (asumiendo que el usuario tiene un único rol)
        $role = Role::find($validated['role_id']);
        // Esto asigna el rol a través de la relación many-to-many de Spatie
        $user->syncRoles([$role->name]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy($rut)
    {
        $user = User::withTrashed()->where('rut', $rut)->first();

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'Usuario no encontrado.');
        }

        if ($user->trashed()) {
            $user->forceDelete();
            return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado permanentemente.');
        } else {
            $user->delete();
            return redirect()->route('admin.users.index')->with('success', 'Usuario desactivado.');
        }
    }

    public function downloadExcel(){
        $report = new Report('Usuarios de la Intranet', 'admin.users');
        $users = User::all();
        $report->setArray($users->toArray());
        return $report->download();
    }



}
