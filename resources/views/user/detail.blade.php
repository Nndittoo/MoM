@extends('layouts.app')

@section('title', 'Detail MoM | MoM Telkom')

@section('content')
<div class="p-4 rounded-lg mt-14">
    <div class="flex flex-col sm:flex-row items-start sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-text-primary dark:text-dark-text-primary">Detail Minute of Meeting</h1>
            <p class="mt-1 text-text-secondary dark:text-dark-text-secondary">EVALUASI PROGRESS PROJECT OTN TR1</p>
        </div>
        <div class="flex space-x-2 mt-4 sm:mt-0 w-full sm:w-auto">
            <a href="{{ url('/draft') }}" class="flex-1 sm:flex-initial inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-text-secondary bg-component-bg border border-border-light rounded-lg hover:bg-body-bg dark:bg-dark-component-bg dark:text-dark-text-secondary dark:border-border-dark dark:hover:text-white dark:hover:bg-dark-body-bg">
                <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
            </a>
            <button id="export-pdf-button" class="flex-1 sm:flex-initial inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-gradient-primary rounded-lg hover:opacity-90">
                <i class="fa-solid fa-file-pdf mr-2"></i>Export
            </button>
        </div>
    </div>

    <div class="bg-component-bg dark:bg-dark-component-bg rounded-lg shadow-md overflow-x-auto">
        <div id="pdf-preview" class="p-6 md:p-8 min-w-[800px] bg-white text-gray-900 font-sans">

            <table class="w-full mb-4 border-collapse">
                <tbody>
                    <tr>
                        <td class="align-middle text-center border border-black p-2 w-1/4">
                            <img src="{{ asset('img/logo.png') }}" alt="Company Logo" class="h-32 mx-auto">
                        </td>
                        <td colspan="3" class="text-center align-middle border border-black">
                            <p class="font-bold text-2xl italic">MINUTE OF MEETING</p>
                            <p class="font-semibold text-xl">EVALUASI PROGRESS PROJECT OTN TR1</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2 font-semibold">Pimpinan Rapat</td>
                        <td class="border border-black p-2">PED TR1</td>
                        <td class="border border-black p-2 font-semibold">Notulen</td>
                        <td class="border border-black p-2">M. Hanif A</td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2 font-semibold">Peserta</td>
                        <td colspan="3" class="border border-black p-2">
                            <ul class="list-disc ml-5">
                                <li>Bg @satriasitorus</li>
                                <li>Anggota Tim A</li>
                                <li>Anggota Tim B</li>
                                <li>Anggota Tim C</li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2 font-semibold">Waktu</td>
                        <td colspan="3" class="border border-black p-2">Senin, 15 September 2025 | 09.00 – 10.50</td>
                    </tr>
                    <tr>
                        <td class="border border-black p-2 font-semibold">Tempat</td>
                        <td colspan="3" class="border border-black p-2">Meeting Zoom (https://us06web.zoom.us/j/84110436337)</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="border border-black p-2 font-semibold bg-gray-200 text-center uppercase">Agenda</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="border border-black p-2">
                            <ul class="ml-4">
                                <li class="list-decimal">Evaluasi Progress Project OTN TR1</li>
                            </ul>
                        </td>
                    </tr>
                        <tr class="h-2"><td colspan="4"></td></tr>
                    <tr>
                        <td colspan="4" class="border border-black p-2 font-semibold bg-gray-200 text-center uppercase">Hasil Pembahasan / Tindak Lanjut</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="border border-black p-4">
                            <pre class="font-sans whitespace-pre-wrap text-sm leading-relaxed">
1. rap058
   - osp berstatus drop dan perlu survei ulang terkait perizinan warga.
   - remark: request mengubah lokasi ke sidamanik desa sibontuan.
2. kis768
   - osp berstatus drop dan perlu survei ulang.
   - status osp matdel.
3. kbj033
   - status fo fi, uplink ready.
   - plan survei: 22 September 2025
   - plan mos: 24 September 2025
   - plan matdel: 23 September 2025
   - plan instalasi: 25 September 2025
4. sumsel oki033
   - planning fi minggu ke-3 Oktober.
   - gpon menggunakan metro.
   - uplink menggunakan core existing node.b (2 core).
   - plan survei: 17 September 2025
   - plan mos: 25 September 2025
   - plan matdel: 22 September 2025
   - plan instalasi: 27 September 2025</pre>
                            <p class="mt-6">Demikian MoM ini dibuat untuk diketahui dan ditindaklanjuti bersama.</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="p-4"></td>
                    </tr>
                        <tr>
                        <td colspan="4" class="text-center font-bold text-2xl pt-10 pb-4">LAMPIRAN</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="p-4 border border-black">
                            <img src="{{ asset('img/lampiran.png') }}" alt="Lampiran Rapat" class="w-full">
                        </td>
                    </tr>
                        <tr>
                        <td colspan="4" class="p-4"></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-center font-normal italic bg-red-600 text-white p-3 border border-black text-xs">
                            All rights reserved. Passing on and copying of this document, use and communication of its contents are prohibited without written authorization from PT. TELKOM.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
