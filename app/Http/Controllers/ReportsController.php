<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InquiryForm;
use App\Models\RegistrationForm;
use App\Models\CourseMaster;
use App\Models\CenterMaster;
use App\Models\ApplicationForm;
use App\Models\ApplicationFormEmi;
use App\Exports\DcrReportsExport;
use App\Exports\McrReportsExport;
use App\Exports\DsrReportsExport;
use App\Exports\MsrReportsExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Illuminate\Http\Request;
use Session;
use Validator;
use Carbon\Carbon;
use DB;
use PDF;

class ReportsController extends Controller {

	/**

	 * Display a listing of the resource.

	 *

	 * @return \Illuminate\Http\Response

	 */

	public function dcr(Request $request) {

		$title = 'DCR Report';

		$centerDetails = CenterMaster::where(function($q) use ($request) {
                            if(Auth::guard()->user()->is_admin != 1){
                                $q->where('id',Auth::guard()->user()->branch_id);
                            }
                        })->get();

		$applications = ApplicationForm::where(function ($q) use ($request) {
							if(Auth::guard()->user()->is_admin != 1){
				            	$q->whereHas('regDetails', function ($qu) use ($request) {
				                    // $qu->where('center_name',Auth::guard()->user()->branch_id);
				    				$qu->where('created_by', Auth::guard()->id());
				                });
				                
				            }
				            if ($request->has('search_by_center') && !empty($request->search_by_center) ){
				                $q->whereHas('regDetails', function ($qu) use ($request) {
				                    $qu->where('center_name', $request->search_by_center);
				                });
				            }
						})->whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->get();
						
		$applications_emi = ApplicationFormEmi::where(function($q) use ($request) {
								if(Auth::guard()->user()->is_admin != 1){
					            	$q->whereHas('application', function ($qu) use ($request) {
					            		$qu->whereHas('regDetails', function ($qur) use ($request) {
					            			$qur->where('center_name',Auth::guard()->user()->branch_id);
					            		});
					                    
					                });
					                
					            }
					            if ($request->has('search_by_center') && !empty($request->search_by_center) ){
					        		$q->whereHas('application', function ($qu) use ($request) {
						                $qu->whereHas('regDetails', function ($qur) use ($request) {
						                    $qur->where('center_name', $request->search_by_center);
						                });
						            });
					            }
							})
							->where(function ($qp) use ($request) {
								$qp->where('payment_type','!=','');
								$qp->orWhereNotNull('payment_type');
							})
							->whereDate('update_date', Carbon::today())
							//->groupBy('application_id')
							->orderBy('id', 'desc')
							->get();

		return view('reports.dcr', compact('title', 'applications','applications_emi','centerDetails','request'));

	}

