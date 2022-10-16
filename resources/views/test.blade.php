<table border="1" style="border-collapse: collapse;width: 30%; height: 30%; text-align:center">
    @foreach ($collection->chunk(3) as $chunk)
        <tr>
            @foreach($chunk as $value)
                <td>{{ $value }}</td>
            @endforeach
        </tr>
    @endforeach
</table>
