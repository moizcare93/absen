<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?php echo html_escape($page_title); ?> | <?php echo html_escape($app_name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf9',
                            100: '#ccfbef',
                            500: '#0f766e',
                            600: '#0b5d57',
                            700: '#114240',
                            900: '#0f172a'
                        }
                    },
                    boxShadow: {
                        soft: '0 20px 50px rgba(15, 23, 42, 0.12)'
                    }
                }
            }
        };
    </script>
    <style>
        body {
            background:
                radial-gradient(circle at top left, rgba(15, 118, 110, 0.18), transparent 24%),
                radial-gradient(circle at bottom right, rgba(15, 23, 42, 0.08), transparent 28%),
                linear-gradient(180deg, #eef3f8 0%, #e7edf4 100%);
        }

        .admin-shell {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.84), rgba(255, 255, 255, 0.74));
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 22px 60px rgba(15, 23, 42, 0.08);
            backdrop-filter: blur(14px);
        }

        .admin-panel {
            border: 1px solid #e2e8f0;
            background: #fff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
        }

        .admin-desktop-card {
            border: 1px solid #e2e8f0;
            background: linear-gradient(180deg, #ffffff, #f8fafc);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        }
    </style>
</head>
<body class="min-h-screen text-slate-800">
    <?php
    $menu = array();
    if (!empty($current_user)) {
        $menu = array(
            array('label' => 'Home', 'url' => site_url('dashboard'), 'icon' => 'M3 12h18M12 3v18'),
            array('label' => 'Absen', 'url' => site_url('absensi'), 'icon' => 'M12 6v6l4 2'),
            array('label' => 'Jadwal', 'url' => site_url('jadwal'), 'icon' => 'M8 2v4M16 2v4M3 10h18'),
            array('label' => 'Cuti', 'url' => site_url('cuti'), 'icon' => 'M4 7h16M4 12h16M4 17h10'),
        );

        if ((int) $current_user['level'] <= 3) {
            $menu[] = array('label' => 'Pegawai', 'url' => site_url('pegawai'), 'icon' => 'M12 12a4 4 0 1 0 0.001-0.001M6 20a6 6 0 0 1 12 0');
            $menu[] = array('label' => 'Lapor', 'url' => site_url('laporan'), 'icon' => 'M5 12h14M5 7h14M5 17h10');
        }
    }
    ?>

    <div class="mx-auto flex min-h-screen w-full max-w-[1700px] gap-6 px-4 pb-24 pt-4 sm:px-6 lg:px-8 lg:pb-8">
        <?php if (!empty($current_user)): ?>
            <aside class="hidden lg:flex lg:w-72 lg:flex-col xl:w-80">
                <div class="sticky top-4 overflow-hidden rounded-[2rem] bg-slate-950 text-white shadow-soft">
                    <div class="border-b border-white/10 px-6 pb-6 pt-7">
                        <p class="text-xs font-semibold uppercase tracking-[0.32em] text-emerald-300">Absensi RS</p>
                        <p class="mt-4 text-2xl font-black leading-tight"><?php echo html_escape($current_user['nama']); ?></p>
                        <p class="mt-2 text-sm text-slate-300"><?php echo html_escape($current_user['nama_role']); ?></p>
                        <p class="text-sm text-slate-400"><?php echo html_escape($current_user['nama_unit']); ?></p>
                    </div>
                    <nav class="space-y-1 px-4 py-5">
                        <?php foreach ($menu as $item): ?>
                            <?php $active = strpos($current_route, trim(parse_url($item['url'], PHP_URL_PATH), '/')) !== FALSE; ?>
                            <a href="<?php echo $item['url']; ?>" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition <?php echo $active ? 'bg-brand-500 text-white shadow-soft' : 'text-slate-300 hover:bg-white/8 hover:text-white'; ?>">
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                    <path d="<?php echo $item['icon']; ?>"></path>
                                </svg>
                                <?php echo html_escape($item['label']); ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                    <div class="border-t border-white/10 p-4">
                        <a href="<?php echo site_url('auth/logout'); ?>" class="flex items-center justify-center rounded-2xl bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/15">Logout</a>
                    </div>
                </div>
            </aside>
        <?php endif; ?>

        <div class="min-w-0 flex-1">
            <?php if (!empty($current_user)): ?>
                <div class="admin-shell mb-4 hidden items-center justify-between rounded-[2rem] px-6 py-4 lg:flex">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-brand-600">Control Center</p>
                        <h1 class="mt-2 text-2xl font-black text-slate-900"><?php echo html_escape($page_title); ?></h1>
                    </div>
                    <div class="flex items-center gap-3 rounded-2xl bg-slate-950 px-4 py-3 text-right text-white">
                        <div>
                            <p class="text-sm font-semibold"><?php echo html_escape($current_user['nama']); ?></p>
                            <p class="text-xs text-slate-400"><?php echo html_escape($current_user['nama_role']); ?></p>
                        </div>
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-500 text-sm font-black">
                            <?php echo html_escape(strtoupper(substr($current_user['nama'], 0, 1))); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mx-auto flex min-h-screen w-full max-w-6xl flex-col">
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <?php echo html_escape($this->session->flashdata('error')); ?>
                    </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        <?php echo html_escape($this->session->flashdata('success')); ?>
                    </div>
                <?php endif; ?>

                <?php $this->load->view($content_view); ?>
            </div>
        </div>
    </div>

    <?php if (!empty($current_user)): ?>
        <nav class="fixed inset-x-0 bottom-0 z-20 border-t border-slate-200 bg-white/95 px-3 pb-safe pt-2 backdrop-blur lg:hidden">
            <div class="mx-auto grid max-w-md gap-2" style="grid-template-columns: repeat(<?php echo count($menu); ?>, minmax(0, 1fr));">
                <?php foreach ($menu as $item): ?>
                    <?php $active = strpos($current_route, trim(parse_url($item['url'], PHP_URL_PATH), '/')) !== FALSE; ?>
                    <a href="<?php echo $item['url']; ?>" class="flex flex-col items-center justify-center rounded-2xl px-2 py-3 text-xs font-semibold <?php echo $active ? 'bg-brand-500 text-white shadow-soft' : 'text-slate-500'; ?>">
                        <svg class="mb-1 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                            <path d="<?php echo $item['icon']; ?>"></path>
                        </svg>
                        <?php echo html_escape($item['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </nav>
    <?php endif; ?>
</body>
</html>
