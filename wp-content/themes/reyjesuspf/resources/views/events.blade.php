{{--
    Template Name: events page
--}}

@extends('layouts.app')

@section('content')
    @include('partials.eventospage.eventos')
    @include('partials.aportepage.aporte')
    @include('partials.contact.contactn')
{{--   
    @while(have_posts()) @php(the_post())
        @include('partials.page-header')
        @include('partials.content-page')
    @endwhile
--}}
@endsection