
<h3 class="p-3 text-2xl text-center">Stable Diffusion</h3>

<div class="sm:text-center w-full">

    <form method="post">
        @csrf

        <div id="mainForm" class="w-full items-center mx-auto mb-3 space-y-4  sm:flex sm:space-y-0">
            <div class="w-full">
                <input class="p-5 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 sm:rounded-none sm:rounded-l-lg focus:ring-primary-500 focus:border-primary-500"  placeholder="Enter your text description" id="prompt" name="prompt" type="text">
            </div>
            <div class="w-full lg:w-60 md:w-0">
                <button type="submit" id="submitForm" class="bg-blue-600 py-5 px-5 text-sm font-medium text-center text-white rounded-lg border cursor-pointer sm:rounded-none sm:rounded-r-lg  content-between w-full">Generate</button>
            </div>
        </div>

</div>

<div class="flex flex-col space-y-2 md:space-y-0 md:flex-row md:space-x-3 mx-auto p-3">

    <input type="text" id="negative_prompt" name="negative_prompt" class="flex p-3 w-full rounded-xl px-6  border-gray-300 focus:border-blue-100" placeholder="Negative Prompt">
    <input type="text"  id="fetch_image" name="response_id" class="hidden p-3 w-full rounded-xl px-6 border-gray-300 focus:border-blue-100" placeholder="Enter Response ID">

    <select name="endpoint" id="endpoint" class="w-full rounded-xl p-3 border-gray-300" >
        <option value="">Select Endpoint</option>
        <option value="text_to_image">Text to Image</option>
        <option value="image_to_image">Image to Image</option>
        <option value="inpaint">Inpainting</option>
        <option value="fetch_endpoint">Fetch Queued Image</option>
    </select>

    {{-- <input type="text" class="p-3 w-full md:w-1/3 rounded-xl border-gray-300 focus:border-blue-100" name="" id=""> --}}
</div>

<div id="width-height" class="hidden space-x-2 my-1 mx-auto justify-center md:w-2/3 w-full">
    <input type="number" id="width" name="width" class="p-3 w-full md:my-0  rounded-xl px-6 text-sm border-gray-300 focus:border-blue-100"  placeholder="Set Width">
    <input type="number" id="height" name="height" class="p-3 w-full  md:my-0 rounded-xl px-6 text-sm border-gray-300 focus:border-blue-100"  placeholder="Set Height">
</div>

<div id="mask_image" class="hidden py-1 space-x-2 mx-auto justify-center w-full">
    <input type="url" id="masked_image" name="mask_image" class="p-3 w-full md:my-0 md:w-2/3 rounded-xl px-6 border-gray-300 focus:border-blue-100"  placeholder="Mask Image URL">
</div>

<div id="initial_image" class="hidden space-x-2 my-2 mx-auto justify-center w-full">
    <input type="url" id="init_image" name="init_image" class="p-3 w-full md:my-0 md:w-2/3 rounded-xl px-6 border-gray-300 focus:border-blue-100"  placeholder="Initial Image URL">
</div>


<div id="generate"  class="hidden w-full my-4 items-center justify-center mx-auto mb-3 space-y-4  sm:space-y-0">
    <button type="submit" id="submitFormTwo" class="bg-blue-600 py-5 px-9 text-sm font-medium text-center text-white rounded-2xl border cursor-pointer">Generate</button>
 </div>

</form>

<div class="flex mx-auto items-center my-2 justify-center" id="more">
    <button type ="button" id="buttonOption" class="items-center px-4 border-spacing-2 py-2 text-black hover:bg-blue-100 rounded-full">More Options</button>
</div>




@push('javascript')

     <script>
        $(document).ready(function(){

        $('#width-height').hide();
        $('#initial_image').hide();
        $('#mask_image').hide();
        $('#fetch_image').hide();
         $("select").change(function(){
            var optionValue = $('#endpoint').val();
            $("#buttonOption").show();
             $('#more').show();
             $("#mainForm").show();
             $('#negative_prompt').show();
             $("#generate").hide();

             if (optionValue == "") {
                $('#width-height').hide();
                $('#initial_image').hide();
                $('#mask_image').hide();
                $('#fetch_image').hide();
                $("#generate").hide();
             }

            if (optionValue == 'image_to_image') {
                $('#mask_image').hide();
                $('#initial_image').css('display', 'flex');
                $('#fetch_image').hide();
            }

            if (optionValue == 'text_to_image') {
                $('#initial_image').hide();
                 $('#mask_image').hide();
                 $('#fetch_image').hide();
                 $("#generate").hide();
            }

            if (optionValue == 'inpaint') {
                $('#mask_image').css('display', 'flex');
                $('#initial_image').css('display', 'flex');
                $('#fetch_image').hide();
                $("#buttonOption").show();
                $("#generate").hide();
            }

            if (optionValue == "fetch_endpoint") {
                 $('#fetch_image').css('display', 'flex');
                 $('#initial_image').hide();
                 $('#negative_prompt').hide();
                 $('#mask_image').hide();
                 $("#buttonOption").hide();
                 $("#mainForm").hide();
                 $("#generate").css('display','flex');


            }

    }).change();
});
     </script>

     <script>

     $(document).ready(function(){

        $('#width-height').hide();

          $("#buttonOption").click(function(){

            if ($("#buttonOption").html() == "Less Option") {
             $('#width-height').hide();
             $("#buttonOption").html("More Option");
           }else{
            $('#width-height').css('display', 'flex');
            $("#buttonOption").html("Less Option");
           }

     });
 });
     </script>

