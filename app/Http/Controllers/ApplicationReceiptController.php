<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InquiryForm;
use App\Models\RegistrationForm;
use App\Models\CourseMaster;
use App\Models\CenterMaster;
use App\Models\ApplicationForm;
use App\Models\ApplicationFormEmi;
use App\Models\User;
use Razorpay\Api\Api;
use Auth;
use Illuminate\Http\Request;
use Session;
use Validator;
use PDF;
use Carbon\Carbon;
use DB;
use Helper;

class ApplicationReceiptController extends Controller {

	/**

	 * Display a listing of the resource.

	 *

	 * @return \Illuminate\Http\Response

	 */

	public function all(Request $request) {

		$title = 'Receipt';

		$corses = CourseMaster::get();
		$centers = CenterMaster::get();

		$applicant = ApplicationForm::get();

		$user_app_id = Helper::getApplicationIdByEmiId(Auth::guard()->user()->email);
 	
		// dd($user_app_id);
		$applications_emi = ApplicationFormEmi::where(function ($q) use ($request,$user_app_id) {
								$q->whereHas('application', function ($qu) use ($request) {
				                    $qu->whereNull('deleted_at');
				                });

				                if(Auth::guard()->user()->is_admin != 1 && empty($user_app_id->id)){
					            	// $q->whereHas('regDetails', function ($qu) use ($request) {
					             //        $qu->where('center_name',Auth::guard()->user()->branch_id);
					             //    });

				                	$q->whereHas('application', function ($qu) use ($request) {
						                $qu->whereHas('regDetails', function ($qur) use ($request) {
						                    $qur->where('center_name', Auth::guard()->user()->branch_id);
						                });
						            });
					                
					            }

					            if(!empty($user_app_id->id)){
					            	$q->where('application_id', $user_app_id->id);
					            }

				                if (!empty($request->input('from_date') && !empty($request->input('to_date')))) 
				                {
					                $datef = $request->from_date;
					                $dateto = $request->to_date;
					                $q->whereBetween('emi_dates', [$datef, $dateto]);
					            }
					            if ($request->has('search_by_center') && !empty($request->search_by_center) ){
					        		$q->whereHas('application', function ($qu) use ($request) {
						                $qu->whereHas('regDetails', function ($qur) use ($request) {
						                    $qur->where('center_name', $request->search_by_center);
						                });
						            });
					            }
					            if ($request->has('search_by_app_id') && !empty($request->search_by_app_id) ){
					        		$q->whereHas('application', function ($qu) use ($request) {
						                $qu->where('application_id', $request->search_by_app_id);
						            });
					            }
					            if ($request->has('students_app_id') && !empty($request->students_app_id) ){
					        		$q->whereHas('application', function ($qu) use ($request) {
						                $qu->where('application_id', $request->students_app_id);
						            });
					            }
							})
							->paginate(100)->appends(request()->query());

		
		//dd($applications_emi);
		$centerDetails = CenterMaster::where(function($q) use ($request) {
                            if(Auth::guard()->user()->is_admin != 1){
                                $q->where('id',Auth::guard()->user()->branch_id);
                            }
                        })->get();

		return view('application-receipt.all', compact('title', 'applications_emi','centerDetails','applicant','request', 'user_app_id'));

	}

