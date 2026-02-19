@extends('layouts.universal')

@section('container2')
    {{-- (Optional) Bisa kosongkan kalau tidak mau header --}}
@endsection

@section('container3')
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            /* Hindari scrollbar ganda */
        }

        .fullscreen-pdf {
            width: 100vw;
            height: 100vh;
            border: none;
        }
    </style>

    <iframe src="{{ $fileUrl }}" class="fullscreen-pdf" loading="lazy"></iframe>
@endsection
