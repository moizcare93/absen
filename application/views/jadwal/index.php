<section class="space-y-4">
    <header class="rounded-[2rem] bg-gradient-to-br from-brand-500 to-brand-700 p-5 text-white shadow-soft">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-100">Jadwal</p>
        <h1 class="mt-3 text-2xl font-black">Shift & Penugasan</h1>
        <p class="mt-2 text-sm text-emerald-50">Admin dapat mengatur jadwal per pegawai, sementara pegawai melihat jadwalnya sendiri.</p>
    </header>

    <?php if ((int) $current_user['level'] <= 3): ?>
        <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-soft">
            <p class="text-sm font-bold text-slate-900">Atur Jadwal Pegawai</p>
            <form method="post" action="<?php echo site_url('jadwal/simpan'); ?>" class="mt-4 space-y-4">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Pegawai</label>
                    <select name="pegawai_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?php echo (int) $employee['id']; ?>"><?php echo html_escape($employee['nama'] . ' - ' . $employee['nip']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal</label>
                        <input type="date" name="tanggal" value="<?php echo html_escape(date('Y-m-d')); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
                        <select name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            <?php foreach (array('PUBLISHED', 'DRAFT', 'REVISI') as $status): ?>
                                <option value="<?php echo $status; ?>"><?php echo html_escape($status); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Shift</label>
                    <select name="shift_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        <?php foreach ($shifts as $shift): ?>
                            <option value="<?php echo (int) $shift['id']; ?>"><?php echo html_escape($shift['nama_shift'] . ' (' . substr($shift['jam_masuk'], 0, 5) . ' - ' . substr($shift['jam_keluar'], 0, 5) . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="w-full rounded-2xl bg-brand-500 px-4 py-3 text-sm font-bold text-white">Simpan Jadwal</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-soft">
        <form method="get" action="<?php echo site_url('jadwal'); ?>" class="space-y-4">
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
            <button type="submit" class="w-full rounded-2xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-700">Tampilkan Jadwal</button>
        </form>
    </div>

    <div class="space-y-3">
        <?php $rows = (int) $current_user['level'] <= 3 ? $all_schedules : $schedules; ?>
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <article class="rounded-[2rem] border border-slate-200 bg-white p-4 shadow-soft">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-slate-900"><?php echo html_escape($row['nama_shift']); ?></p>
                            <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($row['tanggal']); ?></p>
                            <?php if (!empty($row['nama'])): ?>
                                <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($row['nama']); ?> • <?php echo html_escape($row['nama_unit']); ?></p>
                            <?php endif; ?>
                        </div>
                        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-600"><?php echo html_escape($row['status']); ?></span>
                    </div>
                    <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                        <?php echo html_escape(substr($row['jam_masuk'], 0, 5)); ?> - <?php echo html_escape(substr($row['jam_keluar'], 0, 5)); ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white px-5 py-12 text-center text-sm text-slate-500 shadow-soft">Belum ada jadwal aktif untuk filter yang dipilih.</div>
        <?php endif; ?>
    </div>
</section>
