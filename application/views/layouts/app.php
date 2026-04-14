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
    <div class="mx-auto flex min-h-screen max-w-md flex-col px-4 pb-24 pt-4">
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

    <?php if (!empty($current_user)): ?>
        <nav class="fixed inset-x-0 bottom-0 z-20 border-t border-slate-200 bg-white/95 px-3 pb-safe pt-2 backdrop-blur">
            <?php
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
            ?>
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
