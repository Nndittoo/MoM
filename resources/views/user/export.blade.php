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

            .ql-content ul, .ql-content ol {
                margin-left: 1.5em !important;
            }
            /* Pastikan kolom peserta tetap sejajar di media print */
            .attendee-group {
                width: 33% !important;
                padding-right: 1.5rem !important;
                margin-bottom: 0.5rem !important;
            }
        }

        .border-black { border-color: #000; }
        .border { border-style: solid; border-width: 1px; }
        .text-gray-900 { color: #1f2937; }
        .bg-gray-200 { background-color: #e5e7eb; }
        .bg-white { background-color: #fff; }
        .text-white { color: #fff; }
        .bg-red-600 { background-color: #dc2626; }

        .content-quill ul {
            list-style-type: disc;
            margin-left: 20px;
            padding-left: 0;
        }
        .content-quill ol {
            list-style-type: decimal;
            margin-left: 20px;
            padding-left: 0;
        }
        .content-quill li {
            padding-left: 5px;
        }

        /* CSS UNTUK PESERTA DINAMIS (HORIZONTAL WRAPPING SEJAJAR) */
        .attendee-container {
            display: flex;
            flex-wrap: wrap;
            padding: 0;
            margin: 0;
        }
        .attendee-group {
            /* Menentukan 3 kolom sejajar, menggunakan 33% */
            width: 33%;
            box-sizing: border-box;
            padding-right: 1.5rem; /* Ganti gap dengan padding antar kolom */
            margin-bottom: 0.5rem; /* Jarak antar baris */
        }
        .attendee-group h4 {
            font-weight: bold;
            margin-top: 0.5rem;
            margin-bottom: 4px;
        }
        .attendee-group:nth-child(1),
        .attendee-group:nth-child(2),
        .attendee-group:nth-child(3) {
            margin-top: 0;
        }
        .attendee-group ul {
            list-style-type: disc;
            padding-left: 15px;
            margin: 0;
        }

    </style>
</head>
<body class="bg-white">

    @php
        use Carbon\Carbon;
        $meetingDate = Carbon::parse($mom->meeting_date);

        // --- DECODING DAN EKSTRAKSI DATA PESERTA DARI JSON/ARRAY ---

        // Data mentah Internal (nama_peserta)
        $internalDataContainers = is_array($mom->nama_peserta ?? null)
                                        ? $mom->nama_peserta
                                        : json_decode($mom->nama_peserta ?? '[]', true);

        // Data mentah Mitra (nama_mitra)
        $partnerDataContainers = is_array($mom->nama_mitra ?? null)
                                        ? $mom->nama_mitra
                                        : json_decode($mom->nama_mitra ?? '[]', true);


        // --- LOGIC PESERTA INTERNAL (UNTUK TAMPILAN PER UNIT) ---
        $internalAttendeeGroups = [];
        $allAttendeesRaw = []; // List flat untuk total hitungan

        if (is_array($internalDataContainers)) {
            foreach ($internalDataContainers as $unit) {
                if (is_array($unit['attendees'] ?? null) && !empty($unit['attendees'])) {
                    $internalAttendeeGroups[] = [
                        'name' => $unit['unit'] ?? 'Unit Internal',
                        'attendees' => $unit['attendees']
                    ];
                    $allAttendeesRaw = array_merge($allAttendeesRaw, $unit['attendees']);
                }
            }
        }

        // --- LOGIC PESERTA MITRA & TANDA TANGAN ---
        $signatoryGroups = [];

        if (is_array($partnerDataContainers)) {
            foreach ($partnerDataContainers as $mitra) {
                if (is_array($mitra['attendees'] ?? null) && !empty($mitra['attendees'])) {
                    $allAttendeesRaw = array_merge($allAttendeesRaw, $mitra['attendees']);

                    // Siapkan Grup Tanda Tangan (Mitra)
                    $signatoryGroups[] = [
                        'name' => $mitra['name'] ?? 'Pihak Mitra',
                        'attendees' => $mitra['attendees'] ?? []
                    ];
                }
            }
        }

        // Finalisasi Hitungan Total Peserta (Internal + Mitra)
        $allAttendees = array_unique($allAttendeesRaw);
        $totalAttendeesCount = count($allAttendees);

        // Finalisasi Grup Tanda Tangan
        $totalSignatoryGroups = count($signatoryGroups);

    @endphp

    <div id="pdf-preview" class="p-6 md:p-8 min-w-[800px] bg-white text-gray-900 font-sans">
        <table class="w-full mb-4 border-collapse">
            <tbody>
                <tr>
                    {{-- Judul dan Logo --}}
                    <td class="align-middle text-center border border-black p-2 w-1/4">

                        <img src="{{ asset('img/telkom.png') }}" alt="Company Logo" class="h-32 mx-auto">
                    </td>
                    <td colspan="3" class="text-center align-middle border border-black">
                        <p class="font-bold text-2xl italic">MINUTE OF MEETING</p>
                        <p class="font-bold text-xl">{{ $mom->title }}</p>
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
                        @if (!empty($internalAttendeeGroups) || !empty($signatoryGroups))
                            {{-- Menggabungkan Internal dan Mitra untuk tampilan Peserta --}}
                            <div class="attendee-container">
                                @foreach(array_merge($internalAttendeeGroups) as $group)
                                    <div class="attendee-group">
                                        <h4>{{ $group['name'] }}</h4>
                                        <ul>
                                            @foreach($group['attendees'] as $name)
                                                <li>{{ $name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
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

                        <div class="font-sans whitespace-pre-wrap text-sm leading-relaxed content-quill">
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
                    <td colspan="4" class="pt-6">Demikian MoM ini dibuat untuk diketahui dan ditindaklanjutkan bersama.</td>
                </tr>

                {{-- TANDA TANGAN PESERTA DENGAN MULTI-NAMA DINAMIS --}}
                @if($totalSignatoryGroups > 0)
    @php
        // Bagi grup tanda tangan menjadi beberapa baris, masing-masing maksimal 3 kolom
        $chunks = array_chunk($signatoryGroups, 3);
    @endphp

    <tr>
        <td colspan="4" class="pt-10">
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="border-b border-black">
                        <th colspan="3" class="p-2 font-semibold">Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody class="space-y-10"> {{-- Tambahkan jarak antar baris di sini --}}
                    @foreach($chunks as $index => $row)
                        <tr class="align-top {{ $index > 0 ? 'pt-10' : '' }}"> {{-- Tambahkan jarak di baris ke-2, dst --}}
                            @foreach($row as $group)
                                <td class="p-2 w-1/3">
                                    <div class="flex flex-col">
                                        <p class="font-semibold">{{ $group['name'] }}</p>

                                        @forelse($group['attendees'] as $name)
                                            <p class="pt-16 underline">{{ $name }}</p>
                                        @empty
                                            <p class="pt-16 italic text-gray-500">N/A</p>
                                        @endforelse
                                    </div>
                                </td>
                            @endforeach

                            {{-- Tambahkan kolom kosong jika kurang dari 3 kolom --}}
                            @for($i = count($row); $i < 3; $i++)
                                <td class="p-2 w-1/3"></td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>
@endif



                {{-- Lampiran Header --}}
                <tr><td colspan="4" class="text-center font-bold text-2xl pt-10 pb-4">LAMPIRAN</td></tr>

                {{-- Gambar Lampiran --}}
                <tr>
                   <td colspan="4" class="p-4 border border-black">
    @if($mom->attachments->isNotEmpty())
        @php
            // Filter hanya lampiran gambar (images)
            $imageAttachments = $mom->attachments->filter(function($attachment) {
                return str_starts_with($attachment->mime_type, 'image/');
            })->values(); // Pastikan koleksi memiliki indeks numerik berurutan
            $imageCount = $imageAttachments->count();
        @endphp

        {{-- Logika Tampilan Lampiran --}}
        @if ($imageCount === 0)
            <p class="text-sm text-center text-gray-500">Tidak ada lampiran gambar yang dapat ditampilkan.</p>
        @elseif ($imageCount === 1)
            {{-- KASUS 1: Hanya 1 gambar, center --}}
            <div class="flex justify-center">
                <div class="text-center w-full max-w-xl">
                    <img src="{{ asset('storage/' . $imageAttachments[0]->file_path) }}"
                             alt="Lampiran Rapat"
                             class="w-full h-auto mx-auto border object-contain">
                    <p class="mt-2 text-sm">File: {{ $imageAttachments[0]->file_name }}</p>
                </div>
            </div>
        @else
            {{-- KASUS 2: 2 atau lebih gambar, dengan penanganan ganjil/genap --}}
            <div class="grid grid-cols-2 gap-4">
                @foreach($imageAttachments as $index => $attachment)
                    @if ($imageCount % 2 !== 0 && $index === $imageCount - 1)
                        </div> {{-- Tutup grid-cols-2 sebelum gambar terakhir --}}
                        <div class="flex justify-center w-full mt-4">
                            <div class="text-center w-1/2">
                                <img src="{{ asset('storage/' . $attachment->file_path) }}"
                                         alt="Lampiran Rapat"
                                         class="w-full h-auto mx-auto border object-contain">
                                <p class="mt-2 text-sm">File: {{ $attachment->file_name }}</p>
                            </div>
                        </div>
                        {{-- Break loop karena gambar terakhir sudah diurus. --}}
                        @break
                    @else
                        {{-- Untuk semua gambar lain (atau jika total genap), tampilkan di grid --}}
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $attachment->file_path) }}"
                                 alt="Lampiran Rapat"
                                 class="w-full h-auto mx-auto border object-contain">
                            <p class="mt-2 text-sm">File: {{ $attachment->file_name }}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

    @else
        {{-- Tidak ada lampiran sama sekali --}}
        <p class="text-sm text-center text-gray-500">Tidak ada lampiran gambar yang dapat ditampilkan.</p>
    @endif
</td>
                </tr>

                {{-- Footer --}}
                <tr><td colspan="4" class="text-center font-normal italic bg-red-600 text-white p-3 border border-black text-xs"><div><p>All rights reserved by MoMatic.</p>Engginering & Deployment - Telkom Regional 1 Sumatera<p></p><p>2025</p></div></td></tr>
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
