$(function(){

    'use strict';
    // limit show 
    $(document).ready(function(){
        $('#limit-records').change(function(){
            $('form').submit();
        });



        $('#delete-btn').click(function(e){
            var itemid = $(this).data('itemid');
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
              }).then((result) => 
              {
                if (result.value) 
                {
                    $.ajax(
                    {
                        url:"items.php",
                        method:"GET",
                        data:{action:"Delete", itemid:itemid},
                        success:function(data)
                        {
                            if(data = 1)
                            {
                                Swal.fire({
                                    position: 'top-center',
                                    icon: 'success',
                                    title: 'Your file has been deleted.',
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
    });


    //Dashboard
    $('.toggle-info').click(function(){
        $(this).toggleClass('selected').parent().next('.card-body').fadeToggle(100);

        if($(this).hasClass('selected'))
        {
            $(this).html(' <i class="fa fa-plus fa-lg"></i>');
        }
        else
        {
            $(this).html(' <i class="fa fa-minus fa-lg"></i>');
        }
    });

    //Tags Input  plugin
    $('#tokenfield').tokenfield({
        showAutocompleteOnFocus: false
    })

    //start loading
    $(document).ready(function () {
        $("#loading").fadeOut(2000, function () {
            $("body").css("overflow", "auto");
        });
    })
    //end loading

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

    // Category View Option
    $(".cat h3").click(function(){

        $(this).next(".full-view").fadeToggle(100);

    });

    $(".option span").click(function(){

        $(this).addClass('active').siblings('span').removeClass('active');
        if( $(this).data('view') ==='full' ) 
        {
            $('.cat .full-view').fadeIn(200);
        }
        else
        {
            $('.cat .full-view').fadeOut(200);
        }
    });

















    // //Delete alert
    // $('#deleteBtn').click(function(){

    //     var userid = $(this).attr("data-id");
    //     var url = $(this).attr("href");
    //     console.log(userid);
    //     console.log(url);
    //     // Swal.fire({
    //     //     title: 'Are you sure?',
    //     //     text: "You won't be able to revert this!",
    //     //     icon: 'warning',
    //     //     showCancelButton: true,
    //     //     confirmButtonColor: '#3085d6',
    //     //     cancelButtonColor: '#d33',
    //     //     confirmButtonText: 'Yes, delete it!'
    //     //   }).then((result) => {
    //     //     if (result.value) {
    //     //       Swal.fire(
    //     //         'Deleted!',
    //     //         'Your file has been deleted.',
    //     //         'success'
    //     //       )
    //     //     }
    //     //   })

    // })
 
});
