<section class="space-y-4">
    <header class="rounded-[2rem] bg-white p-5 shadow-soft ring-1 ring-slate-200">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-brand-500">Cuti</p>
        <h1 class="mt-3 text-2xl font-black text-slate-900">Pengajuan, Approval & Saldo</h1>
        <div class="mt-4 grid grid-cols-2 gap-3 lg:max-w-md">
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

    <div class="grid gap-4 xl:grid-cols-[1.1fr_0.9fr]">
    <?php if ((int) $current_user['level'] === 1): ?>
        <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-soft">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-slate-900">Master Jenis Cuti</p>
                    <p class="text-xs text-slate-500">Super user mengatur kategori cuti yang nanti dipilih oleh pegawai.</p>
                </div>
                <?php if (!empty($editing_type)): ?>
                    <a href="<?php echo site_url('cuti'); ?>" class="rounded-2xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700">Batal Edit</a>
                <?php endif; ?>
            </div>
            <form method="post" action="<?php echo site_url('cuti/jenis/simpan'); ?>" class="mt-4 space-y-4">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="id" value="<?php echo html_escape(!empty($editing_type['id']) ? $editing_type['id'] : ''); ?>">
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Kode</label>
                        <input type="text" name="kode" value="<?php echo html_escape(!empty($editing_type['kode']) ? $editing_type['kode'] : ''); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" placeholder="TAHUNAN">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Kategori</label>
                        <input type="text" name="nama" value="<?php echo html_escape(!empty($editing_type['nama']) ? $editing_type['nama'] : ''); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" placeholder="Cuti Tahunan">
                    </div>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Jatah per Tahun</label>
                        <input type="number" min="0" name="jatah" value="<?php echo html_escape(isset($editing_type['jatah']) ? $editing_type['jatah'] : 12); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Keterangan</label>
                        <input type="text" name="keterangan" value="<?php echo html_escape(!empty($editing_type['keterangan']) ? $editing_type['keterangan'] : ''); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" placeholder="Opsional">
                    </div>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                        <input type="checkbox" name="aktif" value="1" <?php echo empty($editing_type) || !empty($editing_type['aktif']) ? 'checked' : ''; ?>>
                        Aktif
                    </label>
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                        <input type="checkbox" name="potong_kuota" value="1" <?php echo empty($editing_type) || !empty($editing_type['potong_kuota']) ? 'checked' : ''; ?>>
                        Batasi dengan jatah
                    </label>
                </div>
                <button type="submit" class="w-full rounded-2xl bg-brand-500 px-4 py-3 text-sm font-bold text-white"><?php echo !empty($editing_type) ? 'Update Jenis Cuti' : 'Simpan Jenis Cuti'; ?></button>
            </form>

            <div class="mt-4 space-y-3">
                <?php foreach ($leave_type_admin as $type): ?>
                    <article class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900"><?php echo html_escape($type['nama']); ?></p>
                                <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($type['kode']); ?> • Jatah <?php echo (int) $type['jatah']; ?> hari/tahun</p>
                                <?php if (!empty($type['keterangan'])): ?>
                                    <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($type['keterangan']); ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo !empty($type['aktif']) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700'; ?>"><?php echo !empty($type['aktif']) ? 'Aktif' : 'Nonaktif'; ?></span>
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-3">
                            <a href="<?php echo site_url('cuti?edit_type=' . (int) $type['id']); ?>" class="rounded-2xl bg-slate-100 px-4 py-3 text-center text-xs font-bold text-slate-700">Edit Jenis</a>
                            <form method="post" action="<?php echo site_url('cuti/jenis/hapus/' . (int) $type['id']); ?>">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <button type="submit" class="w-full rounded-2xl bg-red-50 px-4 py-3 text-xs font-bold text-red-700" onclick="return confirm('Hapus jenis cuti ini?')">Hapus Jenis</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-soft">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-slate-900">Saldo per Kategori</p>
                <p class="text-xs text-slate-500">Jatah diambil dari master jenis cuti yang dibuat super user.</p>
            </div>
        </div>
        <div class="mt-4 space-y-3">
            <?php if (!empty($leave_type_balances)): ?>
                <?php foreach ($leave_type_balances as $balance): ?>
                    <article class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900"><?php echo html_escape($balance['nama']); ?></p>
                                <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($balance['kode']); ?></p>
                            </div>
                            <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">Sisa <?php echo (int) $balance['sisa']; ?> hari</span>
                        </div>
                        <p class="mt-3 text-xs text-slate-600">Jatah: <?php echo (int) $balance['jatah']; ?> • Terpakai/Pending: <?php echo (int) $balance['terpakai']; ?></p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="rounded-2xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Belum ada master jenis cuti aktif.</p>
            <?php endif; ?>
        </div>
    </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-[1fr_1fr]">
    <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-soft">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-slate-900"><?php echo !empty($editing_request) ? 'Update Pengajuan Cuti' : 'Ajukan Cuti Baru'; ?></p>
                <p class="text-xs text-slate-500">Alur approval mengikuti role kepala unit dan HR.</p>
            </div>
            <?php if (!empty($editing_request)): ?>
                <a href="<?php echo site_url('cuti'); ?>" class="rounded-2xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700">Batal Edit</a>
            <?php endif; ?>
        </div>
        <form method="post" action="<?php echo site_url('cuti/ajukan'); ?>" class="mt-4 space-y-4">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id" value="<?php echo html_escape(!empty($editing_request['id']) ? $editing_request['id'] : ''); ?>">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Jenis Cuti</label>
                <select name="jenis_cuti" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                    <?php foreach ($leave_types as $jenis): ?>
                        <option value="<?php echo html_escape($jenis['kode']); ?>" <?php echo (!empty($editing_request['jenis_cuti']) && $editing_request['jenis_cuti'] === $jenis['kode']) ? 'selected' : ''; ?>><?php echo html_escape($jenis['nama'] . ' (' . $jenis['kode'] . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Mulai</label>
                    <input type="date" name="tgl_mulai" value="<?php echo html_escape(!empty($editing_request['tgl_mulai']) ? $editing_request['tgl_mulai'] : date('Y-m-d')); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Selesai</label>
                    <input type="date" name="tgl_selesai" value="<?php echo html_escape(!empty($editing_request['tgl_selesai']) ? $editing_request['tgl_selesai'] : date('Y-m-d')); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Catatan</label>
                <textarea name="catatan" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" placeholder="Alasan atau detail pengajuan"><?php echo html_escape(!empty($editing_request['catatan']) ? $editing_request['catatan'] : ''); ?></textarea>
            </div>
            <button type="submit" class="w-full rounded-2xl bg-brand-500 px-4 py-3 text-sm font-bold text-white"><?php echo !empty($editing_request) ? 'Update Pengajuan' : 'Kirim Pengajuan'; ?></button>
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
                        <?php if ($row['status'] === 'PENDING'): ?>
                            <div class="mt-3 grid grid-cols-2 gap-3">
                                <a href="<?php echo site_url('cuti?edit=' . (int) $row['id']); ?>" class="rounded-2xl bg-slate-100 px-4 py-3 text-center text-xs font-bold text-slate-700">Edit Pengajuan</a>
                                <form method="post" action="<?php echo site_url('cuti/hapus/' . (int) $row['id']); ?>">
                                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                    <button type="submit" class="w-full rounded-2xl bg-red-50 px-4 py-3 text-xs font-bold text-red-700" onclick="return confirm('Hapus pengajuan cuti ini?')">Hapus Pengajuan</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="rounded-2xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Belum ada pengajuan cuti.</p>
            <?php endif; ?>
        </div>
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
