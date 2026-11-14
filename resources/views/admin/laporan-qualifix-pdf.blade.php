@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <h2 class="text-[20px] font-bold text-ink mb-6">Rekapitulasi Sesi QualiFix (Klinik PR)</h2>
        
        @if($data['summary']['total'] > 0)
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-sand rounded-lg p-4">
                    <p class="text-[12px] text-bark-500 font-bold">{{ $data['summary']['total'] }}</p>
                    <p class="text-[13px] text-ink">Total Tiket</p>
                </div>
                <div class="bg-gold rounded-lg p-4">
                    <p class="text-[12px] text-espresso font-bold">{{ $data['summary']['menunggu'] }}</p>
                    <p class="text-[13px] text-ink">Menunggu</p>
                </div>
                <div class="bg-bronze rounded-lg p-4">
                    <p class="text-[12px] text-bark-400 font-bold">{{ $data['summary']['dibahas'] }}</p>
                    <p class="text-[13px] text-ink">Dibahas</p>
                </div>
                <div class="bg-forest-600 rounded-lg p-4">
                    <p class="text-[12px] text-gold font-bold">{{ $data['summary']['selesai'] }}</p>
                    <p class="text-[13px] text-ink">Selesai</p>
                </div>
            </div>
        @endif
        
        <div overflow-x-auto>
            <table class="w-full table-auto border-collapse border-line">
                <thead>
                    <tr class="text-[11px] font-bold text-ink border-b border-line bg-sandgold">
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">NIS</th>
                        <th class="p-3">Nama Siswa</th>
                        <th class="p-3">Kelas</th>
                        <th class="p-3">Mata Pelajaran</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['rows'] as $row)
                        <tr class="text-[11px] border-b border-line">
                            <td class="p-3">{{ $row['tanggal'] }}</td>
                            <td class="p-3">{{ $row['nis'] }}</td>
                            <td class="p-3">{{ $row['nama_siswa'] }}</td>
                            <td class="p-3">{{ $row['kelas'] }}</td>
                            <td class="p-3">{{ $row['mata_pelajaran'] }}</td>
                            <td class="p-3">{{ $row['status'] }}</td>
                            <td class="p-3">{{ $row['dicatat_oleh'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($data['summary']['total'] > 0)
            <p class="mt-6 text-[11px] text-muted">
                Total: {{ $data['summary']['total'] }} | 
                Menunggu: {{ $data['summary']['menunggu'] }} | 
                Dibahas: {{ $data['summary']['dibahas'] }} | 
                Selesai: {{ $data['summary']['selesai'] }}
            </p>
        @endif
    </div>
</div>
@endsection