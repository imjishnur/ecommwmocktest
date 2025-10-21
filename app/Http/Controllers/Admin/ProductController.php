<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProductService;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Color;
use App\Models\Size;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    protected $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $products = $this->service->all();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $colors = Color::all();
        $sizes = Size::all();
        return view('admin.products.create', compact('categories','colors','sizes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'category_id'=>'required|exists:categories,id',
            'color_id'=>'nullable|exists:colors,id',
            'size_id'=>'nullable|exists:sizes,id',
            'qty'=>'required|integer',
            'price'=>'required|numeric',
            'image'=>'nullable|image|mimes:jpg,png|max:2048'
        ]);

        $this->service->create($data);

        return redirect()->route('admin.products.index')->with('success','Product created successfully!');
    }

    public function edit($id)
    {
        $product = $this->service->find($id);
        $categories = Category::all();
        $colors = Color::all();
        $sizes = Size::all();
        return view('admin.products.edit', compact('product','categories','colors','sizes'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'category_id'=>'required|exists:categories,id',
            'color_id'=>'nullable|exists:colors,id',
            'size_id'=>'nullable|exists:sizes,id',
            'qty'=>'required|integer',
            'price'=>'required|numeric',
            'image'=>'nullable|image|mimes:jpg,png|max:2048'
        ]);

        $this->service->update($id, $data);

        return redirect()->route('admin.products.index')->with('success','Product updated successfully!');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect()->route('admin.products.index')->with('success','Product deleted successfully!');
    }


public function downloadTemplate()
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers
    $sheet->setCellValue('A1', 'Name');
    $sheet->setCellValue('B1', 'Category');
    $sheet->setCellValue('C1', 'Color');
    $sheet->setCellValue('D1', 'Size');
    $sheet->setCellValue('E1', 'Qty');
    $sheet->setCellValue('F1', 'Price');
    $sheet->setCellValue('G1', 'Image URL');

    // Example dropdowns for category/color/size
    $categories = Category::pluck('name')->toArray();
    $colors = Color::pluck('name')->toArray();
    $sizes = Size::pluck('name')->toArray();

    // Category dropdown
    $sheet->getColumnDimension('B')->setWidth(20);
    $sheet->getColumnDimension('C')->setWidth(15);
    $sheet->getColumnDimension('D')->setWidth(10);

    // Apply data validation for dropdowns
    $sheet->getCell('B2')->getDataValidation()->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
        ->setFormula1('"' . implode(',', $categories) . '"')
        ->setAllowBlank(false)
        ->setShowDropDown(true);

    $sheet->getCell('C2')->getDataValidation()->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
        ->setFormula1('"' . implode(',', $colors) . '"')
        ->setAllowBlank(false)
        ->setShowDropDown(true);

    $sheet->getCell('D2')->getDataValidation()->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
        ->setFormula1('"' . implode(',', $sizes) . '"')
        ->setAllowBlank(false)
        ->setShowDropDown(true);

    $writer = new Xlsx($spreadsheet);

    $response = new StreamedResponse(function() use ($writer) {
        $writer->save('php://output');
    });

    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->headers->set('Content-Disposition', 'attachment;filename="products-template.xlsx"');
    $response->headers->set('Cache-Control','max-age=0');

    return $response;
}
public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,csv',
    ]);

    $file = $request->file('file')->getRealPath();

    $importer = new \App\Imports\ProductImport($file);
    $importer->import();

    return redirect()->back()->with('success', 'Products imported successfully!');
}

}
