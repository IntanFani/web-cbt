<!DOCTYPE html>
<html>
<head>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h3>Laporan Nilai Ujian</h3>
    <p>
        <strong>Judul Ujian:</strong> {{ $exam->title }} <br>
        <strong>Tanggal Download:</strong> {{ date('d M Y') }}
    </p>

    <table>
        <thead>
            <tr>
                <th style="background-color: #4CAF50; color: white;">Peringkat</th>
                <th style="background-color: #4CAF50; color: white;">Nama Siswa</th>
                <th style="background-color: #4CAF50; color: white;">Kelas</th>
                <th style="background-color: #4CAF50; color: white;">Angkatan</th>
                <th style="background-color: #4CAF50; color: white;">Waktu Selesai</th>
                <th style="background-color: #4CAF50; color: white;">Nilai Akhir</th>
                <th style="background-color: #4CAF50; color: white;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $index => $session)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $session->user->name }}</td>
                <td>{{ $session->user->kelas->nama_kelas ?? '-' }}</td>
                <td>'{{ $session->user->angkatan ?? '-' }}</td>
                <td>{{ $session->updated_at->format('d/m/Y H:i') }}</td>
                <td style="font-weight: bold;">{{ $session->score }}</td>
                <td>{{ $session->score >= 70 ? 'Lulus' : 'Remedial' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>