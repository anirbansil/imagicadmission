<?php
  
namespace App\Exports;
  
use App\Models\ApplicationForm;
use App\Models\ApplicationFormEmi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
  
class McrReportsExport implements FromView
{

	// function __construct($from_date,$to_date,$branch,$input_search) {
 //        $this->from_date = $from_date;
 //        $this->to_date = $to_date;
 //        //$this->course = $course;
 //        //$this->class = $class;
 //        $this->branch = $branch;
 //        //$this->st_type = $st_type;
 //        $this->input_search = $input_search;
 //    }

    public function __construct($data) {
        
        $this->applications = $data['applications'];
        $this->applications_emi = $data['applications_emi'];
        
    }

    public function view(): View {
        return view('export.mcr', [
            'applications' => $this->applications,
            'applications_emi' => $this->applications_emi
        ]);
    }

    
}