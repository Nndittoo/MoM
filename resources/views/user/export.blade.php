<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export MoM - EVALUASI PROGRESS PROJECT OTN TR1</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-white">
    <div id="pdf-preview" class="p-6 md:p-8 min-w-[800px] bg-white text-gray-900 font-sans">
        <table class="w-full mb-4 border-collapse">
            <tbody>
                <tr>
                    <td class="align-middle text-center border border-black p-2 w-1/4"><img src="{{ asset('img/logo.png') }}" alt="Company Logo" class="h-32 mx-auto"></td>
                    <td colspan="3" class="text-center align-middle border border-black"><p class="font-bold text-2xl italic">MINUTE OF MEETING</p><p class="font-semibold text-xl">EVALUASI PROGRESS PROJECT OTN TR1</p></td>
                </tr>
                <tr>
                    <td class="border border-black p-2 font-semibold">Pimpinan Rapat</td>
                    <td class="border border-black p-2">PED TR1</td>
                    <td class="border border-black p-2 font-semibold">Notulen</td>
                    <td class="border border-black p-2">M. Hanif A</td>
                </tr>
                <tr>
                    <td class="border border-black p-2 font-semibold">Peserta</td>
                    <td colspan="3" class="border border-black p-2"><ul class="list-disc ml-5"><li>Bg @satriasitorus</li><li>Anggota Tim A</li><li>Anggota Tim B</li><li>Anggota Tim C</li></ul></td>
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
                    <td colspan="4" class="border border-black p-2"><ul class="ml-4"><li class="list-decimal">Evaluasi Progress Project OTN TR1</li></ul></td>
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
... (lanjutan konten) ...
</pre>
                        <p class="mt-6">Demikian MoM ini dibuat untuk diketahui dan ditindaklanjuti bersama.</p>
                    </td>
                </tr>
                <tr><td colspan="4" class="text-center font-bold text-2xl pt-10 pb-4">LAMPIRAN</td></tr>
                <tr><td colspan="4" class="p-4 border border-black"><img src="{{ asset('img/lampiran.png') }}" alt="Lampiran Rapat" class="w-full"></td></tr>
                <tr><td colspan="4" class="text-center font-normal italic bg-red-600 text-white p-3 border border-black text-xs">All rights reserved...</td></tr>
            </tbody>
        </table>
    </div>

    <script>
        // Otomatis membuka dialog print saat halaman selesai dimuat
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
