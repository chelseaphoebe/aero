<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Absensi;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');

        $absensi = Absensi::with('pegawai') // Eager load relasi pegawai
            ->when($search, function ($query) use ($search) {
                return $query->whereHas('pegawai', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                })->orWhere('tanggal', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            })
            ->orderBy('tanggal', 'desc')
            ->paginate($perPage);

        $showing = $absensi->total();
        $from = $absensi->firstItem() ?? 0;
        $to = $absensi->lastItem() ?? 0;

        return view('absensi.index', compact(
            'absensi',
            'perPage',
            'showing',
            'from',
            'to'
        ));
    }

    public function create()
    {
        // Ambil data pegawai untuk pilihan pada form
        $pegawais = DB::table('pegawai')->get();
        
        // Kirim variabel pegawais ke view
        return view('absensi.create', compact('pegawais'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255', // Validasi untuk keterangan
        ]);

        // Cek apakah sudah absen pada tanggal ini
        $exists = DB::table('absensi')
            ->where('pegawai_id', $request->pegawai_id)
            ->where('tanggal', $request->tanggal)
            ->exists();

        if ($exists) {
            return redirect()->route('absensi.create')->withErrors([
                'error' => 'Pegawai sudah absen pada tanggal ini.'
            ]);
        }

        // Simpan data absensi
        DB::table('absensi')->insert([
            'pegawai_id' => $request->pegawai_id,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan, // Simpan keterangan jika ada
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Redirect ke halaman tambah dengan pesan sukses
        return redirect()->route('absensi.create')->with('success', 'Absensi berhasil disimpan.');
    }

    public function filter(Request $request)
    {
        $query = DB::table('absensi as a')
            ->join('pegawai as p', 'a.pegawai_id', '=', 'p.id')
            ->select([
                'a.id',
                'p.nama',
                'a.tanggal',
                'a.keterangan', // Tambahkan kolom keterangan
                'a.created_at'
            ]);

        // Filter berdasarkan pegawai
        if ($request->pegawai_id) {
            $query->where('a.pegawai_id', $request->pegawai_id);
        }

        // Filter berdasarkan rentang tanggal
        if ($request->tanggal_mulai) {
            $query->where('a.tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->tanggal_akhir) {
            $query->where('a.tanggal', '<=', $request->tanggal_akhir);
        }

        $absensi = $query->orderBy('a.tanggal', 'desc')->get();

        return response()->json($absensi);
    }

    public function destroy($id)
    {
        try {
            $absensi = Absensi::findOrFail($id); // Cari data absensi berdasarkan ID
            $absensi->delete(); // Hapus data
            
            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus absensi. Silakan coba lagi.'
            ], 500);
        }
    }

    public function edit($id)
    {
        $absensi = Absensi::findOrFail($id);
        $pegawais = DB::table('pegawai')->get(); // Ambil daftar pegawai untuk dropdown
        return view('absensi.edit', compact('absensi', 'pegawais'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawai,id',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $absensi = Absensi::findOrFail($id);
        $absensi->update($request->only(['pegawai_id', 'tanggal', 'keterangan']));

        return redirect()->route('absensi.index')->with('success', 'Absensi berhasil diperbarui.');
    }

}