	public function index(Request $request) {

		$title = 'Pending Receipt';

		$corses = CourseMaster::get();
		$centers = CenterMaster::get();

		$user_app_id = Helper::getApplicationIdByEmiId(Auth::guard()->user()->email);
 	
		// dd($request->from_date);
		$applications_emi = ApplicationFormEmi::where(function ($q) use ($request) {
								$q->whereHas('application', function ($qu) use ($request) {
				                    $qu->whereNull('deleted_at');
				                });

				                if(Auth::guard()->user()->is_admin != 1 && empty($user_app_id->id)){
					            	// $q->whereHas('regDetails', function ($qu) use ($request) {
					             //        $qu->where('center_name',Auth::guard()->user()->branch_id);
					             //    });

				                	$q->whereHas('application', function ($qu) use ($request) {
						                $qu->whereHas('regDetails', function ($qur) use ($request) {
						                    $qur->where('center_name', Auth::guard()->user()->branch_id);
						                });
						            });
					                
					            }

					            if(!empty($user_app_id->id)){
					            	$q->where('application_id', $user_app_id->id);
					            }

				                if (!empty($request->input('from_date') && !empty($request->input('to_date')))) 
				                {
					                $datef = $request->from_date;
					                $dateto = $request->to_date;
					                $q->whereBetween('emi_dates', [$datef, $dateto]);
					            }
					            if ($request->has('search_by_center') && !empty($request->search_by_center) ){
					        		$q->whereHas('application', function ($qu) use ($request) {
						                $qu->whereHas('regDetails', function ($qur) use ($request) {
						                    $qur->where('center_name', $request->search_by_center);
						                });
						            });
					            }
					            if ($request->has('search_by_app_id') && !empty($request->search_by_app_id) ){
					        		$q->whereHas('application', function ($qu) use ($request) {
						                $qu->where('application_id', $request->search_by_app_id);
						            });
					            }
							})
							->where(function ($qp) use ($request) {
								$qp->where('payment_type','');
								$qp->orWhereNull('payment_type');
							})							
							->orderBy('emi_dates','asc')
							->orderBy('id', 'asc')
							->paginate(25)->appends(request()->query());

		
		//dd($applications_emi);
		$centerDetails = CenterMaster::where(function($q) use ($request) {
                            if(Auth::guard()->user()->is_admin != 1){
                                $q->where('id',Auth::guard()->user()->branch_id);
                            }
                        })->get();

		return view('application-receipt.index', compact('title', 'applications_emi','centerDetails','request','user_app_id'));

	}

	public function cleared(Request $request) {

		$title = 'Cleared Receipt';

		$corses = CourseMaster::get();
		$centers = CenterMaster::get();

		$user_app_id = Helper::getApplicationIdByEmiId(Auth::guard()->user()->email);

		$applications_emi = ApplicationFormEmi::where(function ($q) use ($request) {
								$q->whereHas('application', function ($qu) use ($request) {
				                    $qu->whereNull('deleted_at');
				                });

				                if(Auth::guard()->user()->is_admin != 1 && empty($user_app_id->id)){
				                	
				                	$q->whereHas('application', function ($qu) use ($request) {
						                $qu->whereHas('regDetails', function ($qur) use ($request) {
						                    $qur->where('center_name', Auth::guard()->user()->branch_id);
						                });
						            });
					                
					            }

					            if(!empty($user_app_id->id)){
					            	$q->where('application_id', $user_app_id->id);
					            }

				                if (!empty($request->input('from_date') && !empty($request->input('to_date')))) {
					                $datef = $request->from_date;
					                $dateto = $request->to_date;
					                $q->whereBetween('update_date', [$datef, $dateto]);
					                
					            }
					            if ($request->has('search_by_center') && !empty($request->search_by_center) ){
					        		$q->whereHas('application', function ($qu) use ($request) {
						                $qu->whereHas('regDetails', function ($qur) use ($request) {
						                    $qur->where('center_name', $request->search_by_center);
						                });
						            });
					            }
					            if ($request->has('search_by_app_id') && !empty($request->search_by_app_id) ){
					        		$q->whereHas('application', function ($qu) use ($request) {
						                $qu->where('application_id', $request->search_by_app_id);
						            });
					            }
							})
							->whereHas('application', function ($qu) use ($request) {
				                    $qu->whereNull('deleted_at');
				                })
							// ->where('payment_type','!=','')
							// ->whereNotNull('payment_type')
							->where(function ($qp) use ($request) {
								$qp->where('payment_type','!=','');
								$qp->orWhereNotNull('payment_type');
							})
							->whereNotNull('updated_by')
							->orderBy('update_date','desc')
				            // ->orderBy('created_at','desc')
							// ->orderBy('id', 'desc')
							->paginate(25)->appends(request()->query());
		
		$centerDetails = CenterMaster::where(function($q) use ($request) {
                            if(Auth::guard()->user()->is_admin != 1){
                                $q->where('id',Auth::guard()->user()->branch_id);
                            }
                        })->get();
		
		return view('application-receipt.cleared', compact('title', 'applications_emi','centerDetails','request','user_app_id'));

	}

	/**

	 * Show the form for editing the specified resource.

	 *

	 * @param  int  $id

	 * @return \Illuminate\Http\Response

	 */

