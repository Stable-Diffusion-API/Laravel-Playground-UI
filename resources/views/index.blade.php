@extends('layouts.app')

@section('content')

<section class="container mx-auto p-6">

    <div class="px-8 pt-8 pb-20 mx-auto xl:px-5 max-w-7xl sm:px-6 lg:pt-10 lg:pb-28">

        <div class="py-5 pb-10 mx-auto w-2/3">

            <div class="hidden md:block border-xl border-grey-100 border-2 border-solid rounded-2xl p-2">

                <ul id="menu" class="mt-10 flex md:mt-0 text-sm font-medium text-center text-gray-500 rounded-lg divide-x divide-gray-200 shadow">
                    <li class="w-full">
                        <a href="?channel=stable-diffusion" class="inline-block p-4 w-full {{$channel =="stable-diffusion" ? 'bg-gray-100':'bg-white'}} rounded-l-lg  hover:text-gray-700 hover:bg-gray-50 focus:ring-4 focus:ring-blue-300">Stable Diffusion</a>
                    </li>
                    <li class="w-full">
                        <a href="?channel=dreambooth" class="inline-block p-4 w-full {{$channel =="dreambooth" ? 'bg-gray-100':'bg-white'}} hover:text-gray-700 hover:bg-gray-50 focus:ring-4 focus:ring-blue-300 focus:outline-none">Dreambooth</a>
                    </li>
                    <li class="w-full">
                        <a href="?channel=dreambooth-training" class="inline-block p-4 w-full  {{$channel =="dreambooth-training" ? 'bg-gray-100':'bg-white'}} hover:text-gray-700 hover:bg-gray-50 focus:ring-4 focus:ring-blue-300 focus:outline-none">Dreambooth Training</a>
                    </li>
                    <li class="w-full">
                        <a href="?channel=upload-image" class="inline-block p-4 w-full  {{$channel =="upload" ? 'bg-gray-100':'bg-white'}} hover:text-gray-700 hover:bg-gray-50 focus:ring-4 focus:ring-blue-300 focus:outline-none">Image Upload</a>
                    </li>
                </ul>
            </div>



        </div>



      @if ($channel == "stable-diffusion")
         @include('playground.stable-diffusion')
      @elseif ($channel == "dreambooth")
         @include('playground.dreambooth')
      @elseif ($channel == "dreambooth-training")
         @include('playground.dreambooth-training')
      @elseif ($channel == "upload-image")
      @include('playground.upload')
      @else
        @include('playground.stable-diffusion')
      @endif


            <div id="resultData" class="hidden mx-auto py-8 content-center justify-center">

            </div>
    </div>

    <ul id="menu" class="fixed bottom-0 mx-auto md:hidden bg-white flex text-sm font-medium text-center text-gray-500 rounded-lg divide-x divide-gray-200 shadow w-full">
        <li class="w-full">
            <a href="?channel=stable-diffusion" class="inline-block p-3  {{$channel =="stable-diffusion" ? 'bg-gray-100':'bg-white'}} rounded-l-lg focus:outline-none "><img class="w-10" src="/images/arhive.svg" alt=""></a>
        </li>
        <li class="w-full">
            <a href="?channel=dreambooth" class="inline-block p-3 {{$channel =="dreambooth" ? 'bg-gray-100':'bg-white'}}"><img class="w-10" src="/images/camera.svg" alt=""></a>
        </li>
        <li class="w-full">
            <a href="?channel=dreambooth-training" class="inline-block p-3  {{$channel =="dreambooth-training" ? 'bg-gray-100':'bg-white'}}"><img class="w-10" src="/images/compass.svg" alt=""></a>
        </li>
        <li class="w-full">
            <a href="?channel=upload-image" class="inline-block p-3  {{$channel =="upload-image" ? 'bg-gray-100':'bg-white'}}"><img class="w-10" src="/images/history.svg" alt=""></a>
        </li>

    </ul>

</section>
@endsection
