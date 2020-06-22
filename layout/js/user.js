$(function(){

    'use strict';

    //start loading
    $(document).ready(function () {
        $("#loading").fadeOut(2000, function () {
            $("body").css("overflow", "auto");
        });

        // Start Get Rating
        $('#rating-list span').click(function(e){

            if($(this).nextAll().hasClass('rating-color') )
            {
                $(this).nextAll().removeClass('rating-color');
            }
            else
            {
                $(this).addClass('rating-color');
                $(this).prevAll().addClass('rating-color');
            }
            var index = $(this).data("index");
            var item_id = $(this).parent().data('itemid');
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You have rate "+index +" out of 5 !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Rate it!'
              }).then((result) => {
                if (result.value) 
                {
                    $.ajax(
                    {
                        url:"rating.php",
                        method:"POST",
                        data:{index:index, item_id:item_id},
                        success:function(data)
                        {
                            if(data = "done")
                            {
                                Swal.fire({
                                    position: 'top-center',
                                    icon: 'success',
                                    title: 'Your Rate has been saved',
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                                setTimeout(function(){
                                    location.reload(true);
                                    }, 1500);
                            }
                            else
                            {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong!',
                                })
                            }
                        }
                    });
                }
              });
            
        });
        // End Get Rating

   

        // Start Read More button
        $('.item-box .card-body').each(function() {
            if($(this).children(".card-text").text().length > 56 || $(this).children(".card-title").text().length > 21)
            {
               
                $(this).children('.readMore-btn').css("display","inline-block");

                $(this).children('.readMore-btn').click(function(){
                    if($(this).siblings('.item-box .card-text').hasClass("readMore-content") || $(this).siblings('.item-box .card-title').hasClass("readMore-content"))
                    {
                        $(this).siblings('.item-box .card-title').removeClass("readMore-content");
                        $(this).siblings('.item-box .card-text').removeClass("readMore-content");
                        $(this).html("Read More");
                        $(this).css("background-color","#2196f3");
                    }
                    else
                    {
                        $(this).siblings('.item-box .card-title').addClass("readMore-content");
                        $(this).siblings('.item-box .card-text').addClass("readMore-content");
                        $(this).html("Read Less");
                        $(this).css("background-color","#DC3545");
                    }
                });
            }
        });
        
        // End Read More button
    })
    //end loading

   //Tags Input  plugin
   $('#tokenfield').tokenfield({
    showAutocompleteOnFocus: false
    })
    
    // Start Switch Between login And Signup 
    $(".header-content h1 span").click(function(){

        $(this).addClass('path-active').siblings().removeClass('path-active');
        
        if($(this).hasClass('login'))
        {
            $('.login-card .signup-form').hide();
            $('.login-card .login-form').fadeIn(100);
        }
        if($(this).hasClass('signup'))
        {
            $('.login-card .login-form').hide();
            $('.login-card .signup-form').fadeIn(100);
        }
    });
    // End Switch Between login And Signup 

    // Start Create New Ad Live 
        $(".live").keyup(function () { 
           $($(this).data("class")).text($(this).val());
        });
    // End Create New Ad Live 


    //hide placeholder on form focus
    $('[placeholder]').focus(function(){
        $(this).attr('data-text',$(this).attr('placeholder'));
        $(this).attr('placeholder','');
    }).blur(function(){
        $(this).attr('placeholder',$(this).attr('data-text'));
    })

    //Add Asterisk  On Required Field
    $('input').each(function () { 
         if($(this).attr('required') === 'required' )
         {
             $(this).after('<span class ="asterisk">*</span>');
         }
    });

    //Show Password
    var passField = $('.password');
    $('.show-pass').click(function(){

        if(passField.attr('type')=='text')
        {
            passField.attr('type','password');
        }
        else
        {
            passField.attr('type','text');
        }
    });
















    // //Delete alert
    // $('.deleteBtn').click(function(){

    //     Swal.fire({
    //         title: 'Are you sure?',
    //         text: "You won't be able to revert this!",
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Yes, delete it!'
    //       }).then((result) => {
    //         if (result.value) {
    //           Swal.fire(
    //             'Deleted!',
    //             'Your file has been deleted.',
    //             'success'
    //           )
    //         }
    //       })

    // })
 


});