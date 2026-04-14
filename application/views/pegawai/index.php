<section class="space-y-4">
    <header class="rounded-[2rem] bg-slate-900 p-5 text-white shadow-soft">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-200">Pegawai</p>
        <h1 class="mt-3 text-2xl font-black">Data Master</h1>
        <p class="mt-2 text-sm text-slate-300">Kartu pegawai dibuat padat agar tetap nyaman di layar kecil.</p>
    </header>

    <div class="space-y-3">
        <?php foreach ($employees as $employee): ?>
            <article class="rounded-[2rem] border border-slate-200 bg-white p-4 shadow-soft">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-slate-900"><?php echo html_escape($employee['nama']); ?></p>
                        <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($employee['nip']); ?> • <?php echo html_escape($employee['nama_unit']); ?></p>
                    </div>
                    <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-600"><?php echo html_escape($employee['tipe_kerja']); ?></span>
                </div>
                <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-600">
                    Status: <?php echo html_escape($employee['status']); ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
