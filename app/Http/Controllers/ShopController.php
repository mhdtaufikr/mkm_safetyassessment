<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\RiskAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    // Tampilkan semua data shop dengan search & pagination
    public function index(Request $request)
    {
        $search = $request->input('search');

        $shops = Shop::when($search, function ($query, $search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('name')
            ->paginate(5); // Sesuaikan jumlah item per halaman

        // Ambil 5 assessment terbaru (jika memang digunakan di blade)
        $recentAssessments = RiskAssessment::with('shop')->latest()->take(5)->get();

        return view('shop.index', compact('shops', 'search', 'recentAssessments'));
    }

    // Tampilkan form create (tidak dipakai karena modal)
    public function create()
    {
        return redirect()->route('shop.index');
    }

    // Simpan data shop baru
    public function store(Request $request)
    {
        $validated = $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
    ]);

    if ($request->hasFile('image')) {
        $shopNameSlug = Str::slug($request->name, '_'); // Contoh: "Rear Axle Shaft" => "rear_axle_shaft"
        $extension = $request->file('image')->getClientOriginalExtension();
        $filename = $shopNameSlug . '.' . $extension;

        // Simpan di public/shop_images
        $request->file('image')->storeAs('public/shop_images', $filename);
        $validated['image'] = 'shop_images/' . $filename;
    }

    Shop::create($validated);

    return redirect()->route('shop.index')->with('success', 'Shop created successfully');
    }

    // Edit shop (tidak digunakan karena pakai modal)
    public function edit($id)
    {
        return redirect()->route('shop.index');
    }

    // Update data shop
    public function update(Request $request, $id)
    {
         $validated = $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
    ]);

    $shop = Shop::findOrFail($id);

    if ($request->hasFile('image')) {
        // Hapus file lama jika ada
        if ($shop->image && Storage::exists('public/' . $shop->image)) {
            Storage::delete('public/' . $shop->image);
        }

        // Simpan file baru dengan nama yang sesuai
        $shopNameSlug = Str::slug($request->name, '_');
        $extension = $request->file('image')->getClientOriginalExtension();
        $filename = $shopNameSlug . '.' . $extension;

        $request->file('image')->storeAs('public/shop_images', $filename);
        $validated['image'] = 'shop_images/' . $filename;
    }

    $shop->update($validated);

    return redirect()->route('shop.index')->with('success', 'Shop updated successfully');
    }

    // Hapus data shop
    public function destroy($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->delete();

        return redirect()->route('shop.index')
                         ->with('success', 'Shop deleted successfully');
    }
}
