<section class="space-y-4">
    <header class="rounded-[2rem] bg-gradient-to-br from-brand-500 to-brand-700 p-5 text-white shadow-soft">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-100">Jadwal</p>
        <h1 class="mt-3 text-2xl font-black">Shift & Penugasan</h1>
        <p class="mt-2 text-sm text-emerald-50">Admin dapat mengatur jadwal per pegawai, sementara pegawai melihat jadwalnya sendiri.</p>
    </header>

    <div class="grid gap-4 xl:grid-cols-[0.95fr_1.35fr]">
    <?php if ((int) $current_user['level'] <= 3): ?>
        <div class="admin-panel rounded-[2rem] p-5">
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
                <div class="grid gap-3 md:grid-cols-2">
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

    <div class="admin-panel rounded-[2rem] p-5">
        <form method="get" action="<?php echo site_url('jadwal'); ?>" class="space-y-4">
            <div class="grid gap-3 md:grid-cols-2">
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
    </div>

    <div class="grid gap-4 xl:grid-cols-[1.2fr_0.95fr]">
    <div class="hidden xl:block admin-panel rounded-[2rem] overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4">
            <p class="text-sm font-bold text-slate-900">Jadwal Bulanan</p>
            <p class="text-xs text-slate-500">Tampilan desktop diringkas seperti panel admin.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Pegawai</th>
                        <th class="px-6 py-4">Shift</th>
                        <th class="px-6 py-4">Jam</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $rows = (int) $current_user['level'] <= 3 ? $all_schedules : $schedules; ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="px-6 py-4 font-semibold text-slate-900"><?php echo html_escape($row['tanggal']); ?></td>
                            <td class="px-6 py-4 text-slate-600"><?php echo html_escape(!empty($row['nama']) ? $row['nama'] : $current_user['nama']); ?></td>
                            <td class="px-6 py-4 text-slate-600"><?php echo html_escape($row['nama_shift']); ?></td>
                            <td class="px-6 py-4 text-slate-600"><?php echo html_escape(substr($row['jam_masuk'], 0, 5)); ?> - <?php echo html_escape(substr($row['jam_keluar'], 0, 5)); ?></td>
                            <td class="px-6 py-4"><span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-600"><?php echo html_escape($row['status']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="space-y-3 xl:hidden">
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

    <div class="admin-panel rounded-[2rem] p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-slate-900">Cuti Bulan Ini</p>
                <p class="text-xs text-slate-500">Menampilkan pengajuan cuti pegawai pada bulan yang dipilih, baik pending maupun yang sudah di-approval.</p>
            </div>
        </div>

        <div class="mt-4 space-y-3">
            <?php if (!empty($leave_requests)): ?>
                <?php foreach ($leave_requests as $row): ?>
                    <article class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900"><?php echo html_escape($row['nama']); ?></p>
                                <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($row['nama_unit']); ?> • <?php echo html_escape($row['jenis_cuti']); ?></p>
                                <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($row['tgl_mulai']); ?> s/d <?php echo html_escape($row['tgl_selesai']); ?></p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo $row['status'] === 'APPROVED_HR' ? 'bg-emerald-100 text-emerald-700' : ($row['status'] === 'APPROVED_UNIT' ? 'bg-cyan-100 text-cyan-700' : ($row['status'] === 'DITOLAK' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')); ?>"><?php echo html_escape($row['status']); ?></span>
                        </div>
                        <?php if (!empty($row['catatan'])): ?>
                            <p class="mt-3 text-xs leading-5 text-slate-600"><?php echo html_escape($row['catatan']); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="rounded-2xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Tidak ada pengajuan cuti pada bulan ini.</p>
            <?php endif; ?>
        </div>
    </div>
    </div>
</section>
