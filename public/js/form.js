$(document).ready(function() {

	// Notify
	$('.user-mange-drpdwn').hide();
    
    //  Show hide popover

        $(".notify").click(function(){

             $(this).parent().siblings().find(".user-mange-drpdwn").stop(true, false, true).slideUp(400);

            $(this).parent().children(".user-mange-drpdwn").stop(true, false, true).slideToggle(400);

    });

    $(document).on("click", function(event){

        var $trigger = $(".tpright_list li");
    
        if($trigger !== event.target && !$trigger.has(event.target).length){
    
            $(".user-mange-drpdwn").slideUp("fast");
    
        }            
    
    });

	/*menu**/
	 	

	 $(".drop").click(function(){
	 	$(this).parents('.form-group').toggleClass('open');
	 })

	 $(".searchdrop").click(function(){
	 	$(this).parents('.form-group').toggleClass('open');
	 })

	 $('.tabcontent:first-child').addClass('imp');
	 $(".tablist a").click(function(){
	 	if (this.hash !== "") { 
	      event.preventDefault(); 
	      var hash = this.hash;
	      $('.tabcontent').removeClass('imp'); 
	 	  $(hash).addClass('imp');
		   $(".tablist a").removeClass('active');
          $(this).addClass('active');
	    }
	 })

	 $(".add").click(function(){
	 	$(this).parents('.formdiv').addClass('addholder');
	 })

	 
	
	var id_count = 1;
	$('.plus').on('click', function() {
		var source = $('.append-dive:first'), clone = source.clone();
		// console.log(clone);
		// clone.find(':input').attr('id', function(i, val) {
		// return id_count;
		// });
		clone.find(':input').val("");
		clone.appendTo('.append-div-hldr');
		id_count++;

	});

	$('body').on('click', '.remove-div', function() {
		var closest = $(this).closest('.append-dive').remove();
	  });
	 
});

jQuery(document).ready(function($) {
	var open = false;

   var openSidebar = function(){
   $('.srch-res').addClass('open');
   open = true;
}
var closeSidebar = function(){
   $('.srch-res').removeClass('open');
   open = false;
}

$('.srch-res').click( function(event) {
   event.stopPropagation();
   var toggle = open ? closeSidebar : openSidebar;
   toggle();
});

$(document).click( function(event){
   if ( !$(event.target).closest('.search-dash').length ) {
	   closeSidebar();   
   }
});
});