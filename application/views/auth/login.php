<section class="flex min-h-[calc(100vh-2rem)] flex-col justify-center">
    <div class="relative overflow-hidden rounded-[2rem] bg-slate-900 px-6 pb-10 pt-8 text-white shadow-soft">
        <div class="absolute inset-x-0 top-0 h-28 bg-gradient-to-r from-emerald-400/25 via-cyan-300/10 to-transparent"></div>
        <p class="relative text-xs font-semibold uppercase tracking-[0.28em] text-emerald-200">MoizCare Attendance</p>
        <h1 class="relative mt-4 text-3xl font-black leading-tight">Absensi Rumah Sakit yang cepat, aman, dan nyaman di HP.</h1>
        <p class="relative mt-3 text-sm leading-6 text-slate-300">Masuk untuk mengakses absensi, jadwal shift, cuti, dan rekap kehadiran harian.</p>
        <div class="relative mt-6 grid grid-cols-2 gap-3 text-xs">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-3">
                <div class="font-semibold text-white">Kamera + GPS</div>
                <div class="mt-1 text-slate-300">Validasi ganda saat check-in.</div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-3">
                <div class="font-semibold text-white">Shift 24 Jam</div>
                <div class="mt-1 text-slate-300">Cocok untuk unit rawat dan IGD.</div>
            </div>
        </div>
    </div>

    <div class="-mt-8 rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
        <h2 class="text-lg font-bold text-slate-900">Login</h2>
        <p class="mt-1 text-sm text-slate-500">Gunakan akun yang sudah dibuat oleh admin HR.</p>

        <form method="post" action="<?php echo site_url('auth/login'); ?>" class="mt-6 space-y-4">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                <input type="email" name="email" value="<?php echo set_value('email', 'superadmin@absen.local'); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none ring-brand-500 transition focus:border-brand-500 focus:bg-white focus:ring-2">
                <?php echo form_error('email', '<p class="mt-1 text-xs text-red-600">', '</p>'); ?>
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                <input type="password" name="password" value="Admin@12345" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none ring-brand-500 transition focus:border-brand-500 focus:bg-white focus:ring-2">
                <?php echo form_error('password', '<p class="mt-1 text-xs text-red-600">', '</p>'); ?>
            </div>

            <button type="submit" class="w-full rounded-2xl bg-brand-500 px-4 py-3 text-sm font-bold text-white shadow-soft transition hover:bg-brand-600">
                Masuk ke Dashboard
            </button>
        </form>

        <div class="mt-5 rounded-2xl bg-slate-50 px-4 py-3 text-xs text-slate-500">
            Akun demo seed:
            <span class="font-semibold text-slate-700">superadmin@absen.local</span>
            /
            <span class="font-semibold text-slate-700">Admin@12345</span>
        </div>
    </div>
</section>
