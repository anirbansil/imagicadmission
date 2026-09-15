<?php



use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\UserManageController;

use App\Http\Controllers\RoleController;

use App\Http\Controllers\Auth\ForgotPasswordController;

use App\Http\Controllers\FacultyMasterController;

use App\Http\Controllers\CourseMasterController;

use App\Http\Controllers\InquiryFormController;

use App\Http\Controllers\QuickInquiryFormController;

use App\Http\Controllers\RegistrationFormController;

use App\Http\Controllers\ApplicationFormController;

use App\Http\Controllers\ApplicationReceiptController;

use App\Http\Controllers\ReportsController;

use App\Http\Controllers\StudentsController;

use App\Http\Controllers\CenterMasterController;

use App\Http\Controllers\TutorialVideoController;



/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/

Route::get('/home', function () {
    return redirect('/dashboard');
});

Route::get('/', function () {

    return view('welcome');

});



Route::get('/', [AuthController::class, 'showLogin'])->name('login');

Route::post('login', [AuthController::class, 'storeLogin'])->name('login.store');



// Reset Password

Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('forgot-password');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('forgot-password.store');

Route::get('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('forgot-password.reset');

Route::post('/reset-password-store', [ForgotPasswordController::class, 'resetPasswordStore'])->name('forgot-password.resetstore');
Route::get('/registration' ,[RegistrationFormController::class,'OutsiderRegistrationForm'])->name('registration.outsider');
Route::post('/registration/store' ,[RegistrationFormController::class,'OutsiderRegistrationStore'])->name('registration.outsider.store');
Route::post('/registration/verification' ,[RegistrationFormController::class,'OtpVerification'])->name('registration.outsider.verification');
Route::get('/registration/otp_verification/{vefication_token}' ,[RegistrationFormController::class,'RegistrationVerification'])->name('registration.verification');


Route::group(['middleware' => 'auth'], function () {



	Route::get('/logout', [AuthController::class, 'logout'])->name('logout');



	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

	Route::get('/placements', [DashboardController::class, 'placement'])->name('placement');

	Route::get('/jobs', [DashboardController::class, 'jobs'])->name('jobs');


	// User Management

	Route::get('/user-manage', [UserManageController::class, 'index'])->name('user-manage.index')->middleware('roles:user-list');

	Route::get('/user-manage/creates', [UserManageController::class, 'create'])->name('user-manage.create')->middleware('roles:user-create');

	Route::post('/user-manage', [UserManageController::class, 'store'])->name('user-manage.store')->middleware('roles:user-create');

	Route::get('/user-manage/{id}', [UserManageController::class, 'edit'])->name('user-manage.edit')->middleware('roles:user-edit');

	Route::post('/user-manage/{id}', [UserManageController::class, 'update'])->name('user-manage.update')->middleware('roles:user-edit');

	Route::delete('/user-manage/{id}', [UserManageController::class, 'destroy'])->name('user-manage.delete')->middleware('roles:user-delete');



	// // For Profile

	Route::get('/profile', [UserManageController::class, 'profile'])->name('user-manage.user-profile');

	Route::PUT('/profile-update', [UserManageController::class, 'profile_update'])->name('user-manage.user-profile-update');



	// User Role

	Route::get('/role', [RoleController::class, 'index'])->name('role.index')->middleware('roles:role-list');

	Route::get('/role/create', [RoleController::class, 'create'])->name('role.create')->middleware('roles:role-create');

	Route::post('/role', [RoleController::class, 'store'])->name('role.store')->middleware('roles:role-create');

	Route::get('/role/{id}', [RoleController::class, 'edit'])->name('role.edit')->middleware('roles:role-edit');

	Route::put('/role/{id}', [RoleController::class, 'update'])->name('role.update')->middleware('roles:role-edit');

	Route::delete('/role/{id}', [RoleController::class, 'destroy'])->name('role.delete')->middleware('roles:role-delete');

	// Faculty Master

	Route::get('/faculty-master', [FacultyMasterController::class, 'index'])->name('faculty-master.index');

	Route::get('/faculty-master/creates', [FacultyMasterController::class, 'create'])->name('faculty-master.create');

	Route::post('/faculty-master', [FacultyMasterController::class, 'store'])->name('faculty-master.store');

	Route::get('/faculty-master/{id}', [FacultyMasterController::class, 'edit'])->name('faculty-master.edit');

	Route::post('/faculty-master/{id}', [FacultyMasterController::class, 'update'])->name('faculty-master.update');

	Route::delete('/faculty-master/{id}', [FacultyMasterController::class, 'destroy'])->name('faculty-master.delete');

	// Course Master

	Route::get('/course-master', [CourseMasterController::class, 'index'])->name('course-master.index');

	Route::get('/course-master/creates', [CourseMasterController::class, 'create'])->name('course-master.create');

	Route::post('/course-master', [CourseMasterController::class, 'store'])->name('course-master.store');

	Route::get('/course-master/{id}', [CourseMasterController::class, 'edit'])->name('course-master.edit');

	Route::post('/course-master/{id}', [CourseMasterController::class, 'update'])->name('course-master.update');

	Route::delete('/course-master/{id}', [CourseMasterController::class, 'destroy'])->name('course-master.delete');
	Route::get('/course-master/addsubmodule/{id}', [CourseMasterController::class, 'addSubmodule'])->name('course-master.submodule');
	Route::post('/course-master/addsubmodule/{id}/store', [CourseMasterController::class, 'StoreSubModule'])->name('course-master.submodule.store');
	// Center Master

	Route::get('/center-master', [CenterMasterController::class, 'index'])->name('center-master.index');

	Route::get('/center-master/creates', [CenterMasterController::class, 'create'])->name('center-master.create');

	Route::post('/center-master', [CenterMasterController::class, 'store'])->name('center-master.store');

	Route::get('/center-master/{id}', [CenterMasterController::class, 'edit'])->name('center-master.edit');

	Route::post('/center-master/{id}', [CenterMasterController::class, 'update'])->name('center-master.update');

	Route::delete('/center-master/{id}', [CenterMasterController::class, 'destroy'])->name('center-master.delete');

	// Seat Booking

	Route::get('/seat', [SeatBookingController::class, 'index'])->name('seat.index');

	Route::get('/seat/creates', [SeatBookingController::class, 'create'])->name('seat.create');

	Route::post('/seat', [SeatBookingController::class, 'store'])->name('seat.store');

	Route::get('/seat/{id}', [SeatBookingController::class, 'edit'])->name('seat.edit');

	Route::post('/seat/{id}', [SeatBookingController::class, 'update'])->name('seat.update');

	Route::delete('/seat/{id}', [SeatBookingController::class, 'destroy'])->name('seat.delete');

	Route::get('/seat-export', [SeatBookingController::class, 'seatExport'])->name('seat-export');


	// Inquiry Form

	Route::get('/inquiry-form', [InquiryFormController::class, 'index'])->name('inquiry-form.index');

	Route::get('/inquiry-form/creates', [InquiryFormController::class, 'create'])->name('inquiry-form.create');

	Route::post('/inquiry-form', [InquiryFormController::class, 'store'])->name('inquiry-form.store');

	Route::get('/inquiry-form/{id}', [InquiryFormController::class, 'edit'])->name('inquiry-form.edit');

	Route::post('/inquiry-form/{id}', [InquiryFormController::class, 'update'])->name('inquiry-form.update');

	Route::delete('/inquiry-form/{id}', [InquiryFormController::class, 'destroy'])->name('inquiry-form.delete');

	Route::get('/inquiry-export', [InquiryFormController::class, 'inquiryExport'])->name('inquiry-export');

	// Registration Form

	Route::get('/registration-form', [RegistrationFormController::class, 'index'])->name('registration-form.index');

	Route::get('/registration-form/creates', [registrationFormController::class, 'create'])->name('registration-form.create');

	Route::post('/registration-form', [registrationFormController::class, 'store'])->name('registration-form.store');

	Route::get('/registration-form/{id}', [registrationFormController::class, 'edit'])->name('registration-form.edit');

	Route::post('/registration-form/{id}', [registrationFormController::class, 'update'])->name('registration-form.update');

	Route::delete('/registration-form/{id}', [registrationFormController::class, 'destroy'])->name('registration-form.delete');



	// Application Form

	Route::get('/application-form', [ApplicationFormController::class, 'index'])->name('application-form.index');

	Route::get('/application-form/creates', [ApplicationFormController::class, 'create'])->name('application-form.create');

	Route::post('/application-form', [ApplicationFormController::class, 'store'])->name('application-form.store');

	Route::get('/application-form/{id}', [ApplicationFormController::class, 'edit'])->name('application-form.edit');

	Route::post('/application-form/{id}', [ApplicationFormController::class, 'update'])->name('application-form.update');

	Route::delete('/application-form/{id}', [ApplicationFormController::class, 'destroy'])->name('application-form.delete');

	// Application Reciept
	Route::get('/all-receipt', [ApplicationReceiptController::class, 'all'])->name('application-receipt.all');
	Route::get('/pending-receipt', [ApplicationReceiptController::class, 'index'])->name('application-receipt.index');
	Route::get('/cleared-receipt', [ApplicationReceiptController::class, 'cleared'])->name('application-receipt.cleared');
	Route::get('/edit-receipt/{id}', [ApplicationReceiptController::class, 'edit'])->name('application-receipt.edit');
	Route::post('/update-receipt/{id}', [ApplicationReceiptController::class, 'update'])->name('application-receipt.update');
	Route::post('/admin-update-receipt/{id}', [ApplicationReceiptController::class, 'adminUpdate'])->name('application-receipt.adminupdate');
	Route::delete('/application-receipt/{id}', [ApplicationReceiptController::class, 'destroy'])->name('application-receipt.delete');
	
	Route::get('/extra-amount-add/{id}', [ApplicationReceiptController::class, 'extraAmountUpdate']);

	// Reports
	Route::get('/dcr', [ReportsController::class, 'dcr'])->name('reports.dcr');
	Route::get('/dcr-export', [ReportsController::class, 'dcrExport'])->name('dcr-export');

	Route::get('/mcr', [ReportsController::class, 'mcr'])->name('reports.mcr');
	Route::get('/mcr-export', [ReportsController::class, 'mcrExport'])->name('mcr-export');

	Route::get('/dsr', [ReportsController::class, 'dsr'])->name('reports.dsr');
	Route::get('/dsr-export', [ReportsController::class, 'dsrExport'])->name('dsr-export');

	Route::get('/msr', [ReportsController::class, 'msr'])->name('reports.msr');
	Route::get('/msr-export', [ReportsController::class, 'msrExport'])->name('msr-export');

	// Students List

	Route::get('/students', [StudentsController::class, 'index'])->name('students-list.index');

	// Ajax

	Route::get('/course-fee/{id}', [ApplicationFormController::class, 'courseFee']);

	Route::get('/course-emi/{id}', [ApplicationFormController::class, 'courseEmi']);

	Route::get('/course-duration/{id}', [ApplicationFormController::class, 'courseDuration']);

	// PDF
	Route::get('/application-form-pdf/{id}', [ApplicationFormController::class, 'applicationFormPdf'])->name('pdf.applicationform');
	Route::get('/application-receipt-pdf/{id}', [ApplicationFormController::class, 'applicationReceiptPdf'])->name('pdf.applicationreceipt');
	Route::get('/application-faculty-pdf/{id}', [ApplicationFormController::class, 'applicationFacultyPdf'])->name('pdf.applicationfaculty');
	Route::get('/application-emi-receipt-pdf/{id}', [ApplicationReceiptController::class, 'applicationEmiReceiptPdf'])->name('pdf.applicationemireceipt');

	// Tutorial Video

	Route::get('/video-master', [TutorialVideoController::class, 'index'])->name('video-master.index');

	Route::get('/videos', [TutorialVideoController::class, 'show'])->name('video-master.show');

	Route::get('/video-master/creates', [TutorialVideoController::class, 'create'])->name('video-master.create');

	Route::post('/video-master', [TutorialVideoController::class, 'store'])->name('video-master.store');

	Route::get('/video-master/{id}', [TutorialVideoController::class, 'edit'])->name('video-master.edit');

	Route::post('/video-master/{id}', [TutorialVideoController::class, 'update'])->name('video-master.update');

	Route::delete('/video-master/{id}', [TutorialVideoController::class, 'destroy'])->name('video-master.delete');


	Route::get('/sms-test', [ApplicationReceiptController::class, 'smsTest']);
	Route::get('/cron-sms', [ApplicationReceiptController::class, 'cronSMS']);

	Route::get('/student_marks_distribution/list', [StudentsController::class, 'MarksList'])->name('student.marks.index');
	Route::get('/student_marks_distribution/create', [StudentsController::class, 'AddMarks'])->name('student.marks.create');
	Route::post('/student_marks_distribution/add', [StudentsController::class, 'SaveMarks'])->name('student.marks.save');
	Route::get('/module_by_course/{id}', [StudentsController::class, 'ModuleByCourse'])->name('module_by_course');
	Route::get('/sub_module_by_course_module/{id}', [StudentsController::class, 'SubModule'])->name('sub_module_by_course');

	Route::get('/checkin-checkout/{userid}', [DashboardController::class, 'Checkin'])->name('checkin');
	Route::get('/checkout/{id}', [DashboardController::class, 'Checkout'])->name('checkout');
	Route::get('/checkin-checkout-list/', [DashboardController::class, 'CheckinCheckoutList'])->name('checkin-checkout.list');
	Route::get('/users-by-role/{id}', [DashboardController::class, 'UsersByrole']);
	
	Route::get('/outsider_students/', [StudentsController::class, 'Outsiderstudents'])->name('outsider_students');
	Route::get('/student_marks/export', [StudentsController::class, 'StudentMarksExport'])->name('student.marks.export');
	Route::get('/checkin-checkout-list/export', [DashboardController::class, 'CheckinCheckoutListExport'])->name('checkin-checkout.list.export');
	Route::get('/student_progress_report', [StudentsController::class, 'ProgressReport'])->name('student.progress.report');
	Route::delete('/student_marks_distribution/delete/{id}', [StudentsController::class, 'StudentDestory'])->name('student.progress.delete');
	Route::get('/student_marks_distribution/edit/{id}', [StudentsController::class, 'EditStudentMarks'])->name('student.mark.edit');
	Route::put('/student_marks_distribution/{id}', [StudentsController::class, 'UpdateStudentMarks'])->name('student.marks.update');
	//make student Login 
    // Route::get('/student-login/', [DashboardController::class, 'studentlogin'])->name('student-login');

	// Invoice 
	Route::get('/invoice', [ReportsController::class, 'invoice'])->name('invoice');
	Route::post('/invoice-data', [ReportsController::class, 'invoiceDownload'])->name('invoice.download');

	// Inquiry Form

	Route::get('/quick-inquiry-form', [QuickInquiryFormController::class, 'index'])->name('quick-inquiry-form.index');

	Route::get('/quick-inquiry-form/creates', [QuickInquiryFormController::class, 'create'])->name('quick-inquiry-form.create');

	Route::post('/quick-inquiry-form', [QuickInquiryFormController::class, 'store'])->name('quick-inquiry-form.store');

	Route::get('/quick-inquiry-form/{id}', [QuickInquiryFormController::class, 'edit'])->name('quick-inquiry-form.edit');

	Route::post('/quick-inquiry-form/{id}', [QuickInquiryFormController::class, 'update'])->name('quick-inquiry-form.update');

	Route::delete('/quick-inquiry-form/{id}', [QuickInquiryFormController::class, 'destroy'])->name('quick-inquiry-form.delete');
	
});