<script type="text/javascript">

    $("#submitForm").click(function(e){

        e.preventDefault();

       var  _token = $("input[name='_token']").val()

        var payload  = {
            _token: _token,
             key : $("#key").val(),
             prompt : $("#prompt").val() == "" ? "Man on the moon, ultra hd selfie" : $("#prompt").val(),
             channel : "stablediffusion",
             negative_prompt : $("#negative_prompt").val(),
             endpoint: $("#endpoint").val(),
             width:$("#width").val() == "" ? 512 : $("#width").val(),
             height: $("#height").val() == "" ? 512 : $("#height").val() ,
             mask_image : $("#masked_image").val(),
             init_image : $("#init_image").val(),
             response_id : $("input[name='response_id']").val()
        };

        document.getElementById('fullscreenLoaderMessage').innerText = 'Generating...';
        document.getElementById('fullscreenLoader').classList.remove('hidden');

        $.ajax({
           type:'POST',
           url:"{{ route('stablediffusion') }}",
           data: payload,
           success:function(response){

            document.getElementById('fullscreenLoader').classList.add('hidden');

                if(response.status == "processing"){

                 setTimeout(function() {
                  popToast("success", response.message);

                }, 60);

                }
                if (response.status == "success") {

                    $("#resultData").css('display','flex');

                    var imageTag = '<img class="rounded-xl" src=" '+response.image+' " width=" ' +response.width+ ' " height="'+ response.height+' ">';

                    $("#resultData").append(imageTag);

                    setTimeout(function() {
                      popToast("success", response.message);
                       }, 60);

                }
           },
           error: function(response, status, message) {
            document.getElementById('fullscreenLoader').classList.add('hidden');
            var message = response.responseJSON.message == "Unauthenticated." ? "You need to be logged in to use the playground" : response.responseJSON.message ;
                setTimeout(function() {
                  popToast("danger", message);
                }, 5);

                errorTabForSubscription(message)
          },

        });

    });

</script>

<script type="text/javascript">

    $("#submitFormTwo").click(function(e){

        e.preventDefault();

       var  _token = $("input[name='_token']").val()
        var payload  = {
            _token: _token,
             key : $("#key").val(),
             response_id : $("input[name='response_id']").val(),
             endpoint: $("#endpoint").val(),
        };

        document.getElementById('subscriptionError').classList.add('hidden')
        document.getElementById('fullscreenLoaderMessage').innerText = 'Generating...';
        document.getElementById('fullscreenLoader').classList.remove('hidden');

        $.ajax({
           type:'POST',
           url:"{{ route('stablediffusion') }}",
           data: payload,
           success:function(response){

            document.getElementById('fullscreenLoader').classList.add('hidden');

                if(response.status == "processing"){

                 setTimeout(function() {
                  popToast("success", response.message);

                }, 60);

                }
                if (response.status == "success") {

                    $("#resultData").css('display','flex');

                    var imageTag = '<img class="rounded-xl" src=" '+response.image+' " width=" ' +response.width+ ' " height="'+ response.height+' ">';

                    $("#resultData").append(imageTag);

                    setTimeout(function() {
                      popToast("success", response.message);
                       }, 60);

                }
           },
           error: function(response, status, message) {
            document.getElementById('fullscreenLoader').classList.add('hidden');

            var message = response.responseJSON.message ;

            console.log(message);

                setTimeout(function() {
                  popToast("danger", message);
                }, 5);
                errorTabForSubscription(message)
          },

        });

    });

</script>

<script>
    function errorTabForSubscription(message){
       if (message == "Your monthly limit exceeded, upgrade subscription now on stablediffusionapi.com") {
           document.getElementById('subscriptionError').classList.remove('hidden');
       }
   }
</script>


@endpush