	public function edit(Request $request,$id) {
		$title = 'Receipt';
		$corses = CourseMaster::get();
		$centers = CenterMaster::get();
		$apllication = ApplicationForm::find($id);		
		$registrations = RegistrationForm::find($apllication->reg_id);
		return view('application-receipt.edit', compact('request','title', 'corses','centers','apllication','registrations'));
	}



	// public function updateOld(Request $request, $id) {
	// 	//dd($request->all());
		
	// 	try {

	// 		$input = $request->all();

	// 		$api = new Api('rzp_test_LdMBBcASc883Xj', 'hBwVtJYHA6se3WFXwDvMyiwr');
  
	//         $payment = $api->payment->fetch($input['razorpay_payment_id']);
	  
	//         if(count($input)  && !empty($input['razorpay_payment_id'])) {
	//         	//\DB::beginTransaction();
	//             try {
	//                 $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount'=>$request->ammount));

	//                 for($i=0; $i<count($request->ids); $i++){

	// 					$emi_data = DB::table('application_form_emi')->where('id', $request->ids[$i])
	// 						->update([
	// 							'payment_type' => 'Online',
	// 							'cheque_no' => $request->razorpay_payment_id,
	// 							'extra_amount' => $request->extra_amount,
	// 							'extra_amount_add' => $request->extra_amount_add,
	// 							'updated_by' => Auth::guard()->user()->id,
	// 							'update_date' => date("Y-m-d"),
	// 						]);


	// 					if(!empty($request->emi_payment_type[$i])){
	// 						$reg_details = Helper::getRegPhoneNumberByEmiId($request->ids[$i]);
	// 						$your_phone = $reg_details->mobile_no;
	// 						//dd($your_phone);
	// 						// SMS
	// 			            $url = "https://sms11.infonetservices.in/api/api_http.php";
				            
	// 			              $param = array(
	// 			                  'username' => 'imagic',
	// 			                  'password' => 'imagic122',
	// 			                  'senderid' => 'iMAGIC',
	// 			                  'text' => 'Dear '.$reg_details->mr_mrs.' '.$reg_details->first_name.' '.$reg_details->mid_name.' '.$reg_details->last_name.', we have received your IMAGIC course fee. Thanks for your payment from IMAGIC.',
	// 			                  'route' => 'Informative',
	// 			                  'type' => 'text',
	// 			                  'datetime' => '2021-02-27 16:30:19',
	// 			                  'to' => $your_phone,
	// 			              );

	// 			              $post = http_build_query($param, '', '&');

	// 			              $ch = curl_init();

	// 			              curl_setopt($ch, CURLOPT_URL, $url);
	// 			              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// 			              curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	// 			              curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	// 			              curl_setopt($ch, CURLOPT_HTTPHEADER, array("Connection: close"));

	// 			              $result = curl_exec($ch);

	// 			              if(curl_errno($ch)) {
	// 			                  $result = "cURL ERROR: " . curl_errno($ch) . " " . curl_error($ch);
	// 			              } else {
	// 			                  $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	// 			                  switch($returnCode) {
	// 			                      case 200 :
	// 			                          break;
	// 			                      default :
	// 			                          $result = "HTTP ERROR: " . $returnCode;
	// 			                  }
	// 			              }
	// 			              // dd($result);
	// 			              curl_close($ch);

	// 					}
	// 				}
					
	  
	//             } catch (Exception $e) {
	//             	//\Db::rollback();
	//                 return  $e->getMessage();
	//                 Session::put('error',$e->getMessage());
	//                 return redirect()->back();
	//             }

	            

	//         }
	          
	//         // Session::put('success', 'Payment successful');
	//         // return redirect()->back();

			
				

	// 		// \DB::commit();
	// 		Session::flash('msg', 'Payment successful & application receipt updated sucessfully');
	// 		return redirect()->route('application-receipt.index');
	// 	} catch (\Exception $e) {
			
	// 		Session::flash('error', $e->getMessage());
	// 		return redirect()->back();

	// 	}


	// }

