@extends('layouts.app')

@section('content')
    @include('partials.Eventosspage.eventospage.eventos')
    @include('partials.Seccioneventospage.unetepage.unete')
    @include('partials.Homepage.contact.contactn')
{{--   
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection