<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export MoM - {{ $mom->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    
        @media print {
            body { 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact; 
                font-size: 10pt; 
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid; 
                page-break-after: auto;
            }
            img {
                max-width: 100% !important; 
                height: auto !important;
            }
        }
    
        .border-black { border-color: #000; }
        .border { border-style: solid; border-width: 1px; }
        .text-gray-900 { color: #1f2937; }
        .bg-gray-200 { background-color: #e5e7eb; }
        .bg-white { background-color: #fff; }
        .text-white { color: #fff; }
        .bg-red-600 { background-color: #dc2626; }
    </style>
</head>
<body class="bg-white">

    @php
        use Carbon\Carbon;
        $meetingDate = Carbon::parse($mom->meeting_date);
        
        // --- LOGIC MENGAMBIL DATA PESERTA DARI JSON ---
        $internalAttendees = $mom->nama_peserta ?? []; // Array of names (Internal)
        $partnerData = $mom->nama_mitra ?? []; // Array of objects (Mitra)

        // Menggabungkan semua peserta menjadi satu daftar
        $allAttendees = $internalAttendees;
        foreach ($partnerData as $mitra) {
            if (is_array($mitra['attendees'] ?? null)) {
                $allAttendees = array_merge($allAttendees, $mitra['attendees']);
            }
        }
        $totalAttendeesCount = count($allAttendees);
        
        // Mengubah daftar peserta menjadi string yang dipisahkan koma
        $attendeeListString = implode(', ', $allAttendees);
    @endphp

    <div id="pdf-preview" class="p-6 md:p-8 min-w-[800px] bg-white text-gray-900 font-sans">
        <table class="w-full mb-4 border-collapse">
            <tbody>
                <tr>
                    {{-- Judul dan Logo --}}
                    <td class="align-middle text-center border border-black p-2 w-1/4">
                        <img src="{{ asset('img/logo.png') }}" alt="Company Logo" class="h-32 mx-auto">
                    </td>
                    <td colspan="3" class="text-center align-middle border border-black">
                        <p class="font-bold text-2xl italic">MINUTE OF MEETING</p>
                        <p class="font-semibold text-xl">{{ $mom->title }}</p>
                    </td>
                </tr>

                {{-- Pimpinan & Notulen --}}
                <tr>
                    <td class="border border-black p-2 font-semibold">Pimpinan Rapat</td>
                    <td class="border border-black p-2">{{ $mom->pimpinan_rapat }}</td> 
                    <td class="border border-black p-2 font-semibold">Notulen</td>
                    <td class="border border-black p-2">{{ $mom->notulen }}</td> 
                </tr>

                {{-- Daftar Peserta --}}
                <tr>
                    <td class="border border-black p-2 font-semibold">Peserta</td>
                    <td colspan="3" class="border border-black p-2">
                        {{-- Menggunakan string yang dipisahkan koma --}}
                        @if ($totalAttendeesCount > 0)
                            {{ $attendeeListString }}
                        @else
                            Tidak ada peserta tercatat.
                        @endif
                    </td>
                </tr>

                {{-- Waktu --}}
                <tr>
                    <td class="border border-black p-2 font-semibold">Waktu</td>
                    <td colspan="3" class="border border-black p-2">
                        {{ $meetingDate->translatedFormat('l, d F Y') }} | {{ Carbon::parse($mom->start_time)->format('H:i') }} – {{ Carbon::parse($mom->end_time)->format('H:i') }}
                    </td>
                </tr>

                {{-- Tempat --}}
                <tr>
                    <td class="border border-black p-2 font-semibold">Tempat</td>
                    <td colspan="3" class="border border-black p-2">{{ $mom->location }}</td>
                </tr>

                {{-- Agenda Header --}}
                <tr>
                    <td colspan="4" class="border border-black p-2 font-semibold bg-gray-200 text-center uppercase">Agenda</td>
                </tr>

                {{-- Daftar Agenda --}}
                <tr>
                    <td colspan="4" class="border border-black p-2">
                        <ol class="ml-4">
                            @foreach($mom->agendas as $agenda)
                                <li class="list-decimal">{{ $agenda->item }}</li>
                            @endforeach
                        </ol>
                    </td>
                </tr>
                <tr class="h-2"><td colspan="4"></td></tr>

                {{-- Pembahasan Header --}}
                <tr>
                    <td colspan="4" class="border border-black p-2 font-semibold bg-gray-200 text-center uppercase">Hasil Pembahasan</td>
                </tr>

                {{-- Hasil Pembahasan --}}
                <tr>
                    <td colspan="4" class="border border-black p-4">
                        <div class="font-sans whitespace-pre-wrap text-sm leading-relaxed">
                            {{-- Gunakan prose styles di sini untuk Quill content --}}
                            {!! $mom->pembahasan !!}
                        </div>
                    </td>
                </tr>

                {{-- Tindak Lanjut --}}
                @if($mom->actionItems->isNotEmpty())
                <tr>
                    <td colspan="4" class="border border-black p-2 font-semibold bg-gray-200 text-center uppercase">Tindak Lanjut</td>
                </tr>
                <tr>
                    <td colspan="4" class="border border-black p-4">
                        <ul class="list-disc ml-5 space-y-2 text-sm">
                            @foreach($mom->actionItems as $item)
                                <li>{{ $item->item }} (Deadline: {{ Carbon::parse($item->due)->translatedFormat('d F Y') }})</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                @endif

                <tr>
                    <td colspan="4" class="pt-6">Demikian MoM ini dibuat untuk diketahui dan ditindaklanjuti bersama.</td>
                </tr>

                {{-- TANDA TANGAN PESERTA DENGAN MULTI-NAMA DINAMIS --}}
                <tr>
                    <td colspan="4" class="pt-10">
                        <table class="w-full text-center border-collapse">
                            <thead>
                                <tr class="border-b border-black">
                                    {{-- Kolom Telkom Indonesia --}}
                                    <th class="p-2 font-semibold w-1/{{ count($partnerData) + 1 }}">PT TELKOM INDONESIA</th>
                                    {{-- Kolom Mitra Lain (Dinamis) --}}
                                    @foreach($partnerData as $mitra)
                                        <th class="p-2 font-semibold w-1/{{ count($partnerData) + 1 }}">{{ $mitra['name'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="align-top">
                                    {{-- Kolom untuk Telkom (Internal Attendees) --}}
                                    <td class="p-2">
                                        <div class="flex flex-col">
                                            @forelse($internalAttendees as $name)
                                                <p class="pt-16 underline">{{ $name }}</p>
                                            @empty
                                                <p class="pt-16 italic text-gray-500">N/A</p>
                                            @endforelse
                                        </div>
                                    </td>
                                    
                                    {{-- Kolom untuk Mitra Lain (Dinamis) --}}
                                    @foreach($partnerData as $mitra)
                                        <td class="p-2">
                                            <div class="flex flex-col">
                                                @forelse($mitra['attendees'] ?? [] as $name)
                                                    <p class="pt-16 underline">{{ $name }}</p>
                                                @empty
                                                    <p class="pt-16 italic text-gray-500">N/A</p>
                                                @endforelse
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                {{-- Lampiran Header --}}
                <tr><td colspan="4" class="text-center font-bold text-2xl pt-10 pb-4">LAMPIRAN</td></tr>

                {{-- Gambar Lampiran --}}
                <tr>
                    <td colspan="4" class="p-4 border border-black">
                        @if($mom->attachments->isNotEmpty())
                            @forelse($mom->attachments as $attachment)
                                @if(str_starts_with($attachment->mime_type, 'image/'))
                                    <div class="text-center mb-6">
                                        <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="Lampiran Rapat" class="w-full max-w-xl mx-auto border">
                                        <p class="mt-2 text-sm">File: {{ $attachment->file_name }}</p>
                                    </div>
                                @endif
                            @empty
                                <p class="text-sm text-center text-gray-500">Tidak ada lampiran gambar yang dapat ditampilkan.</p>
                            @endforelse
                        @else
                            <p class="text-sm text-center text-gray-500">Tidak ada lampiran gambar yang dapat ditampilkan.</p>
                        @endif
                    </td>
                </tr>

                {{-- Footer --}}
                <tr><td colspan="4" class="text-center font-normal italic bg-red-600 text-white p-3 border border-black text-xs">All rights reserved by MoM Telkom.</td></tr>
            </tbody>
        </table>
    </div>

    <script>
        // Otomatis membuka dialog print saat halaman selesai dimuat
        window.onload = function() {
            // Memberi waktu browser sebentar untuk merender CSS sebelum mencetak
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>