{{--
    Template Name: login page
--}}

@extends('layouts.minimal')

@section('content')
    @include('partials.loginpage.login')
{{--   
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection
