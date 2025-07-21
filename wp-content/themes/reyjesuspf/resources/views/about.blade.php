{{--
    Template Name: About page
--}}

@extends('layouts.app')

@section('content')

    @include('partials.aboutpage.about')
    @include('partials.somospage.somos')
    @include('partials.visionpage.vision')
    @include('partials.aportepage.aporte')
    @include('partials.contact.contactn')
{{--   
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection
