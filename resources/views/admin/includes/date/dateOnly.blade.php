
    @foreach($data as $dat)
        <p style="border: 1px solid gray; padding:5px 10px;">
            <a href="{{ route($url, $dat['original']) }}">{{ $dat['date'] }}</a>
        </p>
    @endforeach
