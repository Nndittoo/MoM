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
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .content-row {
                 page-break-inside: auto !important; 
                 page-break-after: auto !important;
            }
            
            .content-cell {
                page-break-inside: auto !important;
                padding: 0.75rem !important; 
            }

            #lampiran-header-cell, #ttd-header-cell {
                padding-top: 8px !important;
                padding-bottom: 8px !important;
            }

            img {
                max-width: 100% !important;
                height: auto !important;
            }

            .ql-content ul, .ql-content ol {
                margin-left: 1.5em !important;
            }
            /* Sisa CSS lainnya */
            .border-black { border-color: #000; }
            .border { border-style: solid; border-width: 1px; }
            .export-table { border-collapse: collapse; border-spacing: 0; border: 1px solid #000; }
            .content-quill { word-break: break-word; overflow-wrap: break-word; }
            
            .text-demikian {
                padding-top: 5px !important; 
                padding-bottom: 5px !important; 
                vertical-align: middle;
            }
            
            .ttd-cell-content {
                height: 100%;
                display: block;
            }

            .ttd-wrapper-td {
                padding-top: 0 !important;
                padding-bottom: 0 !important;
            }
        }

        /* Kelas Dasar (Non-print) */
        .border-black { border-color: #000; }
        .border { border-style: solid; border-width: 1px; }
        .text-gray-900 { color: #1f2937; }
        .bg-gray-200 { background-color: #e5e7eb; }
        .bg-white { background-color: #fff; }
        .text-white { color: #fff; }
        .bg-red-600 { background-color: #dc2626; }
        .export-table { border-collapse: collapse; border-spacing: 0; border: 1px solid #000; }
        .content-quill ul { list-style-type: disc; margin-left: 20px; padding-left: 0; }
        .content-quill ol { list-style-type: decimal; margin-left: 20px; padding-left: 0; }
        .content-quill { word-break: break-word; overflow-wrap: break-word; }
        .attendee-container { display: flex; flex-wrap: wrap; padding: 0; margin: 0; }
        .attendee-group { width: 33%; box-sizing: border-box; padding-right: 1.5rem; margin-bottom: 0.5rem; }
        .attendee-group h4 { font-weight: bold; margin-top: 0.5rem; margin-bottom: 4px; }
        .attendee-group ul { list-style-type: disc; padding-left: 15px; margin: 0; }
    </style>
</head>
<body class="bg-white">

    @php
        use Carbon\Carbon;
        $meetingDate = Carbon::parse($mom->meeting_date);
        $internalDataContainers = is_array($mom->nama_peserta ?? null) ? $mom->nama_peserta : json_decode($mom->nama_peserta ?? '[]', true);
        $partnerDataContainers = is_array($mom->nama_mitra ?? null) ? $mom->nama_mitra : json_decode($mom->nama_mitra ?? '[]', true);
        $internalAttendeeGroups = [];
        $allAttendeesRaw = []; 
        if (is_array($internalDataContainers)) {
            foreach ($internalDataContainers as $unit) {
                if (is_array($unit['attendees'] ?? null) && !empty($unit['attendees'])) {
                    $internalAttendeeGroups[] = ['name' => $unit['unit'] ?? 'Unit Internal', 'attendees' => $unit['attendees']];
                    $allAttendeesRaw = array_merge($allAttendeesRaw, $unit['attendees']);
                }
            }
        }
        $signatoryGroups = [];
        if (is_array($partnerDataContainers)) {
            foreach ($partnerDataContainers as $mitra) {
                if (is_array($mitra['attendees'] ?? null) && !empty($mitra['attendees'])) {
                    $allAttendeesRaw = array_merge($allAttendeesRaw, $mitra['attendees']);
                    $signatoryGroups[] = ['name' => $mitra['name'] ?? 'Pihak Mitra', 'attendees' => $mitra['attendees'] ?? []];
                }
            }
        }
        $allAttendees = array_unique($allAttendeesRaw);
        $totalAttendeesCount = count($allAttendees);
        $totalSignatoryGroups = count($signatoryGroups);

        $signatoryColumnCount = max(1, count($signatoryGroups));
        $columnWidth = $signatoryColumnCount > 0 ? (100 / $signatoryColumnCount) : 100;

    @endphp

    <div id="pdf-preview" class="p-6 md:p-8 min-w-[800px] bg-white text-gray-900 font-sans">
        <table class="w-full mb-4 export-table">
            <tbody>
                <tr class="page-break-inside: avoid">
                    <td class="align-middle text-center border border-black p-2 w-1/4">
                        <img src="{{ asset('img/telkom.png') }}" alt="Company Logo" class="h-32 mx-auto">
                    </td>
                    <td colspan="3" class="text-center align-middle border border-black">
                        <p class="font-bold text-2xl italic">MINUTE OF MEETING</p>
                        <p class="font-bold text-xl">{{ $mom->title }}</p>
                    </td>
                </tr>

                <tr class="page-break-inside: avoid">
                    <td class="border border-black p-2 font-semibold">Pimpinan Rapat</td>
                    <td class="border border-black p-2">{{ $mom->pimpinan_rapat }}</td>
                    <td class="border border-black p-2 font-semibold">Notulen</td>
                    <td class="border border-black p-2">{{ $mom->notulen }}</td>
                </tr>

                <tr class="page-break-inside: avoid">
                    <td class="border border-black p-2 font-semibold">Peserta</td>
                    <td colspan="3" class="border border-black p-2">
                        @if (!empty($internalAttendeeGroups) || !empty($signatoryGroups))
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

                <tr class="page-break-inside: avoid">
                    <td class="border border-black p-2 font-semibold">Waktu</td>
                    <td colspan="3" class="border border-black p-2">
                        {{ $meetingDate->translatedFormat('l, d F Y') }} | {{ Carbon::parse($mom->start_time)->format('H:i') }} – {{ Carbon::parse($mom->end_time)->format('H:i') }}
                    </td>
                </tr>

                <tr class="page-break-inside: avoid">
                    <td class="border border-black p-2 font-semibold">Tempat</td>
                    <td colspan="3" class="border border-black p-2">{{ $mom->location }}</td>
                </tr>

                <tr class="page-break-inside: avoid">
                    <td colspan="4" class="border border-black p-2 font-semibold bg-gray-200 text-center uppercase">Agenda</td>
                </tr>

                <tr class="page-break-inside: avoid">
                    <td colspan="4" class="border border-black p-2">
                        <ol class="ml-4">
                            @foreach($mom->agendas as $agenda)
                                <li class="list-decimal">{{ $agenda->item }}</li>
                            @endforeach
                        </ol>
                    </td>
                </tr>
                <tr class="h-2 page-break-inside: avoid"><td colspan="4"></td></tr>

                <tr class="page-break-inside: avoid">
                    <td colspan="4" class="border border-black p-2 font-semibold bg-gray-200 text-center uppercase">Hasil Pembahasan</td>
                </tr>

                {{-- Hasil Pembahasan --}}
                <tr class="content-row"> 
                    <td colspan="4" class="border border-black content-cell">
                        <div class="font-sans whitespace-pre-wrap text-sm leading-relaxed content-quill">
                            {!! $mom->pembahasan !!}
                        </div>
                    </td>
                </tr>

                {{-- Tindak Lanjut Header --}}
                @if($mom->actionItems->isNotEmpty())
                <tr class="page-break-inside: avoid">
                    <td colspan="4" class="border border-black p-2 font-semibold bg-gray-200 text-center uppercase">Tindak Lanjut</td>
                </tr>
                {{-- Tindak Lanjut Content --}}
                <tr class="content-row">
                    <td colspan="4" class="border border-black p-4">
                        <ul class="list-disc ml-5 space-y-2 text-sm">
                            @foreach($mom->actionItems as $item)
                                <li>{{ $item->item }} (Deadline: {{ Carbon::parse($item->due)->translatedFormat('d F Y') }})</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
                @endif

                {{-- Baris Demikian MoM --}}
                <tr class="page-break-inside: avoid">
                    <td colspan="4" class="text-demikian">Demikian MoM ini dibuat untuk diketahui dan ditindaklanjutkan bersama.</td>
                </tr>

                {{-- Tanda tangan peserta --}}
                @if($totalSignatoryGroups > 0)
    @php
        $chunks = array_chunk($signatoryGroups, 3);
    @endphp

    <tr class="page-break-inside: avoid">
        <td colspan="4" class="border-t border-black ttd-wrapper-td"> 
            <table class="w-full text-center border-collapse">
                <thead>
                    <tr class="page-break-inside: avoid">
                        <th id="ttd-header-cell" colspan="{{ $signatoryColumnCount }}" class="text-center font-bold text-2xl align-middle p-3">TANDA TANGAN</th>
                    </tr>
                    <tr class="page-break-inside: avoid">
                        @foreach($signatoryGroups as $group)
                            <th class="font-semibold p-2" style="width: {{ $columnWidth }}%;">{{ $group['name'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($chunks as $index => $row)
                        <tr class="align-top {{ $index > 0 ? 'pt-10' : '' }}"> 
                            @foreach($row as $group)
                                <td class="p-2" style="width: {{ $columnWidth }}%;" >
                                    <div class="ttd-cell-content"> 
                                        @forelse($group['attendees'] as $name)
                                            <p class="pt-16 underline">{{ $name }}</p>
                                        @empty
                                            <p class="pt-16 italic text-gray-500">N/A</p>
                                        @endforelse
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>
@endif


                {{-- Lampiran Header --}}
                <tr class="page-break-inside: avoid"> 
                    <td id="lampiran-header-cell" colspan="4" class="border-t border-black border-l border-r border-b border-black text-center font-bold text-2xl align-middle">LAMPIRAN</td>
                </tr>

                {{-- Gambar Lampiran --}}
                <tr class="content-row">
                   <td colspan="4" class="p-4 border border-black">
    @if($mom->attachments->isNotEmpty())
        @php
            $imageAttachments = $mom->attachments->filter(fn($attachment) => str_starts_with($attachment->mime_type, 'image/'))->values();
            $imageCount = $imageAttachments->count();
        @endphp

        @if ($imageCount === 0)
            <p class="text-sm text-center text-gray-500">Tidak ada lampiran gambar yang dapat ditampilkan.</p>
        @elseif ($imageCount === 1)
            <div class="flex justify-center">
                <div class="text-center w-full max-w-xl">
                    <img src="{{ asset('storage/' . $imageAttachments[0]->file_path) }}" alt="Lampiran Rapat" class="w-full h-auto mx-auto border object-contain">
                    <p class="mt-2 text-sm">File: {{ $imageAttachments[0]->file_name }}</p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4">
                @foreach($imageAttachments as $index => $attachment)
                    @if ($imageCount % 2 !== 0 && $index === $imageCount - 1)
                        </div>
                        <div class="flex justify-center w-full mt-4">
                            <div class="text-center w-1/2">
                                <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="Lampiran Rapat" class="w-full h-auto mx-auto border object-contain">
                                <p class="mt-2 text-sm">File: {{ $attachment->file_name }}</p>
                            </div>
                        </div>
                        @break
                    @else
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="Lampiran Rapat" class="w-full h-auto mx-auto border object-contain">
                            <p class="mt-2 text-sm">File: {{ $attachment->file_name }}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

    @else
        <p class="text-sm text-center text-gray-500">Tidak ada lampiran gambar yang dapat ditampilkan.</p>
    @endif
</td>
                </tr>

                {{-- Footer --}}
                <tr class="page-break-inside: avoid">
                    <td colspan="4" class="text-center font-normal italic bg-red-600 text-white p-3 border border-black text-xs">
                        <p>All rights reserved by MoMatic.</p>
                        <p>Engginering & Deployment - Telkom Regional 1 Sumatera</p>
                        <p>2025</p>
                    </td>
                </tr>
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