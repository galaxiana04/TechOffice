<ul>
    @foreach($notulen->files as $file)
        <li><a href="{{ asset('storage/' . $file->link) }}" target="_blank">{{ $file->filename }}</a></li>
    @endforeach
</ul>