
<h3 class="p-3 text-2xl text-center">Dreambooth Training</h3>

<div class="sm:text-center w-full">

    <form>
        @csrf

          {{-- Drop Down --}}
          <div class="flex  mx-auto my-3 w-full">
            <select name="endpoint" id="endpoint" class="p-3 w-full rounded-xl border-gray-300 focus:border-blue-100" required>
                <option  value="">Select Endpoints</option>
                <option  value="create_request">Create Training Request</option>
                <option value="train_status">Get Training Status</option>
            </select>
        </div>


            <div class="hidden space-x-2 mx-auto justify-center" id="instance_prompt">
                <input type="text" id="instance_prompt" name="instance_prompt" class="p-3 w-full rounded-xl md:px-6  border-gray-300 focus:border-blue-100"  placeholder="Enter Instance Prompt Text">
            </div>

        <div id="create_request_input" class="hidden space-y-2 md:space-y-0 flex-col md:flex-row md:space-x-3 mx-auto justify-center content-center my-4 w-full">

            <input type="text" id="class_prompt" name="class_prompt" class="p-3 w-full md:w-2/3 md:py-3 my-2 md:my-0  rounded-xl md:px-6 border-gray-300 focus:border-blue-100" placeholder="Class Prompt">


            <select name="training_type" id="training_type" class="p-3 w-full md:mx-2 rounded-xl border-gray-300 focus:border-blue-100">
                <option value="">Select Training Type</option>
                <option value="men">Men</option>
                <option value="women">Women</option>
                <option value="couple">Couple</option>
            </select>

            <select name="base_model_id" id="base_model_id" class="p-3 w-full rounded-xl border-gray-300 focus:border-blue-100" >
                <option class="p-5" value="">Select Model Base ID</option>
                {{-- <option class="p-5" value="">Stable Diffusion V1.5</option> --}}

            </select>

            <button id="rowAdder" type="button" class="hidden py-2 px-4 bg-blue-500 rounded-xl text-white w-full"> Add Image</button>

        </div>

         <div class="hidden py-3 space-x-2 mx-auto justify-center w-full" id="train_status">
            <input type="text" id="training_id" name="training_id" class="p-3 w-full md:py-3 my-1 md:my-0  rounded-xl md:px-6 border-gray-300 focus:border-blue-100"  placeholder="Enter Training ID">
        </div>




        <div class="hidden space-y-3 flex-col" id="create_request">
            <div id="row">
                <div class="relative flex items-center">
                    <input type="url" name="images[]" placeholder="Add image url" class="relative h-10 w-full rounded-md bg-gray-50 pl-20 pr-4 border-gray-300 focus:border-blue-100" />
                    <button id="DeleteRow" class="absolute h-10 w-16 rounded-l-md bg-red-400 text-xs font-semibold text-white">Delete</button>
                  </div>
            </div>
            <div id="newinput" class="space-y-3 flex flex-col"></div>
        </div>

        <div class="flex w-full my-4 items-center justify-center mx-auto mb-3 space-y-4  sm:space-y-0">
           <button type="submit" id="generate" class="hidden bg-blue-600 py-5 px-9 text-sm font-medium text-center text-white rounded-2xl border cursor-pointer">Start Training</button>
        </div>

    </form>
</div>



@push('javascript')
<script type="text/javascript">

    $("#rowAdder").click(function () {
        newRowAdd =
        '<div id="row"> <div class="relative flex items-center">' +
        '<input type="url" placeholder="Add image url" name="images[]" class="relative h-10 w-full rounded-md bg-gray-50 pl-20 pr-4 border-gray-300 focus:border-blue-100">' +
        '<button class="absolute h-10 w-16 rounded-l-md bg-red-400 text-xs font-semibold text-white" id="DeleteRow" type="button">' +
        'Delete</button>' +
         '</div> </div>';

        $('#newinput').append(newRowAdd);
    });

    $("body").on("click", "#DeleteRow", function () {
        $(this).parents("#row").remove();
    })
</script>


<script>
    $(document).ready(function(){

     $("select").change(function(){
        var optionValue = $('#endpoint').val();

           $('#more').show();
           $('#create_request').hide();
           $('#create_request_input').hide();
           $('#model_identify').hide();
           $('#generate').hide();
           $('#instance_prompt').hide();
           $('#train_status').hide();

        if (optionValue == 'create_request') {

            $('#create_request').css('display', 'flex');
            $('#rowAdder').css('display', 'block')
            $('#create_request_input').css('display', 'flex');
            $('#generate').css('display', 'flex');
            $('#instance_prompt').css('display', 'flex');
        }

        if (optionValue == 'train_status') {
           $('#train_status').css('display', 'flex');
           $('#generate').css('display', 'block');
        }

}).change();
});
 </script>

<script type="text/javascript">

    $("#generate").click(function(e){

        e.preventDefault();

       var  _token = $("input[name='_token']").val()

        var payload  = {
            _token: _token,
             key : $("#key").val(),
             channel : "train_in_model",
             instance_prompt : $("input[name='instance_prompt']").val(),
             class_prompt : $("input[name='class_prompt']").val(),
             base_model_id : $("#base_model_id").val(),
             endpoint: $("#endpoint").val(),
             training_type : $("#training_type").val(),
             images : $("input[name='images[]']").val(),
             training_id : $("#training_id").val()
        };

        document.getElementById('fullscreenLoaderMessage').innerText = 'Training...';
        document.getElementById('fullscreenLoader').classList.remove('hidden');

        $.ajax({
           type:'POST',
           url:"{{route('dreambooth-training') }}",
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
            var message = response.responseJSON.message;
                setTimeout(function() {
                  popToast("danger", message);
                }, 5);
          },

        });

    });

</script>

<script>

    $("#base_model_id").change(function () {

    var  _token = $("input[name='_token']").val();

    console.log('wcw');

    $.ajax({
        dataType: 'json',
        type: 'POST',
        url: '{{ route("public-models")}}',
        data: {
            _token : _token
        },

        success: function (data) {

            if (data) {
                // $("#model_id").empty();
                $.each(data, function (key, value) {

                    $("#base_model_id").append('<option value="' + value.model_id + '">' + value.model_id +
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
