<x-mail::message>
# {{ $h1 }}
You have requested to log in into our app.

@foreach ($autoLoginUrls as $autoLoginUrl)
<x-mail::button :url="$autoLoginUrl->create()">Log in now</x-mail::button>
@endforeach
</x-mail::message>
