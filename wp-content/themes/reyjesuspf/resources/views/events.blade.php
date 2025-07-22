{{--
    Template Name: events page
--}}

@extends('layouts.app')

@section('content')
    @include('partials.Eventosspage.eventospage.eventos')
    @include('partials.NuestraIglesiapage.aportepage.aporte')
    @include('partials.Homepage.contact.contactn')
{{--   
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection