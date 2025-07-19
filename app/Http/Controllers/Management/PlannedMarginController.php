<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Brand;
use App\Models\Catalog\Category;
use Illuminate\Http\Request;
use App\Models\Management\PlannedMargin;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\Management\PlannedMarginMail;


class PlannedMarginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener las marcas ordenadas
        $brands = Brand::orderBy('id', 'asc')->get();

        // Obtener las categorías con sus relaciones de padres
        $categories = Category::orderBy('id', 'asc')->get();

        // Modificar las categorías para que muestren el formato jerárquico
        $categories = $categories->map(function ($category) {
            $fullCode = '';
            $categoryName = $category->name;

            // Mientras exista un parent_id
            while ($category->parent_id) {
                $parentCategory = Category::find($category->parent_id);

                if ($parentCategory) {
                    $fullCode = $parentCategory->code . ' - ' . $fullCode;

                    $category = $parentCategory;
                } else {
                    break;
                }
            }

            if ($fullCode) {
                $category->name = $fullCode . $categoryName;
            }

            return $category;
        });

        return view('management.margins.index', compact('brands', 'categories'));
    }











    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'target_margin' => 'required|numeric',
            'exclusive_reseller_factor' => 'required|numeric',
            'advanced_reseller_factor' => 'required|numeric',
        ]);

        // Verificar si ya existe un margen planeado con la misma marca y categoría
        $existingMargin = PlannedMargin::where('brand', $validatedData['brand'])
            ->where('category', $validatedData['category'])
            ->first();

        if ($existingMargin) {
            // Si el margen ya existe, devolver una respuesta con error
            return response()->json(['error' => 'El margen planeado para esta marca y rubro ya existe.'], 400);
        }

        // Calcular los margenes en base a los factores
        $exclusiveMargin = $request->target_margin * $request->exclusive_reseller_factor;
        $advancedMargin = $request->target_margin * $request->advanced_reseller_factor;

        // Registrar el nuevo margen con el usuario actual
        $margin = PlannedMargin::create([
            'brand' => $validatedData['brand'],
            'category' => $validatedData['category'],
            'target_margin' => $validatedData['target_margin'],
            'exclusive_reseller_factor' => $validatedData['exclusive_reseller_factor'],
            'exclusive_reseller_margin' => $exclusiveMargin,
            'advanced_reseller_factor' => $validatedData['advanced_reseller_factor'],
            'advanced_reseller_margin' => $advancedMargin,
            'created_by' => Auth::user()->rut,
            'updated_by' => Auth::user()->rut,
        ]);


        // Enviar el correo testeando
        Mail::to('informatica@artilec.com')->send(new PlannedMarginMail($margin));



        return response()->json(['success' => 'Margen planeado creado exitosamente.']);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Obtén el usuario logueado (su RUT)
        $userRut = Auth::user()->rut;

        // Obtén el registro que quieres actualizar
        $margin = PlannedMargin::findOrFail($id);

        // Actualiza los campos según la solicitud
        $margin->target_margin = $request->target_margin;
        $margin->exclusive_reseller_margin = $request->exclusive_reseller_margin;
        $margin->advanced_reseller_margin = $request->advanced_reseller_margin;
        $margin->exclusive_reseller_factor = $request->exclusive_reseller_factor;
        $margin->advanced_reseller_factor = $request->advanced_reseller_factor;
        $margin->updated_by = $userRut;
        $margin->updated_at = Carbon::now();

        $margin->save();

        // Enviar el correo testeando
        Mail::to('informatica@artilec.com')->send(new PlannedMarginMail($margin));

        return response()->json(['success' => 'Datos actualizados correctamente']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function datatable(Request $request)
    {
        $query = PlannedMargin::query();

        return DataTables::eloquent($query)
            ->editColumn('created_at', function($margin) {
                return $margin->created_at->format('d/m/Y H:i');
            })
            ->editColumn('updated_at', function($margin) {
                return $margin->updated_at->format('d/m/Y H:i');
            })
            ->addColumn('exclusive_reseller_margin', function($margin) {
                return number_format($margin->exclusive_reseller_margin, 1);
            })
            ->addColumn('advanced_reseller_margin', function($margin) {
                return number_format($margin->advanced_reseller_margin, 1);
            })
            ->addColumn('target_margin', function($margin) {
                return number_format($margin->target_margin, 1);
            })
            ->addColumn('exclusive_reseller_factor', function($margin) {
                return number_format($margin->exclusive_reseller_factor, 1);
            })
            ->addColumn('advanced_reseller_factor', function($margin) {
                return number_format($margin->advanced_reseller_factor, 1);
            })
            ->addColumn('updated_by', function($margin) {
                // Acceder a la relación user y devolver el nombre y apellido
                return $margin->user ? $margin->user->name . ' ' . $margin->user->surname_1 : 'Desconocido';
            })
            ->addColumn('action', function ($margin) {
                return '<button type="button" class="btn btn-sm btn-primary edit-btn" title="Editar">
                <i class="fas fa-edit"></i>
            </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }



}
