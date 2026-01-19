<?php

namespace App\Exports;

use App\Models\Schedule;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ScheduleExport implements FromView, ShouldAutoSize
{
    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function view(): View {
        // Retorna a view que montará a tabela com os 3 níveis por linha

        // dd($this->data);
        return view('exports.schedule_excel', [
            'days'    => $this->data['days'],
            'times'   => $this->data['times'],
            'courses' => $this->data['courses']
        ]);
    }

    public function exportExcel()
    {
        $data = Schedule::mountSchedulePdf($schedules); // Sua lógica de montagem atual
        
        return Excel::download(
            new ScheduleMultiLineExport($data), 
            'horario-' . now()->format('d-m-Y') . '.xlsx'
        );
    }
}
