<header class="rounded-[2rem] bg-slate-900 p-5 text-white shadow-soft lg:p-7">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-200">Dashboard</p>
            <h1 class="mt-3 text-2xl font-black leading-tight">Halo, <?php echo html_escape($current_user['nama']); ?></h1>
            <p class="mt-1 text-sm text-slate-300"><?php echo html_escape($current_user['nama_role']); ?> • <?php echo html_escape($current_user['nama_unit']); ?></p>
        </div>
        <a href="<?php echo site_url('auth/logout'); ?>" class="rounded-2xl border border-white/15 px-3 py-2 text-xs font-semibold text-white/80">Logout</a>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-3 lg:max-w-2xl">
        <div class="rounded-2xl bg-white/10 p-4">
            <p class="text-xs text-slate-300">Status Hari Ini</p>
            <p class="mt-2 text-lg font-bold"><?php echo html_escape(isset($snapshot['attendance']['status']) ? $snapshot['attendance']['status'] : 'Belum Absen'); ?></p>
        </div>
        <div class="rounded-2xl bg-white/10 p-4">
            <p class="text-xs text-slate-300">Jam Masuk</p>
            <p class="mt-2 text-lg font-bold"><?php echo html_escape(isset($snapshot['attendance']['jam_masuk']) ? $snapshot['attendance']['jam_masuk'] : '--:--'); ?></p>
        </div>
    </div>
 </header>

<section class="mt-4 space-y-4">
    <?php if (!empty($admin_snapshot)): ?>
        <div class="grid grid-cols-3 gap-3 xl:grid-cols-3">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-4 shadow-soft">
                <p class="text-xs text-slate-500">Pegawai Aktif</p>
                <p class="mt-2 text-2xl font-black text-slate-900"><?php echo (int) $admin_snapshot['employees']; ?></p>
            </div>
            <div class="rounded-[2rem] border border-slate-200 bg-white p-4 shadow-soft">
                <p class="text-xs text-slate-500">Cuti Pending</p>
                <p class="mt-2 text-2xl font-black text-slate-900"><?php echo (int) $admin_snapshot['pending_leave']; ?></p>
            </div>
            <div class="rounded-[2rem] border border-slate-200 bg-white p-4 shadow-soft">
                <p class="text-xs text-slate-500">Absen Hari Ini</p>
                <p class="mt-2 text-2xl font-black text-slate-900"><?php echo (int) $admin_snapshot['attendance_today']; ?></p>
            </div>
        </div>
    <?php endif; ?>

    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-soft">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-slate-900">Aksi Cepat</p>
                <p class="text-xs text-slate-500">Dirancang untuk penggunaan satu tangan di layar HP.</p>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
            <a href="<?php echo site_url('absensi'); ?>" class="rounded-2xl bg-brand-500 px-4 py-4 text-sm font-bold text-white">Absen Sekarang</a>
            <a href="<?php echo site_url('cuti'); ?>" class="rounded-2xl bg-slate-100 px-4 py-4 text-sm font-bold text-slate-700">Ajukan Cuti</a>
            <?php if ((int) $current_user['level'] <= 3): ?>
                <a href="<?php echo site_url('pegawai'); ?>" class="rounded-2xl bg-slate-100 px-4 py-4 text-sm font-bold text-slate-700">Kelola Pegawai</a>
                <a href="<?php echo site_url('laporan'); ?>" class="rounded-2xl bg-slate-100 px-4 py-4 text-sm font-bold text-slate-700">Lihat Laporan</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-[1.4fr_0.9fr]">
    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-soft">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-slate-900">Jadwal Terdekat</p>
                <p class="text-xs text-slate-500">5 jadwal berikutnya untuk memudahkan persiapan shift.</p>
            </div>
            <a href="<?php echo site_url('jadwal'); ?>" class="text-xs font-semibold text-brand-500">Lihat Semua</a>
        </div>

        <div class="mt-4 space-y-3">
            <?php if (!empty($snapshot['next_schedule'])): ?>
                <?php foreach ($snapshot['next_schedule'] as $row): ?>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900"><?php echo html_escape($row['nama_shift']); ?></p>
                                <p class="text-xs text-slate-500"><?php echo html_escape($row['tanggal']); ?></p>
                            </div>
                            <div class="text-right text-sm font-semibold text-brand-600">
                                <?php echo html_escape(substr($row['jam_masuk'], 0, 5)); ?> - <?php echo html_escape(substr($row['jam_keluar'], 0, 5)); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="rounded-2xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Belum ada jadwal pada bulan ini.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-soft">
        <p class="text-sm font-bold text-slate-900">Saldo Cuti</p>
        <div class="mt-4 grid grid-cols-2 gap-3">
            <div class="rounded-2xl bg-emerald-50 p-4">
                <p class="text-xs text-emerald-700">Sisa Tahunan</p>
                <p class="mt-2 text-2xl font-black text-emerald-800">
                    <?php echo html_escape(isset($snapshot['leave_balance']['saldo_tahunan']) ? (int) $snapshot['leave_balance']['saldo_tahunan'] - (int) $snapshot['leave_balance']['terpakai_tahunan'] : 0); ?>
                </p>
            </div>
            <div class="rounded-2xl bg-amber-50 p-4">
                <p class="text-xs text-amber-700">Sudah Dipakai</p>
                <p class="mt-2 text-2xl font-black text-amber-800"><?php echo html_escape(isset($snapshot['leave_balance']['terpakai_tahunan']) ? $snapshot['leave_balance']['terpakai_tahunan'] : 0); ?></p>
            </div>
        </div>
    </div>
    </div>
</section>
