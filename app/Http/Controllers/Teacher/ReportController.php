<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeachingSession;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = $request->user()->id;
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $base = TeachingSession::with(['subject', 'classRoom', 'material'])
            ->where('teacher_id', $teacherId)
            ->whereMonth('session_date', $month)
            ->whereYear('session_date', $year);

        $sessions = (clone $base)->orderByDesc('session_date')->orderBy('start_time')->paginate(10)->withQueryString();
        $completedCount = (clone $base)->where('status', 'completed')->count();
        $upcomingCount = (clone $base)->whereIn('status', ['scheduled', 'in_progress'])->whereDate('session_date', '>=', today())->count();
        $attentionCount = (clone $base)->whereDate('session_date', '<', today())->whereNotIn('status', ['completed', 'cancelled'])->count();

        $labels = [];
        $finished = [];
        $unfinished = [];
        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $weekBuckets = [0, 0, 0, 0, 0];
        $unfinishedBuckets = [0, 0, 0, 0, 0];
        foreach ((clone $base)->get() as $item) {
            $bucket = min(4, (int) floor(($item->session_date->day - 1) / 7));
            if ($item->status === 'completed') $weekBuckets[$bucket]++;
            elseif (!in_array($item->status, ['cancelled'])) $unfinishedBuckets[$bucket]++;
        }
        for ($i = 1; $i <= 5; $i++) {
            $labels[] = 'Minggu '.$i;
            $finished[] = $weekBuckets[$i - 1];
            $unfinished[] = $unfinishedBuckets[$i - 1];
        }

        $monthName = \Illuminate\Support\Carbon::create($year, $month, 1)->translatedFormat('F Y');
        return view('teacher.reports.index', compact(
            'sessions', 'month', 'year', 'completedCount', 'upcomingCount', 'attentionCount',
            'labels', 'finished', 'unfinished', 'monthName', 'days'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $teacherId = $request->user()->id;
        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);
        $rows = TeachingSession::with(['subject', 'classRoom', 'material'])
            ->where('teacher_id', $teacherId)
            ->whereMonth('session_date', $month)
            ->whereYear('session_date', $year)
            ->orderBy('session_date')->orderBy('start_time')->get();

        $filename = "laporan-aktivitas-pengajar-{$month}-{$year}.csv";
        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM helps Microsoft Excel display Indonesian text correctly.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Tanggal', 'Mata Pelajaran', 'Kelas', 'Materi', 'Jam Mulai', 'Jam Selesai', 'Jumlah Siswa', 'Status', 'Catatan'], ';');
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->session_date->format('d-m-Y'), $row->subject?->name,
                    $row->classRoom?->name, $row->material?->title, $row->start_time,
                    $row->end_time, $row->student_count,
                    $row->display_status, $row->notes,
                ], ';');
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
