@extends('layouts.app')

@section('content')
    @include('partials.eventospage.eventos')
    @include('partials.unetepage.unete')
    @include('partials.contact.contactn')
{{--   
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection