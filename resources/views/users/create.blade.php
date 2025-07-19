@extends('adminlte::page')

@section('title', 'Crear Usuario')

@section('content')

    <div class="card mt-4 card-navy card-outline">
        <div class="card-header">
            <h3 class="card-title">Crear Usuario</h3>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <!-- Fila 1: Nombre, Segundo Nombre, Apellido, Segundo Apellido, Correo Electrónico -->
                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <label for="name" class="form-label">Nombre</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="second_name" class="form-label">Segundo Nombre <span class="text-muted">(Opcional)</span></label>
                        <input type="text" id="second_name" name="second_name" class="form-control" value="{{ old('second_name') }}">
                        @error('second_name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="surname_1" class="form-label">Apellido</label>
                        <input type="text" id="surname_1" name="surname_1" class="form-control" value="{{ old('surname_1') }}" required>
                        @error('surname_1')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="surname_2" class="form-label">Segundo Apellido</label>
                        <input type="text" id="surname_2" name="surname_2" class="form-control" value="{{ old('surname_2') }}" required>
                        @error('surname_2')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Fila 2: RUT, Fecha Nacimiento, Local, Teléfono, Anexo -->
                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <label for="rut" class="form-label">RUT</label>
                        <input type="text" id="rut" name="rut" class="form-control text-center" value="{{ session('rut_formateado', old('rut')) }}" placeholder="XX.XXX.XXX-X" maxlength="12" required>
                        @error('rut')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="date_birthday" class="form-label">Fecha Nacimiento</label>
                        <input type="date" id="date_birthday" name="date_birthday" class="form-control" value="{{ old('date_birthday') }}" required>
                        @error('date_birthday')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="local" class="form-label">Local</label>
                        <select id="local" name="local" class="form-control card-navy card-outline" required>
                            <option value="" disabled selected>Seleccionar</option>
                            <option value="HUECHURABA" {{ old('local') == 'HUECHURABA' ? 'selected' : '' }}>HUECHURABA</option>
                            <option value="EXEQUIEL" {{ old('local') == 'EXEQUIEL' ? 'selected' : '' }}>EXEQUIEL</option>
                        </select>
                        @error('local')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input type="text" id="phone" name="phone" class="form-control"
                               value="{{ old('phone') }}" placeholder="9XXXXXXXX" maxlength="9"
                               pattern="^9\d{8}$" title="Debe ingresar un número de 9 dígitos que empiece con 9"
                               onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')" >
                        @error('phone') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>



                    <div class="col-md-2">
                        <label for="annex" class="form-label">Anexo <span class="text-muted">(Opcional)</span></label>
                        <input type="text" id="annex" name="annex" class="form-control" value="{{ old('annex') }}">
                        @error('annex')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Fila 3: Rol, Fecha de Admisión, Foto de Perfil -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="role_id" class="form-label">Rol</label>
                        <select id="role_id" name="role_id" class="form-control card-navy card-outline" required>
                            <option value="" disabled selected>Seleccionar</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
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
                        <input type="text" id="role_description" name="role_description" class="form-control" value="{{ old('role_description') }}" required>
                        @error('role_description')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="date_admission" class="form-label">Fecha de Admisión</label>
                        <input type="date" id="date_admission" name="date_admission" class="form-control" value="{{ date('Y-m-d') }}" required>
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
                    <div class="col-12 col-md-2 mb-1">
                        <label for="profile_photo_path" class="form-label">
                            Foto de Perfil <span class="text-muted">(Opcional)</span>
                        </label>
                        <div class="d-flex align-items-center">
                            <label class="btn btn-primary bg-navy btn-sm" for="profile_photo_path" style="height: 38px; line-height: 38px; padding: 0 12px;">
                                <i class="fas fa-upload"></i> Añadir Foto
                            </label>
                            <!-- Input file oculto -->
                            <input type="file" id="profile_photo_path" name="profile_photo_path" class="d-none" accept="image/*">
                            <!-- Campo oculto para almacenar la imagen recortada en Base64 -->
                            <input type="hidden" name="cropped_image" id="cropped_image" value="{{ old('cropped_image') }}">
                        </div>
                        <small class="form-text text-muted">
                            Sube una imagen PNG o JPG (máx. 2MB).
                        </small>
                        @error('profile_photo_path')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Columna: Vista Previa Actual -->
                    <div class="col-12 col-md-2 mb-1 text-center">
                        <div>
                            @if(old('cropped_image'))
                                <img id="currentPhotoPreview"
                                     src="{{ old('cropped_image') }}"
                                     alt="Foto Recortada"
                                     class="img-thumbnail"
                                     style="width: 80px; height: auto;">
                            @elseif(isset($user) && $user->profile_photo_path)
                                <img id="currentPhotoPreview"
                                     src="{{ $user->adminlte_image() }}"
                                     alt="Foto Actual"
                                     class="img-thumbnail"
                                     style="width: 80px; height: auto;">
                            @else
                                <div id="currentPhotoPreview" class="img-thumbnail d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fas fa-times fa-2x text-danger"></i>
                                </div>
                            @endif
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

                        const inputFile          = document.getElementById('profile_photo_path');
                        const cropImage          = document.getElementById('cropImage');
                        const cropButton         = document.getElementById('cropButton');
                        const cancelCrop         = document.getElementById('cancelCrop');
                        const cropModalEl        = document.getElementById('cropModal');
                        const cropModal          = new bootstrap.Modal(cropModalEl, {
                            backdrop: 'static',
                            keyboard: false
                        });
                        // Obtener el contenedor de previsualización que puede ser <img> o <div>
                        let currentPhotoPreview = document.getElementById('currentPhotoPreview');

                        // Inicializa la vista previa con la foto actual (si se pasa "existingProfilePhotoUrl" desde el servidor)
                        function initEditMode() {
                            if (typeof existingProfilePhotoUrl !== 'undefined' && existingProfilePhotoUrl) {
                                // Si el contenedor es un <img>, actualiza su src, de lo contrario, reemplaza su contenido con un <img>
                                if (currentPhotoPreview.tagName.toLowerCase() === 'img') {
                                    currentPhotoPreview.src = existingProfilePhotoUrl;
                                } else {
                                    currentPhotoPreview.innerHTML = `<img src="${existingProfilePhotoUrl}" alt="Foto Actual" class="img-thumbnail" style="width: 80px; height: auto;">`;
                                }
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
                                // Actualiza la previsualización en el contenedor "currentPhotoPreview"
                                if (currentPhotoPreview.tagName.toLowerCase() === 'img') {
                                    currentPhotoPreview.src = croppedDataURL;
                                } else {
                                    currentPhotoPreview.innerHTML = `<img src="${croppedDataURL}" alt="Foto Recortada" class="img-thumbnail" style="width: 80px; height: auto;">`;
                                }
                                // Guarda el recorte en el campo oculto
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
                            <option value="Masculino" {{ old('gender') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="Femenino" {{ old('gender') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                            <option value="Prefiero No Decirlo" {{ old('gender') == 'Prefiero No Decirlo' ? 'selected' : '' }}>Prefiero No Decirlo</option>
                        </select>
                        @error('gender') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="marital_status" class="form-label">Estado Civil</label>
                        <select id="marital_status" name="marital_status" class="form-control card-navy card-outline" >
                            <option value="" disabled selected>Seleccionar</option>
                            <option value="Soltero/a" {{ old('marital_status') == 'Soltero/a' ? 'selected' : '' }}>Soltero/a</option>
                            <option value="Casado/a" {{ old('marital_status') == 'Casado/a' ? 'selected' : '' }}>Casado/a</option>
                            <option value="Divorciado/a" {{ old('marital_status') == 'Divorciado/a' ? 'selected' : '' }}>Divorciado/a</option>
                            <option value="Viudo/a" {{ old('marital_status') == 'Viudo/a' ? 'selected' : '' }}>Viudo/a</option>
                            <option value="Unión Civil" {{ old('marital_status') == 'Unión Civil' ? 'selected' : '' }}>Unión Civil</option>
                        </select>
                        @error('marital_status') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="nationality" class="form-label">Nacionalidad</label>
                        <select id="nationality" name="nationality" class="form-control card-navy card-outline" >
                            <option value="" disabled selected>Seleccionar</option>
                            <option value="Argentina" {{ old('nationality') == 'Argentina' ? 'selected' : '' }}>Argentina</option>
                            <option value="Bolivia" {{ old('nationality') == 'Bolivia' ? 'selected' : '' }}>Bolivia</option>
                            <option value="Brasil" {{ old('nationality') == 'Brasil' ? 'selected' : '' }}>Brasil</option>
                            <option value="Chile" {{ old('nationality') == 'Chile' ? 'selected' : '' }}>Chile</option>
                            <option value="Colombia" {{ old('nationality') == 'Colombia' ? 'selected' : '' }}>Colombia</option>
                            <option value="Costa Rica" {{ old('nationality') == 'Costa Rica' ? 'selected' : '' }}>Costa Rica</option>
                            <option value="Cuba" {{ old('nationality') == 'Cuba' ? 'selected' : '' }}>Cuba</option>
                            <option value="Ecuador" {{ old('nationality') == 'Ecuador' ? 'selected' : '' }}>Ecuador</option>
                            <option value="El Salvador" {{ old('nationality') == 'El Salvador' ? 'selected' : '' }}>El Salvador</option>
                            <option value="España" {{ old('nationality') == 'España' ? 'selected' : '' }}>España</option>
                            <option value="Guatemala" {{ old('nationality') == 'Guatemala' ? 'selected' : '' }}>Guatemala</option>
                            <option value="Honduras" {{ old('nationality') == 'Honduras' ? 'selected' : '' }}>Honduras</option>
                            <option value="México" {{ old('nationality') == 'México' ? 'selected' : '' }}>México</option>
                            <option value="Nicaragua" {{ old('nationality') == 'Nicaragua' ? 'selected' : '' }}>Nicaragua</option>
                            <option value="Panamá" {{ old('nationality') == 'Panamá' ? 'selected' : '' }}>Panamá</option>
                            <option value="Paraguay" {{ old('nationality') == 'Paraguay' ? 'selected' : '' }}>Paraguay</option>
                            <option value="Perú" {{ old('nationality') == 'Perú' ? 'selected' : '' }}>Perú</option>
                            <option value="Puerto Rico" {{ old('nationality') == 'Puerto Rico' ? 'selected' : '' }}>Puerto Rico</option>
                            <option value="República Dominicana" {{ old('nationality') == 'República Dominicana' ? 'selected' : '' }}>República Dominicana</option>
                            <option value="Uruguay" {{ old('nationality') == 'Uruguay' ? 'selected' : '' }}>Uruguay</option>
                            <option value="Venezuela" {{ old('nationality') == 'Venezuela' ? 'selected' : '' }}>Venezuela</option>
                        </select>
                        @error('nationality') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="personal_phone" class="form-label">Teléfono Personal</label>
                        <input type="text" id="personal_phone" name="personal_phone" class="form-control" style="width: 100%;"
                               value="{{ old('personal_phone') }}" placeholder="9XXXXXXXX" maxlength="9"
                               pattern="^9\d{8}$" title="Debe ingresar un número de 9 dígitos que empiece con 9"
                               onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')" >
                        @error('personal_phone') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="personal_email" class="form-label">Correo Personal</label>
                        <input type="email" id="personal_email" name="personal_email" class="form-control" value="{{ old('personal_email') }}" >
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
                            <option value="Arica y Parinacota">Arica y Parinacota</option>
                            <option value="Tarapacá">Tarapacá</option>
                            <option value="Antofagasta">Antofagasta</option>
                            <option value="Atacama">Atacama</option>
                            <option value="Coquimbo">Coquimbo</option>
                            <option value="Valparaíso">Valparaíso</option>
                            <option value="Metropolitana de Santiago">Metropolitana de Santiago</option>
                            <option value="O'Higgins">O'Higgins</option>
                            <option value="Maule">Maule</option>
                            <option value="Ñuble">Ñuble</option>
                            <option value="Biobío">Biobío</option>
                            <option value="La Araucanía">La Araucanía</option>
                            <option value="Los Ríos">Los Ríos</option>
                            <option value="Los Lagos">Los Lagos</option>
                            <option value="Aysén">Aysén</option>
                            <option value="Magallanes y la Antártica Chilena">Magallanes y la Antártica Chilena</option>
                        </select>
                    </div>



                    <div class="col-md-3">
                        <label for="personal_address_city" class="form-label">Ciudad/Comuna</label>
                        <select id="personal_address_city" name="personal_address_city" class="form-control card-navy card-outline" >
                            <option value="" disabled selected>Seleccionar</option>
                        </select>
                    </div>



                    <div class="col-md-3">
                        <label for="personal_address_street" class="form-label">Calle</label>
                        <input type="text" id="personal_address_street" name="personal_address_street" class="form-control" value="{{ old('personal_address_street') }}" >
                        @error('personal_address_street') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="personal_address_apartment" class="form-label">Departamento/Casa</label>
                        <input type="text" id="personal_address_apartment" name="personal_address_apartment" class="form-control" value="{{ old('personal_address_apartment') }}" >
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
                            <option value="Banco de Chile" {{ old('bank_name') == 'Banco de Chile' ? 'selected' : '' }}>Banco de Chile</option>
                            <option value="Banco Internacional" {{ old('bank_name') == 'Banco Internacional' ? 'selected' : '' }}>Banco Internacional</option>
                            <option value="Scotiabank" {{ old('bank_name') == 'Scotiabank' ? 'selected' : '' }}>Scotiabank</option>
                            <option value="BancoEstado" {{ old('bank_name') == 'BancoEstado' ? 'selected' : '' }}>BancoEstado</option>
                            <option value="Banco BICE" {{ old('bank_name') == 'Banco BICE' ? 'selected' : '' }}>Banco BICE</option>
                            <option value="HSBC Chile" {{ old('bank_name') == 'HSBC Chile' ? 'selected' : '' }}>HSBC Chile</option>
                            <option value="Banco Santander" {{ old('bank_name') == 'Banco Santander' ? 'selected' : '' }}>Banco Santander</option>
                            <option value="Itaú" {{ old('bank_name') == 'Itaú' ? 'selected' : '' }}>Itaú</option>
                            <option value="Banco Security" {{ old('bank_name') == 'Banco Security' ? 'selected' : '' }}>Banco Security</option>
                            <option value="Banco Falabella" {{ old('bank_name') == 'Banco Falabella' ? 'selected' : '' }}>Banco Falabella</option>
                            <option value="Banco Consorcio" {{ old('bank_name') == 'Banco Consorcio' ? 'selected' : '' }}>Banco Consorcio</option>
                            <option value="Banco Ripley" {{ old('bank_name') == 'Banco Ripley' ? 'selected' : '' }}>Banco Ripley</option>
                            <option value="Banco BCI" {{ old('bank_name') == 'Banco BCI' ? 'selected' : '' }}>Banco BCI</option>
                            <option value="Banco Edwards" {{ old('bank_name') == 'Banco Edwards' ? 'selected' : '' }}>Banco Edwards</option>
                            <option value="Cooperativa Coopeuch" {{ old('bank_name') == 'Cooperativa Coopeuch' ? 'selected' : '' }}>Cooperativa Coopeuch</option>

                            <!-- Bancos Digitales y Cuentas de Prepago -->
                            <option value="Banco BTG Pactual" {{ old('bank_name') == 'Banco BTG Pactual' ? 'selected' : '' }}>Banco BTG Pactual</option>
                            <option value="Tenpo" {{ old('bank_name') == 'Tenpo' ? 'selected' : '' }}>Tenpo</option>
                            <option value="Mach" {{ old('bank_name') == 'Mach' ? 'selected' : '' }}>Mach</option>
                            <option value="Chek" {{ old('bank_name') == 'Chek' ? 'selected' : '' }}>Chek</option>
                            <option value="Luka" {{ old('bank_name') == 'Luka' ? 'selected' : '' }}>Luka</option>
                            <option value="Mercado Pago" {{ old('bank_name') == 'Mercado Pago' ? 'selected' : '' }}>Mercado Pago</option>
                            <option value="Fpay" {{ old('bank_name') == 'Fpay' ? 'selected' : '' }}>Fpay</option>
                        </select>
                        @error('bank_name') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>


                    <div class="col-md-3">
                        <label for="account_type" class="form-label">Tipo de Cuenta</label>
                        <select id="account_type" name="account_type" class="form-control card-navy card-outline" >
                            <option value="" disabled selected>Seleccionar</option>
                            <option value="Cuenta Corriente" {{ old('account_type') == 'Cuenta Corriente' ? 'selected' : '' }}>Cuenta Corriente</option>
                            <option value="Cuenta Vista" {{ old('account_type') == 'Cuenta Vista' ? 'selected' : '' }}>Cuenta Vista</option>
                            <option value="Cuenta RUT" {{ old('account_type') == 'Cuenta RUT' ? 'selected' : '' }}>Cuenta RUT</option>
                            <option value="Cuenta de Ahorro" {{ old('account_type') == 'Cuenta de Ahorro' ? 'selected' : '' }}>Cuenta de Ahorro</option>
                            <option value="Cuenta de Cheques" {{ old('account_type') == 'Cuenta de Cheques' ? 'selected' : '' }}>Cuenta de Cheques</option>
                            <option value="Cuenta Digital" {{ old('account_type') == 'Cuenta Digital' ? 'selected' : '' }}>Cuenta Digital</option>
                            <option value="Tarjeta de Prepago" {{ old('account_type') == 'Tarjeta de Prepago' ? 'selected' : '' }}>Tarjeta de Prepago</option>
                        </select>
                        @error('account_type') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>


                    <div class="col-md-3">
                        <label for="salary_bank_account" class="form-label">N.º de cuenta</label>
                        <input type="text" id="salary_bank_account" name="salary_bank_account" class="form-control" value="{{ old('salary_bank_account') }}"
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
                            <option value="FONASA" {{ old('health_insurance') == 'FONASA' ? 'selected' : '' }}>FONASA</option>
                            <option value="Banmédica" {{ old('health_insurance') == 'Banmédica' ? 'selected' : '' }}>Banmédica</option>
                            <option value="Colmena" {{ old('health_insurance') == 'Colmena' ? 'selected' : '' }}>Colmena</option>
                            <option value="Consalud" {{ old('health_insurance') == 'Consalud' ? 'selected' : '' }}>Consalud</option>
                            <option value="Cruz Blanca" {{ old('health_insurance') == 'Cruz Blanca' ? 'selected' : '' }}>Cruz Blanca</option>
                            <option value="Nueva Masvida" {{ old('health_insurance') == 'Nueva Masvida' ? 'selected' : '' }}>Nueva Masvida</option>
                            <option value="Vida Tres" {{ old('health_insurance') == 'Vida Tres' ? 'selected' : '' }}>Vida Tres</option>
                            <option value="Isapre Fundación" {{ old('health_insurance') == 'Isapre Fundación' ? 'selected' : '' }}>Isapre Fundación</option>
                        </select>
                        @error('health_insurance') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="pension_fund" class="form-label">Fondo de Pensiones</label>
                        <select id="pension_fund" name="pension_fund" class="form-control card-navy card-outline" >
                            <option value="" disabled selected>Seleccionar</option>
                            <option value="AFP Uno" {{ old('pension_fund') == 'AFP Uno' ? 'selected' : '' }}>AFP Uno</option>
                            <option value="AFP Capital" {{ old('pension_fund') == 'AFP Capital' ? 'selected' : '' }}>AFP Capital</option>
                            <option value="AFP Cuprum" {{ old('pension_fund') == 'AFP Cuprum' ? 'selected' : '' }}>AFP Cuprum</option>
                            <option value="AFP Habitat" {{ old('pension_fund') == 'AFP Habitat' ? 'selected' : '' }}>AFP Habitat</option>
                            <option value="AFP Modelo" {{ old('pension_fund') == 'AFP Modelo' ? 'selected' : '' }}>AFP Modelo</option>
                            <option value="AFP PlanVital" {{ old('pension_fund') == 'AFP PlanVital' ? 'selected' : '' }}>AFP PlanVital</option>
                            <option value="AFP Provida" {{ old('pension_fund') == 'AFP Provida' ? 'selected' : '' }}>AFP Provida</option>
                            <option value="IPS (Ex INP)" {{ old('pension_fund') == 'IPS (Ex INP)' ? 'selected' : '' }}>IPS (Ex INP)</option>
                        </select>
                        @error('pension_fund') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>


                    <!-- Contacto de Emergencia -->
                    <div class="col-md-2">
                        <label for="emergency_contact" class="form-label">Contacto de Emergencia <span class="text-muted">(Opcional)</span></label>
                        <input type="text" id="emergency_contact" name="emergency_contact" class="form-control" value="{{ old('emergency_contact') }}" placeholder="Nombre">
                        @error('emergency_contact') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="emergency_phone" class="form-label">Teléfono de Emergencia <span class="text-muted">(Opcional)</span></label>
                        <input type="text" id="emergency_phone" name="emergency_phone" class="form-control"
                               maxlength="15"
                               value="{{ old('emergency_phone', $user->emergency_phone ?? '') }}"
                               placeholder="56XXXXXXXXX"
                               pattern="^\d{9,14}$"
                               title="Debe ingresar el código de país y el número en el formato 56XXXXXXXXX"
                               onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                               oninput="this.value = this.value.replace(/\D/g, '');">
                        @error('emergency_phone')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>






                    <script>
                        // Función que actualiza el campo oculto con el teléfono completo y ajusta el límite de dígitos
                        function updateFinalPhone() {
                            const countrySelect = document.getElementById('country_select');
                            const phoneInput = document.getElementById('phone_number_input');
                            const finalPhoneInput = document.getElementById('final_phone');

                            // Obtener la opción seleccionada, su código y el máximo de dígitos permitido
                            const selectedOption = countrySelect.options[countrySelect.selectedIndex];
                            const countryCode = selectedOption ? selectedOption.getAttribute('data-code') : '';
                            const maxLength = selectedOption ? selectedOption.getAttribute('data-max') : '';

                            // Establecer el atributo maxlength y el patrón de validación según el país seleccionado
                            if (maxLength) {
                                phoneInput.setAttribute('maxlength', maxLength);
                                phoneInput.setAttribute('pattern', '\\d{' + maxLength + '}');
                            }

                            // Concatenar el código de país y el número ingresado
                            finalPhoneInput.value = countryCode + phoneInput.value;
                        }

                        // Actualiza el teléfono completo cada vez que se cambia el país
                        document.getElementById('country_select').addEventListener('change', updateFinalPhone);
                    </script>


                </div>

                <!-- Redes Profesionales y Hobbies -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="professional_social_networks" class="form-label">Red Social <span class="text-muted">(Opcional)</span></label>
                        <div class="d-flex">
                            <!-- Select de Redes sociales -->
                            <select id="social_network_select" name="social_network_select" class="form-control card-navy card-outline" style="width: 40%;">
                                <option value="" disabled {{ old('social_network_select') ? '' : 'selected' }}>Seleccionar</option>
                                <option value="linkedin" {{ old('social_network_select') == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                                <option value="github" {{ old('social_network_select') == 'github' ? 'selected' : '' }}>GitHub</option>
                                <option value="x" {{ old('social_network_select') == 'x' ? 'selected' : '' }}>X (Twitter)</option>
                                <option value="instagram" {{ old('social_network_select') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                <option value="facebook" {{ old('social_network_select') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                <option value="youtube" {{ old('social_network_select') == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                <option value="tiktok" {{ old('social_network_select') == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                                <option value="portfolio" {{ old('social_network_select') == 'portfolio' ? 'selected' : '' }}>Portafolio</option>
                                <option value="other" {{ old('social_network_select') == 'other' ? 'selected' : '' }}>Otros (link)</option>
                            </select>

                            <!-- Campo para ingresar el usuario o URL -->
                            <input type="text" id="social_username" name="social_username" class="form-control ms-5 card-navy card-outline"
                                   placeholder="Ingresa tu usuario" value="{{ old('social_username') }}" style="width: 60%;">

                            <!-- Campo oculto donde se almacena la URL final que se enviará a la BD -->
                            <input type="hidden" id="final_social_url" name="professional_social_networks" value="{{ old('professional_social_networks') }}">

                            @error('professional_social_networks')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>



                    <div class="col-md-4">
                        <label for="hobbies_interests" class="form-label">Hobbies e Intereses <span class="text-muted">(Opcional)</span></label>
                        <input type="text" id="hobbies_interests" name="hobbies_interests" class="form-control card-navy card-outline"
                               value="{{ old('hobbies_interests') }}" autocomplete="off">
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
                        <input type="password" id="password" name="password" class="form-control" required
                               autocomplete="new-password">
                        @error('password')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
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
        document.addEventListener('DOMContentLoaded', function() {
            const rutInput = document.getElementById('rut');

            rutInput.addEventListener('input', function(e) {
                // Remover cualquier carácter que no sea dígito, 'k' o 'K'
                let value = rutInput.value.replace(/[^0-9kK]/g, '');

                // Limitar a 9 caracteres: 8 dígitos + dígito verificador
                if (value.length > 9) {
                    value = value.slice(0, 9);
                }

                // Separar dígito verificador
                let body = value.slice(0, -1);
                let dv = value.slice(-1).toUpperCase();

                // Dar formato con puntos cada tres dígitos
                let formattedBody = '';
                while(body.length > 3) {
                    formattedBody = '.' + body.slice(-3) + formattedBody;
                    body = body.slice(0, -3);
                }
                formattedBody = body + formattedBody;

                // Actualizar el valor del input con el formato correcto
                rutInput.value = formattedBody + (dv ? '-' + dv : '');
            });
        });




        // Manejo de link red social
            document.addEventListener('DOMContentLoaded', function () {
            const networkSelect = document.getElementById('social_network_select'); // Nuevo ID para evitar conflictos
            const usernameField = document.getElementById('social_username');
            const finalUrlField = document.getElementById('final_social_url'); // Este es el campo oculto que se enviará al backend

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
            usernameField.value = ""; // Limpiar campo
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

                if (finalUrlField.value) {
                    Object.keys(baseUrls).forEach(network => {
                        if (finalUrlField.value.startsWith(baseUrls[network])) {
                            networkSelect.value = network;
                            usernameField.value = finalUrlField.value.replace(baseUrls[network], '');
                        }
                    });
                }
        });
    </script>

@endsection
