<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'navbar.php'; 

// Data Jadwal Hardcoded sesuai permintaan
$jadwal_weekday = [
    'Senin'  => ['Shift 1 (08:00 - 15:00)' => 'Cahya & Maia', 'Shift 2 (15:00 - 20:00)' => 'Aghni & Zidni'],
    'Selasa' => ['Shift 1 (08:00 - 15:00)' => 'Ferra & Haadziq', 'Shift 2 (15:00 - 20:00)' => 'Cahya & Maia'],
    'Rabu'   => ['Shift 1 (08:00 - 15:00)' => 'Aghni & Zidni', 'Shift 2 (15:00 - 20:00)' => 'Ferra & Haadziq'],
    'Kamis'  => ['Shift 1 (08:00 - 15:00)' => 'Cahya & Maia', 'Shift 2 (15:00 - 20:00)' => 'Aghni & Zidni'],
    'Jumat'  => ['Shift 1 (08:00 - 15:00)' => 'Ferra & Haadziq', 'Shift 2 (15:00 - 20:00)' => 'Cahya & Maia']
];

$jadwal_weekend = [
    'Sabtu'  => ['Shift 1 (09:00 - 15:00)' => 'Aghni & Zidni', 'Shift 2 (15:00 - 21:00)' => 'Ferra & Haadziq'],
    'Minggu' => ['Shift 1 (09:00 - 15:00)' => 'Cahya & Maia', 'Shift 2 (15:00 - 21:00)' => 'Aghni & Zidni']
];
?>

<div class="p-8">
    <div class="mb-8">
        <h1 class="text-4xl font-black text-[#800000] tracking-tight">Jadwal Admin</h1>
        <p class="text-slate-500 mt-1 font-medium italic">Jam operasional dan pembagian shift kerja staff admin.</p>
    </div>

    <!-- Jam Operasional Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <i data-lucide="sun" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Senin - Jumat</h4>
                <p class="text-xl font-black text-slate-800">08:00 - 20:00 <span class="text-sm font-medium text-slate-500">WIB</span></p>
                <p class="text-xs text-slate-400 mt-1">Shift 1: 08:00-15:00 | Shift 2: 15:00-20:00</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                <i data-lucide="sunset" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Sabtu - Minggu (Weekend)</h4>
                <p class="text-xl font-black text-slate-800">09:00 - 21:00 <span class="text-sm font-medium text-slate-500">WIB</span></p>
                <p class="text-xs text-slate-400 mt-1">Shift 1: 09:00-15:00 | Shift 2: 15:00-21:00</p>
            </div>
        </div>
    </div>

    <!-- Table Jadwal Weekday -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm mb-8">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <i data-lucide="calendar-days" class="w-5 h-5 text-blue-600"></i> Jadwal Weekday
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-y border-slate-200 text-slate-500 text-xs uppercase tracking-wider font-bold">
                        <th class="p-4 rounded-tl-xl w-1/3">Hari</th>
                        <th class="p-4 w-1/3">Shift 1 (08:00 - 15:00)</th>
                        <th class="p-4 rounded-tr-xl w-1/3">Shift 2 (15:00 - 20:00)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jadwal_weekday as $hari => $shifts): ?>
                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                        <td class="p-4 font-bold text-slate-800"><?= $hari ?></td>
                        <td class="p-4 text-slate-600">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-sm font-semibold border border-blue-100">
                                <i data-lucide="user" class="w-3.5 h-3.5"></i> <?= $shifts['Shift 1 (08:00 - 15:00)'] ?>
                            </span>
                        </td>
                        <td class="p-4 text-slate-600">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-sm font-semibold border border-indigo-100">
                                <i data-lucide="user" class="w-3.5 h-3.5"></i> <?= $shifts['Shift 2 (15:00 - 20:00)'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table Jadwal Weekend -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <i data-lucide="party-popper" class="w-5 h-5 text-orange-600"></i> Jadwal Weekend
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-orange-50 border-y border-orange-100 text-orange-800 text-xs uppercase tracking-wider font-bold">
                        <th class="p-4 rounded-tl-xl w-1/3">Hari</th>
                        <th class="p-4 w-1/3">Shift 1 (09:00 - 15:00)</th>
                        <th class="p-4 rounded-tr-xl w-1/3">Shift 2 (15:00 - 21:00)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jadwal_weekend as $hari => $shifts): ?>
                    <tr class="border-b border-orange-50/50 hover:bg-orange-50 transition-colors">
                        <td class="p-4 font-bold text-slate-800"><?= $hari ?></td>
                        <td class="p-4 text-slate-600">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-sm font-semibold border border-blue-100">
                                <i data-lucide="user" class="w-3.5 h-3.5"></i> <?= $shifts['Shift 1 (09:00 - 15:00)'] ?>
                            </span>
                        </td>
                        <td class="p-4 text-slate-600">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-sm font-semibold border border-indigo-100">
                                <i data-lucide="user" class="w-3.5 h-3.5"></i> <?= $shifts['Shift 2 (15:00 - 21:00)'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</main>
</div>
<script>lucide.createIcons();</script>
</body>
</html>