	public function update(Request $request, $id) {
		//dd($request->all());
		
		try {

			$input = $request->all();

			$api = new Api('rzp_live_G4haDmHJPHcZJE', 'ITpXo4MV4x6GyK');

			// $api = new Api('rzp_test_F8PJXr8T0a9huk', 'uIwWjSUQyZCz1mhqU5CkTj3T');
  
	        $payment = $api->payment->fetch($input['razorpay_payment_id']);
	  		// dd($payment);
	        if(count($input)  && !empty($input['razorpay_payment_id'])) {
	        	//\DB::beginTransaction();
	            try {
	                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount'=>$request->ammount));

	                for($i=0; $i<count($request->ids); $i++){

						$emi_data = DB::table('application_form_emi')->where('id', $request->ids[$i])
							->update([
								'payment_type' => 'Online',
								'cheque_no' => $request->razorpay_payment_id,
								'extra_amount' => $request->extra_amount,
								'extra_amount_add' => $request->extra_amount_add,
								'updated_by' => Auth::guard()->user()->id,
								'update_date' => date("Y-m-d"),
							]);

						$user = User::find(Auth::guard()->user()->id);
						$center_detials = CenterMaster::find($user->branch_id);
									         
			            $use_amount = $center_detials->use_amount + ($request->ammount/100);
					
    					$center_amount = $center_detials->center_amount - ($request->ammount/100);
                		
                		$reg_details = Helper::getRegPhoneNumberByEmiId($request->ids[$i]);

    		            $course_update = CenterMaster::where('id', $reg_details->center_name)
    		                ->update([
    		                'center_amount' => $center_amount,
    		                'use_amount' => $use_amount
    		            ]);

				// 		if(!empty($request->emi_payment_type[$i])){
				// 			$reg_details = Helper::getRegPhoneNumberByEmiId($request->ids[$i]);
				// 			$your_phone = $reg_details->mobile_no;
				// 			//dd($your_phone);
				// 			// SMS
				//             $url = "https://sms11.infonetservices.in/api/api_http.php";
				            
				//               $param = array(
				//                   'username' => 'imagic',
				//                   'password' => 'imagic122',
				//                   'senderid' => 'iMAGIC',
				//                   'text' => 'Dear '.$reg_details->mr_mrs.' '.$reg_details->first_name.' '.$reg_details->mid_name.' '.$reg_details->last_name.', we have received your IMAGIC course fee. Thanks for your payment from IMAGIC.',
				//                   'route' => 'Informative',
				//                   'type' => 'text',
				//                   'datetime' => '2021-02-27 16:30:19',
				//                   'to' => $your_phone,
				//               );

				//               $post = http_build_query($param, '', '&');

				//               $ch = curl_init();

				//               curl_setopt($ch, CURLOPT_URL, $url);
				//               curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				//               curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				//               curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
				//               curl_setopt($ch, CURLOPT_HTTPHEADER, array("Connection: close"));

				//               $result = curl_exec($ch);

				//               if(curl_errno($ch)) {
				//                   $result = "cURL ERROR: " . curl_errno($ch) . " " . curl_error($ch);
				//               } else {
				//                   $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
				//                   switch($returnCode) {
				//                       case 200 :
				//                           break;
				//                       default :
				//                           $result = "HTTP ERROR: " . $returnCode;
				//                   }
				//               }
				//               // dd($result);
				//               curl_close($ch);

				// 		}
					}

					
					Session::flash('msg', 'Payment successful & application receipt updated sucessfully');
					return redirect()->route('application-receipt.all');
	  
	            } catch (Exception $e) {
	            	//\Db::rollback();
	                return  $e->getMessage();
	                Session::put('error',$e->getMessage());
	                return redirect()->back();
	            }

	            

	        }
	          
	        // Session::put('success', 'Payment successful');
	        // return redirect()->back();

			
				

			// \DB::commit();
			Session::flash('msg', 'Payment successful & application receipt updated sucessfully');
			return redirect()->route('application-receipt.all');
			
		} catch (\Exception $e) {
			
			Session::flash('error', $e->getMessage());
			return redirect()->back();

		}


	}

