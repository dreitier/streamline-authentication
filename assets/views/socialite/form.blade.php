<h2 class="text-4xl font-extrabold dark:text-white">Socialite</h2>
<ul class="space-y-1 max-w-md list-disc list-inside text-gray-500 dark:text-gray-400 mt-6">
    @foreach ($socialiteAuthenticationMethods as $method)
        <li><a class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
               href="{{ $method->toHandoverUrl() }}">
                {{ $method->getDriverAdapter()->getName() }}
            </a>
        </li>
    @endforeach
</ul>
