<section class="space-y-4">
    <header class="rounded-[2rem] bg-white p-5 shadow-soft ring-1 ring-slate-200">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-brand-500">Cuti</p>
        <h1 class="mt-3 text-2xl font-black text-slate-900">Pengajuan, Approval & Saldo</h1>
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
                <p class="text-sm font-bold text-slate-900">Ajukan Cuti Baru</p>
                <p class="text-xs text-slate-500">Alur approval mengikuti role kepala unit dan HR.</p>
            </div>
        </div>
        <form method="post" action="<?php echo site_url('cuti/ajukan'); ?>" class="mt-4 space-y-4">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Jenis Cuti</label>
                <select name="jenis_cuti" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                    <?php foreach (array('TAHUNAN', 'SAKIT', 'MELAHIRKAN', 'DUKA', 'PENTING', 'TANPA_KETERANGAN') as $jenis): ?>
                        <option value="<?php echo $jenis; ?>"><?php echo html_escape($jenis); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Mulai</label>
                    <input type="date" name="tgl_mulai" value="<?php echo html_escape(date('Y-m-d')); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Selesai</label>
                    <input type="date" name="tgl_selesai" value="<?php echo html_escape(date('Y-m-d')); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Catatan</label>
                <textarea name="catatan" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" placeholder="Alasan atau detail pengajuan"></textarea>
            </div>
            <button type="submit" class="w-full rounded-2xl bg-brand-500 px-4 py-3 text-sm font-bold text-white">Kirim Pengajuan</button>
        </form>
    </div>

    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-soft">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-slate-900">Pengajuan Terakhir</p>
                <p class="text-xs text-slate-500">Riwayat pribadi beserta approver terakhir.</p>
            </div>
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
                        <?php if (!empty($row['approver_nama'])): ?>
                            <p class="mt-2 text-xs text-slate-500">Approver: <?php echo html_escape($row['approver_nama']); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="rounded-2xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Belum ada pengajuan cuti.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ((int) $current_user['level'] <= 3): ?>
        <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-soft">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-slate-900">Approval Cuti</p>
                    <p class="text-xs text-slate-500">Kepala unit memproses tahap unit, HR atau super admin menyelesaikan final approval.</p>
                </div>
            </div>
            <div class="mt-4 space-y-3">
                <?php if (!empty($pending_approvals)): ?>
                    <?php foreach ($pending_approvals as $row): ?>
                        <article class="rounded-2xl bg-slate-50 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-bold text-slate-900"><?php echo html_escape($row['nama']); ?></p>
                                    <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($row['nama_unit']); ?> • <?php echo html_escape($row['jenis_cuti']); ?></p>
                                    <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($row['tgl_mulai']); ?> s/d <?php echo html_escape($row['tgl_selesai']); ?></p>
                                </div>
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700"><?php echo html_escape($row['status']); ?></span>
                            </div>
                            <?php if (!empty($row['catatan'])): ?>
                                <p class="mt-3 text-xs leading-5 text-slate-600"><?php echo html_escape($row['catatan']); ?></p>
                            <?php endif; ?>
                            <div class="mt-3 grid grid-cols-2 gap-3">
                                <form method="post" action="<?php echo site_url('cuti/aksi/' . (int) $row['id']); ?>">
                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                    <input type="hidden" name="approval_action" value="APPROVE">
                                    <button type="submit" class="w-full rounded-2xl bg-emerald-500 px-4 py-3 text-xs font-bold text-white">Setujui</button>
                                </form>
                                <form method="post" action="<?php echo site_url('cuti/aksi/' . (int) $row['id']); ?>">
                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                    <input type="hidden" name="approval_action" value="DITOLAK">
                                    <button type="submit" class="w-full rounded-2xl bg-red-50 px-4 py-3 text-xs font-bold text-red-700">Tolak</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="rounded-2xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Tidak ada pengajuan cuti yang menunggu approval.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
