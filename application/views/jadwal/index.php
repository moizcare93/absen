<section class="space-y-4">
    <header class="rounded-[2rem] bg-gradient-to-br from-brand-500 to-brand-700 p-5 text-white shadow-soft">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-100">Jadwal</p>
        <h1 class="mt-3 text-2xl font-black">Shift Bulan Ini</h1>
        <p class="mt-2 text-sm text-emerald-50">Daftar ringkas yang mudah discroll di layar HP.</p>
    </header>

    <div class="space-y-3">
        <?php if (!empty($schedules)): ?>
            <?php foreach ($schedules as $row): ?>
                <article class="rounded-[2rem] border border-slate-200 bg-white p-4 shadow-soft">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-slate-900"><?php echo html_escape($row['nama_shift']); ?></p>
                            <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($row['tanggal']); ?></p>
                        </div>
                        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-600"><?php echo html_escape($row['status']); ?></span>
                    </div>
                    <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                        <?php echo html_escape(substr($row['jam_masuk'], 0, 5)); ?> - <?php echo html_escape(substr($row['jam_keluar'], 0, 5)); ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white px-5 py-12 text-center text-sm text-slate-500 shadow-soft">Belum ada jadwal aktif pada bulan ini.</div>
        <?php endif; ?>
    </div>
</section>
