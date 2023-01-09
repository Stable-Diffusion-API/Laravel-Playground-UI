
@push('css')
    <style>
        #gallery {
  margin-top: 10px;
  flex-direction: column;
}
#gallery img {
  width: 150px;
  margin-bottom: 10px;
  margin-right: 10px;
  vertical-align: middle;
}
    </style>
@endpush
<h3 class="p-3 text-2xl text-center">Upload </h3>

<div class="sm:text-center w-full">

    <form method="post">
        @csrf
        <div id="drop-area" class="bg-white border-2 border-gray-300 border-dashed rounded-md appearance-none cursor-pointer hover:border-gray-400 focus:outline-none">

            <label class="flex justify-center w-full px-4 transition ">
                <span class="flex items-center space-x-2 py-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <span class="font-medium text-gray-600">
                        Drop files to Attach,
                        <span id="browse" class="text-blue-600 underline">browse</span>
                    </span>
                </span>
                <input accept="image/*" id="image" type="file" name="file_upload" class="hidden">
                <input type="hidden" id="imageData" value="">
            </label>
            <div class="flex flex-col justify-center items-center" id="gallery"></div>

        </div>

        <div id="generate"  class="w-full my-4 items-center justify-center mx-auto mb-3 space-y-4  sm:space-y-0">
            <button type="submit" id="submitForm" class="bg-blue-600 py-5 px-9 text-sm font-medium text-center text-white rounded-2xl border cursor-pointer">Upload</button>
         </div>
    </form>

</div>






@push('javascript')


<script type="text/javascript">

    $("#submitForm").click(function(e){

        e.preventDefault();

       var  _token = $("input[name='_token']").val()

        var payload  = {
            _token: _token,
             image: $("#image").val(),
             crop:true
        };
        console.log($("#imageData").val());

        document.getElementById('fullscreenLoaderMessage').innerText = 'Generating...';
        document.getElementById('fullscreenLoader').classList.remove('hidden');

        $.ajax({
           type:'POST',
           url:"{{ route('upload-image') }}",
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

                errorTabForSubscription(message)
          },

        });

    });

</script>

<script>

    let dropArea = document.getElementById('drop-area')
    let browse = document.getElementById('browse')

    ;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
         dropArea.addEventListener(eventName, preventDefaults, false)
    })

    ;['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false)
    })

    ;['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, unhighlight, false)
    })

    dropArea.addEventListener('drop', handleDrop, false)

  function handleDrop(e) {
    let dt = e.dataTransfer
    let files = dt.files
    file = files[0]
    console.log(file);
    // ([...files]).forEach(uploadFile)
    previewFile(file)
      }

    function highlight(e) {
    dropArea.classList.add('bg-gray-100')
    }

    function unhighlight(e) {
    dropArea.classList.remove('bg-gray-100')
    }

    function preventDefaults (e) {
        e.preventDefault()
        e.stopPropagation()
    }

    function previewFile(file) {
        let reader = new FileReader()
        reader.readAsDataURL(file)
        reader.onloadend = function() {
            let img = document.createElement('img')
            img.src = reader.result

            document.getElementById('imageData').value == reader.result;
            document.getElementById('gallery').appendChild(img)
        }
   }

   browse.addEventListener('click', function(){

    const input = document.getElementById("image").val();

        console.log(input);

    input.addEventListener("change", function() {
        const file = input.files
        console.log(file);

     })

   })


</script>

@endpush
