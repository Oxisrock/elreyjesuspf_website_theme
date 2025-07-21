{{--
    Template Name: signup page
--}}

@extends('layouts.minimal')

@section('content')
    @include('partials.signuppage.signup')
{{--   
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection
