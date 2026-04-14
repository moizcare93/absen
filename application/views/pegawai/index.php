<section class="space-y-4">
    <header class="rounded-[2rem] bg-slate-900 p-5 text-white shadow-soft">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-200">Pegawai</p>
        <h1 class="mt-3 text-2xl font-black">Data Master & Akun</h1>
        <p class="mt-2 text-sm text-slate-300">Menu ini sudah mendukung tambah, update, dan hapus/nonaktifkan pegawai.</p>
    </header>

    <div class="admin-panel rounded-[2rem] p-5 lg:p-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-bold text-slate-900"><?php echo !empty($editing) ? 'Edit Pegawai' : 'Tambah Pegawai'; ?></p>
                <p class="text-xs text-slate-500">Password default akun baru: `Admin@12345` bila dikosongkan.</p>
            </div>
            <?php if (!empty($editing)): ?>
                <a href="<?php echo site_url('pegawai'); ?>" class="rounded-2xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700">Batal Edit</a>
            <?php endif; ?>
        </div>

        <form method="post" action="<?php echo site_url('pegawai/simpan'); ?>" class="mt-4 space-y-4">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id" value="<?php echo html_escape(!empty($editing['id']) ? $editing['id'] : ''); ?>">

            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Nama</label>
                    <input type="text" name="nama" value="<?php echo html_escape(set_value('nama', !empty($editing['nama']) ? $editing['nama'] : '')); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">NIP</label>
                    <input type="text" name="nip" value="<?php echo html_escape(set_value('nip', !empty($editing['nip']) ? $editing['nip'] : '')); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>
            </div>

            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                    <input type="email" name="email" value="<?php echo html_escape(set_value('email', !empty($editing['email']) ? $editing['email'] : '')); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">No. HP</label>
                    <input type="text" name="no_hp" value="<?php echo html_escape(set_value('no_hp', !empty($editing['no_hp']) ? $editing['no_hp'] : '')); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>
            </div>

            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Jabatan</label>
                    <input type="text" name="jabatan" value="<?php echo html_escape(set_value('jabatan', !empty($editing['jabatan']) ? $editing['jabatan'] : '')); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" value="<?php echo html_escape(set_value('tanggal_masuk', !empty($editing['tanggal_masuk']) ? $editing['tanggal_masuk'] : date('Y-m-d'))); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                </div>
            </div>

            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Unit</label>
                    <select name="unit_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        <?php foreach ($units as $unit): ?>
                            <option value="<?php echo (int) $unit['id']; ?>" <?php echo (string) set_value('unit_id', !empty($editing['unit_id']) ? $editing['unit_id'] : '') === (string) $unit['id'] ? 'selected' : ''; ?>><?php echo html_escape($unit['nama_unit']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Role</label>
                    <select name="role_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo (int) $role['id']; ?>" <?php echo (string) set_value('role_id', !empty($editing['role_id']) ? $editing['role_id'] : 4) === (string) $role['id'] ? 'selected' : ''; ?>><?php echo html_escape($role['nama_role']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Tipe Kerja</label>
                    <select name="tipe_kerja" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        <?php foreach (array('SHIFT', 'NON_SHIFT') as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo set_value('tipe_kerja', !empty($editing['tipe_kerja']) ? $editing['tipe_kerja'] : 'NON_SHIFT') === $type ? 'selected' : ''; ?>><?php echo html_escape($type); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
                    <select name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        <?php foreach (array('AKTIF', 'NONAKTIF', 'CUTI_PANJANG', 'MAGANG') as $status): ?>
                            <option value="<?php echo $status; ?>" <?php echo set_value('status', !empty($editing['status']) ? $editing['status'] : 'AKTIF') === $status ? 'selected' : ''; ?>><?php echo html_escape($status); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                    <input type="password" name="password" value="" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm" placeholder="<?php echo !empty($editing) ? 'Kosongkan bila tidak ganti' : 'Minimal 8 karakter'; ?>">
                </div>
                <div class="flex items-end">
                    <label class="flex w-full items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                        <input type="checkbox" name="is_active" value="1" <?php echo (string) set_value('is_active', isset($editing['is_active']) ? $editing['is_active'] : 1) === '1' ? 'checked' : ''; ?>>
                        Akun aktif
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full rounded-2xl bg-brand-500 px-4 py-3 text-sm font-bold text-white"><?php echo !empty($editing) ? 'Simpan Perubahan Pegawai' : 'Tambah Pegawai Baru'; ?></button>
        </form>
    </div>

    <div class="hidden xl:block admin-panel rounded-[2rem] overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4">
            <p class="text-sm font-bold text-slate-900">Daftar Pegawai</p>
            <p class="text-xs text-slate-500">Mode tabel desktop untuk monitoring cepat.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.16em] text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Pegawai</th>
                        <th class="px-6 py-4">Unit</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($employees as $employee): ?>
                        <tr class="bg-white">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-900"><?php echo html_escape($employee['nama']); ?></p>
                                <p class="text-xs text-slate-500"><?php echo html_escape($employee['nip']); ?> • <?php echo html_escape($employee['email']); ?></p>
                            </td>
                            <td class="px-6 py-4 text-slate-600"><?php echo html_escape($employee['nama_unit']); ?></td>
                            <td class="px-6 py-4 text-slate-600"><?php echo html_escape($employee['nama_role'] ? $employee['nama_role'] : 'Belum ada akun'); ?></td>
                            <td class="px-6 py-4"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700"><?php echo html_escape($employee['status']); ?></span></td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="<?php echo site_url('pegawai?edit=' . (int) $employee['id']); ?>" class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700">Update</a>
                                    <form method="post" action="<?php echo site_url('pegawai/hapus/' . (int) $employee['id']); ?>">
                                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                        <button type="submit" class="rounded-xl bg-red-50 px-3 py-2 text-xs font-bold text-red-700" onclick="return confirm('Hapus/nonaktifkan pegawai ini?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid gap-4 xl:hidden">
        <?php foreach ($employees as $employee): ?>
            <article class="rounded-[2rem] border border-slate-200 bg-white p-4 shadow-soft">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-slate-900"><?php echo html_escape($employee['nama']); ?></p>
                        <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($employee['nip']); ?> • <?php echo html_escape($employee['nama_unit']); ?></p>
                        <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($employee['email']); ?><?php if (!empty($employee['jabatan'])): ?> • <?php echo html_escape($employee['jabatan']); ?><?php endif; ?></p>
                    </div>
                    <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-600"><?php echo html_escape($employee['tipe_kerja']); ?></span>
                </div>
                <div class="mt-4 rounded-2xl bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-600">
                    Status: <?php echo html_escape($employee['status']); ?> • Role: <?php echo html_escape($employee['nama_role'] ? $employee['nama_role'] : 'Belum ada akun'); ?> • Akun: <?php echo !empty($employee['is_active']) ? 'Aktif' : 'Nonaktif'; ?>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <a href="<?php echo site_url('pegawai?edit=' . (int) $employee['id']); ?>" class="rounded-2xl bg-slate-100 px-4 py-3 text-center text-xs font-bold text-slate-700">Update Pegawai</a>
                    <form method="post" action="<?php echo site_url('pegawai/hapus/' . (int) $employee['id']); ?>">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <button type="submit" class="w-full rounded-2xl bg-red-50 px-4 py-3 text-xs font-bold text-red-700" onclick="return confirm('Hapus/nonaktifkan pegawai ini?')">Hapus Pegawai</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
