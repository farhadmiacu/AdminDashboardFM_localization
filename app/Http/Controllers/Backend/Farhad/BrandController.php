<?php

namespace App\Http\Controllers\Backend\Farhad;

use App\Models\Brand;
use App\Models\BrandTranslation;
use App\Helpers\MiaHelper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $brands = Brand::with(['translations' => function ($q) {
                $q->where('locale', app()->getLocale());
            }])->latest()->get();

            return DataTables::of($brands)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return optional($row->translations->first())->name ?? '<span class="text-muted">N/A</span>';
                })
                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        return '<img src="' . asset($row->image) . '" alt="brand" width="60" height="60" style="object-fit:cover;">';
                    }
                    return '<span class="text-muted">No Image</span>';
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="form-check form-switch form-switch-right form-switch-md">
                        <input class="form-check-input status-switch" type="checkbox" data-id="' . $row->id . '" data-type="brand" ' . $checked . '>
                    </div>';
                })
                ->addColumn('action', function ($row) {
                    $action  = '<a href="' . route('admin.brands.edit', $row->id) . '" class="btn btn-sm btn-primary me-1">';
                    $action .= '<i class="fa-regular fa-pen-to-square"></i></a>';
                    $action .= '<form action="' . route('admin.brands.destroy', $row->id) . '" method="POST" style="display:inline-block;">';
                    $action .= csrf_field() . method_field('DELETE');
                    $action .= '<button type="submit" class="btn btn-sm btn-danger delete-button"><i class="fa-regular fa-trash-can"></i></button>';
                    $action .= '</form>';
                    return $action;
                })
                ->rawColumns(['name', 'image', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.brands.index');
    }

    public function create()
    {
        return view('backend.layouts.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name.en'  => 'required|string|max:255',
            'name.es'  => 'nullable|string|max:255',
            'image'    => 'nullable|mimes:jpg,jpeg,png,webp|max:10240',
            'status'   => 'required|in:0,1',
        ]);

        $brand = new Brand();

        if ($request->hasFile('image')) {
            $brand->image = MiaHelper::uploadFile($request->file('image'), 'brand-images');
        }

        $brand->slug   = Str::slug($request->input('name.en'));
        $brand->status = $request->status;
        $brand->save();

        foreach ($request->input('name', []) as $locale => $name) {
            if (!empty($name)) {
                BrandTranslation::create([
                    'brand_id' => $brand->id,
                    'locale'   => $locale,
                    'name'     => $name,
                ]);
            }
        }

        return back()->with('success', 'New Brand added successfully');
    }

    public function edit($id)
    {
        $brand        = Brand::with('translations')->findOrFail($id);
        $translations = $brand->translations->keyBy('locale');
        return view('backend.layouts.brands.edit', compact('brand', 'translations'));
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $request->validate([
            'name.en'  => 'required|string|max:255',
            'name.es'  => 'nullable|string|max:255',
            'image'    => 'nullable|mimes:jpg,jpeg,png,webp|max:10240',
            'status'   => 'required|in:0,1',
        ]);

        if ($request->hasFile('image')) {
            $brand->image = MiaHelper::updateFile($brand->image, $request->file('image'), 'brand-images');
        }

        $brand->slug   = Str::slug($request->input('name.en'));
        $brand->status = $request->status;
        $brand->save();

        foreach ($request->input('name', []) as $locale => $name) {
            if (!empty($name)) {
                BrandTranslation::updateOrCreate(
                    ['brand_id' => $brand->id, 'locale' => $locale],
                    ['name' => $name]
                );
            }
        }

        return back()->with('success', 'Brand updated successfully');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        if ($brand->image && file_exists(public_path($brand->image))) {
            unlink(public_path($brand->image));
        }

        $brand->translations()->delete();
        $brand->delete();

        return back()->with('success', 'Brand deleted successfully');
    }
}