	public function dcrExport(Request $request) {

		$title = 'DCR Report';

		$applications = ApplicationForm::where(function ($q) use ($request) {
							if(Auth::guard()->user()->is_admin != 1){
				            	$q->whereHas('regDetails', function ($qu) use ($request) {
				                    $qu->where('center_name',Auth::guard()->user()->branch_id);
				                });
				                
				            }
				            if ($request->has('search_by_center') && !empty($request->search_by_center) ){
				                $q->whereHas('regDetails', function ($qu) use ($request) {
				                    $qu->where('center_name', $request->search_by_center);
				                });
				            }
						})->whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->get();;
		$applications_emi = ApplicationFormEmi::where(function($q) use ($request) {
								if(Auth::guard()->user()->is_admin != 1){
					            	$q->whereHas('application', function ($qu) use ($request) {
					            		$qu->whereHas('regDetails', function ($qur) use ($request) {
					            			$qur->where('center_name',Auth::guard()->user()->branch_id);
					            		});
					                    
					                });
					                
					            }
					            if ($request->has('search_by_center') && !empty($request->search_by_center) ){
					        		$q->whereHas('application', function ($qu) use ($request) {
						                $qu->whereHas('regDetails', function ($qur) use ($request) {
						                    $qur->where('center_name', $request->search_by_center);
						                });
						            });
					            }
							})
							->where(function ($qp) use ($request) {
								$qp->where('payment_type','!=','');
								$qp->orWhereNotNull('payment_type');
							})
							->whereDate('update_date', Carbon::today())
							//->groupBy('application_id')
							->orderBy('id', 'desc')
							->get();

		$data = [
			'applications' => $applications,
			'applications_emi' => $applications_emi
		];

		return Excel::download(new DcrReportsExport($data), 'dcr-reports.xlsx');

	}
	// ===========================================
	public function mcr(Request $request) {

		$title = 'MCR Report';

		$centerDetails = CenterMaster::where(function($q) use ($request) {
                            if(Auth::guard()->user()->is_admin != 1){
                                $q->where('id',Auth::guard()->user()->branch_id);
                            }
                        })->get();

		$applications = ApplicationForm::where(function($q) use ($request) {
							if (!empty($request->input('from_date') && !empty($request->input('to_date'))))
				        	{

				        		$datef = Carbon::parse($request->from_date)
						                 ->startOfDay() 
						                 ->toDateTimeString();

								$dateto = Carbon::parse($request->to_date)
						                 ->endOfDay()
						                 ->toDateTimeString();
				        		$q->whereBetween('created_at',[$datef, $dateto]);
				    		}else{
				    			$q->whereMonth('created_at', Carbon::now()->month);
				    		}

				    		if (!empty($request->input('search')))
				        	{
				        		$reg_id = RegistrationForm::where('roll_no',$request->input('search'))->first();
				        		if($reg_id){
				        			$q->where('reg_id',$reg_id->id);	
				        		}else{
				        			$q->where('reg_id',0);
				        		}
				    		}

				    		// if ((empty($request->input('from_date') && empty($request->input('to_date')))) && empty($request->input('search')))
				      //   	{
				      //   		$q->whereMonth('created_at', Carbon::now()->month);
				      //   	}
				    		if($request->has('from_date') || $request->has('to_date') || $request->has('search') || $request->has('search_by_center') )
				        	{
				        		
				        	}else{
				        		$q->whereMonth('created_at', Carbon::now()->month);
				        	}

				        	if ($request->has('search_by_center') && !empty($request->search_by_center) ){
				                $q->whereHas('regDetails', function ($qu) use ($request) {
				                    $qu->where('center_name', $request->search_by_center);
				                });
				            }

				        	if(Auth::guard()->user()->is_admin != 1){
				            	$q->whereHas('regDetails', function ($qu) use ($request) {
				                    // $qu->where('center_name',Auth::guard()->user()->branch_id);
				    				$qu->where('created_by', Auth::guard()->id());
				                });
				                
				            }
						})						
						->orderBy('id', 'desc')
						->get();;
		$applications_emi = ApplicationFormEmi::where(function($q) use ($request) {
								if (!empty($request->input('from_date') && !empty($request->input('to_date'))))
					        	{

					        		$datef = Carbon::parse($request->from_date)
							                 ->startOfDay() 
							                 ->toDateTimeString();

									$dateto = Carbon::parse($request->to_date)
							                 ->endOfDay()
							                 ->toDateTimeString();
					        		$q->whereBetween('updated_at',[$datef, $dateto]);
					    		}else{
					    			$q->whereMonth('updated_at', Carbon::now()->month);
					    		}

					    		if (!empty($request->input('search')))
					        	{
					        		$reg_id = RegistrationForm::where('roll_no',$request->input('search'))->first();
					        		
					        		if($reg_id){
					        			$app_id = ApplicationForm::where('reg_id',$reg_id->id)->first();
					        			$q->where('application_id',$app_id->id);	
					        		}else{
					        			$q->where('application_id',0);	
					        		}
					        		
					    		}

					    		if($request->has('from_date') || $request->has('to_date') || $request->has('search') || $request->has('search_by_center') )
					        	{
					        		
					        	}else{
					        		$q->whereMonth('updated_at', Carbon::now()->month);
					        	}

					        	if ($request->has('search_by_center') && !empty($request->search_by_center) ){
					        		$q->whereHas('application', function ($qu) use ($request) {
						                $qu->whereHas('regDetails', function ($qur) use ($request) {
						                    $qur->where('center_name', $request->search_by_center);
						                });
						            });
					            }

					        	if(Auth::guard()->user()->is_admin != 1){
					            	$q->whereHas('application', function ($qu) use ($request) {
					            		$qu->whereHas('regDetails', function ($qur) use ($request) {
					            			$qur->where('center_name',Auth::guard()->user()->branch_id);
					            		});
					                    
					                });
					                
					            }
							})
							->where(function ($qp) use ($request) {
								$qp->where('payment_type','!=','');
								$qp->orWhereNotNull('payment_type');
							})
							// ->groupBy('application_id')
							->orderBy('id', 'desc')
							->get();
							
		// dd($applications_emi);
		return view('reports.mcr', compact('title', 'applications','applications_emi','request','centerDetails'));

	}

	public function mcrExport(Request $request) {

		$title = 'MCR Report';

		$applications = ApplicationForm::where(function($q) use ($request) {
							if (!empty($request->input('from_date') && !empty($request->input('to_date'))))
				        	{

				        		$datef = Carbon::parse($request->from_date)
						                 ->startOfDay() 
						                 ->toDateTimeString();

								$dateto = Carbon::parse($request->to_date)
						                 ->endOfDay()
						                 ->toDateTimeString();
				        		$q->whereBetween('created_at',[$datef, $dateto]);
				    		}else{
				    			$q->whereMonth('created_at', Carbon::now()->month);
				    		}

				    		if (!empty($request->input('search')))
				        	{
				        		$reg_id = RegistrationForm::where('roll_no',$request->input('search'))->first();
				        		if($reg_id){
				        			$q->where('reg_id',$reg_id->id);	
				        		}else{
				        			$q->where('reg_id',0);
				        		}
				    		}

				    		// if ((empty($request->input('from_date') && empty($request->input('to_date')))) && empty($request->input('search')))
				      //   	{
				      //   		$q->whereMonth('created_at', Carbon::now()->month);
				      //   	}
				    		if($request->has('from_date') || $request->has('to_date') || $request->has('search') || $request->has('search_by_center') )
				        	{
				        		
				        	}else{
				        		$q->whereMonth('created_at', Carbon::now()->month);
				        	}

				        	if ($request->has('search_by_center') && !empty($request->search_by_center) ){
				                $q->whereHas('regDetails', function ($qu) use ($request) {
				                    $qu->where('center_name', $request->search_by_center);
				                });
				            }

				        	if(Auth::guard()->user()->is_admin != 1){
				            	$q->whereHas('regDetails', function ($qu) use ($request) {
				                    $qu->where('center_name',Auth::guard()->user()->branch_id);
				                });
				                
				            }
						})						
						->orderBy('id', 'desc')
						->get();;
		$applications_emi = ApplicationFormEmi::where(function($q) use ($request) {
								if (!empty($request->input('from_date') && !empty($request->input('to_date'))))
					        	{

					        		$datef = Carbon::parse($request->from_date)
							                 ->startOfDay() 
							                 ->toDateTimeString();

									$dateto = Carbon::parse($request->to_date)
							                 ->endOfDay()
							                 ->toDateTimeString();
					        		$q->whereBetween('updated_at',[$datef, $dateto]);
					    		}else{
					    			$q->whereMonth('updated_at', Carbon::now()->month);
					    		}

					    		if (!empty($request->input('search')))
					        	{
					        		$reg_id = RegistrationForm::where('roll_no',$request->input('search'))->first();
					        		
					        		if($reg_id){
					        			$app_id = ApplicationForm::where('reg_id',$reg_id->id)->first();
					        			$q->where('application_id',$app_id->id);	
					        		}else{
					        			$q->where('application_id',0);	
					        		}
					        		
					    		}

					    		if($request->has('from_date') || $request->has('to_date') || $request->has('search') || $request->has('search_by_center') )
					        	{
					        		
					        	}else{
					        		$q->whereMonth('updated_at', Carbon::now()->month);
					        	}

					        	if ($request->has('search_by_center') && !empty($request->search_by_center) ){
					        		$q->whereHas('application', function ($qu) use ($request) {
						                $qu->whereHas('regDetails', function ($qur) use ($request) {
						                    $qur->where('center_name', $request->search_by_center);
						                });
						            });
					            }

					        	if(Auth::guard()->user()->is_admin != 1){
					            	$q->whereHas('application', function ($qu) use ($request) {
					            		$qu->whereHas('regDetails', function ($qur) use ($request) {
					            			$qur->where('center_name',Auth::guard()->user()->branch_id);
					            		});
					                    
					                });
					                
					            }
							})
							->where(function ($qp) use ($request) {
								$qp->where('payment_type','!=','');
								$qp->orWhereNotNull('payment_type');
							})
				// 			->groupBy('application_id')
							->orderBy('id', 'desc')
							->get();


		$data = [
			'applications' => $applications,
			'applications_emi' => $applications_emi
		];
		dd($request->all());
		return Excel::download(new McrReportsExport($data), 'mcr-reports.xlsx');

	}

	// ============================================

	public function dsr(Request $request) {

		$title = 'DSR Report';

		$centerDetails = CenterMaster::where(function($q) use ($request) {
                            if(Auth::guard()->user()->is_admin != 1){
                                $q->where('id',Auth::guard()->user()->branch_id);
                            }
                        })->get();

		$applications = ApplicationForm::where(function ($q) use ($request) {
								if(Auth::guard()->user()->is_admin != 1){
					            	$q->whereHas('regDetails', function ($qu) use ($request) {
					                    // $qu->where('center_name',Auth::guard()->user()->branch_id);
				    					$qu->where('created_by', Auth::guard()->id());
					                });
					            }
					            if ($request->has('search_by_center') && !empty($request->search_by_center) ){
					                $q->whereHas('regDetails', function ($qu) use ($request) {
					                    $qu->where('center_name', $request->search_by_center);
					                });
					            }				                
				            })->whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->get();

		return view('reports.dsr', compact('title', 'applications','centerDetails','request'));

	}

	public function dsrExport(Request $request) {

		$title = 'DSR Report';

		$applications = ApplicationForm::where(function ($q) use ($request) {
								if(Auth::guard()->user()->is_admin != 1){
					            	$q->whereHas('regDetails', function ($qu) use ($request) {
					                    $qu->where('center_name',Auth::guard()->user()->branch_id);
					                });
					            }
					            if ($request->has('search_by_center') && !empty($request->search_by_center) ){
					                $q->whereHas('regDetails', function ($qu) use ($request) {
					                    $qu->where('center_name', $request->search_by_center);
					                });
					            }				                
				            })->whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->get();;

		$data = [
			'applications' => $applications
		];

		return Excel::download(new DsrReportsExport($data), 'dsr-reports.xlsx');

	}

	// ============================================

