<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;

class SiswaAdminController extends Controller
{
    // 1. Tampilkan Daftar Siswa (Dengan Filter & Search)
    public function index(Request $request)
    {
        // A. Siapkan Data untuk Dropdown Filter
        $kelas = Kelas::all();
        $angkatan = User::where('role', 'siswa')
                        ->whereNotNull('angkatan')
                        ->select('angkatan')
                        ->distinct()
                        ->orderBy('angkatan', 'desc')
                        ->pluck('angkatan');

        // B. Mulai Query Dasar
        $query = User::where('role', 'siswa');

        // C. Logika Filter
        // 1. Cari Nama/Email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // 2. Filter Kelas (Disesuaikan agar menangkap parameter dari Halaman Kelas)
        if ($request->filled('filter_kelas')) {
            $query->where('kelas_id', $request->filter_kelas);
        } elseif ($request->filled('kelas_id')) { 
            // Tambahan: Jika redirect datang membawa 'kelas_id' bukan 'filter_kelas'
            $query->where('kelas_id', $request->kelas_id);
        }

        // 3. Filter Angkatan
        if ($request->filled('filter_angkatan')) {
            $query->where('angkatan', $request->filter_angkatan);
        }

        // D. Ambil Data (Pakai PAGINATE)
        $siswa = $query->latest()->paginate(10)->withQueryString(); 

        return view('dashboard.guru.siswa.index', compact('siswa', 'kelas', 'angkatan'));
    }

    // 2. Tampilkan Form Tambah
    public function create()
    {
        $kelas = Kelas::all(); // Ambil semua kelas
        return view('dashboard.guru.siswa.create', compact('kelas'));
    }

    // 3. Simpan Siswa Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'kelas_id' => 'required|exists:kelas,id',
            'angkatan' => 'required|numeric|digits:4',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'kelas_id' => $request->kelas_id,
            'angkatan' => $request->angkatan,
            'password' => Hash::make($request->password),
            'role' => 'siswa', // Otomatis set role jadi siswa
        ]);

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil ditambahkan!');
    }

    // 4. Tampilkan Form Edit
    public function edit($id)
    {
        $siswa = User::where('role', 'siswa')->findOrFail($id);
        $kelas = Kelas::all(); // Ambil semua kelas juga
        return view('dashboard.guru.siswa.edit', compact('siswa', 'kelas'));
    }

    // 5. Update Data Siswa
    public function update(Request $request, $id)
    {
        $siswa = User::where('role', 'siswa')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            // Validasi email unik, KECUALI untuk user ini sendiri (biar ga error kalau email ga diganti)
            'email' => 'required|email|unique:users,email,'.$id,
            'kelas_id' => 'required|exists:kelas,id',
            'angkatan' => 'required|numeric|digits:4',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'kelas_id' => $request->kelas_id,
            'angkatan' => $request->angkatan,
        ];

        // Cek apakah password diisi? Kalau iya, update. Kalau kosong, biarkan password lama.
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $siswa->update($data);

        return redirect()->route('siswa.index')->with('success', 'Data siswa diperbarui!');
    }

    // 6. Hapus Siswa
    public function destroy($id)
    {
        $siswa = User::where('role', 'siswa')->findOrFail($id);
        $siswa->delete();
        
        return redirect()->back()->with('success', 'Akun siswa berhasil dihapus.');
    }
}