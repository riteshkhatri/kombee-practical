<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use App\Exports\SuppliersExport;
use App\Exports\UsersExport;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    /**
     * Export data to Excel, CSV or PDF.
     *
     * @return BinaryFileResponse
     */
    public function export(Request $request, string $module, string $format)
    {
        $modelClass = $this->getModelClass($module);

        if (! $modelClass) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        Gate::authorize('export', $modelClass);

        $export = $this->getExportClass($module);

        if (! $export) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        $filename = "{$module}_export_".now()->format('Ymd_His');
        $extension = $this->getExtension($format);
        $excelFormat = $this->getExcelFormat($format);

        if (! $extension || ! $excelFormat) {
            return response()->json(['message' => 'Invalid format'], 400);
        }

        return Excel::download($export, "{$filename}.{$extension}", $excelFormat);
    }

    /**
     * Get the export class based on the module name.
     *
     * @return mixed
     */
    private function getExportClass(string $module)
    {
        return match ($module) {
            'users' => new UsersExport,
            'customers' => new CustomersExport,
            'suppliers' => new SuppliersExport,
            default => null,
        };
    }

    /**
     * Get the file extension based on the format.
     */
    private function getExtension(string $format): ?string
    {
        return match ($format) {
            'excel', 'xlsx' => 'xlsx',
            'csv' => 'csv',
            'pdf' => 'pdf',
            default => null,
        };
    }

    /**
     * Get the Excel format constant based on the format string.
     */
    private function getExcelFormat(string $format): ?string
    {
        return match ($format) {
            'excel', 'xlsx' => \Maatwebsite\Excel\Excel::XLSX,
            'csv' => \Maatwebsite\Excel\Excel::CSV,
            'pdf' => \Maatwebsite\Excel\Excel::DOMPDF,
            default => null,
        };
    }

    /**
     * Get the model class based on the module name.
     */
    private function getModelClass(string $module): ?string
    {
        return match ($module) {
            'users' => User::class,
            'customers' => Customer::class,
            'suppliers' => Supplier::class,
            default => null,
        };
    }
}
