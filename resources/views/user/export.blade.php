<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export MoM - {{ $mom->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* --- STYLING KHUSUS UNTUK KONTEN WYSIWYG (TinyMCE/Quill) SAAT DICETAK --- */
        .wysiwyg-content {
            font-family: 'Inter', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #000; /* Pastikan teks hitam saat dicetak */
        }

        /* Tabel dari TinyMCE */
        .wysiwyg-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .wysiwyg-content table td, .wysiwyg-content table th {
            border: 1px solid #000 !important; /* Border hitam tegas */
            padding: 5px 8px;
            vertical-align: top;
        }

        /* List (Bullet & Numbering) */
        .wysiwyg-content ul { list-style-type: disc; padding-left: 1.5em; margin: 5px 0; }
        .wysiwyg-content ol { list-style-type: decimal; padding-left: 1.5em; margin: 5px 0; }
        .wysiwyg-content li { margin-bottom: 0.2em; }

        /* Heading & Paragraph */
        .wysiwyg-content h1, .wysiwyg-content h2, .wysiwyg-content h3 { font-weight: bold; margin-top: 1em; margin-bottom: 0.5em; }
        .wysiwyg-content p { margin-bottom: 0.5em; }

        /* --- SISA CSS PRINTING --- */
        @media print {
            @page { margin: 1cm; }
            body { margin: 0; }

            tr { page-break-inside: avoid; page-break-after: auto; }
            .content-row { page-break-inside: auto !important; page-break-after: auto !important; }
            .content-cell { page-break-inside: auto !important; padding: 0.75rem !important; }

            img { max-width: 100% !important; height: auto !important; }

            .border-black { border-color: #000 !important; }
            .border { border-style: solid; border-width: 1px; }
            .export-table { border-collapse: collapse; border-spacing: 0; border: 1px solid #000; width: 100%; }

            .bg-gray-200 { background-color: #e5e7eb !important; }
            .bg-red-600 { background-color: #dc2626 !important; }
            .text-white { color: #fff !important; }
        }

        .attendee-container { display: flex; flex-wrap: wrap; padding: 0; margin: 0; }
        .attendee-group { width: 33%; box-sizing: border-box; padding-right: 1rem; margin-bottom: 0.5rem; }
        .attendee-group h4 { font-weight: bold; font-size: 0.9em; margin-bottom: 2px; text-decoration: underline; }
        .attendee-group ul { list-style-type: disc; padding-left: 15px; margin: 0; font-size: 0.9em; }
    </style>
</head>
<body class="bg-white text-black">

    @php
        use Carbon\Carbon;
        $meetingDate = Carbon::parse($mom->meeting_date);

        // --- LOGIKA PESERTA ---
        $internalDataContainers = is_array($mom->nama_peserta ?? null) ? $mom->nama_peserta : json_decode($mom->nama_peserta ?? '[]', true);
        $partnerDataContainers = is_array($mom->nama_mitra ?? null) ? $mom->nama_mitra : json_decode($mom->nama_mitra ?? '[]', true);

        $internalAttendeeGroups = [];
        if (is_array($internalDataContainers)) {
            foreach ($internalDataContainers as $unit) {
                if (is_array($unit['attendees'] ?? null) && !empty($unit['attendees'])) {
                    $internalAttendeeGroups[] = ['name' => $unit['unit'] ?? 'Unit Internal', 'attendees' => $unit['attendees']];
                }
            }
        }

        $signatoryGroups = [];
        if (is_array($partnerDataContainers)) {
            foreach ($partnerDataContainers as $mitra) {
                if (is_array($mitra['attendees'] ?? null) && !empty($mitra['attendees'])) {
                    $signatoryGroups[] = ['name' => $mitra['name'] ?? 'Pihak Mitra', 'attendees' => $mitra['attendees'] ?? []];
                }
            }
        }

        // Hitung kolom tanda tangan
        $totalSignatoryGroups = count($signatoryGroups);
        $signatoryColumnCount = max(1, $totalSignatoryGroups);
        $columnWidth = $signatoryColumnCount > 0 ? (100 / $signatoryColumnCount) : 100;
    @endphp

    <div id="pdf-preview" class="p-0 min-w-[800px] font-sans">
        <table class="w-full mb-4 export-table border border-black">
            <tbody>
                {{-- Header Logo & Judul --}}
                <tr class="page-break-inside: avoid">
                    <td class="align-middle text-center border border-black p-2 w-1/4">
                        <img src="{{ asset('img/telkom.png') }}" alt="Company Logo" class="h-24 mx-auto object-contain">
                    </td>
                    <td colspan="3" class="text-center align-middle border border-black bg-gray-100">
                        <p class="font-bold text-2xl italic mb-1">MINUTE OF MEETING</p>
                        <p class="font-bold text-lg uppercase">{{ $mom->title }}</p>
                    </td>
                </tr>

                {{-- Informasi Dasar --}}
                <tr class="page-break-inside: avoid">
                    <td class="border border-black p-2 font-semibold bg-gray-50 w-1/6">Pimpinan Rapat</td>
                    <td class="border border-black p-2 w-1/3">{{ $mom->pimpinan_rapat }}</td>
                    <td class="border border-black p-2 font-semibold bg-gray-50 w-1/6">Notulen</td>
                    <td class="border border-black p-2 w-1/3">{{ $mom->notulen }}</td>
                </tr>

                <tr class="page-break-inside: avoid">
                    <td class="border border-black p-2 font-semibold bg-gray-50">Waktu</td>
                    <td colspan="3" class="border border-black p-2">
                        {{ $meetingDate->translatedFormat('l, d F Y') }} | {{ Carbon::parse($mom->start_time)->format('H:i') }} – {{ Carbon::parse($mom->end_time)->format('H:i') }}
                    </td>
                </tr>

                <tr class="page-break-inside: avoid">
                    <td class="border border-black p-2 font-semibold bg-gray-50">Tempat</td>
                    <td colspan="3" class="border border-black p-2">{{ $mom->location }}</td>
                </tr>

                {{-- Peserta --}}
                <tr class="page-break-inside: avoid">
                    <td class="border border-black p-2 font-semibold bg-gray-50 align-top">Peserta</td>
                    <td colspan="3" class="border border-black p-2">
                        @if (!empty($internalAttendeeGroups) || !empty($signatoryGroups))
                            <div class="attendee-container">
                                @foreach(array_merge($internalAttendeeGroups, $signatoryGroups) as $group)
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
                            <span class="italic text-gray-500">Tidak ada peserta tercatat.</span>
                        @endif
                    </td>
                </tr>

                {{-- Agenda --}}
                <tr class="page-break-inside: avoid">
                    <td colspan="4" class="border border-black p-2 font-bold bg-gray-200 text-center uppercase tracking-wider">Agenda</td>
                </tr>
                <tr class="page-break-inside: avoid">
                    <td colspan="4" class="border border-black p-3">
                        <ol class="list-decimal ml-5 space-y-1">
                            @foreach($mom->agendas as $agenda)
                                <li>{{ $agenda->item }}</li>
                            @endforeach
                        </ol>
                    </td>
                </tr>

                {{-- Spacer --}}
                <tr class="h-2 border-l border-r border-black"><td colspan="4"></td></tr>

                {{-- Hasil Pembahasan --}}
                <tr class="page-break-inside: avoid">
                    <td colspan="4" class="border border-black p-2 font-bold bg-gray-200 text-center uppercase tracking-wider">Hasil Pembahasan</td>
                </tr>
                <tr class="content-row">
                    <td colspan="4" class="border border-black content-cell p-4">
                        {{-- INI BAGIAN PENTING: Class 'wysiwyg-content' akan memformat HTML mentah dari TinyMCE --}}
                        <div class="wysiwyg-content">
                            {!! $mom->pembahasan !!}
                        </div>
                    </td>
                </tr>

                {{-- Tindak Lanjut --}}
                @if($mom->actionItems->isNotEmpty())
                <tr class="page-break-inside: avoid">
                    <td colspan="4" class="border border-black p-2 font-bold bg-gray-200 text-center uppercase tracking-wider">Tindak Lanjut</td>
                </tr>
                <tr class="content-row">
                    <td colspan="4" class="border border-black p-4">
                        <ul class="list-disc ml-5 space-y-2 text-sm">
                            @foreach($mom->actionItems as $item)
                                <li>
                                    <strong>{{ $item->item }}</strong>
                                    <span class="text-gray-600 text-xs ml-2">(Deadline: {{ Carbon::parse($item->due)->translatedFormat('d F Y') }})</span>
                                </li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                @endif

                {{-- Penutup --}}
                <tr class="page-break-inside: avoid">
                    <td colspan="4" class="p-4 text-center italic text-sm">
                        Demikian MoM ini dibuat untuk diketahui dan ditindaklanjutkan bersama.
                    </td>
                </tr>

                {{-- Tanda Tangan --}}
                @if($totalSignatoryGroups > 0)
                    @php $chunks = array_chunk($signatoryGroups, 3); @endphp
                    <tr class="page-break-inside: avoid">
                        <td colspan="4" class="border-t border-black pt-4 px-4 pb-10">
                            <table class="w-full text-center border-collapse" style="border: none;">
                                <thead>
                                    <tr><th colspan="{{ $signatoryColumnCount }}" class="text-center font-bold text-xl pb-6" style="border: none;">TANDA TANGAN</th></tr>
                                    <tr>
                                        @foreach($signatoryGroups as $group)
                                            <th class="font-semibold pb-16 align-bottom" style="width: {{ $columnWidth }}%; border: none;">{{ $group['name'] }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($chunks as $row)
                                        <tr>
                                            @foreach($row as $group)
                                                <td class="align-top pt-4" style="width: {{ $columnWidth }}%; border: none;">
                                                    @foreach($group['attendees'] as $name)
                                                        <div class="mt-8 border-b border-black w-3/4 mx-auto"></div>
                                                        <p class="font-medium text-sm mt-1">{{ $name }}</p>
                                                    @endforeach
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @endif

                {{-- Lampiran --}}
                <tr class="page-break-inside: avoid">
                    <td colspan="4" class="border border-black p-2 font-bold bg-gray-200 text-center uppercase tracking-wider">LAMPIRAN</td>
                </tr>
                <tr class="content-row">
                    <td colspan="4" class="p-4 border border-black">
                        @if($mom->attachments->isNotEmpty())
                            @php
                                $imageAttachments = $mom->attachments->filter(fn($attachment) => str_starts_with($attachment->mime_type, 'image/'))->values();
                            @endphp

                            @if ($imageAttachments->count() > 0)
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach($imageAttachments as $attachment)
                                        <div class="text-center break-inside-avoid mb-4">
                                            <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="Lampiran" class="max-h-64 mx-auto border object-contain">
                                            <p class="mt-1 text-xs italic">{{ $attachment->file_name }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-center text-gray-500 italic">Lampiran tersedia dalam format non-gambar (PDF/Doc).</p>
                            @endif
                        @else
                            <p class="text-sm text-center text-gray-500 italic">Tidak ada lampiran gambar yang dapat ditampilkan.</p>
                        @endif
                    </td>
                </tr>

                {{-- Footer --}}
                <tr class="page-break-inside: avoid">
                    <td colspan="4" class="text-center font-normal italic bg-red-600 text-white p-2 border border-black text-xs">
                        <p>All rights reserved by MoMatic.</p>
                        <p>Engginering & Deployment - Telkom Regional 1 Sumatera</p>
                        <p>2025</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 800); // Delay sedikit lebih lama agar gambar terload
        }
    </script>
</body>
</html>
