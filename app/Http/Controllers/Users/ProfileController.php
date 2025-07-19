<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ProfileController extends Controller
{
    public function show()
    {
        // Retorna la vista de perfil (por ejemplo, resources/views/profile/show.blade.php)
        // Puedes usar auth()->user() en la vista para acceder a los datos del usuario
        return view('profile.show');
    }

    public function edit()
    {
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        // Obtener usuario autenticado
        $user = auth()->user();

        // Definir reglas de validación
        $rules = [
            'name'                      => ['required', 'string', 'max:255'],
            'second_name'               => ['nullable', 'string', 'max:255'],
            'surname_1'                 => ['required', 'string', 'max:255'],
            'surname_2'                 => ['nullable', 'string', 'max:255'],
            'email'                     => ['required', 'string', 'email', 'max:255'],
            'password'                  => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone'                     => ['nullable', 'digits_between:9,15'],
            'annex'                     => ['nullable', 'integer'],
            'local'                     => ['nullable', 'string', 'max:255'],
            'date_admission'            => ['nullable', 'date'],
            'date_birthday'             => ['nullable', 'date'],
            'profile_photo_path'        => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'role_description'          => ['nullable', 'string', 'max:255'],
            'gender'                    => ['nullable', 'string', 'max:50'],
            'marital_status'            => ['nullable', 'string', 'max:50'],
            'nationality'               => ['nullable', 'string', 'max:100'],
            'personal_phone'            => ['nullable', 'digits_between:9,15'],
            'personal_email'            => ['nullable', 'email', 'max:255'],
            'personal_address_region'   => ['nullable', 'string', 'max:100'],
            'personal_address_city'     => ['nullable', 'string', 'max:100'],
            'personal_address_street'   => ['nullable', 'string', 'max:255'],
            'personal_address_apartment'=> ['nullable', 'string', 'max:100'],
            'health_insurance'          => ['nullable', 'string', 'max:100'],
            'pension_fund'              => ['nullable', 'string', 'max:100'],
            'salary_bank_account'       => ['nullable', 'string', 'regex:/^\d+$/'],
            'account_type'              => ['nullable', 'string', 'max:50'],
            'bank_name'                 => ['nullable', 'string', 'max:100'],
            'emergency_contact'         => ['nullable', 'string', 'max:255'],
            'emergency_phone'           => ['nullable'],
            'professional_social_networks' => ['nullable', 'string', 'max:500'],
            'hobbies_interests'         => ['nullable', 'string', 'max:500'],
        ];

        // **Solo validar la contraseña actual si el usuario quiere cambiarla**
        if ($request->filled('password')) {
            $rules['current_password'] = ['required', 'string', 'min:8'];
        }

        $messages = [
            'name.required' => 'El campo Nombre es obligatorio.',
            'surname_1.required' => 'El campo Apellido es obligatorio.',
            'email.required' => 'El campo Correo Electrónico es obligatorio.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'phone.digits_between' => 'El teléfono debe tener entre 9 y 15 dígitos.',
            'personal_phone.digits_between' => 'El teléfono personal debe tener entre 9 y 15 dígitos.',
            'emergency_phone.regex' => 'El teléfono de emergencia debe estar en formato +56XXXXXXXXX.',
            'current_password.required' => 'Debes ingresar tu contraseña actual para cambiarla.',
            'mimes' => 'Debe ser un archivo de tipo: :values.',
            'salary_bank_account.regex' => 'El número de cuenta debe contener solo dígitos.',

        ];

        if ($request->email !== $user->email) {
            $rules['email'][] = 'unique:users,email';
        }

        // **Validación de datos**
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



        // **Si el usuario quiere cambiar su contraseña**
        if ($request->filled('password')) {
            // **Verificar que la contraseña actual sea correcta**
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
            }

            // **Cerrar otras sesiones antes de cambiar la contraseña**
            auth()->logoutOtherDevices($request->current_password);

            // **Actualizar la contraseña**
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']); // Si el usuario no cambió la contraseña, no actualizarla
        }



        // **Agregar prefijo "56" a los teléfonos si no lo tienen**
        foreach (['phone', 'personal_phone'] as $field) {
            if (!empty($validated[$field]) && substr($validated[$field], 0, 2) !== '56') {
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






        // **Actualizar usuario**
        $user->update($validated);

        // **Si cambió la contraseña, cerrar sesión en TODOS los dispositivos y pestañas**
        if ($request->filled('password')) {
            auth()->logout();

            // **Eliminar TODAS las sesiones activas en la base de datos (si usas Laravel con sesiones en DB)**
            \DB::table('sessions')->where('user_id', $user->rut)->delete();

            // **Invalidar la sesión actual, regenerar token y forzar logout**
            session()->flush();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response('<script>
        // Forzar cierre de sesión en todas las pestañas
        localStorage.setItem("force_logout", "true");
        sessionStorage.clear(); // Eliminar cualquier sesión almacenada

        // Limpiar cookies forzando su expiración
        document.cookie.split(";").forEach(function(c) {
            document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
        });

        // Redirigir al login con parámetro
        if (window.top !== window.self) {
            window.top.location.href = "'.route('login', ['status' => 'password_changed']).'";
        } else {
            window.location.href = "'.route('login', ['status' => 'password_changed']).'";
        }
    </script>');
        }

        return redirect()->route('profile.show')->with('success', 'Perfil actualizado exitosamente recarga la pagina.');


    }

}