	public function adminUpdate(Request $request, $id) {
		//dd($request->all());
    	//\DB::beginTransaction();
        try {

            for($i=0; $i<count($request->ids); $i++){
                
                $reg_details = Helper::getRegPhoneNumberByEmiId($request->ids[$i]);
            	$or_amount = $request->ammount/100;
    			$center_detials = CenterMaster::find($reg_details->center_name);
    			$use_amount = $center_detials->use_amount + ($request->ammount/100);
    			$center_amount = $center_detials->center_amount;
                
                
    			if($center_amount >= $or_amount){
    			    
    				$emi_data = DB::table('application_form_emi')->where('id', $request->ids[$i])
    					->update([
    						'payment_type' => !empty($request->emi_payment_type[0]) ? $request->emi_payment_type[0] : '',
    						'cheque_no' => !empty($request->emi_payment_type[0]) ? $request->cheque_no[0] : '',
    						'extra_amount' => $request->extra_amount_add == 'on' ? $request->extra_amount : '',
    						'extra_amount_add' => $request->extra_amount_add,
    						'updated_by' => !empty($request->emi_payment_type[0]) ? Auth::guard()->user()->id : '',
    						'update_date' => !empty($request->emi_payment_type[0]) ? date("Y-m-d") : '',
    					]);
    
    				if(!empty($request->emi_payment_type[0]) && ($request->emi_payment_type[0] == 'Cash' || $request->emi_payment_type[0] == 'Cheque' || $request->emi_payment_type[0] == 'Online')){
    					$reg_details = Helper::getRegPhoneNumberByEmiId($request->ids[$i]);
    					
    					$center_detials = CenterMaster::find($reg_details->center_name);
    
    					$use_amount = $center_detials->use_amount + ($request->ammount/100);
    					
    					$center_amount = $center_detials->center_amount - ($request->ammount/100);
                			
    		            $course_update = CenterMaster::where('id', $reg_details->center_name)
    		                ->update([
    		                'center_amount' => $center_amount,
    		                'use_amount' => $use_amount
    		            ]);
    		            
    		            
    
    				// 	if(!empty($request->emi_payment_type[$i])){
    				// 		$reg_details = Helper::getRegPhoneNumberByEmiId($request->ids[$i]);
    				// 		$your_phone = $reg_details->mobile_no;
    				// 		//dd($your_phone);
    				// 		// SMS
    			 //           $url = "https://sms11.infonetservices.in/api/api_http.php";
    			            
    			 //             $param = array(
    			 //                 'username' => 'imagic',
    			 //                 'password' => 'imagic122',
    			 //                 'senderid' => 'iMAGIC',
    			 //                 'text' => 'Dear '.$reg_details->mr_mrs.' '.$reg_details->first_name.' '.$reg_details->mid_name.' '.$reg_details->last_name.', we have received your IMAGIC course fee. Thanks for your payment from IMAGIC.',
    			 //                 'route' => 'Informative',
    			 //                 'type' => 'text',
    			 //                 'datetime' => '2021-02-27 16:30:19',
    			 //                 'to' => $your_phone,
    			 //             );
    
    			 //             $post = http_build_query($param, '', '&');
    
    			 //             $ch = curl_init();
    
    			 //             curl_setopt($ch, CURLOPT_URL, $url);
    			 //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    			 //             curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    			 //             curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    			 //             curl_setopt($ch, CURLOPT_HTTPHEADER, array("Connection: close"));
    
    			 //             $result = curl_exec($ch);
    
    			 //             if(curl_errno($ch)) {
    			 //                 $result = "cURL ERROR: " . curl_errno($ch) . " " . curl_error($ch);
    			 //             } else {
    			 //                 $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    			 //                 switch($returnCode) {
    			 //                     case 200 :
    			 //                         break;
    			 //                     default :
    			 //                         $result = "HTTP ERROR: " . $returnCode;
    			 //                 }
    			 //             }
    			 //             // dd($result);
    			 //             curl_close($ch);
    
    				// 	}
    				}
					Session::flash('msg', 'Payment successful & application receipt updated sucessfully');
					return redirect()->route('application-receipt.all');
                }else{
					Session::flash('error', "You didn't have credit, please contact to the main office.");
					return redirect()->back();
				}       
				
			}
			
// 			Session::flash('msg', 'Application receipt updated sucessfully');
// 			return redirect()->route('application-receipt.index');

        } catch (Exception $e) {
        	//\Db::rollback();
            return  $e->getMessage();
            Session::put('error',$e->getMessage());
            return redirect()->back();
        }


	}

	public function applicationEmiReceiptPdf($id)
	{
		$emi_details = ApplicationFormEmi::find($id);
		$application = ApplicationForm::find($emi_details->application_id);
		$registrations = RegistrationForm::find($application->reg_id);
		$data = [
			'apllication' => $application,
			'registrations' => $registrations,
			'emi_details' => $emi_details
		];

		$pdf = PDF::loadView('pdf.applicationemireceipt', $data);
		return $pdf->stream('apllication-emi-receipt.pdf');
	}