	public function msr(Request $request) {

		$title = 'MSR Report';

		$centerDetails = CenterMaster::where(function($q) use ($request) {
                            if(Auth::guard()->user()->is_admin != 1){
                                $q->where('id',Auth::guard()->user()->branch_id);
                            }
                        })->get();

		$applications = ApplicationForm::where(function($q) use ($request) {
							if (!empty($request->input('from_date') && !empty($request->input('to_date'))))
				        	{

				        		$datef = Carbon::parse($request->from_date)
						                 ->startOfDay() 
						                 ->toDateTimeString();

								$dateto = Carbon::parse($request->to_date)
						                 ->endOfDay()
						                 ->toDateTimeString();
						        
				        		$q->whereBetween('created_at',[$datef, $dateto]);
				    		}else{
				    			$q->whereMonth('created_at', Carbon::now()->month);
				    		}

				    		if (!empty($request->input('search')))
				        	{
				        		$reg_id = RegistrationForm::where('roll_no',$request->input('search'))->first();
				        		if($reg_id){
				        			$q->where('reg_id',$reg_id->id);	
				        		}else{
				        			$q->where('reg_id',0);
				        		}
				    		}

				    		// if ((empty($request->input('from_date') && empty($request->input('to_date')))) && empty($request->input('search')))
				    		if($request->has('from_date') || $request->has('to_date') || $request->has('search') || $request->has('search_by_center') )
				        	{
				        		
				        	}else{
				        		$q->whereMonth('created_at', Carbon::now()->month);
				        	}

				        	if ($request->has('search_by_center') && !empty($request->search_by_center) ){
				                $q->whereHas('regDetails', function ($qu) use ($request) {
				                    $qu->where('center_name', $request->search_by_center);
				                });
				            }

				        	if(Auth::guard()->user()->is_admin != 1){
				            	$q->whereHas('regDetails', function ($qu) use ($request) {
				                    // $qu->where('center_name',Auth::guard()->user()->branch_id);
				    				$qu->where('created_by', Auth::guard()->id());
				                });
				                
				            }
						})						
						->orderBy('id', 'desc')
						->get();
		

		return view('reports.msr', compact('title', 'applications','request','centerDetails'));

	}

	public function msrExport(Request $request) {

		$title = 'MSR Report';

		$applications = ApplicationForm::where(function($q) use ($request) {
							if (!empty($request->input('from_date') && !empty($request->input('to_date'))))
				        	{

				        		$datef = Carbon::parse($request->from_date)
						                 ->startOfDay() 
						                 ->toDateTimeString();

								$dateto = Carbon::parse($request->to_date)
						                 ->endOfDay()
						                 ->toDateTimeString();
				        		$q->whereBetween('created_at',[$datef, $dateto]);
				    		}else{
				    			$q->whereMonth('created_at', Carbon::now()->month);
				    		}

				    		if (!empty($request->input('search')))
				        	{
				        		$reg_id = RegistrationForm::where('roll_no',$request->input('search'))->first();
				        		if($reg_id){
				        			$q->where('reg_id',$reg_id->id);	
				        		}else{
				        			$q->where('reg_id',0);
				        		}
				    		}

				    		// if ((empty($request->input('from_date') && empty($request->input('to_date')))) && empty($request->input('search')))
				      //   	{
				      //   		$q->whereMonth('created_at', Carbon::now()->month);
				      //   	}
				    		if($request->has('from_date') || $request->has('to_date') || $request->has('search') || $request->has('search_by_center') )
				        	{
				        		
				        	}else{
				        		$q->whereMonth('created_at', Carbon::now()->month);
				        	}

				        	if ($request->has('search_by_center') && !empty($request->search_by_center) ){
				                $q->whereHas('regDetails', function ($qu) use ($request) {
				                    $qu->where('center_name', $request->search_by_center);
				                });
				            }

				        	if(Auth::guard()->user()->is_admin != 1){
				            	$q->whereHas('regDetails', function ($qu) use ($request) {
				                    $qu->where('center_name',Auth::guard()->user()->branch_id);
				                });
				                
				            }
						})						
						->orderBy('id', 'desc')
						->get();;

		$data = [
			'applications' => $applications
		];
		
		return Excel::download(new MsrReportsExport($data), 'msr-reports.xlsx');

	}

	public function invoice(Request $request){

		$title = "Invoice";
		return view('invoice.create', compact('title', 'request'));
	}

	public function invoiceDownload(Request $request){

		$data = $request->all();
	    
		$pdf = PDF::loadView('pdf.invoice', $data);
        return $pdf->stream('invoice.pdf');

	}

}
