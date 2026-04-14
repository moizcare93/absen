<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Laporan_model');
    }

    public function index()
    {
        $user = $this->current_user();
        $month = $this->input->get('bulan', TRUE) ?: date('Y-m');
        $pegawai_id = (int) $this->input->get('pegawai_id');

        $this->render('laporan/index', array(
            'page_title' => 'Laporan',
            'month' => $month,
            'selected_pegawai_id' => $pegawai_id,
            'employees' => $this->Laporan_model->employees($user),
            'attendance_report' => $this->Laporan_model->attendance_report($user, $month, $pegawai_id),
            'leave_report' => $this->Laporan_model->leave_report($user, $month, $pegawai_id),
            'schedule_report' => $this->Laporan_model->schedule_report($user, $month, $pegawai_id),
        ));
    }

    public function export($type = 'excel')
    {
        $user = $this->current_user();
        $month = $this->input->get('bulan', TRUE) ?: date('Y-m');
        $pegawai_id = (int) $this->input->get('pegawai_id');
        $dataset = $this->build_export_dataset($user, $month, $pegawai_id);

        if ($type === 'pdf') {
            $pdf = $this->build_simple_pdf($dataset['lines']);
            $filename = 'laporan_absen_' . str_replace('-', '_', $month) . '.pdf';

            return $this->output
                ->set_content_type('application/pdf')
                ->set_header('Content-Disposition: attachment; filename="' . $filename . '"')
                ->set_output($pdf);
        }

        $filename = 'laporan_absen_' . str_replace('-', '_', $month) . '.csv';
        $stream = fopen('php://temp', 'w+');
        foreach ($dataset['csv'] as $row) {
            fputcsv($stream, $row);
        }
        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return $this->output
            ->set_content_type('text/csv')
            ->set_header('Content-Disposition: attachment; filename="' . $filename . '"')
            ->set_output($csv);
    }

    protected function build_export_dataset(array $user, $month, $pegawai_id)
    {
        $attendance = $this->Laporan_model->attendance_report($user, $month, $pegawai_id);
        $leave = $this->Laporan_model->leave_report($user, $month, $pegawai_id);
        $schedule = $this->Laporan_model->schedule_report($user, $month, $pegawai_id);

        $csv = array(
            array('Laporan Operasional Absensi RS'),
            array('Bulan', $month),
            array(''),
            array('Laporan Absensi'),
            array('Nama', 'Total Hari', 'Hadir', 'Terlambat', 'Cuti', 'Izin'),
        );

        $lines = array(
            'Laporan Operasional Absensi RS',
            'Bulan: ' . $month,
            '',
            'Laporan Absensi',
            'Nama | Total | Hadir | Terlambat | Cuti | Izin',
        );

        foreach ($attendance as $row) {
            $csv[] = array($row['nama'], $row['total_hari'], $row['hadir'], $row['terlambat'], $row['cuti'], $row['izin']);
            $lines[] = sprintf('%s | %d | %d | %d | %d | %d', $row['nama'], $row['total_hari'], $row['hadir'], $row['terlambat'], $row['cuti'], $row['izin']);
        }

        $csv[] = array('');
        $csv[] = array('Laporan Cuti');
        $csv[] = array('Nama', 'Total Pengajuan', 'Pending', 'Approval Unit', 'Approval HR', 'Ditolak');
        $lines[] = '';
        $lines[] = 'Laporan Cuti';
        $lines[] = 'Nama | Total | Pending | Approval Unit | Approval HR | Ditolak';

        foreach ($leave as $row) {
            $csv[] = array($row['nama'], $row['total_pengajuan'], $row['pending'], $row['approved_unit'], $row['approved_hr'], $row['ditolak']);
            $lines[] = sprintf('%s | %d | %d | %d | %d | %d', $row['nama'], $row['total_pengajuan'], $row['pending'], $row['approved_unit'], $row['approved_hr'], $row['ditolak']);
        }

        $csv[] = array('');
        $csv[] = array('Laporan Jadwal');
        $csv[] = array('Nama', 'Total Shift');
        $lines[] = '';
        $lines[] = 'Laporan Jadwal';
        $lines[] = 'Nama | Total Shift';

        foreach ($schedule as $row) {
            $csv[] = array($row['nama'], $row['total_shift']);
            $lines[] = sprintf('%s | %d', $row['nama'], $row['total_shift']);
        }

        return array('csv' => $csv, 'lines' => $lines);
    }

    protected function build_simple_pdf(array $lines)
    {
        $pages = array_chunk($lines, 45);
        $objects = array();
        $kids = array();
        $font_object_id = 1;
        $pages_object_id = 2;
        $next_object_id = 3;

        $objects[$font_object_id] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";

        foreach ($pages as $page_lines) {
            $content = "BT\n/F1 11 Tf\n50 790 Td\n14 TL\n";
            foreach ($page_lines as $line) {
                $sanitized = str_replace(array('\\', '(', ')'), array('\\\\', '\\(', '\\)'), $line);
                $content .= '(' . $sanitized . ") Tj\nT*\n";
            }
            $content .= "ET\n";

            $content_object_id = $next_object_id++;
            $page_object_id = $next_object_id++;
            $objects[$content_object_id] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "endstream";
            $objects[$page_object_id] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 1 0 R >> >> /Contents " . $content_object_id . " 0 R >>";
            $kids[] = $page_object_id . ' 0 R';
        }

        $objects[$pages_object_id] = "<< /Type /Pages /Kids [" . implode(' ', $kids) . "] /Count " . count($kids) . " >>";
        $catalog_object_id = $next_object_id++;
        $objects[$catalog_object_id] = "<< /Type /Catalog /Pages 2 0 R >>";

        ksort($objects);
        $pdf = "%PDF-1.4\n";
        $offsets = array(0);

        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $body . "\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . ($catalog_object_id + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= $catalog_object_id; $i++) {
            $offset = isset($offsets[$i]) ? $offsets[$i] : 0;
            $pdf .= sprintf('%010d 00000 n ', $offset) . "\n";
        }

        $pdf .= "trailer\n<< /Size " . ($catalog_object_id + 1) . " /Root " . $catalog_object_id . " 0 R >>\nstartxref\n" . $xref . "\n%%EOF";

        return $pdf;
    }
}
