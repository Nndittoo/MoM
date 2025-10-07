<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Export MoM - {{ $mom->title }}</title> 
    <script src="https://cdn.tailwindcss.com"></script> 
    <style> 
    
        @media print { 
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; } 
        } 
    </style> 
</head> 
<body class="bg-white"> 
    
    @php 
        use Carbon\Carbon;
        // Ambil attachment pertama untuk ditampilkan di bagian bawah
        $attachment = $mom->attachments->first();
        $imageUrl = $attachment 
            ? asset('storage/' . $attachment->file_path) 
            : asset('img/lampiran-kosong.png'); 
        $meetingDate = Carbon::parse($mom->meeting_date);
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
                    <td class="border border-black p-2">{{ $mom->leader->name }}</td> 
                    <td class="border border-black p-2 font-semibold">Notulen</td> 
                    <td class="border border-black p-2">{{ $mom->notulen->name }}</td> 
                </tr> 
                
                {{-- Peserta --}}
                <tr> 
                    <td class="border border-black p-2 font-semibold">Peserta</td> 
                    <td colspan="3" class="border border-black p-2">
                        <ul class="list-disc ml-5">
                            @foreach($mom->attendees as $attendee)
                                <li>{{ $attendee->name }}</li>
                            @endforeach
                        </ul>
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
                    <td colspan="4" class="border border-black p-2 font-semibold bg-gray-200 text-center uppercase">Hasil Pembahasan / Tindak Lanjut</td> 
                </tr> 
                
                {{-- Hasil Pembahasan --}}
                <tr> 
                    <td colspan="4" class="border border-black p-4"> 
                        <div class="font-sans whitespace-pre-wrap text-sm leading-relaxed">
                            {{-- Output Pembahasan (dari editor QuillJS) --}}
                            {!! $mom->pembahasan !!}
                        </div>
                    </td> 
                </tr> 
                
                {{-- Tindak Lanjut --}}
                @if($mom->actionItems->isNotEmpty())
                <tr>
                    <td colspan="4" class="border border-black p-2 font-semibold bg-gray-200 text-center uppercase">Tindak Lanjut (Action Items)</td>
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
                
                {{-- Lampiran Header --}}
                <tr><td colspan="4" class="text-center font-bold text-2xl pt-10 pb-4">LAMPIRAN</td></tr> 
                
                {{-- Gambar Lampiran --}}
                <tr>
                    <td colspan="4" class="p-4 border border-black text-center">
                        @if($attachment && str_starts_with($attachment->mime_type, 'image/'))
                            <img src="{{ $imageUrl }}" alt="Lampiran Rapat" class="w-full max-w-md mx-auto">
                            <p class="mt-2 text-xs">File: {{ $attachment->file_name }}</p>
                        @else
                            <p class="text-sm text-gray-500">Tidak ada lampiran gambar utama yang dapat ditampilkan.</p>
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