	public function smsTest(){

		$your_phone = '8350088681';
		$url = "https://sms11.infonetservices.in/api/api_http.php";
		            
		  $param = array(
		      'username' => 'imagic',
		      'password' => 'imagic122',
		      'senderid' => 'iMAGIC',
		      // 'text' => 'Dear surajit, we have received your IMAGIC course fee. Thanks for your payment from IMAGIC.',
		      'text' => 'Dear surajit, your abcd IMAGIC course fee is due on 22-07-2022. Please pay on/before due date to avoid late fee. Ignore if already paid.',
		      'route' => 'Informative',
		      'type' => 'text',
		      'datetime' => '2021-02-27 16:30:19',
		      'to' => $your_phone,
		  );

		  $post = http_build_query($param, '', '&');

		  $ch = curl_init();

		  curl_setopt($ch, CURLOPT_URL, $url);
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Connection: close"));

		  $result = curl_exec($ch);

		  if(curl_errno($ch)) {
		      $result = "cURL ERROR: " . curl_errno($ch) . " " . curl_error($ch);
		  } else {
		      $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		      switch($returnCode) {
		          case 200 :
		              break;
		          default :
		              $result = "HTTP ERROR: " . $returnCode;
		      }
		  }
		  
		  curl_close($ch);
		
		dd($result);
		//dd($app_details);

		
	}

	public function cronSMS(){
		// SMS
        $next_date = Carbon::today()->addDays(1);
        $next_emis = ApplicationFormEmi::whereDate('emi_dates', $next_date)->get();

        foreach ($next_emis as $emi) {
            $app_details = Helper::getApplicationDetails($emi->application_id);
            $reg_details = Helper::getRegDetails($app_details->reg_id);
            $course_details = Helper::getCourseName($app_details->course);

            $your_phone = $reg_details->mobile_no;
            $url = "https://sms11.infonetservices.in/api/api_http.php";
                        
              $param = array(
                  'username' => 'imagic',
                  'password' => 'imagic122',
                  'senderid' => 'iMAGIC',
                  'text' => 'Dear '.$reg_details->first_name.', your '.$course_details.' IMAGIC course fee is due on '.$emi->emi_dates.'. Please pay on/before due date to avoid late fee. Ignore if already paid.',
                  'route' => 'Informative',
                  'type' => 'text',
                  'datetime' => '2021-02-27 16:30:19',
                  'to' => $your_phone,
              );

              $post = http_build_query($param, '', '&');

              $ch = curl_init();

              curl_setopt($ch, CURLOPT_URL, $url);
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              curl_setopt($ch, CURLOPT_TIMEOUT, 30);
              curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
              curl_setopt($ch, CURLOPT_HTTPHEADER, array("Connection: close"));

              $result = curl_exec($ch);

              if(curl_errno($ch)) {
                  $result = "cURL ERROR: " . curl_errno($ch) . " " . curl_error($ch);
              } else {
                  $returnCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                  switch($returnCode) {
                      case 200 :
                          break;
                      default :
                          $result = "HTTP ERROR: " . $returnCode;
                  }
              }
              curl_close($ch);
        }
	}

	public function destroy($id) {

		try {
			$application = ApplicationFormEmi::find($id);
			$application->delete();
			return response()->json(['status' => 200, 'status_text' => 'Application EMI data successfuly deleted']);
		} catch (\Exception $e) {
			\DB::rollback();
			return response()->json(['status' => 500, 'status_text' => $e->getMessage()]);

		}

	}
	
	public function extraAmountUpdate(Request $request, $id) {

		try {
			$emi_data = DB::table('application_form_emi')->where('id', $id)
    					->update([
    						'extra_amount_add' => !empty($request->extra_amount_add) ? $request->extra_amount_add : '',
    						'updated_by' => !empty($request->emi_payment_type[0]) ? Auth::guard()->user()->id : '',
    						'update_date' => !empty($request->emi_payment_type[0]) ? date("Y-m-d") : '',
    					]);
			return response()->json(['status' => 200, 'status_text' => 'Application EMI data successfuly updated']);
		} catch (\Exception $e) {
			\DB::rollback();
			return response()->json(['status' => 500, 'status_text' => $e->getMessage()]);

		}

	}


}
