{{--
    Template Name: Siembra page
--}}

@extends('layouts.minimal')

@section('content')
    @include('partials.Siembrapage.Siembra')
{{--   
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection