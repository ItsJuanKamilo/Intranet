@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content')
    @include('components._page')

    <div class="card mt-4 card-navy card-outline">
        <div class="card-header">
            <h3 class="card-title">Editar Usuario</h3>
        </div>

        <form action="{{ route('admin.users.update', $user->rut) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">

                <!-- Fila 1: Nombre, Segundo Nombre, Apellido, Segundo Apellido, Correo Electrónico -->
                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="second_name" class="form-label">Segundo Nombre <span class="text-muted">(Opcional)</span></label>
                        <input type="text" id="second_name" name="second_name" class="form-control" value="{{ old('second_name', $user->second_name) }}">
                        @error('second_name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="surname_1" class="form-label">Apellido</label>
                        <input type="text" id="surname_1" name="surname_1" class="form-control" value="{{ old('surname_1', $user->surname_1) }}" required>
                        @error('surname_1')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="surname_2" class="form-label">Segundo Apellido</label>
                        <input type="text" id="surname_2" name="surname_2" class="form-control" value="{{ old('surname_2', $user->surname_2) }}" required>
                        @error('surname_2')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Fila 2: RUT, Fecha Nacimiento, Local, Teléfono, Anexo -->
                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <label for="rut" class="form-label">RUT</label>
                        <input type="text" id="rut" name="rut" class="form-control text-center"
                               value="{{ old('rut', $formattedRut ?? $user->rut) }}"
                               placeholder="XX.XXX.XXX-X" maxlength="12" readonly required>
                        @error('rut')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="date_birthday" class="form-label">Fecha Nacimiento</label>
                        <input type="date" id="date_birthday" name="date_birthday" class="form-control"
                               value="{{ old('date_birthday', $user->date_birthday ? $user->date_birthday->format('Y-m-d') : '') }}" required>
                        @error('date_birthday')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="local" class="form-label">Local</label>
                        <select id="local" name="local" class="form-control" required>
                            <option value="" disabled {{ empty(old('local', $user->local)) ? 'selected' : '' }}>Seleccionar Local</option>
                            <option value="HUECHURABA" {{ (old('local', $user->local) == 'HUECHURABA') ? 'selected' : '' }}>HUECHURABA</option>
                            <option value="EXEQUIEL" {{ (old('local', $user->local) == 'EXEQUIEL') ? 'selected' : '' }}>EXEQUIEL</option>
                        </select>
                        @error('local')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input type="text" id="phone" name="phone" class="form-control"
                               value="{{ old('phone', $phoneWithoutPrefix) }}"
                               placeholder="9XXXXXXXX" maxlength="9"
                               pattern="^\d{9}$"
                               title="Debe ingresar un número de 9 dígitos"
                               onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('phone') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>


                    <div class="col-md-2">
                        <label for="annex" class="form-label">Anexo <span class="text-muted">(Opcional)</span></label>
                        <input type="text" id="annex" name="annex" class="form-control"
                               value="{{ old('annex', $user->annex) }}">
                        @error('annex')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Fila 3: Rol, Fecha de Admisión, Foto de Perfil -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="role_id" class="form-label">Rol</label>
                        <select id="role_id" name="role_id" class="form-control" required>
                            <option value="" disabled {{ !$user->role_id ? 'selected' : '' }}>Seleccionar rol</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }} required>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="role_description" class="form-label">Descripcion del rol</label>
                        <input type="text" id="role_description" name="role_description" class="form-control"
                               value="{{ old('role_description', $user->role_description) }}" required>
                        @error('role_description')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="date_admission" class="form-label">Fecha de Admisión</label>
                        <input type="date" id="date_admission" name="date_admission" class="form-control"
                               value="{{ old('date_admission', $user->date_admission ? $user->date_admission->format('Y-m-d') : '') }}"
                               required>
                        @error('date_admission')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- para la vista previa -->
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                    <script src="https://kit.fontawesome.com/xxxxxxxxxx.js" crossorigin="anonymous"></script>
                    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>


                    <!-- Columna: Campo para Foto de Perfil (Botón) -->
                    <div class="col-md-2">
                        <label for="profile_photo_path" class="form-label">
                            Foto de Perfil <span class="text-muted">(Opcional)</span>
                        </label>
                        <div class="d-flex align-items-center">
                            <label class="btn btn-primary bg-navy btn-sm mb-0" for="profile_photo_path" style="height: 38px; line-height: 38px; padding: 0 12px;">
                                <i class="fas fa-upload"></i> Añadir Foto
                            </label>
                            <!-- Input file oculto -->
                            <input type="file" id="profile_photo_path" name="profile_photo_path" class="d-none" accept="image/*">
                            <!-- Campo oculto para almacenar la imagen recortada en Base64 -->
                            <input type="hidden" name="cropped_image" id="cropped_image">
                        </div>

                        <small class="form-text text-muted">
                            Sube una imagen PNG o JPG (máx. 2MB).
                        </small>
                        @error('profile_photo_path')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Columna: Vista Previa Actual -->
                    <div class="col-md-2">
                        <div>
                            <img id="currentPhotoPreview"
                                 src="{{ $user->adminlte_image() }}"
                                 alt="Foto Actual"
                                 class="img-thumbnail"
                                 style="width: 80px; height: auto;">
                        </div>
                    </div>



                    <!-- Modal de Recorte -->
                    <div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="cropModalLabel">Recortar Imagen</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body p-2 d-flex justify-content-center align-items-start">
                                    <img id="cropImage" src="" alt="Recorte" class="img-fluid">
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <button type="button" class="btn btn-primary" id="cropButton">Guardar Recorte</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelCrop">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de Vista Previa (opcional, si deseas mostrar el recorte antes de guardarlo) -->
                    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="previewModalLabel">Vista Previa del Recorte</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body d-flex justify-content-center">
                                    <img id="previewImage" src="" alt="Vista Previa" class="img-fluid">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Evitar que se muestre fondo por defecto de Cropper -->
                    <style>
                        .cropper-modal {
                            display: none !important;
                        }
                    </style>

                    <!-- JavaScript para el manejo de recorte y vista previa -->
                    <script>
                        let cropper;
                        let croppedDataURL = ''; // Almacena la imagen recortada

                        const inputFile    = document.getElementById('profile_photo_path');
                        const cropImage    = document.getElementById('cropImage');
                        const cropButton   = document.getElementById('cropButton');
                        const previewImage = document.getElementById('previewImage');
                        const cancelCrop   = document.getElementById('cancelCrop');
                        const cropModalEl  = document.getElementById('cropModal');
                        const cropModal    = new bootstrap.Modal(cropModalEl, {
                            backdrop: 'static',
                            keyboard: false
                        });

                        // Inicializa la vista previa con la foto actual del usuario
                        function initEditMode() {
                            // Si existe una foto actual, se carga en el preview
                            // "existingProfilePhotoUrl" se debe definir en el controlador o a través de un accesor en el modelo
                            if (typeof existingProfilePhotoUrl !== 'undefined' && existingProfilePhotoUrl) {
                                document.getElementById('currentPhotoPreview').src = existingProfilePhotoUrl;
                            }
                        }
                        window.addEventListener('DOMContentLoaded', initEditMode);

                        // Al seleccionar una imagen
                        inputFile.addEventListener('change', (e) => {
                            const files = e.target.files;
                            if (files && files.length > 0) {
                                const reader = new FileReader();
                                reader.onload = () => {
                                    cropImage.src = reader.result;
                                    if (cropper) {
                                        cropper.destroy();
                                    }
                                    cropImage.onload = () => {
                                        cropper = new Cropper(cropImage, {
                                            aspectRatio: 1,          // Recorte cuadrado
                                            viewMode: 1,
                                            autoCropArea: 1,
                                            background: false,
                                            modal: false,
                                            minContainerWidth: 500,
                                            minContainerHeight: 300
                                        });
                                    };
                                    cropModal.show();
                                };
                                reader.readAsDataURL(files[0]);
                            }
                        });

                        // Guardar recorte al hacer clic en "Guardar Recorte"
                        cropButton.addEventListener('click', () => {
                            if (cropper) {
                                const canvas = cropper.getCroppedCanvas({
                                    width: 300,
                                    height: 300,
                                    imageSmoothingQuality: 'high'
                                });
                                // Genera el JPEG con calidad máxima (1)
                                croppedDataURL = canvas.toDataURL('image/jpeg', 1);
                                // Actualiza el modal de vista previa (si se usa)
                                previewImage.src = croppedDataURL;
                                // Actualiza la previsualización en la vista de edición
                                document.getElementById('currentPhotoPreview').src = croppedDataURL;
                                // Guarda el recorte en el campo oculto para enviarlo al servidor
                                document.getElementById('cropped_image').value = croppedDataURL;
                                cropModal.hide();
                            }
                        });


                        // Cancelar recorte y limpiar estado
                        cancelCrop.addEventListener('click', () => {
                            cropImage.src = '';
                            if (cropper) {
                                cropper.destroy();
                                cropper = null;
                            }
                            inputFile.value = '';
                        });
                    </script>

                </div>

                <!-- Datos Personales -->
                <h5 class="text-center text-gray">Datos Personales</h5>
                <hr class="my-1">

                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <label for="gender" class="form-label">Género</label>
                        <select id="gender" name="gender" class="form-control card-navy card-outline" >
                            <option value="" disabled selected>Seleccionar</option>
                            <option value="Masculino" {{ old('gender',$user->gender) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="Femenino" {{ old('gender',$user->gender) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                            <option value="Prefiero No Decirlo" {{ old('gender',$user->gender) == 'Prefiero No Decirlo' ? 'selected' : '' }}>Prefiero No Decirlo</option>
                        </select>
                        @error('gender') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="marital_status" class="form-label">Estado Civil</label>
                        <select id="marital_status" name="marital_status" class="form-control card-navy card-outline" >
                            <option value="" disabled selected>Seleccionar</option>
                            <option value="Soltero/a" {{ old('marital_status',$user->marital_status) == 'Soltero/a' ? 'selected' : '' }}>Soltero/a</option>
                            <option value="Casado/a" {{ old('marital_status',$user->marital_status) == 'Casado/a' ? 'selected' : '' }}>Casado/a</option>
                            <option value="Divorciado/a" {{ old('marital_status',$user->marital_status) == 'Divorciado/a' ? 'selected' : '' }}>Divorciado/a</option>
                            <option value="Viudo/a" {{ old('marital_status',$user->marital_status) == 'Viudo/a' ? 'selected' : '' }}>Viudo/a</option>
                            <option value="Unión Civil" {{ old('marital_status',$user->marital_status) == 'Unión Civil' ? 'selected' : '' }}>Unión Civil</option>
                        </select>
                        @error('marital_status') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="nationality" class="form-label">Nacionalidad</label>
                        <select id="nationality" name="nationality" class="form-control card-navy card-outline" >
                            <option value="" disabled selected>Seleccionar</option>
                            <option value="Argentina" {{ old('nationality',$user->nationality) == 'Argentina' ? 'selected' : '' }}>Argentina</option>
                            <option value="Bolivia" {{ old('nationality',$user->nationality) == 'Bolivia' ? 'selected' : '' }}>Bolivia</option>
                            <option value="Brasil" {{ old('nationality',$user->nationality) == 'Brasil' ? 'selected' : '' }}>Brasil</option>
                            <option value="Chile" {{ old('nationality',$user->nationality) == 'Chile' ? 'selected' : '' }}>Chile</option>
                            <option value="Colombia" {{ old('nationality',$user->nationality) == 'Colombia' ? 'selected' : '' }}>Colombia</option>
                            <option value="Costa Rica" {{ old('nationality',$user->nationality) == 'Costa Rica' ? 'selected' : '' }}>Costa Rica</option>
                            <option value="Cuba" {{ old('nationality',$user->nationality) == 'Cuba' ? 'selected' : '' }}>Cuba</option>
                            <option value="Ecuador" {{ old('nationality',$user->nationality) == 'Ecuador' ? 'selected' : '' }}>Ecuador</option>
                            <option value="El Salvador" {{ old('nationality',$user->nationality) == 'El Salvador' ? 'selected' : '' }}>El Salvador</option>
                            <option value="España" {{ old('nationality',$user->nationality) == 'España' ? 'selected' : '' }}>España</option>
                            <option value="Guatemala" {{ old('nationality',$user->nationality) == 'Guatemala' ? 'selected' : '' }}>Guatemala</option>
                            <option value="Honduras" {{ old('nationality',$user->nationality) == 'Honduras' ? 'selected' : '' }}>Honduras</option>
                            <option value="México" {{ old('nationality',$user->nationality) == 'México' ? 'selected' : '' }}>México</option>
                            <option value="Nicaragua" {{ old('nationality',$user->nationality) == 'Nicaragua' ? 'selected' : '' }}>Nicaragua</option>
                            <option value="Panamá" {{ old('nationality',$user->nationality) == 'Panamá' ? 'selected' : '' }}>Panamá</option>
                            <option value="Paraguay" {{ old('nationality',$user->nationality) == 'Paraguay' ? 'selected' : '' }}>Paraguay</option>
                            <option value="Perú" {{ old('nationality',$user->nationality) == 'Perú' ? 'selected' : '' }}>Perú</option>
                            <option value="Puerto Rico" {{ old('nationality',$user->nationality) == 'Puerto Rico' ? 'selected' : '' }}>Puerto Rico</option>
                            <option value="República Dominicana" {{ old('nationality',$user->nationality) == 'República Dominicana' ? 'selected' : '' }}>República Dominicana</option>
                            <option value="Uruguay" {{ old('nationality',$user->nationality) == 'Uruguay' ? 'selected' : '' }}>Uruguay</option>
                            <option value="Venezuela" {{ old('nationality',$user->nationality) == 'Venezuela' ? 'selected' : '' }}>Venezuela</option>
                        </select>
                        @error('nationality') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="personal_phone" class="form-label">Teléfono Personal</label>
                        <input type="text" id="phone" name="personal_phone" class="form-control"
                               value="{{ old('personal_phone',substr($user->personal_phone, 2)) }}" placeholder="9XXXXXXXX" maxlength="9"
                               pattern="^9\d{8}$" title="Debe ingresar un número de 9 dígitos que empiece con 9"
                               onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')" >
                        @error('personal_phone') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="personal_email" class="form-label">Correo Personal</label>
                        <input type="email" id="personal_email" name="personal_email" class="form-control" value="{{ old('personal_email',$user->personal_email) }}" >
                        @error('personal_email') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <script src="{{ asset('assets/locations.js') }}"></script>
                <!-- Dirección -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="personal_address_region" class="form-label">Región</label>
                        <select id="personal_address_region" name="personal_address_region" class="form-control card-navy card-outline" >
                            <option value="" disabled selected>Seleccionar</option>
                            <option value="Arica y Parinacota" {{ old('personal_address_region', $user->personal_address_region) == 'Arica y Parinacota' ? 'selected' : '' }}>Arica y Parinacota</option>
                            <option value="Tarapacá" {{ old('personal_address_region', $user->personal_address_region) == 'Tarapacá' ? 'selected' : '' }}>Tarapacá</option>
                            <option value="Antofagasta" {{ old('personal_address_region', $user->personal_address_region) == 'Antofagasta' ? 'selected' : '' }}>Antofagasta</option>
                            <option value="Atacama" {{ old('personal_address_region', $user->personal_address_region) == 'Atacama' ? 'selected' : '' }}>Atacama</option>
                            <option value="Coquimbo" {{ old('personal_address_region', $user->personal_address_region) == 'Coquimbo' ? 'selected' : '' }}>Coquimbo</option>
                            <option value="Valparaíso" {{ old('personal_address_region', $user->personal_address_region) == 'Valparaíso' ? 'selected' : '' }}>Valparaíso</option>
                            <option value="Metropolitana de Santiago" {{ old('personal_address_region', $user->personal_address_region) == 'Metropolitana de Santiago' ? 'selected' : '' }}>Metropolitana de Santiago</option>
                            <option value="O'Higgins" {{ old('personal_address_region', $user->personal_address_region) == "O'Higgins" ? 'selected' : '' }}>O'Higgins</option>
                            <option value="Maule" {{ old('personal_address_region', $user->personal_address_region) == 'Maule' ? 'selected' : '' }}>Maule</option>
                            <option value="Ñuble" {{ old('personal_address_region', $user->personal_address_region) == 'Ñuble' ? 'selected' : '' }}>Ñuble</option>
                            <option value="Biobío" {{ old('personal_address_region', $user->personal_address_region) == 'Biobío' ? 'selected' : '' }}>Biobío</option>
                            <option value="La Araucanía" {{ old('personal_address_region', $user->personal_address_region) == 'La Araucanía' ? 'selected' : '' }}>La Araucanía</option>
                            <option value="Los Ríos" {{ old('personal_address_region', $user->personal_address_region) == 'Los Ríos' ? 'selected' : '' }}>Los Ríos</option>
                            <option value="Los Lagos" {{ old('personal_address_region', $user->personal_address_region) == 'Los Lagos' ? 'selected' : '' }}>Los Lagos</option>
                            <option value="Aysén" {{ old('personal_address_region', $user->personal_address_region) == 'Aysén' ? 'selected' : '' }}>Aysén</option>
                            <option value="Magallanes y la Antártica Chilena" {{ old('personal_address_region', $user->personal_address_region) == 'Magallanes y la Antártica Chilena' ? 'selected' : '' }}>Magallanes y la Antártica Chilena</option>
                        </select>
                        @error('personal_address_region')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>




                    <div class="col-md-3">
                        <label for="personal_address_city" class="form-label">Ciudad/Comuna</label>
                        <select id="personal_address_city" name="personal_address_city" class="form-control card-navy card-outline" data-selected="{{ $user->personal_address_city }}">
                            <option value="" disabled selected>Seleccionar</option>
                        </select>
                    </div>




                    <div class="col-md-3">
                        <label for="personal_address_street" class="form-label">Calle</label>
                        <input type="text" id="personal_address_street" name="personal_address_street" class="form-control" value="{{ old('personal_address_street',$user->personal_address_street) }}">
                        @error('personal_address_street') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="personal_address_apartment" class="form-label">Departamento/Casa</label>
                        <input type="text" id="personal_address_apartment" name="personal_address_apartment" class="form-control" value="{{ old('personal_address_apartment',$user->personal_address_apartment) }}" >
                        @error('personal_address_apartment') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Información Bancaria -->
                <h5 class="text-center text-gray">Informacion Bancaria</h5>
                <hr class="my-1">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="bank_name" class="form-label">Banco</label>
                        <select id="bank_name" name="bank_name" class="form-control card-navy card-outline" >
                            <option value="" disabled selected>Seleccionar</option>

                            <!-- Bancos Tradicionales -->
                            <option value="Banco de Chile" {{ old('bank_name',$user->bank_name) == 'Banco de Chile' ? 'selected' : '' }}>Banco de Chile</option>
                            <option value="Banco Internacional" {{ old('bank_name',$user->bank_name) == 'Banco Internacional' ? 'selected' : '' }}>Banco Internacional</option>
                            <option value="Scotiabank" {{ old('bank_name',$user->bank_name) == 'Scotiabank' ? 'selected' : '' }}>Scotiabank</option>
                            <option value="BancoEstado" {{ old('bank_name',$user->bank_name) == 'BancoEstado' ? 'selected' : '' }}>BancoEstado</option>
                            <option value="Banco BICE" {{ old('bank_name',$user->bank_name) == 'Banco BICE' ? 'selected' : '' }}>Banco BICE</option>
                            <option value="HSBC Chile" {{ old('bank_name',$user->bank_name) == 'HSBC Chile' ? 'selected' : '' }}>HSBC Chile</option>
                            <option value="Banco Santander" {{ old('bank_name',$user->bank_name) == 'Banco Santander' ? 'selected' : '' }}>Banco Santander</option>
                            <option value="Itaú" {{ old('bank_name',$user->bank_name) == 'Itaú' ? 'selected' : '' }}>Itaú</option>
                            <option value="Banco Security" {{ old('bank_name',$user->bank_name) == 'Banco Security' ? 'selected' : '' }}>Banco Security</option>
                            <option value="Banco Falabella" {{ old('bank_name',$user->bank_name) == 'Banco Falabella' ? 'selected' : '' }}>Banco Falabella</option>
                            <option value="Banco Consorcio" {{ old('bank_name',$user->bank_name) == 'Banco Consorcio' ? 'selected' : '' }}>Banco Consorcio</option>
                            <option value="Banco Ripley" {{ old('bank_name',$user->bank_name) == 'Banco Ripley' ? 'selected' : '' }}>Banco Ripley</option>
                            <option value="Banco BCI" {{ old('bank_name',$user->bank_name) == 'Banco BCI' ? 'selected' : '' }}>Banco BCI</option>
                            <option value="Banco Edwards" {{ old('bank_name',$user->bank_name) == 'Banco Edwards' ? 'selected' : '' }}>Banco Edwards</option>
                            <option value="Cooperativa Coopeuch" {{ old('bank_name',$user->bank_name) == 'Cooperativa Coopeuch' ? 'selected' : '' }}>Cooperativa Coopeuch</option>

                            <!-- Bancos Digitales y Cuentas de Prepago -->
                            <option value="Banco BTG Pactual" {{ old('bank_name') == 'Banco BTG Pactual' ? 'selected' : '' }}>Banco BTG Pactual</option>
                            <option value="Tenpo" {{ old('bank_name',$user->bank_name) == 'Tenpo' ? 'selected' : '' }}>Tenpo</option>
                            <option value="Mach" {{ old('bank_name',$user->bank_name) == 'Mach' ? 'selected' : '' }}>Mach</option>
                            <option value="Chek" {{ old('bank_name',$user->bank_name) == 'Chek' ? 'selected' : '' }}>Chek</option>
                            <option value="Luka" {{ old('bank_name',$user->bank_name) == 'Luka' ? 'selected' : '' }}>Luka</option>
                            <option value="Mercado Pago" {{ old('bank_name',$user->bank_name) == 'Mercado Pago' ? 'selected' : '' }}>Mercado Pago</option>
                            <option value="Fpay" {{ old('bank_name',$user->bank_name) == 'Fpay' ? 'selected' : '' }}>Fpay</option>
                        </select>
                        @error('bank_name') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>


                    <div class="col-md-3">
                        <label for="account_type" class="form-label">Tipo de Cuenta</label>
                        <select id="account_type" name="account_type" class="form-control card-navy card-outline" >
                            <option value="" disabled selected>Seleccionar</option>
                            <option value="Cuenta Corriente" {{ old('account_type',$user->account_type) == 'Cuenta Corriente' ? 'selected' : '' }}>Cuenta Corriente</option>
                            <option value="Cuenta Vista" {{ old('account_type',$user->account_type) == 'Cuenta Vista' ? 'selected' : '' }}>Cuenta Vista</option>
                            <option value="Cuenta RUT" {{ old('account_type',$user->account_type) == 'Cuenta RUT' ? 'selected' : '' }}>Cuenta RUT</option>
                            <option value="Cuenta de Ahorro" {{ old('account_type',$user->account_type) == 'Cuenta de Ahorro' ? 'selected' : '' }}>Cuenta de Ahorro</option>
                            <option value="Cuenta de Cheques" {{ old('account_type',$user->account_type) == 'Cuenta de Cheques' ? 'selected' : '' }}>Cuenta de Cheques</option>
                            <option value="Cuenta Digital" {{ old('account_type',$user->account_type) == 'Cuenta Digital' ? 'selected' : '' }}>Cuenta Digital</option>
                            <option value="Tarjeta de Prepago" {{ old('account_type',$user->account_type) == 'Tarjeta de Prepago' ? 'selected' : '' }}>Tarjeta de Prepago</option>
                        </select>
                        @error('account_type') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>


                    <div class="col-md-3">
                        <label for="salary_bank_account" class="form-label">N.º de cuenta</label>
                        <input type="text" id="salary_bank_account" name="salary_bank_account" class="form-control" value="{{ old('salary_bank_account',$user->salary_bank_account) }}"
                               maxlength="20" title="Numero de cuenta" >
                        @error('salary_bank_account') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Salud y Pensiones -->
                <h5 class="text-center text-gray">Otros</h5>
                <hr class="my-1">
                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <label for="health_insurance" class="form-label">Seguro de Salud</label>
                        <select id="health_insurance" name="health_insurance" class="form-control card-navy card-outline" >
                            <option value="" disabled selected>Seleccionar</option>
                            <option value="FONASA" {{ old('health_insurance',$user->health_insurance) == 'FONASA' ? 'selected' : '' }}>FONASA</option>
                            <option value="Banmédica" {{ old('health_insurance',$user->salary_bank_account) == 'Banmédica' ? 'selected' : '' }}>Banmédica</option>
                            <option value="Colmena" {{ old('health_insurance',$user->health_insurance) == 'Colmena' ? 'selected' : '' }}>Colmena</option>
                            <option value="Consalud" {{ old('health_insurance',$user->health_insurance) == 'Consalud' ? 'selected' : '' }}>Consalud</option>
                            <option value="Cruz Blanca" {{ old('health_insurance',$user->health_insurance) == 'Cruz Blanca' ? 'selected' : '' }}>Cruz Blanca</option>
                            <option value="Nueva Masvida" {{ old('health_insurance',$user->health_insurance) == 'Nueva Masvida' ? 'selected' : '' }}>Nueva Masvida</option>
                            <option value="Vida Tres" {{ old('health_insurance',$user->health_insurance) == 'Vida Tres' ? 'selected' : '' }}>Vida Tres</option>
                            <option value="Isapre Fundación" {{ old('health_insurance',$user->health_insurance) == 'Isapre Fundación' ? 'selected' : '' }}>Isapre Fundación</option>
                        </select>
                        @error('health_insurance') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="pension_fund" class="form-label">Fondo de Pensiones</label>
                        <select id="pension_fund" name="pension_fund" class="form-control card-navy card-outline" >
                            <option value="" disabled selected>Seleccionar</option>
                            <option value="AFP Uno" {{ old('pension_fund', $user->pension_fund) == 'AFP Uno' ? 'selected' : '' }}>AFP Uno</option>
                            <option value="AFP Capital" {{ old('pension_fund', $user->pension_fund) == 'AFP Capital' ? 'selected' : '' }}>AFP Capital</option>
                            <option value="AFP Cuprum" {{ old('pension_fund', $user->pension_fund) == 'AFP Cuprum' ? 'selected' : '' }}>AFP Cuprum</option>
                            <option value="AFP Habitat" {{ old('pension_fund', $user->pension_fund) == 'AFP Habitat' ? 'selected' : '' }}>AFP Habitat</option>
                            <option value="AFP Modelo" {{ old('pension_fund', $user->pension_fund) == 'AFP Modelo' ? 'selected' : '' }}>AFP Modelo</option>
                            <option value="AFP PlanVital" {{ old('pension_fund', $user->pension_fund) == 'AFP PlanVital' ? 'selected' : '' }}>AFP PlanVital</option>
                            <option value="AFP Provida" {{ old('pension_fund', $user->pension_fund) == 'AFP Provida' ? 'selected' : '' }}>AFP Provida</option>
                            <option value="IPS (Ex INP)" {{ old('pension_fund', $user->pension_fund) == 'IPS (Ex INP)' ? 'selected' : '' }}>IPS (Ex INP)</option>
                        </select>
                        @error('pension_fund')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>



                    <!-- Contacto de Emergencia -->
                    <div class="col-md-2">
                        <label for="emergency_contact" class="form-label">Contacto de Emergencia <span class="text-muted">(Opcional)</span></label>
                        <input type="text" id="emergency_contact" name="emergency_contact" class="form-control" value="{{ old('emergency_contact',$user->emergency_contact) }}" placeholder="Nombre">
                        @error('emergency_contact') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="emergency_phone" class="form-label">Teléfono de Emergencia <span class="text-muted">(Opcional)</span></label>
                        <input type="text" id="emergency_phone" name="emergency_phone" class="form-control"
                               value="{{ old('emergency_phone', $user->emergency_phone) }}"
                               placeholder="569XXXXXXXX" maxlength="15"
                               pattern="^\d{9,14}$"
                               onkeypress="return (event.charCode >= 48 && event.charCode <= 57) || event.charCode === 43">
                        @error('emergency_phone')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>


                </div>

                <div class="row g-3 mb-4">
                    <!-- Redes Sociales -->
                    @php
                        $network = null;
                        $baseUrls = [
                            'linkedin' => "https://linkedin.com/in/",
                            'github' => "https://github.com/",
                            'x' => "https://x.com/",
                            'instagram' => "https://instagram.com/",
                            'facebook' => "https://facebook.com/",
                            'youtube' => "https://youtube.com/@",
                            'tiktok' => "https://www.tiktok.com/@",
                            'portfolio' => ""
                        ];

                        foreach ($baseUrls as $key => $url) {
                            if (!empty($user->professional_social_networks) && strpos($user->professional_social_networks, $url) === 0) {
                                $network = $key;
                                break;
                            }
                        }
                    @endphp

                    <div class="col-md-6">
                        <label for="professional_social_networks" class="form-label">Red Social <span class="text-muted">(Opcional)</span></label>
                        <div class="d-flex">
                            <!-- Select de Redes Sociales -->
                            <select id="professional_social_networks" name="professional_social_networks" class="form-control card-navy card-outline" style="width: 40%;">
                                <option value="" disabled {{ $network ? '' : 'selected' }}>Seleccionar</option>
                                <option value="linkedin" {{ $network == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                                <option value="github" {{ $network == 'github' ? 'selected' : '' }}>GitHub</option>
                                <option value="x" {{ $network == 'x' ? 'selected' : '' }}>X (Twitter)</option>
                                <option value="instagram" {{ $network == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                <option value="facebook" {{ $network == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                <option value="youtube" {{ $network == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                <option value="tiktok" {{ $network == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                                <option value="portfolio" {{ $network == 'portfolio' ? 'selected' : '' }}>Portafolio</option>
                                <option value="other" {{ !$network ? 'selected' : '' }}>Otros (link)</option>
                            </select>


                            <input type="text" id="social_username" name="social_username" class="form-control ms-5 card-navy card-outline"
                                   placeholder="Ingresa tu usuario" value="{{ $network ? str_replace($baseUrls[$network], '', $user->professional_social_networks) : $user->professional_social_networks }}"
                                   style="width: 60%;">
                        </div>

                        <!-- Campo oculto para almacenar la URL final -->
                        <input type="hidden" id="final_social_url" name="professional_social_networks" value="{{ old('professional_social_networks', $user->professional_social_networks) }}">

                        @error('professional_social_networks')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="hobbies_interests" class="form-label">Hobbies e Intereses <span class="text-muted">(Opcional)</span></label>
                        <input type="text" id="hobbies_interests" name="hobbies_interests" class="form-control card-navy card-outline"
                               value="{{ old('hobbies_interests', $user->hobbies_interests) }}" autocomplete="new-password">
                        @error('hobbies_interests')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <!-- Fila 4: Contraseña y Confirmar Contraseña -->
                <hr class="my-4">
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Dejar en blanco para no modificar"
                               autocomplete="new-password">
                        @error('password')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Dejar en blanco para no modificar">
                        @error('password_confirmation')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="float-right">
                    <button type="submit" class="btn btn-primary">
                        Guardar <i class="far fa-save"></i>
                    </button>
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-dark">
                    <i class="fas fa-undo-alt"></i> Volver atrás
                </a>
            </div>
        </form>
    </div>



    <script>

        // Formateo del RUT
        document.getElementById('rut').addEventListener('input', function (e) {
            let value = e.target.value.replace(/[^0-9kK-]/g, ''); // Permitir solo números, K/k y guion
            let parts = value.split('-');
            let rutParte = parts[0].replace(/\D/g, '').substring(0, 8); // Solo 8 dígitos para el RUT
            let dvParte = parts[1] ? parts[1].substring(0, 1).toUpperCase() : ''; // DV con 1 carácter

            let formatted = '';
            if (rutParte.length > 0) formatted = rutParte.substring(0, 2);
            if (rutParte.length > 2) formatted += '.' + rutParte.substring(2, 5);
            if (rutParte.length > 5) formatted += '.' + rutParte.substring(5, 8);

            e.target.value = dvParte ? `${formatted}-${dvParte}` : formatted;
        });

        // manejo de regiones

// manejo de red social
        document.addEventListener('DOMContentLoaded', function () {
            const networkSelect = document.getElementById('professional_social_networks');
            const usernameField = document.getElementById('social_username');
            const finalUrlField = document.getElementById('final_social_url');

            const baseUrls = {
                linkedin: "https://linkedin.com/in/",
                github: "https://github.com/",
                x: "https://x.com/",
                instagram: "https://instagram.com/",
                facebook: "https://facebook.com/",
                youtube: "https://youtube.com/@",
                tiktok: "https://www.tiktok.com/@",
                portfolio: ""
            };

            function updateFinalUrl() {
                const selectedNetwork = networkSelect.value;
                const username = usernameField.value.trim();

                if (baseUrls[selectedNetwork]) {
                    finalUrlField.value = username ? baseUrls[selectedNetwork] + username : "";
                } else {
                    finalUrlField.value = username; // Si es "Otros", el usuario ingresa la URL completa
                }
            }

            // Detectar cambios en el select de redes sociales
            networkSelect.addEventListener('change', function () {
                const selectedNetwork = this.value;

                if (baseUrls[selectedNetwork]) {
                    usernameField.placeholder = "Ingresa tu usuario";
                    usernameField.value = ""; // Limpiar campo al cambiar de red
                    usernameField.disabled = false;
                } else {
                    usernameField.placeholder = "Ingresa tu URL completa";
                    usernameField.value = "";
                    usernameField.disabled = false;
                }

                updateFinalUrl();
            });

            // Detectar cambios en el campo de usuario y actualizar la URL final
            usernameField.addEventListener('input', updateFinalUrl);

            // Asegurar que el campo oculto siempre se envíe correctamente al backend
            document.querySelector('form').addEventListener('submit', function () {
                updateFinalUrl();
            });
        });

    </script>
@endsection
