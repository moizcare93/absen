<section class="space-y-4">
    <header class="rounded-[2rem] bg-white p-5 shadow-soft ring-1 ring-slate-200">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-brand-500">Cuti</p>
        <h1 class="mt-3 text-2xl font-black text-slate-900">Riwayat & Saldo</h1>
        <div class="mt-4 grid grid-cols-2 gap-3">
            <div class="rounded-2xl bg-emerald-50 p-4">
                <p class="text-xs text-emerald-700">Saldo Tahunan</p>
                <p class="mt-2 text-2xl font-black text-emerald-900"><?php echo html_escape(isset($leave_summary['saldo_tahunan']) ? $leave_summary['saldo_tahunan'] : 0); ?></p>
            </div>
            <div class="rounded-2xl bg-amber-50 p-4">
                <p class="text-xs text-amber-700">Terpakai</p>
                <p class="mt-2 text-2xl font-black text-amber-900"><?php echo html_escape(isset($leave_summary['terpakai_tahunan']) ? $leave_summary['terpakai_tahunan'] : 0); ?></p>
            </div>
        </div>
    </header>

    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-soft">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-slate-900">Pengajuan Terakhir</p>
                <p class="text-xs text-slate-500">Siap diperluas ke alur approval berjenjang.</p>
            </div>
            <button class="rounded-2xl bg-brand-500 px-4 py-2 text-xs font-bold text-white">Ajukan</button>
        </div>

        <div class="mt-4 space-y-3">
            <?php if (!empty($leave_requests)): ?>
                <?php foreach ($leave_requests as $row): ?>
                    <article class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900"><?php echo html_escape($row['jenis_cuti']); ?></p>
                                <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($row['tgl_mulai']); ?> s/d <?php echo html_escape($row['tgl_selesai']); ?></p>
                            </div>
                            <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700"><?php echo html_escape($row['status']); ?></span>
                        </div>
                        <?php if (!empty($row['catatan'])): ?>
                            <p class="mt-3 text-xs leading-5 text-slate-600"><?php echo html_escape($row['catatan']); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="rounded-2xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Belum ada pengajuan cuti.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
