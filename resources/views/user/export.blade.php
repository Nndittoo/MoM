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
        $allAttendeesRaw = $internalAttendees;
        foreach ($partnerData as $mitra) {
            if (is_array($mitra['attendees'] ?? null)) {
                $allAttendeesRaw = array_merge($allAttendeesRaw, $mitra['attendees']);
            }
        }
        
        $allAttendees = array_unique($allAttendeesRaw); 
        $totalAttendeesCount = count($allAttendees);
        
        // Mengubah daftar peserta menjadi string yang dipisahkan koma
        $attendeeListString = implode(', ', $allAttendees);
        
        // STRUKTUR DATA TANDA TANGAN DINAMIS
        $signatoryGroups = [];

        
        
        // Gabungkan dengan grup Mitra
        foreach ($partnerData as $mitra) {
            $signatoryGroups[] = [
                'name' => $mitra['name'],
                'attendees' => $mitra['attendees'] ?? []
            ];
        }
        
        $totalSignatoryGroups = count($signatoryGroups);
    
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
                    <td colspan="4" class="pt-6">Demikian MoM ini dibuat untuk diketahui dan ditindaklanjuti bersama.</td>
                </tr>

                {{-- TANDA TANGAN PESERTA DENGAN MULTI-NAMA DINAMIS --}}
                @if($totalSignatoryGroups > 0)
                <tr>
                    <td colspan="4" class="pt-10">
                        <table class="w-full text-center border-collapse">
                            <thead>
                                <tr class="border-b border-black">
                                   
                                    @foreach($signatoryGroups as $group)
                                        {{-- Gunakan total kelompok untuk membagi lebar --}}
                                        <th class="p-2 font-semibold w-1/{{ $totalSignatoryGroups }}">{{ $group['name'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="align-top">
                                    {{-- ITERASI SEMUA DAFTAR PESERTA BERDASARKAN GRUP --}}
                                    @foreach($signatoryGroups as $group)
                                        <td class="p-2">
                                            <div class="flex flex-col">
                                                @forelse($group['attendees'] as $name)
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
                        {{-- Jika total gambar ganjil dan ini adalah gambar terakhir,
                             tutup grid saat ini, dan tampilkan gambar ini di tengah
                             di luar grid. 
                        </div> {{-- Tutup grid-cols-2 sebelum gambar terakhir --}}
                        <div class="flex justify-center w-full mt-4"> 
                            <div class="text-center w-1/2">
                                <img src="{{ asset('storage/' . $attachment->file_path) }}" 
                                     alt="Lampiran Rapat" 
                                     class="w-full h-auto mx-auto border object-contain">
                                <p class="mt-2 text-sm">File: {{ $attachment->file_name }}</p>
                            </div>
                        </div>
                        {{-- Karena sudah menutup div.grid-cols-2 di atas,
                             tidak perlu membukanya lagi untuk loop ini.
                             Break loop karena gambar terakhir sudah diurus. --}}
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
