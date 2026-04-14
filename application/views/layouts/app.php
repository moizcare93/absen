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
                radial-gradient(circle at top, rgba(15, 118, 110, 0.14), transparent 36%),
                linear-gradient(180deg, #f7fafc 0%, #eef6f4 100%);
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

    <div class="mx-auto flex min-h-screen w-full max-w-7xl gap-6 px-4 pb-24 pt-4 sm:px-6 lg:px-8 lg:pb-8">
        <?php if (!empty($current_user)): ?>
            <aside class="hidden lg:flex lg:w-72 lg:flex-col">
                <div class="sticky top-4 rounded-[2rem] border border-white/60 bg-white/80 p-4 shadow-soft backdrop-blur">
                    <div class="rounded-[1.5rem] bg-slate-900 p-5 text-white">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-200">Absensi RS</p>
                        <p class="mt-3 text-xl font-black"><?php echo html_escape($current_user['nama']); ?></p>
                        <p class="mt-1 text-sm text-slate-300"><?php echo html_escape($current_user['nama_role']); ?></p>
                        <p class="text-sm text-slate-400"><?php echo html_escape($current_user['nama_unit']); ?></p>
                    </div>
                    <nav class="mt-4 space-y-2">
                        <?php foreach ($menu as $item): ?>
                            <?php $active = strpos($current_route, trim(parse_url($item['url'], PHP_URL_PATH), '/')) !== FALSE; ?>
                            <a href="<?php echo $item['url']; ?>" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold <?php echo $active ? 'bg-brand-500 text-white shadow-soft' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'; ?>">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                    <path d="<?php echo $item['icon']; ?>"></path>
                                </svg>
                                <?php echo html_escape($item['label']); ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                    <a href="<?php echo site_url('auth/logout'); ?>" class="mt-4 flex items-center justify-center rounded-2xl bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200">Logout</a>
                </div>
            </aside>
        <?php endif; ?>

        <div class="min-w-0 flex-1">
            <?php if (!empty($current_user)): ?>
                <div class="mb-4 hidden rounded-[2rem] border border-white/60 bg-white/80 p-3 shadow-soft backdrop-blur lg:block">
                    <div class="grid gap-2" style="grid-template-columns: repeat(<?php echo count($menu); ?>, minmax(0, 1fr));">
                        <?php foreach ($menu as $item): ?>
                            <?php $active = strpos($current_route, trim(parse_url($item['url'], PHP_URL_PATH), '/')) !== FALSE; ?>
                            <a href="<?php echo $item['url']; ?>" class="flex items-center justify-center gap-2 rounded-2xl px-3 py-3 text-sm font-semibold <?php echo $active ? 'bg-brand-500 text-white shadow-soft' : 'text-slate-600 hover:bg-slate-100'; ?>">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                                    <path d="<?php echo $item['icon']; ?>"></path>
                                </svg>
                                <?php echo html_escape($item['label']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mx-auto flex min-h-screen w-full max-w-5xl flex-col">
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
