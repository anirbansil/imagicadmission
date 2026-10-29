/********************************************************

 *

 * Custom Javascript code for Flexor Bootstrap theme

 *

 *******************************************************/



 $(document).ready(function() {
     
    // $(function(){
        var dtToday = new Date();
        
        var month = dtToday.getMonth() + 1;
        var day = dtToday.getDate();
        var year = dtToday.getFullYear();
        if(month < 10)
            month = '0' + month.toString();
        if(day < 10)
            day = '0' + day.toString();
        
        var maxDate = year + '-' + month + '-' + day;
    
        // or instead:
        // var maxDate = dtToday.toISOString().substr(0, 10);
    
        //alert(maxDate);
        $('#emi_date').attr('min', maxDate);
    // });

  $('.add_module').click(function(){
    console.log('ok')
    var source = $('.module_row:first')
    moduleclone = source.clone();
    moduleclone.find(':input').val("");
    // moduleclone.find('tbody tr:not(:first)').remove();
    moduleclone.find('.remove_module').show();
    // moduleclone.insertAfter('#module_section');
    $("#module_section").append(moduleclone);
    console.log(moduleclone)
  });

  $('#extra_amount_add').change(function(){
      
      var extra_amount_add = $(this).val();
      var emiid = $('#emiid').val();
      console.log(extra_amount_add+'--'+emiid);
      $.ajax({

        type: 'GET',

        url: $('.logo').attr('href')+"/extra-amount-add/"+emiid,
        
        data:{extra_amount_add:extra_amount_add},

        dataType:'json',

        async: false,

        success: (response) => {

          console.log(response);

          window.location.reload();

        },

        error: () => {
            window.location.reload();

        }

      });
  });


  // $(".datepicker").datepicker({ dateFormat: 'dd/mm/yy' });



  function convertNumberToWords(num) {

    var ones = ["", "One ", "Two ", "Three ", "Four ", "Five ", "Six ", "Seven ", "Eight ", "Nine ", "Ten ", "Eleven ", "Twelve ", "Thirteen ", "Fourteen ", "Fifteen ", "Sixteen ", "Seventeen ", "Eighteen ", "Nineteen "];

    var tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

    if ((num = num.toString()).length > 9) return "Overflow: Maximum 9 digits supported";

    n = ("000000000" + num).substr(-9).match(/^(\d{2})(\d{2})(\d{2})(\d{1})(\d{2})$/);

    if (!n) return;

    var str = "";

    str += n[1] != 0 ? (ones[Number(n[1])] || tens[n[1][0]] + " " + ones[n[1][1]]) + "Crore " : "";

    str += n[2] != 0 ? (ones[Number(n[2])] || tens[n[2][0]] + " " + ones[n[2][1]]) + "Lakh " : "";

    str += n[3] != 0 ? (ones[Number(n[3])] || tens[n[3][0]] + " " + ones[n[3][1]]) + "Thousand " : "";

    str += n[4] != 0 ? (ones[Number(n[4])] || tens[n[4][0]] + " " + ones[n[4][1]]) + "Hundred " : "";

    str += n[5] != 0 ? (str != "" ? "and " : "") + (ones[Number(n[5])] || tens[n[5][0]] + " " + ones[n[5][1]]) : "";

    return str;

  }



  // For appication Form



  $('#application_submit').click(function(){
    
    var course = $('select[name=course]').val();

    var batch_start_date = $('input[name=batch_start_date]').val();

    var course_fee = $('input[name=course_fee]').val();

    var pay_on_admission = $('input[name=pay_on_admission]').val();

    var min_admission_fee = $('input[name=min_admission_fee]').val();

    var batch_end_date = $('input[name=batch_end_date]').val();

    var balance = $('input[name=balance]').val();

    var batch_time = $('input[name=batch_time]').val();

    var admission_fee = $('input[name=admission_fee]').val();

    var course_by_word = $('input[name=course_by_word]').val();

    var net_total = $('input[name=net_total]').val();

    var number_of_emi = $('input[name=number_of_emi]').val();

    var emi = $('input[name=emi]').val();

    var emi_date = $('input[name=emi_date]').val();

    var job_required = $('select[name=job_required]').val();

    var scholarship = $('input[name=scholarship]').val();

    var scholarship_per = $('input[name=scholarship_per]').val();

    

    if (scholarship > 0 && scholarship_per > 0) {

        $get_per = (course_fee * scholarship_per)/100;

        if(scholarship > $get_per){

          $("input[name=scholarship]").css({"border" : "1px solid #f00"});

          $("input[name=scholarship]").focus();

          $("#sc_error").html("(Scholarship limit exceeded)");

          return false;

        }else{

          $("select[name=course]").css({"border": "1px solid #d2cccc"});

          $("input[name=scholarship]").css({"border" : "2px solid #eca107"});

          $("#sc_error").html("");

        }
        

    }

    if (course == "") {

        $("select[name=course]").css({"border" : "1px solid #f00"});

        $("select[name=course]").focus();

        return false;

    }else{

        $("select[name=course]").css({"border": "1px solid #d2cccc"});

    }



    if (batch_start_date == "") {

        $("input[name=batch_start_date]").css({"border" : "1px solid #f00"});

        $("input[name=batch_start_date]").focus();

        return false;

    }else{

        $("input[name=batch_start_date]").css({"border": "1px solid #d2cccc"});

    }

    if (batch_end_date == "") {

        $("input[name=batch_end_date]").css({"border" : "1px solid #f00"});

        $("input[name=batch_end_date]").focus();

        return false;

    }else{

        $("input[name=batch_end_date]").css({"border": "1px solid #d2cccc"});

    }

    if (course_fee == "" || course_fee == 0) {

        $("input[name=course_fee]").css({"border" : "1px solid #f00"});

        $("input[name=course_fee]").focus();

        return false;

    }else{

        $("input[name=course_fee]").css({"border": "1px solid #d2cccc"});

    }

    if (pay_on_admission == "" || pay_on_admission == 0) {

        $("input[name=pay_on_admission]").css({"border" : "1px solid #f00"});

        $("input[name=pay_on_admission]").focus();

        return false;

    }else if(pay_on_admission < min_admission_fee){
      $("input[name=pay_on_admission]").css({"border" : "1px solid #f00"});
      $("input[name=pay_on_admission]").focus();
      $('.admission_fee_error').html('Please add amount greater then '+min_admission_fee);
      return false;
    }else{
        $('.admission_fee_error').html('');
        $("input[name=pay_on_admission]").css({"border": "1px solid #d2cccc"});

    }

    // if(pay_on_admission < min_admission_fee){
    //   $("input[name=pay_on_admission]").css({"border" : "1px solid #f00"});
    //   $("input[name=pay_on_admission]").focus();
    //   $('.admission_fee_error').html('Please add amount greater then '+min_admission_fee);
    //   return false;
    // }else{
    //   $('.admission_fee_error').html('');
    // }

    if (balance == "" ) {

        $("input[name=balance]").css({"border" : "1px solid #f00"});

        $("input[name=balance]").focus();

        return false;

    }else{

        $("input[name=balance]").css({"border": "1px solid #d2cccc"});

    }

    if (batch_time == "") {

        $("input[name=batch_time]").css({"border" : "1px solid #f00"});

        $("input[name=batch_time]").focus();

        return false;

    }else{

        $("input[name=batch_time]").css({"border": "1px solid #d2cccc"});

    }

    // if (admission_fee == "" || admission_fee == 0) {

    //     $("input[name=admission_fee]").css({"border" : "1px solid #f00"});

    //     $("input[name=admission_fee]").focus();

    //     return false;

    // }else{

    //     $("input[name=admission_fee]").css({"border": "1px solid #d2cccc"});

    // }

    if (course_by_word == "") {

        $("input[name=course_by_word]").css({"border" : "1px solid #f00"});

        $("input[name=course_by_word]").focus();

        return false;

    }else{

        $("input[name=course_by_word]").css({"border": "1px solid #d2cccc"});

    }

    if (net_total == "" || net_total == 0) {

        $("input[name=net_total]").css({"border" : "1px solid #f00"});

        $("input[name=net_total]").focus();

        return false;

    }else{

        $("input[name=net_total]").css({"border": "1px solid #d2cccc"});

    }



    if(net_total != pay_on_admission){

      if (number_of_emi > 0 && emi == 0) {

          $("input[name=emi]").css({"border" : "1px solid #f00"});

          $("input[name=emi]").focus();

          return false;

      }else{

          $("input[name=emi]").css({"border": "1px solid #d2cccc"});

      }

      if (emi_date == "") {

          $("input[name=emi_date]").css({"border" : "1px solid #f00"});

          $("input[name=emi_date]").focus();

          return false;

      }else{

          $("input[name=emi_date]").css({"border": "1px solid #d2cccc"});

      }

    }



    if (job_required == "") {

        $("select[name=job_required]").css({"border" : "1px solid #f00"});

        $("select[name=job_required]").focus();

        return false;

    }else{

        $("select[name=job_required]").css({"border": "1px solid #d2cccc"});

    }

    return true;

    $("form").submit();

  });



  $('#application_course').change(function(){

    var application_course = $(this).val();



    $('#batch_start_date').val('');

    $('#course_fee').val('');

    $('#batch_start_date').val('');

    $('#pay_on_admission').val(0);

    $('#batch_end_date').val('');

    $('#scholarship').val(0);

    $('#balance').val(0);

    $('#batch_time').val('');

    $('#admission_fee').val(0);

    $('#number_of_emi').val(0);

    $('#course_by_word').val('');

    $('#net_total').val(0);

    $('#emi').val(0);

    $('#emi_date').val('');

    $('.five_column_append').html('');



    var is_gst = $('#is_gst').val();

    var total = 0;

    var gst = 0;



    if(application_course > 0){

      var html = '';

      $.ajax({

        type: 'GET',

        url: $('.logo').attr('href')+"/course-fee/"+application_course,

        dataType:'json',

        async: false,

        success: (response) => {

          console.log(response);

          // var data = JSON.parse(response);

          console.log(response.course.fees);

          $('#course_fee').val(response.course.fees);

          $('#number_of_emi').val(response.course.number_of_emi);

          $('#min_admission_fee').val(response.course.min_admission_fee);

          if(response.course.number_of_emi > 0){

              $('#emi').removeAttr('disabled');

              $('#emi_date').removeAttr('disabled');



              for (let i = 1; i <= response.course.number_of_emi; i++) {

                $('.five_column_append').append('<div class="five_column"><div class="form-group"><input type="text" name="sl_no[]" id="sl_no" class="form-control required_field sl_no" value="'+i+'"></div><div class="form-group"><input type="date" name="emi_dates[]" id="emi_dates'+i+'" class="form-control required_field emi_dates" value="" readonly></div><div class="form-group"><select name="emi_payment_type" class="form-control required_field"><option value="">Select</option><option value="Cash">Cash</option><option value="Cheque">Cheque</option></select></div><div class="form-group"><input type="text" name="cheque_no[]" id="cheque_no" class="form-control required_field cheque_no" value=""></div><div class="form-group"><input type="text" name="emi_amounts[]" id="emi_amounts'+i+'" class="form-control required_field emi_amounts" value="" readonly></div></div>');

              }

          }else{

                $('.five_column_append').html('');

                $('#emi').attr('disabled','disabled');

                $('#emi_date').attr('disabled','disabled');

          }



          $('#faculty_id').html('');

          $('#faculty_id').html(response.append_faculties);

          // var words = convertNumberToWords(response);

          // $('#course_by_word').val(words);



          total = response.course.fees;



          if(response.course.gst == 2 && is_gst == 0){

            $('#is_gst').val(0);

            $('#gst_amount').val(0);

          }else if(response.course.gst == 1 && is_gst == 1){

            $('#is_gst').val(1);

            var gst = Math.ceil((response.course.fees * 18)/100);

            $('#gst_amount').val(gst);



            total += gst;

          }else{

            $('#is_gst').val(0);

            $('#gst_amount').val(0);

          }

          $('#balance').val(total);

          $('#net_total').val(Math.ceil(total));

          var words = convertNumberToWords(total);

          $('#course_by_word').val(words);

        },

        error: () => {

        }

      });

    }else{

        $('#course_fee').val('');

    }



  });



  $('#batch_start_date').change(function(){



    var application_course = $('#application_course').val();

    var batch_start_date = $(this).val();

    // console.log(batch_start_date);

    if(application_course > 0){

      var html = '';

      $.ajax({

        type: 'GET',

        url: $('.logo').attr('href')+"/course-duration/"+application_course,

        data:{batch_start_date:batch_start_date},

        async: false,

        success: (response) => {

          console.log(response);

          $('#batch_end_date').val(response);



        },

        error: () => {

        }

      });

    }else{

        $('#batch_start_date').val('');

        $('#batch_start_date_error').html('Please select course first');

    }



  });



  $('#pay_on_admission').change(function(){

    var pay_on_admission = $(this).val();

    var course_fee = $('#course_fee').val();

    var scholarship = $('#scholarship').val();

    var admission_fee = $('#admission_fee').val();

    var min_admission_fee = $('#min_admission_fee').val();

    var is_gst = $('#is_gst').val();



    var gst = 0;



    // $('#number_of_emi').val(0);

    // $('#emi').val(0);

    // $('#emi_date').val('');

    // $('.five_column_append').html('');



    if(pay_on_admission == ''){ pay_on_admission = 0;}

    if(course_fee == ''){ course_fee = 0;}

    if(scholarship == ''){ scholarship = 0;}

    if(admission_fee == ''){ admission_fee = 0;}



    if(is_gst == 1){

      var before_gst = course_fee - scholarship;

      gst = Math.ceil((before_gst * 18)/100);

    }



    var balance = ((parseInt(course_fee) + parseInt(admission_fee) + parseInt(gst)) - scholarship) - parseInt(pay_on_admission);



    $('#balance').val(balance);

    if(pay_on_admission < min_admission_fee){
      $("input[name=pay_on_admission]").css({"border" : "1px solid #f00"});
      $("input[name=pay_on_admission]").focus();
      $('.admission_fee_error').html('Please add amount greater then '+min_admission_fee);
      return false;
    }else{
      $('.admission_fee_error').html('');
    }

  });



  $('#scholarship').change(function(){

    var pay_on_admission = $('#pay_on_admission').val();

    var course_fee = $('#course_fee').val();

    var scholarship = $(this).val();

    var admission_fee = $('#admission_fee').val();

    var is_gst = $('#is_gst').val();



    var gst = 0;



    // $('#number_of_emi').val(0);

    $('#emi').val(0);

    $('#emi_date').val('');

    // $('.five_column_append').html('');



    if(pay_on_admission == ''){ pay_on_admission = 0;}

    if(course_fee == ''){ course_fee = 0;}

    if(scholarship == ''){ scholarship = 0;}

    if(admission_fee == ''){ admission_fee = 0;}



    if(is_gst == 1){

      var before_gst = course_fee - scholarship;

      gst = Math.ceil((before_gst * 18)/100);

      $('#gst_amount').val(gst);

    }



    var balance = ((parseInt(course_fee) + parseInt(admission_fee) + parseInt(gst)) - scholarship) - parseInt(pay_on_admission);



    var net_total = (parseInt(course_fee) - parseInt(scholarship)) + parseInt(gst);



    $('#balance').val(balance);

    $('#net_total').val(net_total);



    var words = convertNumberToWords(net_total);

    $('#course_by_word').val(words);



  });



  $('#admission_fee').change(function(){

    var pay_on_admission = $('#pay_on_admission').val();

    var course_fee = $('#course_fee').val();

    var scholarship = $('#scholarship').val();

    var admission_fee = $(this).val();



    // $('#number_of_emi').val(0);

    $('#emi').val(0);

    $('#emi_date').val('');

    $('.five_column_append').html('');



    if(pay_on_admission == ''){ pay_on_admission = 0;}

    if(course_fee == ''){ course_fee = 0;}

    if(scholarship == ''){ scholarship = 0;}

    if(admission_fee == ''){ admission_fee = 0;}



    var balance = ((parseInt(course_fee) + parseInt(admission_fee)) - scholarship) - parseInt(pay_on_admission);



    var net_total = parseInt(course_fee) - parseInt(scholarship);



    $('#balance').val(balance);

    $('#net_total').val(net_total);



  });







  $('#details_of_payment').hide();

  if($('#number_of_emi').val() <= 0){

    $('#emi').attr('disabled','disabled');

    $('#emi_date').attr('disabled','disabled');

  }





  $('#number_of_emi').change(function(){

    $('#emi').val(0);

    $('#emi_date').val('');

    $('.five_column_append').html('');

    var number_of_emi = $(this).val();

    var emi = $('#emi').val();

    var emi_date = $('#emi_date').val();

    var application_course = $('#application_course').val();



    if(number_of_emi > 0){

        $('#emi').removeAttr('disabled');

        $('#emi_date').removeAttr('disabled');

        // console.log(number_of_emi);

        // for (let i = 1; i <= number_of_emi; i++) {

        //   $('.five_column_append').append('<div class="five_column"><div class="form-group"><input type="text" name="sl_no[]" id="sl_no" class="form-control required_field sl_no" value="'+i+'"></div><div class="form-group"><input type="date" name="emi_dates[]" id="emi_dates'+i+'" class="form-control required_field emi_dates" value="" readonly></div><div class="form-group"><select name="emi_payment_type" class="form-control required_field"><option value="">Select</option><option value="Cash">Cash</option><option value="Cheque">Cheque</option></select></div><div class="form-group"><input type="text" name="cheque_no[]" id="cheque_no" class="form-control required_field cheque_no" value=""></div><div class="form-group"><input type="text" name="emi_amounts[]" id="emi_amounts'+i+'" class="form-control required_field emi_amounts" value=""></div></div>');

        // }



        $.ajax({

          type: 'GET',

          url: $('.logo').attr('href')+"/course-emi/"+application_course,

          // dataType:'json',

          async: false,

          success: (response) => {

            // console.log(jQuery.type( number_of_emi ) +''+ jQuery.type( response ))

            if(parseInt(number_of_emi) <= parseInt(response)){

                $('#emi').removeAttr('disabled');

                $('#emi_date').removeAttr('disabled');

                $(".number_of_emi_error").html('');



                for (let i = 1; i <= number_of_emi; i++) {

                  $('.five_column_append').append('<div class="five_column"><div class="form-group"><input type="text" name="sl_no[]" id="sl_no" class="form-control required_field sl_no" value="'+i+'"></div><div class="form-group"><input type="date" name="emi_dates[]" id="emi_dates'+i+'" class="form-control required_field emi_dates" value="" readonly></div><div class="form-group"><select name="emi_payment_type" class="form-control required_field"><option value="">Select</option><option value="Cash">Cash</option><option value="Cheque">Cheque</option></select></div><div class="form-group"><input type="text" name="cheque_no[]" id="cheque_no" class="form-control required_field cheque_no" value=""></div><div class="form-group"><input type="text" name="emi_amounts[]" id="emi_amounts'+i+'" class="form-control required_field emi_amounts" value=""></div></div>');

                }

            }else{

              $('.five_column_append').html('');

              $('#emi').attr('disabled','disabled');

              $('#emi_date').attr('disabled','disabled');

              $('#number_of_emi').val(0);

              $(".number_of_emi_error").html("Please select number of EMI less then or equal to "+response);

            }





          },

          error: () => {

          }

        });



    }

    // else{

    //       $('.five_column_append').html('');

    //       $('#emi').attr('disabled','disabled');

    //       $('#emi_date').attr('disabled','disabled');

    // }



  });



  $('#emi').change(function(){

    var balance = $('#balance').val();

    var number_of_emi = $('#number_of_emi').val();

    var emi = $(this).val();



    var tem = 0;

    var etem = 0;



    if(emi > 0){

      if(balance > 0){

        $('.error_msg').html('');

        tem = emi*number_of_emi;

        if(tem != balance){

          etem = balance - tem;

        }



        for (let i = 1; i <= number_of_emi; i++) {

          if (i != number_of_emi) {

            $('#emi_amounts'+i).val(emi);

          }else{

            $('#emi_amounts'+i).val(parseInt(emi) + parseInt(etem));

          }

        }

      }else{

        $('.error_msg').html('Please fillup balance amount first');

        $('#emi').val(0);

        $('.emi_amounts').val(0);

      }

    }else{

        $('.emi_amounts').val(0);

    }



  });



  $('#emi_date').change(function(){

    var number_of_emi = $('#number_of_emi').val();

    var emi_date = $(this).val();

    var con = 1;



    for (let i = 1; i <= number_of_emi; i++) {

      if(i != 1){

        var d = new Date(emi_date);

        var emi_date = d.setMonth(d.getMonth() + 1);

        var today = new Date(emi_date);

        var dd = String(today.getDate()).padStart(2, '0');

        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!

        var yyyy = today.getFullYear();

        today =  yyyy+ '-' + mm + '-' + dd;



      }else{

        var d = new Date(emi_date);

        var emi_date = d.setMonth(d.getMonth());

        var today = new Date(emi_date);

        var dd = String(today.getDate()).padStart(2, '0');

        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!

        var yyyy = today.getFullYear();



        today =  yyyy+ '-' + mm + '-' + dd;

      }



      $('#emi_dates'+i).val(today);

    }



  });



  if($('.paid_by_edit').val() != 1){

    $('.cheque_fields').hide();

    $('.online_field').hide();

  }





  // if($('input[type=radio][name=paid_by]').val() == 'cheque'){

  //   $('.cheque_fields').show();

  //   $('.online_field').hide();

  // }else if($('input[type=radio][name=paid_by]').val() == 'online'){

  //   $('.online_field').show();

  //   $('.cheque_fields').hide();

  // }else{

  //   $('.cheque_fields').hide();

  //   $('.online_field').hide();

  // }



  $('input[type=radio][name=paid_by]').change(function(){

    var paid_by = $(this).val();

    if(paid_by == 'cheque'){

      $('.cheque_fields').show();

      $('.online_field').hide();

    }else if(paid_by == 'online'){

      $('.online_field').show();

      $('.cheque_fields').hide();

    }else{

      $('.cheque_fields').hide();

      $('.online_field').hide();

    }

  });



  // ==========================================



  $('#report_view input').attr('readonly', 'readonly');

  $('#report_view textarea').attr('readonly', 'readonly');

  $('#report_view select').attr("disabled", true);



  $(function () {

    $('[data-toggle="tooltip"]').tooltip()

  });



  //============ Role CheckBox select dselect ===========

    $('.checkAll').on('click', (ev)=>{

        let checkBox = $(ev.target), allInput = $(`.${checkBox.data('chk')}`);

        if(checkBox.is(':checked'))

          allInput.prop('checked', true);

        else

          allInput.prop('checked', false);

    });



  $('.chng_pswrd').hide();

  $('#chng_pass').click(function(){

      if($(this).prop("checked") == true){

          $('.chng_pswrd').show();

      }

      else if($(this).prop("checked") == false){

          $('.chng_pswrd').hide();

      }

  });
$(document).on('click', '#add_module', function(){
    var source = $('.append-dive:first')
    var i =  $('.append-dive:last').attr('data-id');
    var d = i++;
    var source = $('.append-dive:last'),
    clone = source.clone();
    clone.find(':input').val('');
    clone.find('.add').remove();
    clone.find('.remove-div').show();
    clone.find('#module_'+d).attr('id','module_'+i).attr('name','module[]');
    clone.find('#module_date_'+d).attr('id','module_date_'+i).attr('name','module_date[]');
    $("table tbody").append(clone);
    $('.append-dive:last').attr('data-id',i)

  })
  $(document).on('click', '#add_new', function(){

    var source = $('.append-dive:first')

    var i =  $('.append-dive:first').attr('data-id');

    var d = i++;

    var source = $('.append-dive:first'),

    clone = source.clone();

    clone.find(':input').val("");

    clone.find(':input').val("");

    clone.find('select[name="course_name[]"]').attr('id','course_name'+i);

    clone.find('select[name="course_name[]"]').attr('data-target',i);

    clone.find('select[name="module_name[]"]').attr('id','module_name'+i);
    clone.find('select[name="module_name[]"]').attr('data-target',i);

    clone.find('select[name="sub_module_name[]"]').attr('id','sub_module_name'+i);
    clone.find('select[name="student[]"]').attr('id','student_name'+i);

    clone.find('.add').remove();

    clone.find('.remove-div').show();

    $('.append-dive:first').attr('data-id',i)

    $("table tbody").append(clone);

    $('.course_name').on('change',function(){

      var id = $(this).val();

      var data_taget =$(this).attr('data-target');

      var module_html = '<option value="">Select Module</option>';

      var student_html = '<option value="">Select Students</option>';

      var route = $('#baseurl').val();

      var url = route + '/module_by_course/'+id

      $.ajax({

          type: 'GET',

          url: url,

          success: (response) => {

            console.log(response)

           

            var module_data = response.module;

            var students_data = response.students;

            for(var i=0; i<module_data.length; i++){

              module_html += '<option   value="' + module_data[i].module_name+ '" data-id="'+module_data[i].id+'" >' + module_data[i].module_name + '</option>'

            };

            $("#module_name"+data_taget).html(module_html);

            for(var j=0; j<students_data.length; j++){

              student_html += '<option value="' + students_data[j].id+ '">' + students_data[j].reg_details.first_name + ' '+ students_data[j].reg_details.last_name+'</option>'

            };

            $("#student_name"+data_taget).html(student_html);

          },

      });

    });

    $(document).on('click', '.remove-div', function(){
      $(this).closest('.append-dive').remove();
    });




  });

  $(document).on('click', '.remove-div', function(){
    $(this).closest('.append-dive').remove();
  });

  $('.course_name').on('change',function(){

    var id = $(this).val();

    var data_taget =$(this).attr('data-target');

    var module_html = '<option value="">Select Module</option>';

    var student_html = '<option value="">Select Students</option>';
    var sub_module_html = '<option value="">Select Module</option>';

    var route = $('#baseurl').val();

    var url = route + '/module_by_course/'+id

    $.ajax({

        type: 'GET',

        url: url,

        success: (response) => {

          console.log(response)
          var module_data = response.module;
          var students_data = response.students;
          var sub_module= response.sub_module;
          for(var i=0; i<module_data.length; i++){

            module_html += '<option  value="' + module_data[i].module_name+ '"  data-id="'+module_data[i].id+'">' + module_data[i].module_name + '</option>'

          };

          $("#module_name"+data_taget).html(module_html);

          for(var j=0; j<students_data.length; j++){

            student_html += '<option value="' + students_data[j].id+ '">' + students_data[j].reg_details.first_name + ' '+ students_data[j].reg_details.last_name+'</option>'

          };
          for(var i=0; i<sub_module.length; i++){
            sub_module_html += '<option value="' + sub_module[i].sub_module_name+ '">' + sub_module[i].sub_module_name + '</option>'
          };
          $("#sub_module_name"+data_taget).html(sub_module_html);

          $("#student_name"+data_taget).html(student_html);

        },

    });

  })

   $(document).on('change','.module_name',function(){
    
    //  var id = $(this).attr('data-id');
    var id = $(this).find(":selected").data("id");
    console.log(id);
     var data_taget =$(this).attr('data-target');
     var sub_module_name = '<option value="">Select Module</option>';
     var route = $('#baseurl').val();
     var url = route + '/sub_module_by_course_module/'+id
     $.ajax({
         type: 'GET',
         url: url,
         success: (response) => {
           var module_data = response.module;
           var sub_module_html = '<option value="">Select Module</option>'
           for(var i=0; i<module_data.length; i++){
             sub_module_name += '<option value="' + module_data[i].sub_module_name+ '">' + module_data[i].sub_module_name + '</option>'
           };
           $("#sub_module_name"+data_taget).html(sub_module_name);

         },

     });

   }).change();





  //checkin_checkout

  $('#chkIn_course_name').on('change',function(){

    var id = $(this).val();

    var module_html = '<option value="">Select Module</option>';

    var route = $('#baseurl').val();

  console.log(route);

    var url =route+'module_by_course/'+id

  $.ajax({

        type: 'GET',

        url: url,

        success: (response) => {

          console.log(response)

          var module_data = response.module;

          for(var i=0; i<module_data.length; i++){

            module_html += '<option value="' + module_data[i].module_name+ '">' + module_data[i].module_name + '</option>'

          };

          $("#chkIn_module_name").html(module_html);

        },

    });

  })
  // users By role 

$('#search_by_role').on('change',function(){

    var id = $(this).val();

    var module_html = '<option value="">Select User</option>';

    var route = $('#baseurl').val();

  console.log(route);

    var url =route+'/users-by-role/'+id

  $.ajax({

        type: 'GET',

        url: url,

        success: (response) => {

          var users = response.users;
          for(var i=0; i<users.length; i++){
                console.log(users[i].id)
            module_html += '<option value="' + users[i].id+ '">' + users[i].name + '</option>'
          };

          $("#search_by_users").html(module_html);

        },

    });

  })

    //stickeyheader



    $(window).scroll(function(){

        if($(this).scrollTop() > 0){

             if($(window).width() > 0){

                $('body').addClass('sticky')

             }

        }

        else{

            $('body').removeClass('sticky')

        }



    });















    // Sicknav

             $('.navbar').slicknav();



    $('.select_btn').click( function(e){

      e.stopPropagation();

            $('.select_btn').parent().toggleClass('current');



    });



    $(document).on("click", function(e) {

        if ($(e.target).is(".select_btn, .current") === false) {

          $(".select_btn").parent().removeClass("current");

        }

      });



    });





    //in-view-js

    var $animation_elements = $('.animation-element');

    var $window = $(window);



    function check_if_in_view() {

      var window_height = $window.height();

      var window_top_position = $window.scrollTop();

      var window_bottom_position = (window_top_position + window_height);



      $.each($animation_elements, function() {

        var $element = $(this);

        var element_height = $element.outerHeight();

        var element_top_position = $element.offset().top;

        var element_bottom_position = (element_top_position + element_height);



        //check to see if this current container is within viewport

        if ((element_bottom_position >= window_top_position) &&

            (element_top_position <= window_bottom_position)) {

          $element.addClass('in-view');

        } else {

          $element.removeClass('in-view');

        }

      });

    }



    $window.on('scroll resize', check_if_in_view);

    $window.trigger('scroll');





    //close-view-js



    function closeView(){

            var mainElement = document.getElementsByClassName('slicknav_btn');

            mainElement[0].classList.remove('slicknav_open');

            mainElement[0].classList.add('slicknav_collapsed');

            var getclass = document.getElementsByClassName('slicknav_nav');

            getclass[0].classList.add('slicknav_hidden');

            getclass[0].style.display = 'none';

          }



  // Delete data

  function deleteData(url, token){



    event.preventDefault();

    swal({



        title: "Are you sure?",



        text: "You want to delete this!",



        type: "warning",



        showCancelButton: true,



        confirmButtonClass: "btn-danger",



        confirmButtonText: "Yes, delete it!",



        closeOnConfirm: false,



        showLoaderOnConfirm: true



    },



        function () {



            $.ajax({



                type: 'POST',



                url: url,



                data: { '_method': 'delete', '_token': token},



                async: false,



                success: (response) => {



                    //swal("Deleted!", "Your has been deleted.", "success");

                    // alert(response);

                    window.location.reload();



                },



                error: () => {



                }

            });



        });

  }



