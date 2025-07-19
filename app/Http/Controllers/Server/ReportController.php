<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Models\Admin\Report;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = Report::all();  // O ajusta la consulta si deseas hacerla más específica
        return view('reports.index', compact('reports'));
    }

    // Mostrar un solo reporte
    public function show($id)
    {
        $report = Report::findOrFail($id);  // Obtener el reporte por ID
        return view('reports.show', compact('report'));
    }

    public function sendEmail($id)
    {
        // Obtener el reporte por ID
        $report = Report::findOrFail($id);
        $report->send();
        return redirect()->route('admin.reports.index')->with('success', 'Correo enviado exitosamente.');
    }







}
