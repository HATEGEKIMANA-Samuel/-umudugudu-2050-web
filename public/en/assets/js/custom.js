$(function(){
	$('.navbar .navbar-nav li.menu-item-has-children .sub-menu li').mouseover(function(e){
		$(this).css('background-color','#6a005b');
	});

	$('.navbar .navbar-nav li.menu-item-has-children .sub-menu li').mouseleave(function(e){
		$(this).css('background-color','transparent');
	});

	$('.header-left .dropdown .dropdown-menu .dropdown-item').mouseover(function(e){
		$(this).css({'background-color':'#dbecfd', 'color':'#fff'});
	});

	$('.header-left .dropdown .dropdown-menu .dropdown-item').mouseleave(function(e){
		$(this).css('background-color','transparent');
	});


	$('.dropdown-toggle').click(function(e){
		e.preventDefault();
		// e.stopPropagation();
		if($(this).next('.sub-menu').is(':visible')){
			$(this).next('.sub-menu').hide();
			$(this).parents('.menu-item-has-children').siblings().find('.sub-menu').hide();
		}
	})
	
	$('.menuMenu').mouseleave(function(e){
	    e.preventDefault();
	    $(this).hide('fast');
	})

	$('input.form-control, select.form-control').focusin(function(e){
		e.preventDefault();
		$(this).css({'border':'2px solid #25a3ff', 'background-color':'#fff'});
	})
	$('input.form-control, select.form-control').focusout(function(e){
		e.preventDefault();
		$(this).css({'border':'none', 'background-color':'#fff'});
		if ($(this).hasClass('required') && $(this).val() == '') {
			// $(this).css({'border':'1px solid #e74c3c', 'background-color':'#fff'});
		}
	})

	$('.info-list').mouseover(function() {
		$(this).css({'background-color':'#dbecfd', 'color':'#fff'});
	});

	$('.info-list').mouseleave(function() {
		$(this).css('background-color','transparent');
	});
	
	$('.theTitle').change(function(){
	    if($(this).val() == 'Other'){ notification-notice
	       // alert('tested')
	        $('.nationalityField').removeClass('col-md-6').addClass('col-md-4');
	        $('.otherIfAny').removeClass('col-md-6').addClass('col-md-4');
	    } else{
	        $('.nationalityField').addClass('col-md-6').removeClass('col-md-4');
	        $('.otherIfAny').addClass('col-md-6').removeClass('col-md-4');
	    }
	});
	
	$('.datepicker').change(function(){
	   $('div.datepicker.datepicker-dropdown.dropdown-menu.datepicker-orient-left.datepicker-orient-bottom').css('display','none'); 
	});
	
	$('.menu-item-has-children').click(function(){
	   $(this).find('.open aside.left-panel .navbar .navbar-nav li.menu-item-has-children .sub-menu').css('display','block !important'); 
	});
	
	$('.seePassword').click(function(e){
	    e.preventDefault();
        
        if($(this).parents('.position-relative').find('input.form-control').attr('type') === 'password'){
            $(this).parents('.position-relative').find('input.form-control').attr('type','text');
        } else{
            $(this).parents('.position-relative').find('input.form-control').attr('type','password');
        }
	})
	
// 	EDIT PASSWORD
	$('#changePasswordForm').submit(function(e){
	   e.preventDefault();
	   
	   const password = $('#password').val();
	   const confirmPassword = $('#p_check').val();
	   const id=$("#updatebtn").attr('data-id');
	       
	   if($.trim(password) !== $.trim(confirmPassword)){
	       $('.resp').html('<p class="alert mb-10 fs-13 w-100p alert-danger">Passwords dont match</p>');
	       return false
	   } else if( password === '' || confirmPassword === '' ){
	       $('.resp').html('<p class="alert mb-10 fs-13 w-100p alert-danger">Fill all the fields</p>');
	   } else{
	       $.ajax({
	         url:'userAction.php',
	          method:'POST',
	          data:{id:id,adminChange:'editpswd',password:password},
	          beforeSend:function(){
	           $('.resp').html('<p class="alert mb-10 fs-13 w-100p alert-info">changing....</p>');
	          },
	           success:function(data){
	           if(data.trim()=='404'){
	           $('.resp').html('<p class="alert mb-10 fs-13 w-100p alert-danger">password not changed</p>'); 
	           }
	           else
	           {
	           $('.resp').html('<p class="alert mb-10 fs-13 w-100p alert-success">Passwords changed</p>');   
	           }
	          },
	          error:function(xhr,status){
	           console.log(xhr.status);
	          }
	       });
	      
	   }
	});
	
});