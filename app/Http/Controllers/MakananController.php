<?php

namespace App\Http\Controllers;

use App\Models\Makanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MakananController extends Controller
{
    public function index()
    {
        $makanans = Makanan::latest()->get();
        return view('makanan.index', compact('makanans'));
    }

    public function show($id)
    {
        $makanan = Makanan::findOrFail($id);
        return view('makanan.show', compact('makanan'));
    }

    // ===== ADMIN =====
    public function create()
    {
        abort_unless(auth()->user()->is_admin, 403);
        return view('makanan.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->is_admin, 403);

        $data = $request->validate([
            'nama' => 'required',
            'asal' => 'required',
            'harga' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'resep' => 'required',
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $file = $request->file('foto');
        $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $file->move(public_path('images'), $filename);
        $data['foto'] = $filename;
        return redirect('/')->with('success', 'Makanan berhasil ditambahkan');
    }

    public function edit($id)
    {
        abort_unless(auth()->user()->is_admin, 403);
        $makanan = Makanan::findOrFail($id);
        return view('makanan.edit', compact('makanan'));
    }

    public function update(Request $request, $id)
    {
        abort_unless(auth()->user()->is_admin, 403);

        $makanan = Makanan::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required',
            'asal' => 'required',
            'harga' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'resep' => 'required',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('foto')) {
            $oldPath = public_path('images/' . $makanan->foto);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
            $file = $request->file('foto');
            $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('images'), $filename);
            $data['foto'] = $filename;
        }

        $makanan->update($data);

        return redirect('/makanan/' . $makanan->id)
            ->with('success', 'Makanan berhasil diupdate');
    }

    public function destroy($id)
    {
        abort_unless(auth()->user()->is_admin, 403);

        $makanan = Makanan::findOrFail($id);
        $oldPath = public_path('images/' . $makanan->foto);
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
        $makanan->delete();

        return redirect('/')->with('success', 'Makanan berhasil dihapus');
    }
}
