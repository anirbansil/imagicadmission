<?php
  
namespace App\Exports;
  
use App\Models\InquiryForm;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
  
class InquiryExport implements FromView
{

    public function __construct($data) {
        
        $this->inquires = $data['inquires'];
        
    }

    public function view(): View {
        return view('export.inquiry', [
            'inquires' => $this->inquires
        ]);
    }

    
}