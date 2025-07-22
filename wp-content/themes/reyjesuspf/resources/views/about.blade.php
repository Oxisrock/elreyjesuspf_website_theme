{{--
    Template Name: About page
--}}

@extends('layouts.app')

@section('content')

    @include('partials.NuestraIglesiapage.aboutpage.about')
    @include('partials.NuestraIglesiapage.somospage.somos')
    @include('partials.NuestraIglesiapage.visionpage.vision')
    @include('partials.NuestraIglesiapage.aportepage.aporte')
    @include('partials.Homepage.contact.contactn')
{{--   
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection
