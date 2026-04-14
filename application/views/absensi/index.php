<section class="space-y-4">
    <header class="rounded-[2rem] bg-slate-900 p-5 text-white shadow-soft">
        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-200">Absensi</p>
        <h1 class="mt-3 text-2xl font-black">Check-in Mobile</h1>
        <p class="mt-2 text-sm text-slate-300">Tombol aksi utama dibuat selalu dekat jangkauan ibu jari agar check-in lebih cepat.</p>
    </header>

    <div class="grid gap-4 xl:grid-cols-[1.1fr_0.9fr]">
    <div class="admin-panel rounded-[2rem] p-5">
        <div class="rounded-[1.75rem] bg-slate-950 p-3 text-white">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">Status Cepat</p>
                    <p class="mt-2 text-lg font-black"><?php echo html_escape(isset($attendance_today['status']) ? $attendance_today['status'] : 'BELUM ABSEN'); ?></p>
                </div>
                <span id="camera-badge" class="rounded-full bg-amber-400/15 px-3 py-1 text-xs font-semibold text-amber-300">Menyalakan kamera</span>
            </div>

            <div class="mt-4 grid grid-cols-3 gap-2 text-center text-[11px]">
                <div class="rounded-2xl bg-white/5 px-3 py-2">
                    Radius<br><span class="mt-1 block text-sm font-bold text-white"><?php echo html_escape($allowed_radius); ?> m</span>
                </div>
                <div class="rounded-2xl bg-white/5 px-3 py-2">
                    Masuk<br><span class="mt-1 block text-sm font-bold text-white"><?php echo html_escape(!empty($attendance_today['jam_masuk']) ? substr($attendance_today['jam_masuk'], 11, 5) : '--:--'); ?></span>
                </div>
                <div class="rounded-2xl bg-white/5 px-3 py-2">
                    Keluar<br><span class="mt-1 block text-sm font-bold text-white"><?php echo html_escape(!empty($attendance_today['jam_keluar']) ? substr($attendance_today['jam_keluar'], 11, 5) : '--:--'); ?></span>
                </div>
            </div>

            <div class="mt-4 relative overflow-hidden rounded-[1.25rem] border border-dashed border-white/25 bg-slate-900">
                <video id="attendance-video" class="aspect-[4/5] w-full object-cover xl:aspect-[16/10]" autoplay playsinline muted></video>
                <canvas id="attendance-canvas" class="hidden"></canvas>
                <img id="attendance-preview" alt="Preview foto absensi" class="hidden aspect-[4/5] w-full object-cover xl:aspect-[16/10]">
                <div id="camera-placeholder" class="absolute inset-0 flex items-center justify-center px-4 text-center text-sm text-slate-300">
                    Meminta akses kamera browser untuk mengambil foto absensi.
                </div>
            </div>
        </div>

        <div class="sticky bottom-20 z-10 mt-4 rounded-[1.75rem] border border-slate-200 bg-white/95 p-3 shadow-soft backdrop-blur xl:static xl:border-0 xl:bg-transparent xl:p-0 xl:shadow-none">
            <div class="grid grid-cols-2 gap-3">
                <form id="checkin-form" method="post" action="<?php echo site_url('absensi/masuk'); ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="latitude">
                    <input type="hidden" name="longitude">
                    <input type="hidden" name="photo_data">
                    <input type="hidden" name="catatan">
                    <button type="submit" class="w-full rounded-2xl bg-brand-500 px-4 py-4 text-sm font-black text-white shadow-soft disabled:cursor-not-allowed disabled:bg-slate-300" <?php echo !empty($attendance_today['jam_masuk']) ? 'disabled' : ''; ?>>
                        <?php echo !empty($attendance_today['jam_masuk']) ? 'Masuk Tercatat' : 'Absen Masuk'; ?>
                    </button>
                </form>

                <form id="checkout-form" method="post" action="<?php echo site_url('absensi/pulang'); ?>">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="latitude">
                    <input type="hidden" name="longitude">
                    <input type="hidden" name="photo_data">
                    <input type="hidden" name="catatan">
                    <button type="submit" class="w-full rounded-2xl bg-slate-100 px-4 py-4 text-sm font-bold text-slate-700 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400" <?php echo empty($attendance_today['jam_masuk']) || !empty($attendance_today['jam_keluar']) ? 'disabled' : ''; ?>>
                        <?php echo !empty($attendance_today['jam_keluar']) ? 'Keluar Tercatat' : 'Absen Keluar'; ?>
                    </button>
                </form>
            </div>

            <div class="mt-3 grid grid-cols-2 gap-3">
                <button id="capture-button" type="button" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-700">Ambil Ulang Foto</button>
                <button id="refresh-location-button" type="button" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-700">Refresh GPS</button>
            </div>
        </div>

        <div class="mt-4 space-y-3 xl:hidden">
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-slate-900"><?php echo html_escape(!empty($reference_location['nama_lokasi']) ? $reference_location['nama_lokasi'] : 'GPS Rumah Sakit'); ?></p>
                        <p class="mt-1 text-xs text-slate-500">Lat <?php echo html_escape($office_latitude); ?>, Lng <?php echo html_escape($office_longitude); ?></p>
                    </div>
                    <span id="gps-badge" class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Menunggu izin</span>
                </div>
                <p id="gps-status" class="mt-3 text-xs text-slate-500">Lokasi perangkat belum dibaca.</p>
            </div>

            <?php if (!empty($schedule_today)): ?>
                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    Jadwal hari ini:
                    <span class="font-semibold text-slate-900"><?php echo html_escape($schedule_today['nama_shift']); ?></span>
                    <?php echo html_escape(substr($schedule_today['jam_masuk'], 0, 5)); ?> - <?php echo html_escape(substr($schedule_today['jam_keluar'], 0, 5)); ?>
                </div>
            <?php endif; ?>

            <details class="rounded-2xl border border-slate-200 bg-white p-4">
                <summary class="cursor-pointer text-sm font-semibold text-slate-700">Tambahkan catatan absensi</summary>
                <div class="mt-3">
                    <textarea id="attendance-note" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none ring-brand-500 transition focus:border-brand-500 focus:bg-white focus:ring-2" placeholder="Opsional, mis. kunjungan unit luar atau pergantian shift."><?php echo set_value('catatan'); ?></textarea>
                </div>
            </details>
        </div>
    </div>

    <div class="space-y-4">
    <div class="admin-panel rounded-[2rem] p-5">
        <div class="space-y-3">
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-slate-900"><?php echo html_escape(!empty($reference_location['nama_lokasi']) ? $reference_location['nama_lokasi'] : 'GPS Rumah Sakit'); ?></p>
                        <p class="mt-1 text-xs text-slate-500">Lat <?php echo html_escape($office_latitude); ?>, Lng <?php echo html_escape($office_longitude); ?></p>
                    </div>
                    <span id="gps-badge-desktop" class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 xl:inline-flex hidden">Sinkron</span>
                </div>
                <p class="mt-3 text-xs text-slate-500">Gunakan panel kiri untuk ambil foto dan tombol aksi. Panel kanan fokus pada konteks lokasi, jadwal, dan riwayat.</p>
            </div>
            <?php if (!empty($schedule_today)): ?>
                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    Jadwal hari ini:
                    <span class="font-semibold text-slate-900"><?php echo html_escape($schedule_today['nama_shift']); ?></span>
                    <?php echo html_escape(substr($schedule_today['jam_masuk'], 0, 5)); ?> - <?php echo html_escape(substr($schedule_today['jam_keluar'], 0, 5)); ?>
                </div>
            <?php endif; ?>
            <details class="rounded-2xl border border-slate-200 bg-white p-4" open>
                <summary class="cursor-pointer text-sm font-semibold text-slate-700">Catatan absensi</summary>
                <div class="mt-3">
                    <textarea id="attendance-note-desktop" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none ring-brand-500 transition focus:border-brand-500 focus:bg-white focus:ring-2" placeholder="Opsional, mis. kunjungan unit luar atau pergantian shift."><?php echo set_value('catatan'); ?></textarea>
                </div>
            </details>
        </div>
    </div>

    <div class="admin-panel rounded-[2rem] p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-slate-900">Rekap Absensi</p>
                <p class="text-xs text-slate-500">Filter bulanan untuk riwayat pribadi atau seluruh unit bila Anda admin.</p>
            </div>
        </div>

        <form method="get" action="<?php echo site_url('absensi'); ?>" class="mt-4 space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Bulan</label>
                    <input type="month" name="bulan" value="<?php echo html_escape($history_month); ?>" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
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
            <button type="submit" class="w-full rounded-2xl bg-slate-100 px-4 py-3 text-sm font-bold text-slate-700">Tampilkan Rekap</button>
        </form>

        <div class="mt-4 grid grid-cols-3 gap-3">
            <div class="rounded-2xl bg-emerald-50 p-4">
                <p class="text-xs text-emerald-700">Hadir</p>
                <p class="mt-2 text-2xl font-black text-emerald-900"><?php echo (int) $attendance_summary['HADIR']; ?></p>
            </div>
            <div class="rounded-2xl bg-amber-50 p-4">
                <p class="text-xs text-amber-700">Terlambat</p>
                <p class="mt-2 text-2xl font-black text-amber-900"><?php echo (int) $attendance_summary['TERLAMBAT']; ?></p>
            </div>
            <div class="rounded-2xl bg-slate-100 p-4">
                <p class="text-xs text-slate-600">Total Data</p>
                <p class="mt-2 text-2xl font-black text-slate-900"><?php echo (int) $attendance_summary['total']; ?></p>
            </div>
        </div>

        <div class="mt-4 space-y-3">
            <?php if (!empty($attendance_history)): ?>
                <?php foreach ($attendance_history as $row): ?>
                    <article class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-900"><?php echo html_escape($row['tanggal']); ?></p>
                                <?php if (!empty($row['nama'])): ?>
                                    <p class="mt-1 text-xs text-slate-500"><?php echo html_escape($row['nama']); ?> • <?php echo html_escape($row['nama_unit']); ?></p>
                                <?php endif; ?>
                            </div>
                            <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-600"><?php echo html_escape($row['status']); ?></span>
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-3 text-xs text-slate-600">
                            <div class="rounded-2xl bg-white px-4 py-3">Masuk<br><span class="mt-1 block text-sm font-bold text-slate-900"><?php echo html_escape(!empty($row['jam_masuk']) ? $row['jam_masuk'] : '-'); ?></span></div>
                            <div class="rounded-2xl bg-white px-4 py-3">Keluar<br><span class="mt-1 block text-sm font-bold text-slate-900"><?php echo html_escape(!empty($row['jam_keluar']) ? $row['jam_keluar'] : '-'); ?></span></div>
                        </div>
                        <?php if (!empty($row['catatan'])): ?>
                            <p class="mt-3 text-xs leading-5 text-slate-600"><?php echo html_escape($row['catatan']); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="rounded-2xl bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">Belum ada data absensi pada bulan ini.</p>
            <?php endif; ?>
        </div>
    </div>
    </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var forms = [document.getElementById('checkin-form'), document.getElementById('checkout-form')];
    var video = document.getElementById('attendance-video');
    var canvas = document.getElementById('attendance-canvas');
    var preview = document.getElementById('attendance-preview');
    var placeholder = document.getElementById('camera-placeholder');
    var cameraBadge = document.getElementById('camera-badge');
    var gpsBadge = document.getElementById('gps-badge');
    var gpsStatus = document.getElementById('gps-status');
    var noteFields = Array.prototype.slice.call(document.querySelectorAll('#attendance-note, #attendance-note-desktop'));
    var captureButton = document.getElementById('capture-button');
    var refreshLocationButton = document.getElementById('refresh-location-button');
    var latestPhoto = '';
    var latestCoords = null;

    function syncForms() {
        forms.forEach(function (form) {
            if (!form) {
                return;
            }

            form.querySelector('input[name="photo_data"]').value = latestPhoto;
            form.querySelector('input[name="catatan"]').value = noteFields.length ? noteFields[0].value : '';
            form.querySelector('input[name="latitude"]').value = latestCoords ? latestCoords.latitude : '';
            form.querySelector('input[name="longitude"]').value = latestCoords ? latestCoords.longitude : '';
        });
    }

    function syncNoteFields(source) {
        noteFields.forEach(function (field) {
            if (field !== source) {
                field.value = source.value;
            }
        });
        syncForms();
    }

    function setGpsState(kind, message) {
        gpsStatus.textContent = message;
        gpsBadge.className = 'rounded-full px-3 py-1 text-xs font-semibold';

        if (kind === 'ready') {
            gpsBadge.textContent = 'GPS siap';
            gpsBadge.classList.add('bg-emerald-100', 'text-emerald-700');
            return;
        }

        if (kind === 'error') {
            gpsBadge.textContent = 'GPS gagal';
            gpsBadge.classList.add('bg-red-100', 'text-red-700');
            return;
        }

        gpsBadge.textContent = 'Memuat GPS';
        gpsBadge.classList.add('bg-amber-100', 'text-amber-700');
    }

    function requestLocation() {
        if (!navigator.geolocation) {
            setGpsState('error', 'Browser tidak mendukung geolocation.');
            return;
        }

        setGpsState('loading', 'Mengambil koordinat perangkat...');
        navigator.geolocation.getCurrentPosition(function (position) {
            latestCoords = {
                latitude: position.coords.latitude.toFixed(7),
                longitude: position.coords.longitude.toFixed(7)
            };
            syncForms();
            setGpsState('ready', 'Lokasi terdeteksi: ' + latestCoords.latitude + ', ' + latestCoords.longitude);
        }, function (error) {
            setGpsState('error', 'Izin lokasi ditolak atau GPS tidak tersedia: ' + error.message);
        }, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        });
    }

    function captureFrame() {
        if (!video.videoWidth || !video.videoHeight) {
            cameraBadge.textContent = 'Kamera belum siap';
            cameraBadge.className = 'rounded-full bg-red-400/15 px-3 py-1 text-xs font-semibold text-red-300';
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        latestPhoto = canvas.toDataURL('image/jpeg', 0.85);
        preview.src = latestPhoto;
        preview.classList.remove('hidden');
        placeholder.classList.add('hidden');
        cameraBadge.textContent = 'Foto siap';
        cameraBadge.className = 'rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-300';
        syncForms();
    }

    function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            cameraBadge.textContent = 'Kamera tidak didukung';
            cameraBadge.className = 'rounded-full bg-red-400/15 px-3 py-1 text-xs font-semibold text-red-300';
            placeholder.textContent = 'Browser ini tidak mendukung akses kamera.';
            return;
        }

        navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'user',
                width: { ideal: 640 },
                height: { ideal: 480 }
            },
            audio: false
        }).then(function (stream) {
            video.srcObject = stream;
            cameraBadge.textContent = 'Kamera aktif';
            cameraBadge.className = 'rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-300';
            placeholder.textContent = 'Tekan "Ambil Ulang Foto" untuk merekam frame terbaru.';
            video.addEventListener('loadedmetadata', function () {
                captureFrame();
            }, { once: true });
        }).catch(function () {
            cameraBadge.textContent = 'Izin kamera ditolak';
            cameraBadge.className = 'rounded-full bg-red-400/15 px-3 py-1 text-xs font-semibold text-red-300';
            placeholder.textContent = 'Akses kamera ditolak. Izinkan kamera lalu muat ulang halaman.';
        });
    }

    forms.forEach(function (form) {
        if (!form) {
            return;
        }

        form.addEventListener('submit', function (event) {
            syncForms();
            if (!latestPhoto) {
                event.preventDefault();
                alert('Foto absensi belum diambil.');
                return;
            }

            if (!latestCoords) {
                event.preventDefault();
                alert('Lokasi GPS belum tersedia.');
            }
        });
    });

    noteFields.forEach(function (field) {
        field.addEventListener('input', function () {
            syncNoteFields(field);
        });
    });
    captureButton.addEventListener('click', captureFrame);
    refreshLocationButton.addEventListener('click', requestLocation);

    syncForms();
    startCamera();
    requestLocation();
});
</script>
