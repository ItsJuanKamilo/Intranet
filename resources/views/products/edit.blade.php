@extends('adminlte::page')

@section('plugins.Summernote', true)
@section('title', 'Editar Producto')

@section('content')
    @include('components._page')

    <div class="row">
        <div class="col-12">
            <div class="card card-navy card-tabs mt-4">
                <div class="card-header p-0 pt-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="pl-3">
                            <h3 class="card-title m-0">Editar Producto / {{ $product->sku }}</h3>
                        </div>
                        <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="producto-tab" data-toggle="pill" href="#producto"
                                   role="tab">Producto</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="analisis-tab" data-toggle="pill" href="#analisis"
                                   role="tab">Análisis</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-two-tabContent">
                        {{-- Pestaña Producto --}}
                        <div class="tab-pane fade show active" id="producto">
                            <form action="{{ route('products.update', $product->sku) }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  id="form-product">
                                @csrf
                                @method('PUT')

                                <div class="card card-navy card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            Detalles del Producto / {{ $product->sku }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row gx-3">
                                            <div class="col-md-7">
                                                @include('products.components.form_edit')
                                            </div>
                                            <div class="col-md-5">
                                                <h5 class="text-center mb-3">Imágenes del Producto</h5>

                                                <div id="new-image-gallery"
                                                     class="d-flex flex-wrap gap-3 justify-content-center mt-4">
                                                    @for ($i = 0; $i < 8; $i++)
                                                        @php
                                                            $img = $product->images->where('order', $i)->first();
                                                        @endphp

                                                        <div class="image-item"
                                                             data-order="{{ $i }}"
                                                             id="image-{{ $i }}">
                                                            {{-- Índice flotante --}}
                                                            <div class="image-index-label">{{ $i }}</div>

                                                            {{-- Hidden ID --}}
                                                            <input type="hidden"
                                                                   name="image_id[{{ $i }}]"
                                                                   value="{{ $img->id ?? '' }}"
                                                                   class="image-id-input">

                                                            {{-- File input --}}
                                                            <input type="file"
                                                                   name="images[{{ $i }}]"
                                                                   accept="image/*"
                                                                   class="d-none image-upload-input">

                                                            @if($img)
                                                                <img src="{{ $img->url }}"
                                                                     alt="Img {{ $i }}">
                                                                <i class="fas fa-times delete-icon"></i>
                                                            @else
                                                                <span class="plus-icon">+</span>
                                                            @endif
                                                        </div>
                                                    @endfor
                                                </div>

                                                <div class="text-center mt-4">
                                                    <label for="image-upload"
                                                           class="btn btn-primary bg-navy btn-lg px-4 py-2">
                                                        <i class="fas fa-upload"></i> Carga de Imágenes
                                                    </label>
                                                    <input type="file"
                                                           id="image-upload"
                                                           name="images[]"
                                                           accept="image/*"
                                                           multiple
                                                           class="d-none">
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">
                                            Guardar Cambios <i class="far fa-save"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Pestaña Análisis --}}
                        <div class="tab-pane fade" id="analisis">
                            <p>Aquí iría el contenido de la pestaña de análisis.</p>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between align-items-center w-100">
                    <a href="{{ route('products.index') }}" class="btn btn-dark" id="btnVolver">
                        <i class="fas fa-undo-alt"></i> Volver atrás
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        #new-image-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }
        .image-item {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 5px;
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .image-item[draggable="true"] { cursor: move; }
        /* slots vacíos deben mostrar pointer */
        .image-item:not([draggable="true"]) { cursor: pointer; }

        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .image-index-label {
            position: absolute;
            top: 6px;
            left: 6px;
            background: rgba(0,0,0,0.6);
            color: #fff;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 0.25rem;
            z-index: 5;
        }
        .plus-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #007bff;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .delete-icon {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #fff;
            background-color: #dc3545;
            border-radius: 0.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s ease;
            z-index: 5;
        }
        .image-item:hover .delete-icon {
            opacity: 1;
        }
        .delete-icon:hover {
            background-color: rgba(220,53,69,1);
        }
    </style>
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const gallery = document.getElementById('new-image-gallery');
            let draggedEl = null;

            // redimensionar con “contain” sin recorte
            function resizeImageTo800(file) {
                return new Promise(resolve => {
                    const img = new Image();
                    const reader = new FileReader();
                    reader.onload = e => {
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            canvas.width = canvas.height = 800;
                            const ctx = canvas.getContext('2d');
                            ctx.fillStyle = '#fff';
                            ctx.fillRect(0, 0, 800, 800);
                            const scale = Math.min(800 / img.width, 800 / img.height);
                            const w = img.width * scale;
                            const h = img.height * scale;
                            const dx = (800 - w) / 2;
                            const dy = (800 - h) / 2;
                            ctx.drawImage(img, dx, dy, w, h);
                            canvas.toBlob(blob => {
                                const resized = new File([blob], file.name, { type: file.type });
                                resolve(resized);
                            }, file.type);
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            function refreshDraggables() {
                gallery.querySelectorAll('.image-item').forEach(slot => {
                    slot.draggable = !!slot.querySelector('img');
                });
            }

            // click en “+”
            gallery.addEventListener('click', e => {
                if (e.target.classList.contains('plus-icon')) {
                    const slot = e.target.closest('.image-item');
                    slot.querySelector('.image-upload-input')?.click();
                }
            });

            // subida por slot individual
            async function handleSlotUpload(e) {
                const orig = e.target.files[0];
                if (!orig) return;
                // validar .webp
                const ext = orig.name.split('.').pop().toLowerCase();
                if (ext === 'webp') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Formato no permitido',
                        text: 'No se pueden subir imágenes en formato .webp',
                    });
                    e.target.value = '';
                    return;
                }
                const slot = e.target.closest('.image-item');
                const file = await resizeImageTo800(orig);

                slot.querySelector('.plus-icon')?.remove();
                let img = slot.querySelector('img');
                if (!img) {
                    img = document.createElement('img');
                    slot.appendChild(img);
                }
                img.src = URL.createObjectURL(file);

                const dt = new DataTransfer();
                dt.items.add(file);
                e.target.files = dt.files;

                slot.querySelector('.image-id-input').value = '';
                if (!slot.querySelector('.delete-icon')) {
                    const del = document.createElement('i');
                    del.className = 'fas fa-times delete-icon';
                    slot.appendChild(del);
                    del.addEventListener('click', handleDelete);
                }
                refreshDraggables();
            }
            gallery.querySelectorAll('.image-upload-input')
                .forEach(inp => inp.addEventListener('change', handleSlotUpload));

            // subida global
            document.getElementById('image-upload').addEventListener('change', async function() {
                const files = Array.from(this.files);
                const emptySlots = Array.from(gallery.querySelectorAll('.image-item'))
                    .filter(s => !s.querySelector('img'));
                if (!emptySlots.length) {
                    Swal.fire('No hay huecos libres','Elimina alguna antes.','warning');
                    this.value = '';
                    return;
                }
                for (let f of files) {
                    const ext = f.name.split('.').pop().toLowerCase();
                    if (ext === 'webp') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Formato no permitido',
                            text: 'No se pueden subir imágenes en formato .webp',
                        });
                        continue;
                    }
                    if (emptySlots.length === 0) break;
                    const slot = emptySlots.shift();
                    const input = slot.querySelector('.image-upload-input');
                    const resized = await resizeImageTo800(f);
                    const dt = new DataTransfer();
                    dt.items.add(resized);
                    input.files = dt.files;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
                this.value = '';
                refreshDraggables();
            });

            // helper swap/move
            function swapElements(a,b,sel) {
                const n1=a.querySelector(sel), n2=b.querySelector(sel);
                if (n1 && n2) { a.appendChild(n2); b.appendChild(n1); }
                else if (n1 && !n2) b.appendChild(n1);
                else if (!n1 && n2) a.appendChild(n2);
            }
            // borrar imagen
            function handleDelete(e){
                e.stopPropagation();
                const slot=e.currentTarget.closest('.image-item');
                slot.querySelectorAll('img,.delete-icon').forEach(x=>x.remove());
                slot.querySelector('.image-id-input').value='';
                slot.querySelector('.image-upload-input').value='';
                if(!slot.querySelector('.plus-icon')){
                    const plus=document.createElement('span');
                    plus.className='plus-icon'; plus.textContent='+';
                    slot.appendChild(plus);
                }
                refreshDraggables();
            }
            gallery.querySelectorAll('.delete-icon')
                .forEach(ic=>ic.addEventListener('click', handleDelete));

            refreshDraggables();
            gallery.querySelectorAll('.image-item').forEach(item=>{
                item.addEventListener('dragstart', e=>{
                    if(!item.draggable) return;
                    draggedEl=item; item.style.opacity='0.5';
                    e.dataTransfer.effectAllowed='move';
                });
                item.addEventListener('dragend', ()=>draggedEl&&(draggedEl.style.opacity='1', draggedEl=null));
                item.addEventListener('dragover', e=>{ e.preventDefault(); e.dataTransfer.dropEffect='move'; });
                item.addEventListener('drop', function(e){
                    e.preventDefault(); if(!draggedEl||draggedEl===this) return;
                    ['img','.delete-icon','.image-id-input','.image-upload-input']
                        .forEach(sel=>swapElements(draggedEl,this,sel));
                    gallery.querySelectorAll('.image-item').forEach(slot=>{
                        if(!slot.querySelector('img')){
                            if(!slot.querySelector('.plus-icon')){
                                const plus=document.createElement('span');
                                plus.className='plus-icon'; plus.textContent='+';
                                slot.appendChild(plus);
                            }
                        } else slot.querySelector('.plus-icon')?.remove();
                    });
                    gallery.querySelectorAll('.image-item').forEach((el,idx)=>{
                        el.dataset.order=idx;
                        el.querySelector('.image-index-label').innerText=idx;
                        el.querySelector('.image-id-input').name=`image_id[${idx}]`;
                        el.querySelector('.image-upload-input').name=`images[${idx}]`;
                    });
                    refreshDraggables();
                });
            });

            // animar botón guardar
            document.getElementById('form-product')
                .addEventListener('submit', ()=>{
                    const btn = document.querySelector('#form-product button[type=submit]');
                    if (!btn) return;
                    btn.disabled = true;
                    btn.classList.replace('btn-primary','btn-secondary');
                    btn.innerHTML =
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
                });

            document.getElementById('btnVolver').addEventListener('click', e=>{
                e.preventDefault();
                window.parent.postMessage({action:'closeIframeTab',tabId:window.name},'*');
                window.location.href='{{ route("products.index") }}';
            });
            $('#summernote').summernote({
                height:150,
                toolbar:[
                    ['style',['style']],['font',['bold','underline','clear']],
                    ['color',['color']],['para',['ul','ol','paragraph']],
                    ['insert',['link','picture']],['view',['fullscreen','codeview','help']]
                ]
            });
        });
    </script>
@endpush
