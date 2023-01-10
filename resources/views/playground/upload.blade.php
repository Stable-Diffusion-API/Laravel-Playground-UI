
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
                        Drop files Here OR
                        <span id="browse" class="text-blue-600 underline">
                            <label for="file-ip-1">Browse</label>
                            <input type="file" class="hidden" id="file-ip-1" accept="image/*" onchange="showPreview(event);">
                    </span>
                </span>


            </label>
            <div class="flex flex-col justify-center items-center" id="gallery"></div>
            <div class="flex flex-col justify-center items-center p-2"><img class="hidden w-1/6"  id="file-ip-1-preview"></div>

        </div>

        <div id="generate"  class="w-full flex my-4 items-center justify-center mx-auto mb-3 space-y-4  sm:space-y-0">
            <button type="submit" id="submitForm" class="bg-blue-600 py-5 px-9 text-sm font-medium text-center text-white rounded-2xl border cursor-pointer">Upload</button>
            <button type="button" id="submitFormTwo" class="hidden bg-blue-600 py-5 px-9 text-sm font-medium text-center text-white rounded-2xl border cursor-pointer">Upload</button>
         </div>
    </form>

</div>



@push('javascript')


<script type="text/javascript">



    $("#submitForm").click(function(e){

        e.preventDefault();

        var payload = {};

        var  _token = $("input[name='_token']").val();


        var file = document.querySelector('input[type=file]')['files'][0];

        if (file == undefined) {
            setTimeout(function() {
                  popToast("danger", "kindly add an image");
          }, 5);
        }

        var base64String = "";

        var reader = new FileReader();

        reader.onload = function () {
            base64String = reader.result;
            imageBase64Stringsep = base64String;

            var image = base64String;

             payload  = {
            _token: _token,
             image: base64String,
             crop:true
           };

           document.getElementById('fullscreenLoaderMessage').innerText = 'Loading...';
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

                            document.getElementById("file-ip-1-preview").classList.add("hidden");

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

        }
        reader.readAsDataURL(file);


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
    // console.log(file);
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

            var  _token = $("input[name='_token']").val();

            document.getElementById('gallery').appendChild(img)

            document.getElementById('submitForm').classList.add("hidden");
            document.getElementById('submitFormTwo').classList.remove("hidden");

            $("#submitFormTwo").click(function(){

            var payload  = { _token: _token, image: reader.result, crop:true };

                    document.getElementById('fullscreenLoaderMessage').innerText = 'Loading...';
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

        }
   }

   function showPreview(event){
  if(event.target.files.length > 0){
    var src = URL.createObjectURL(event.target.files[0]);
    var preview = document.getElementById("file-ip-1-preview");
    preview.src = src;
    preview.style.display = "block";
  }
}

</script>

@endpush
