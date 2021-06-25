<?php

namespace App\Http\Controllers\Company\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Excel\Client\ClientExcelImport;
use App\Services\Excel\Employee\EmployeeExcelImport;
use App\Services\Excel\Product\ProductExcelImport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * class ExcelController
 * 
 * @package: App\Http\Controllers\Company\Admin
 * @author: Shashank Jha <shashankj677@gmail.com>
 */

class ExcelController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('permission:product-create', ['only' => ['product']]);
        $this->middleware('permission:party-create', ['only' => ['client']]);
        $this->middleware('permission:employee-create', ['only' => ['employee']]);
    }

    // custom validator function
    private function customValidator($request) {
        $rule = [
            'spreadsheet'  => 'required|mimes:xls,xlsx'
        ];
        $custom_message = [
            'required' => "You can't submit empty file",
            'spreadsheet.mimes' => "The file you've submitted must be a type of excel and end with either .xls or .xlsx extension"
        ];
        return $this->validate($request, $rule, $custom_message);
    }

    // function that returns the view page for product import
    public function product() {
        return view('company.excel_import.product');
    }

    // function to import the product spreadsheet
    public function importProduct(Request $request) {
        // check validation
        $this->customValidator($request);

        // if validation succeeds then import spreadsheet else throw error and return back
        try {
            Excel::import(new ProductExcelImport(),$request->file('spreadsheet'));
            return redirect()->route('company.admin.import.product', ['domain' => domain()])
                ->with('success', "Your file was imported successfully");
        } catch (\Exception $error) {
            return redirect()->back()
                ->with('alert', $error->getMessage());
        }
    }

    // function that return the view page for employee import
    public function employee() {
        return view('company.excel_import.employee');
    }

    // function to import the employee spreadsheet
    public function importEmployee(Request $request) {
        // check validation
        $this->customValidator($request);

        // if validation succeeds then import spreadsheet else throw error and return back
        try {
            Excel::import(new EmployeeExcelImport(), $request->file('spreadsheet'));
            return redirect()->route('company.admin.import.employee', ['domain' => domain()])
                ->with('success', "Your file was imported successfully");
        } catch (\Exception $error) {
            return redirect()->back()
                ->with('alert', $error->getMessage());
        }
    }

    // function that returns the view page for client import
    public function client() {
        return view('company.excel_import.client');
    }

    // function to import the client spreadsheet
    public function importClient(Request $request) {
        // check validation
        $this->customValidator($request);

        // if validation succeeds then import spreadsheet else throw error and return back
        try {
            Excel::import(new ClientExcelImport(), $request->file('spreadsheet'));
            return redirect()->route('company.admin.import.client', ['domain' => domain()])
                ->with('success', "Your file was imported successfully");
        } catch (\Exception $error) {
            return redirect()->back()
                ->with('alert', $error->getMessage());
        }
    }
}
