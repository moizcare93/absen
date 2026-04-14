<section class="space-y-4">
    <header class="rounded-[2rem] bg-slate-900 p-5 text-white shadow-soft">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-200">Laporan</p>
        <h1 class="mt-3 text-2xl font-black">Rekap Operasional</h1>
        <p class="mt-2 text-sm text-slate-300">Gabungan laporan absensi, cuti, dan jadwal per bulan.</p>
    </header>

    <div class="admin-panel rounded-[2rem] p-5">
        <form method="get" action="<?php echo site_url('laporan'); ?>" class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Bulan</label>
                    <input type="month" name="bulan" value="<?php echo html_escape($month); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>
                <?php if ((int) $current_user['level'] <= 3): ?>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Pegawai</label>
                        <select name="pegawai_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            <option value="">Semua pegawai</option>
                            <?php foreach ($employees as $employee): ?>
                                <option value="<?php echo (int) $employee['id']; ?>" <?php echo (string) $selected_pegawai_id === (string) $employee['id'] ? 'selected' : ''; ?>><?php echo html_escape($employee['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>
            <button type="submit" class="w-full rounded-2xl bg-brand-500 px-4 py-3 text-sm font-bold text-white">Tampilkan Laporan</button>
        </form>
        <div class="mt-4 grid grid-cols-2 gap-3">
            <a href="<?php echo site_url('laporan/export/excel?bulan=' . rawurlencode($month) . ($selected_pegawai_id ? '&pegawai_id=' . (int) $selected_pegawai_id : '')); ?>" class="rounded-2xl bg-emerald-500 px-4 py-3 text-center text-sm font-bold text-white">Export Excel</a>
            <a href="<?php echo site_url('laporan/export/pdf?bulan=' . rawurlencode($month) . ($selected_pegawai_id ? '&pegawai_id=' . (int) $selected_pegawai_id : '')); ?>" class="rounded-2xl bg-slate-900 px-4 py-3 text-center text-sm font-bold text-white">Export PDF</a>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-3">
    <div class="admin-panel rounded-[2rem] p-5">
        <p class="text-sm font-bold text-slate-900">Laporan Absensi</p>
        <div class="mt-4 space-y-3">
            <?php foreach ($attendance_report as $row): ?>
                <article class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-sm font-bold text-slate-900"><?php echo html_escape($row['nama']); ?></p>
                    <p class="mt-2 text-xs text-slate-600">Total: <?php echo (int) $row['total_hari']; ?> • Hadir: <?php echo (int) $row['hadir']; ?> • Terlambat: <?php echo (int) $row['terlambat']; ?> • Cuti: <?php echo (int) $row['cuti']; ?> • Izin: <?php echo (int) $row['izin']; ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="admin-panel rounded-[2rem] p-5">
        <p class="text-sm font-bold text-slate-900">Laporan Cuti</p>
        <div class="mt-4 space-y-3">
            <?php foreach ($leave_report as $row): ?>
                <article class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-sm font-bold text-slate-900"><?php echo html_escape($row['nama']); ?></p>
                    <p class="mt-2 text-xs text-slate-600">Total: <?php echo (int) $row['total_pengajuan']; ?> • Pending: <?php echo (int) $row['pending']; ?> • Approval Unit: <?php echo (int) $row['approved_unit']; ?> • Approval HR: <?php echo (int) $row['approved_hr']; ?> • Ditolak: <?php echo (int) $row['ditolak']; ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="admin-panel rounded-[2rem] p-5">
        <p class="text-sm font-bold text-slate-900">Laporan Jadwal</p>
        <div class="mt-4 space-y-3">
            <?php foreach ($schedule_report as $row): ?>
                <article class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-sm font-bold text-slate-900"><?php echo html_escape($row['nama']); ?></p>
                    <p class="mt-2 text-xs text-slate-600">Total shift terjadwal bulan ini: <?php echo (int) $row['total_shift']; ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
    </div>
</section>
