<?php

namespace App\Exports;

use App\Models\Catalogue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CatalogueExport implements FromQuery, WithHeadings, WithStrictNullComparison, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    
        protected $periodeid;
        protected $from_date;
        protected $to_date;
    
        // function __construct($from_date,$to_date) {
        //         $this->from_date = $from_date;
        //         $this->to_date = $to_date;
        // }
        function __construct($periodeid) {
            $this->periodeid = $periodeid;
        }
    
        public function query()
        {
            $data = DB::table('catalogue')
            ->where('ID_PERIOD', '=', $this->periodeid)
            ->select('ID_CATALOGUE','NAME_CATALOGUE', 'STOCK', 'STOCK_GUDANG',
                DB::raw("(SELECT COUNT(ID_REDEEM_LOG) FROM redeem_log
                            WHERE redeem_log.ID_CATALOGUE = catalogue.ID_CATALOGUE AND redeem_log.ID_REDEEM_STATUS = 1
                            GROUP BY redeem_log.ID_CATALOGUE) as JumlahPending"),
                DB::raw("(SELECT COUNT(ID_REDEEM_LOG) FROM redeem_log
                            WHERE redeem_log.ID_CATALOGUE = catalogue.ID_CATALOGUE AND redeem_log.ID_REDEEM_STATUS = 2
                            GROUP BY redeem_log.ID_CATALOGUE) as JumlahKeluar"))
            ->orderBy('ID_CATALOGUE')
            ->groupBy('ID_CATALOGUE');

            // $data = DB::table('catalogue as c')
            // ->leftJoin('redeem_log AS rl', 'c.ID_CATALOGUE', '=', 'rl.ID_CATALOGUE')
            // ->where('c.ID_PERIOD', '=', $this->periodeid)
            // ->select('c.ID_CATALOGUE','c.NAME_CATALOGUE', 'c.STOCK', 'c.STOCK_GUDANG',
            // DB::raw('COUNT(rl.ID_REDEEM_LOG) as JumlahKeluar'))
            // ->orderBy('c.ID_CATALOGUE')
            // ->groupBy('c.ID_CATALOGUE');
    
            return $data;
        }

        public function getStartDate()
        {
            

            return $this->from_date->format('d-m-Y');
        }

        public function getFinishDate()
        {
            $this->from_date = DB::table('year_period')
            ->select('YEAR_FINISH')
            ->where('ID_PERIOD', '=', $this->periodeid)
            ->get();

            return $this->to_date->format('d-m-Y');
        }

        public function registerEvents(): array
        {
            return [
                AfterSheet::class    => function(AfterSheet $event) {
                    $cellRange = 'A1:W2'; // All headers
                    $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                    $styleHeader = [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => 'thin',
                                'color' => ['rgb' => '808080']
                            ],
                        ]
                    ];
                    $event->sheet->mergeCells('A1:F1');
                    $event->sheet->getStyle("A2:F2")->applyFromArray($styleHeader);
                    $event->sheet->getDelegate()->getStyle('A1:F2')
                                ->getFont()
                                ->setBold(true);
                    $event->sheet->getDelegate()->getStyle('A1:F2')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $event->sheet->getDelegate()->getStyle('A3:F2000')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                },
            ];
        }
        
        public function headings(): array
        {
            $this->from_date = DB::table('year_period')
            ->select(DB::raw('DATE_FORMAT(YEAR_START, "%d-%b-%Y") as YEAR_START'))
            ->where('ID_PERIOD', '=', $this->periodeid)
            ->get()
            ->first()->YEAR_START;

            $this->to_date = DB::table('year_period')
            ->select(DB::raw('DATE_FORMAT(YEAR_FINISH, "%d-%b-%Y") as YEAR_FINISH'))
            ->where('ID_PERIOD', '=', $this->periodeid)
            ->get()
            ->first()->YEAR_FINISH;

            return [
                ["Laporan Stock Barang Katalog Tanggal: $this->from_date s/d $this->to_date" ],
                [
                    'ID_Katalog',
                    'Nama_Barang',
                    'Stock_Aplikasi',
                    'Stock_Gudang',
                    'Keluar_Finish',
                    'Keluar_Pending',
                ]
            ];
        }
}
