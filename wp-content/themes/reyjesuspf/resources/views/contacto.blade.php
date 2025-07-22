{{--
    Template Name: contacto page
--}}

@extends('layouts.minimal')

@section('content')
    @include('partials.Contactopage.formpage.form')
{{--   
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection