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


    if ( $('.overlay').hasClass('network_overlay1') ) {
        $('.overlay').removeClass('network_overlay1');
    } else {
        $('.overlay').addClass('network_overlay1');    
    }
});

 $('.side-list li').click( function(){
    if ( $('.side-list li').hasClass('active') ) {
        $('.side-list li').removeClass('active');
        $(this).addClass('active');  
    }
});

 $('.side-list li a').click( function(){
    $('.sidenav').removeClass('closebtn');
});

/*********wishlist**********/
 $('.wslst').click( function(){
    $(this).addClass('wsadd');    
});
/******right-panel*******/  

 $('.srcindustry').click( function(){
        $('.network_main').addClass('network_main1');    
        $('.right_panel').addClass('category_right'); 
        $('.right_panel').removeClass('filter_right');     
         $('.right_panel.filtersec').hide(); 
         $('.right_panel.connectionsec').hide();
         $('.right_panel.categorysec').show(); 


          $('.right_hd').css('display','block'); 
         // $('.admin_panel').addClass('admin_overlay');
        $('.overlay').addClass('network_overlay'); 
        $('.sidenav').removeClass('closebtn');
        $('.overlay').removeClass('network_overlay1');

});

$('.srcfilter').click( function(){
        $('.network_main').addClass('network_main1');
        $('.right_panel').addClass('filter_right');     
        $('.right_panel').removeClass('category_right');  
        $('.right_panel.categorysec').hide(); 
        $('.right_panel.connectionsec').hide(); 
        $('.right_panel.filtersec').show(); 

        $('.right_hd').hide(); 
        $('.right_hd.filter').show(); 

        $('.filter_btn_holder').hide(); 
        $('.filter_btn_holder.fst_btn').show(); 


        $('.fllist').hide(); 

        $('.overlay').addClass('network_overlay');
        $('.sidenav').removeClass('closebtn');
        $('.overlay').removeClass('network_overlay1');
});

$('.cnnct').click( function(){
        $('.network_main').addClass('network_main1');
        $('.right_panel').addClass('filter_right');     
        $('.right_panel').removeClass('category_right');  
        $('.right_panel.categorysec').hide();  
        $('.right_panel.filtersec').hide(); 
        $('.right_panel.connectionsec').hide();
        $('.right_panel.connectionsec.connectionsec1').show();

        $('.right_hd').hide(); 
        $('.right_hd.cnncthd').show(); 

        $('.filter_btn_holder').hide(); 
        $('.filter_btn_holder.confirm_btn').show(); 


        $('.fllist.connect').show(); 

        $('.overlay').addClass('network_overlay');
        $('.sidenav').removeClass('closebtn');
        $('.overlay').removeClass('network_overlay1');
});

$('.sent').click( function(){
        $('.network_main').addClass('network_main1');
        $('.right_panel').addClass('filter_right');     
        $('.right_panel').removeClass('category_right');  
        $('.right_panel.categorysec').hide();  
        $('.right_panel.filtersec').hide(); 
        $('.right_panel.connectionsec').hide();
        $('.right_panel.connectionsec.connectionsec2').show();

        $('.right_hd').hide(); 
        $('.right_hd.cnncthd').show(); 

        $('.filter_btn_holder').hide(); 
        $('.filter_btn_holder.confirm_btn').show(); 


        $('.fllist.connect').show(); 

        $('.overlay').addClass('network_overlay');
        $('.sidenav').removeClass('closebtn');
        $('.overlay').removeClass('network_overlay1');
});

$('.accept').click( function(){
        $('.network_main').addClass('network_main1');
        $('.right_panel').addClass('filter_right');     
        $('.right_panel').removeClass('category_right');  
        $('.right_panel.categorysec').hide();  
        $('.right_panel.filtersec').hide(); 
        $('.right_panel.connectionsec').hide();
        $('.right_panel.connectionsec.connectionsec3').show();

        $('.right_hd').hide(); 
        $('.right_hd.cnncthd').show(); 

        $('.filter_btn_holder').hide(); 
        $('.filter_btn_holder.confirm_btn').show(); 


        $('.fllist.connect').show(); 

        $('.overlay').addClass('network_overlay');
        $('.sidenav').removeClass('closebtn');
        $('.overlay').removeClass('network_overlay1');
});



 $('.right-top .cross').click( function(){
     $('.network_main').removeClass('network_main1');
     $('.right_panel').removeClass('category_right');
     $('.right_panel').removeClass('filter_right');

     // $('.admin_panel').removeClass('admin_overlay');
     $('.overlay').removeClass('network_overlay');

});

/******for-overlay******/  
$('.overlay').click( function(){
     $('.network_main').removeClass('network_main1');
     $('.right_panel').removeClass('category_right');
     $('.right_panel').removeClass('filter_right');
     $('.sidenav').removeClass('closebtn');

     $(this).removeClass('network_overlay');
});

$('.resp-tab-item').click( function(){
     $('.network_main').removeClass('network_main1');
     $('.right_panel').removeClass('category_right');
     $('.right_panel').removeClass('filter_right');

     $('.overlay').removeClass('network_overlay');
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


//  $('.right_hd').click( function(){
//     if ( $(this).text('All Categories') ) {
//         $(this).removeClass('backoption');  
//     }
// });



/******right-panel*sub_division******/  

$('.right-bottom ul li').each(function(){
    var lastliul = $(this).find('ul').last();
    $(lastliul).addClass('lastlist');
})


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


// $('.right_hd').off().on('click',function(){

  
// $('.extranot').parent('.cf-list').find( " > li" ).not('this').show();;
    
//     var parentTab = $(this).attr('data-parent');
//     if(parentTab == "root"){
//         $(this).text("All Categories");
//         $(this).removeClass('backoption');
//     }
//     else{
//         $(this).text(parentTab);
//         $(this).addClass('backoption');
//     }
    
//     $(this).attr('data-parent',$('.' + parentTab).attr('data-parent'));
//     var allCfList = $('ul.cf-list'), 
//         currentUl = allCfList.find('.' + parentTab), 
//         cfDiv = $('.cf_division')
//         ;
    
//     cfDiv.hide();
    
//     if(parentTab == "root"){
//         $('.' + parentTab).parent('.cf_division').show();
//         $('.' + parentTab).find('a').show();
//         $('.' + parentTab).find('a').removeClass('backoption');
//     }else{
//         currentUl.parents('.cf_division').show();
//         cfDiv.find('li').hide();
//         currentUl.parent('.cf_division').parents('li').show();
//         currentUl.find('li').show();
//         currentUl.find('a').show();
//     }
    
//  });   


/************filter-sec**************/

 $('.apply_filter').click( function(){
     $('.add_filter').css('display','block');
     $('.filter').hide();
     $('.fst_btn').hide();
     $('.scnd_btn').show();
});

$('.scnd_btn').click( function(){
     $('.add_filter').css('display','none');
     $('.filter').hide();
     $('.fst_btn').hide();
     $('.fllist').css('display','none');
     $('.fllist.add_filter').css('display','block');
     $('.right_hd.add_filter').css('display','block');
});

$('.connected_channel').click( function(){
     $('.fllist.add_filter').css('display','none');
     $('.connected').css('display','block');
});
$('.prefered_channel').click( function(){
     $('.fllist.add_filter').css('display','none');
     $('.prefered').css('display','block');
});

$('.resource_channel').click( function(){
     $('.fllist.add_filter').css('display','none');
     $('.resource').css('display','block');
});

$('.location_channel').click( function(){
     $('.fllist.add_filter').css('display','none');
     $('.location').css('display','block');
});



/***apply******/

});



