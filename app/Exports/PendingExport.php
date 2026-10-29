<?php
  
namespace App\Exports;
  
use App\Models\InquiryForm;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
  
class PendingExport implements FromView
{

    public function __construct($data) {
        
        $this->applications_emi = $data['applications_emi'];
        
    }

    public function view(): View {
        return view('export.pending', [
            'applications_emi' => $this->applications_emi
        ]);
    }

    
}