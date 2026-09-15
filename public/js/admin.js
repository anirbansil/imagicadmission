/********************************************************

 *

 * Custom Javascript code for Flexor Bootstrap theme

 *

 *******************************************************/



$(document).ready(function() {







/******left-panel*******/ 

  



    $('.openbtn').click( function(){

        if ( $('.sidenav').hasClass('closebtn') ) {

            $('.sidenav').removeClass('closebtn');

        } else {

            $('.sidenav').addClass('closebtn');    

        }





        if ( $('.overlay').hasClass('admin_overlay1') ) {

            $('.overlay').removeClass('admin_overlay1');

        } else {

            $('.overlay').addClass('admin_overlay1');    

        }

    });



if($(window).width() > 0){  

    $(".sidenav").removeClass("closebtn");

}

if($(window).width() > 1023){  

    $(".sidenav").addClass("closebtn");

}



 $('.side-list li').click( function(){

    if ( $('.side-list li').hasClass('active') ) {

        $('.side-list li').removeClass('active');

        $(this).addClass('active');  

    }

});



 $('.side-list li a').click( function(){

    $('.sidenav').removeClass('active');

});



/*********wishlist**********/

 $('.wslst').click( function(){

    $(this).addClass('wsadd');    

});

/******right-panel*******/  



 $('.srccategory').click( function(){

        $('.admin_main').addClass('admin_main1');    

        $('.right_panel').addClass('category_right'); 

        $('.right_panel').removeClass('filter_right');     

         $('.right_panel.filtersec').hide(); 

         $('.right_panel.categorysec').show(); 





          $('.right_hd').css('display','block'); 

         // $('.admin_panel').addClass('admin_overlay');

        $('.overlay').addClass('admin_overlay'); 

        $('.sidenav').removeClass('closebtn');

        $('.overlay').removeClass('admin_overlay1');



});

$('.srcfilter').click( function(){

        $('.admin_main').addClass('admin_main1');

        $('.right_panel').addClass('filter_right');     

        $('.right_panel').removeClass('category_right');  

         $('.right_panel.categorysec').hide();  

        $('.right_panel.filtersec').show();  



        // $('.right_hd').removeClass('backoption');  

        // $('.admin_panel').addClass('admin_overlay');

        $('.overlay').addClass('admin_overlay');

        $('.sidenav').removeClass('closebtn');

        $('.overlay').removeClass('admin_overlay1');

});





 $('.right-top .cross').click( function(){

     $('.admin_main').removeClass('admin_main1');

     $('.right_panel').removeClass('category_right');

     $('.right_panel').removeClass('filter_right');



     // $('.admin_panel').removeClass('admin_overlay');

     $('.overlay').removeClass('admin_overlay');



});



/******for-overlay******/  

$('.overlay').click( function(){

     $('.admin_main').removeClass('admin_main1');

     $('.right_panel').removeClass('category_right');

     $('.right_panel').removeClass('filter_right');

     $('.sidenav').removeClass('closebtn');



     $(this).removeClass('admin_overlay');

});



$('.resp-tab-item').click( function(){

     $('.admin_main').removeClass('admin_main1');

     $('.right_panel').removeClass('category_right');

     $('.right_panel').removeClass('filter_right');



     $('.overlay').removeClass('admin_overlay');

});



/******left-click_ main_content**on****/



$('.main_content').hide('');

 $('.search_main_content').show('');



$('.search').click( function(){

     $('.main_content').hide('');

     $('.search_main_content').show('');

});

$('.settings').click( function(){

    $('.main_content').hide('');

     $('.process_main_content').show('');

});

$('.resource').click( function(){

    $('.main_content').hide('');

     $('.booked_main_content').show('');

});



/**********reload***********/





 $('.right_hd').click( function(){

    if ( $(this).text('All Categories') ) {

        $(this).removeClass('backoption');  

    }

});







/******right-panel*sub_division******/  

//  $('.cf_head').click( function(){

//         $('.right_panel').addClass('right_panel1');       

// });



//  $('.right_hd').click( function(){

//      $(this).parents('.right_panel').removeClass('right_panel1');

// });



$('.right-bottom ul li').each(function(){

    var lastliul = $(this).find('ul').last();

    $(lastliul).addClass('lastlist');

})





// $('.cf_head').off().on('click',function(){

//  var catname = $(this).find('.text').text();

//  $('.right_hd').text(catname);

//     $('.right_hd').attr('data-parent',$(this).find('.text').attr('data-parent'));

//     $('.right_hd').addClass('backoption');



//  $(this).hide().next('.cf_division').css('display','block');

//  $(this).parent('').addClass( 'notli');

//  $(this).parents('.cf-list').find( " > li" ).not('this').hide();

//  $(this).parents('.cf-list ').find( ".notli" ).not('this').show();

//     $(this).parent(".notli").addClass('extranot');

// });



$('.cf_head .ico').off().on('click',function(){

    var catname = $(this).parent('.cf_head').find('.text').text();

    $('.right_hd').text(catname);

    console.log($(this).closest('ul').attr('class'));



    $('.right_hd').attr('data-parent',$(this).parent('.cf_head').find('.text').attr('data-parent'));

    $('.right_hd').addClass('backoption');



    $(this).parent('.cf_head').hide().next('.cf_division').css('display','block');

    $(this).parent('.cf_head').parent('').addClass( 'notli');

    $(this).parent('.cf_head').parents('.cf-list').find( " > li" ).not('this').hide();

    $(this).parent('.cf_head').parents('.cf-list ').find( ".notli" ).not('this').show();

    $(this).parent('.cf_head').parent(".notli").addClass('extranot');

});





$('.right_hd').off().on('click',function(){



  

$('.extranot').parent('.cf-list').find( " > li" ).not('this').show();;

    

    var parentTab = $(this).attr('data-parent');

    if(parentTab == "root"){

        $(this).text("All Categories");

        $(this).removeClass('backoption');

    }

    else{

        $(this).text(parentTab);

        $(this).addClass('backoption');

    }

    

    $(this).attr('data-parent',$('.' + parentTab).attr('data-parent'));

    var allCfList = $('ul.cf-list'), 

        currentUl = allCfList.find('.' + parentTab), 

        cfDiv = $('.cf_division')

        ;

    

    cfDiv.hide();

    

    if(parentTab == "root"){

        $('.' + parentTab).parent('.cf_division').show();

        $('.' + parentTab).find('a').show();

        $('.' + parentTab).find('a').removeClass('backoption');

    }else{

        currentUl.parents('.cf_division').show();

        cfDiv.find('li').hide();

        currentUl.parent('.cf_division').parents('li').show();

        currentUl.find('li').show();

        currentUl.find('a').show();

    }

    

 });   





/************filter-sec**************/



 $('.apply_filter').click( function(){

     $('.add_filter').css('display','block');

     $('.filter').hide();

     $('.fst_btn').hide();

     $('.scnd_btn').show();

});



$('.presaved_filter').click( function(){

     $('.add_filter').css('display','none');

     $('.filter').hide();

     $('.presaved').css('display','block');

     $('.fst_btn').hide();

     $('.thrd_btn').show();

});

$('.thrd_btn').click( function(){

     $('.add_filter').css('display','none');

     $('.filter').hide();

     $('.presaved').css('display','none');

     $('.fst_btn').hide();

     $('.thrd_btn').show();



     $('.right_hd').css('display','none');

    $('.right_hd.save_filter').css('display','block');

    $('.fllist.save').show();

    $('.thrd_btn').hide();

    $('.scnd_btn').show();

});





$('.location_channel').click( function(){

     $('.fllist.add_filter').css('display','none');

     $('.location').css('display','block');

     $('.location.final_location').css('display','none');

     $('.availability.final_availability').css('display','none');

     $('.ratings.final_ratings').css('display','none')



     $('.right_hd.filter').css('display','none');

     $('.right_hd.location').css('display','block');

     $('.scnd_btn').hide();

     $('.thrd_btn').hide();

     $('.location_btn').show();

});

$('.location_btn').click( function(){

    $('.fllist.add_filter').css('display','none');

    $('.fllist.location').css('display','none');



    $('.location.final_location').css('display','block');



    $('.right_hd').css('display','none');

    $('.right_hd.location').css('display','block');



    $('.scnd_btn').show();

    $('.thrd_btn').hide();

    $('.location_btn').hide();



});

$('.save_btn').click( function(){

     $('.fllist.add_filter').css('display','none');

    $('.fllist.location').css('display','none');

    $('.fllist.availability').css('display','none');

    $('.ratings.final_ratings').css('display','none');



    $('.fllist.save').show();

    $('.right_hd').css('display','none');

    $('.right_hd.save_filter').css('display','block');



});





$('.final_location .ed-crss .cross').click( function(){

    $('.fllist.add_filter').css('display','block');

     $('.location').css('display','none');

     $('.right_hd.add_filter').css('display','block');

});

$('.final_location .ed-crss .edit').click( function(){

    $('.fllist.add_filter').css('display','none');

    $('.fllist.location').css('display','block');

     $('.final_location').css('display','none');

});



$('.final_availability .ed-crss .cross').click( function(){

    $('.fllist.add_filter').css('display','block');

     $('.location').css('display','none');

     $('.right_hd.add_filter').css('display','block');

});

$('.final_availability .ed-crss .edit').click( function(){

    $('.fllist.add_filter').css('display','none');

    $('.fllist.availability').css('display','block');

     $('.final_location').css('display','none');

});



$('.scnd_btn .apply_filter').click( function(){

     $('.fllist.add_filter').css('display','block');

    $('.fllist.location').css('display','none');

});



$('.final_ratings .ed-crss .edit').click( function(){

    $('.fllist.add_filter').css('display','none');

    $('.fllist.ratings').css('display','block');

     $('.final_ratings').css('display','none');

});

$('.final_ratings .ed-crss .cross').click( function(){

    $('.fllist.add_filter').css('display','block');

    $('.fllist.ratings').css('display','none');

    $('.right_hd.add_filter').css('display','block');

});



$('.av_channel').click( function(){

     $('.add_filter').css('display','none');

     $('.availability').css('display','block');

     $('.availability.final_availability').css('display','none');

     $('.fllist.final_location').css('display','none');

     $('.scnd_btn').hide();

     $('.thrd_btn').hide();

     $('.av_btn').show();

});

$('.av_btn').click( function(){

    $('.fllist.add_filter').css('display','none');

    $('.fllist.availability').css('display','none');

    $('.availability.final_availability').css('display','block');



    $('.right_hd').css('display','none');

    $('.right_hd.availability').css('display','block');



    $('.scnd_btn').show();

    $('.thrd_btn').hide();

    $(this).hide();

});





$('.rating_channel').click( function(){

     $('.add_filter').css('display','none');

     $('.ratings').css('display','block');

     $('.ratings.final_ratings').css('display','none');

     $('.fllist.final_location').css('display','none');

     $('.availability.final_availability').css('display','none');



    

     $('.right_hd.filter').css('display','none');

     $('.right_hd.ratings').css('display','block');

     $('.scnd_btn').hide();

     $('.thrd_btn').hide();

     $('.rating_btn').show();

});

$('.rating_btn').click( function(){

    $('.fllist.add_filter').css('display','none');

    $('.fllist.ratings').css('display','none');

    $('.ratings.final_ratings').css('display','block');



    $('.right_hd').css('display','none');

    $('.right_hd.ratings').css('display','block');



    $('.scnd_btn').show();

    $('.thrd_btn').hide();

    $(this).hide();

});



// $('.booked_channel').click( function(){

//      $('.add_filter').css('display','none');

//      $('.pbkd').css('display','block');

//      $('.scnd_btn').hide();

//      $('.thrd_btn').show();

// });



// $('.favourite_channel').click( function(){

//      $('.add_filter').css('display','none');

//      $('.favourite').css('display','block');

//      $('.scnd_btn').hide();

//      $('.thrd_btn').show();

// });









/***apply******/



// $('.submenu').hide();

    

    //  Show hide popover



        $(".hashchild > a").click(function(){

          

            $('.hashchild > a.show').not(this).removeClass('show');

            $(this).toggleClass('show');



             $(this).parent().siblings().find(".submenu").stop(true, false, true).slideUp(400);



            $(this).parent().children(".submenu").stop(true, false, true).slideToggle(400);



    });
    
        $('input[name="student_marks[]"], input[name="student_marksp[]"]').keyup(function(){
        var $row = $(this).closest('tr'); // Get the parent row
        var marks = parseFloat($row.find('input[name="student_marks[]"]').val()) || 0; // Get value of marks input or default to 0 if empty or NaN
        var marksp = parseFloat($row.find('input[name="student_marksp[]"]').val()) || 0; // Get value of marksp input or default to 0 if empty or NaN
        var total = marks + marksp; // Calculate total
        // Set the total value in student_markstotal field
        $row.find('input[name="student_markstotal[]"]').val(total);
    });




});
$(document).ready(function() {
    var start_time = $('#clock-wrapper').attr('data-value');
    var timeArray = start_time.split(":"); // Split time string into hours, minutes, and seconds
    var hours = parseInt(timeArray[0]); // Parse hours as integer
    var minutes = parseInt(timeArray[1]); // Parse minutes as integer
    var seconds = parseInt(timeArray[2]); // Parse seconds as integer
    var totalSeconds = (hours * 60 * 60) + (minutes * 60) + seconds; // Calculate total time in seconds
    setInterval(function() {
      totalSeconds++;
      var hours = Math.floor(totalSeconds / 3600);
      var minutes = Math.floor((totalSeconds % 3600) / 60);
      var seconds = Math.floor(totalSeconds % 60);

      $('#clock-wrapper').html(
        hours + ":" + minutes + ":" + seconds
                );
    }, 1000);
})



