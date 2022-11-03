<link rel="stylesheet" href="https://unpkg.com/flowbite@1.5.3/dist/flowbite.min.css"/>
<script src="https://unpkg.com/flowbite@1.5.3/dist/flowbite.js"></script>
<section class="h-screen">
    <div
            class="flex xl:justify-center lg:justify-between justify-center items-center flex-wrap h-full g-8"
    >
        <div
                class="grow-0 shrink-1 md:shrink-0 basis-auto xl:w-12/12 lg:w-12/12 md:w-12/12 mb-12 md:mb-0"
        >
            <h1 class="text-4xl font-extrabold tracking-tight leading-none text-gray-900 md:text-5xl lg:text-6xl dark:text-white">
                Login
            </h1>
            @if (Session::has('message'))
                <section class="mt-6 mb-6">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="alert alert-{{ Session::has('message_type') ? Session::get('message_type') : 'info' }}">
                                {{ Session::get('message') }}
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <section class="mt-6 mb-6">
                <em>Active user: {{ (Auth::user() ? Auth::user()->email : "not active")}}</em>
            </section>
            @if ($errors && (sizeof($errors->getBags()) > 0))
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                    <h2 class="text-4xl font-extrabold dark:text-white">Errors</h2>
                    @foreach ($errors->getBags() as $bag)
                        <ul class="space-y-1 max-w-md list-disc list-inside text-gray-500 dark:text-gray-400 mt-6">
                            @foreach ($bag->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            @endif

            @foreach ($decorators as $decorator)
                <section class="mt-6 mb-6">
                    {!! $decorator->render() !!}
                </section>
                <hr/>
            @endforeach
        </div>
    </div>
</section>
