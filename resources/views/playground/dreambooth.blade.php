<h3 class="p-3 text-2xl text-center">Dreambooth</h3>

<div class="sm:text-center w-full">

    <form>
        @csrf
        <div id="mainForm" class="w-full items-center mx-auto mb-3 space-y-4  sm:flex sm:space-y-0">
            <div class="w-full">
                <input class="p-5 pl-10 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 sm:rounded-none sm:rounded-l-lg focus:ring-primary-500 focus:border-primary-500" placeholder="Enter your text description" id="prompt" name="prompt" type="text" required="">
            </div>
            <div class="w-full lg:w-60 md:w-0">
                <button type="submit" id="submitForm" class="bg-blue-600 py-5 px-5 text-sm font-medium text-center text-white rounded-lg border cursor-pointer sm:rounded-none sm:rounded-r-lg content-between w-full">Generate</button>
            </div>
        </div>


</div>

<div class="flex flex-col space-y-3 md:space-y-0 md:flex-row md:pace-x-3 mx-auto w-full">

        <input type="text"  id="fetch_image" name="response_id" class="hidden p-3 w-full rounded-xl px-6 text-sm border-gray-300 focus:border-blue-100" placeholder="Enter Response ID">
        <input type="text" id="negative_prompt" name="negative_prompt" class="p-3 w-full md:w-2/3 md:py-3 my-3 md:my-0 rounded-xl md:px-6 border-gray-300 focus:border-blue-100" placeholder="Negative Prompt">
    <select name="model_id" id="model_id" class="p-3 w-full mx-3 rounded-xl md:w-1/3 border-gray-300 focus:border-blue-100" >
        <option value="">Select Model ID</option>
    </select>

        <select name="endpoint" id="endpoint" class="p-3 w-full mx-3 md:w-1/3 rounded-xl border-gray-300 focus:border-blue-100" id="">
            <option class="p-5" value="">Select Endpoint</option>
            <option class="p-5" value="text_to_image">Text to Image</option>
            <option value="image_to_image">Image to Image</option>
            <option value="inpaint">Inpainting</option>
            <option value="fetch_endpoint">Fetch Queue Image</option>
        </select>

</div>

<div id="width-height" class="hidden space-x-2 my-3 mx-auto justify-center w-full">
    <input type="number" id="width" name="width" class="p-3 w-full   rounded-xl px-6 text-sm border-gray-300 focus:border-blue-100"  placeholder="Set Width">
    <input type="number" id="height" name="height" class="p-3 w-full  rounded-xl px-6 text-sm border-gray-300 focus:border-blue-100"  placeholder="Set Height">
</div>

<div id="mask_image" class="hidden py-1 space-x-2 mx-auto justify-center w-full">
    <input type="url" id="masked_image" name="masked_image" class="p-3 w-full md:my-2 rounded-xl px-6 border-gray-300 focus:border-blue-100"  placeholder="Mask Image URL">
</div>

<div id="initial_image" class="hidden space-x-2 my-2 mx-auto justify-center content-center">
    <input type="url" id="init_image" name="init_image" class="p-3 w-full md:my-0  rounded-xl px-6 text-sm border-gray-300 focus:border-blue-100"  placeholder="Initial Image URL">
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
        $("#generate").hide();
        $("#mainForm").show();


         $("select").change(function(){
            var optionValue = $('#endpoint').val();
             $('#more').show();

             $("#generate").hide();

            if (optionValue == 'image_to_image') {
                $('#mask_image').hide();
                $('#initial_image').show();
                $("#mainForm").show();
                $("#generate").hide();
                $('#negative_prompt').show();
                $('#fetch_image').hide();
            }

            if (optionValue == 'text_to_image') {
                $('#initial_image').hide();
                 $('#mask_image').hide();
                 $("#mainForm").show();
                 $("#generate").hide();
                 $('#negative_prompt').show();
                 $('#fetch_image').hide();
            }

            if (optionValue == 'inpaint') {
                $('#mask_image').show();
                $('#initial_image').show();
                $("#mainForm").show();
                $('#negative_prompt').show();
                $("#generate").hide();
                $('#fetch_image').hide();
            }

            if (optionValue == 'fetch_endpoint') {
                $('#mask_image').hide();
                $('#fetch_image').css('display', 'flex');
                $('#negative_prompt').hide();
                $('#initial_image').hide();
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
             channel : "dreambooth",
             model_id : $("#model_id").val(),
             prompt : $("#prompt").val() == "" ? "Man on the moon, ultra hd selfie" : $("#prompt").val(),
             negative_prompt : $("#negative_prompt").val(),
             endpoint: $("#endpoint").val(),
             width:$("#width").val() == "" ? 512 : $("#width").val(),
             height: $("#height").val() == "" ? 512 : $("#height").val() ,
             mask_image : $("#masked_image").val(),
             init_image : $("#init_image").val(),
             response_id : $("#response_id").val()
        };

        document.getElementById('fullscreenLoaderMessage').innerText = 'Generating...';
        document.getElementById('fullscreenLoader').classList.remove('hidden');

        $.ajax({
           type:'POST',
           url:"{{ route('dreambooth') }}",
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

                    $("#resultData").html(imageTag);

                    setTimeout(function() {
                      popToast("success", response.message);
                       }, 60);


                }
           },
           error: function(response, status, message) {
            document.getElementById('fullscreenLoader').classList.add('hidden');
            var message = response.responseJSON.message ;
                setTimeout(function() {
                  popToast("danger", message);
                }, 5);
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

        document.getElementById('fullscreenLoaderMessage').innerText = 'Generating...';
        document.getElementById('fullscreenLoader').classList.remove('hidden');

        $.ajax({
           type:'POST',
           url:"{{ route('dreambooth') }}",
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
            var message = response.responseJSON.message;
                setTimeout(function() {
                  popToast("danger", message);
                }, 5);


          },

        });

    });

</script>


<script>

$("#model_id").change(function () {

    var  _token = $("input[name='_token']").val()
console.log('wcw');

$.ajax({
    dataType: 'json',
    type: 'POST',
    url: '{{ route("public-models")}}',
    data: {
        _token : _token
    },

    success: function (data) {

         console.log(data);

        if (data) {
            // $("#model_id").empty();
            // $("#model_id").append('<option>Select Model ID</option>');

            $.each(data, function (key, value) {

                $("#model_id").append('<option value="' + value.model_id + '">' + value.model_id +
                    '</option>');
            });
        }
    },

    error: function () {

        console.log('fail');
        // alert("fail");

    }

});

});

</script>


@endpush
