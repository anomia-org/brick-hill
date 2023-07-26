@extends('layouts.header')

@noAds
@noBanner

@section('content-no-grid')
<style>
body {
    background-color: #222226;
}
</style>
<div class="new-theme landing-banner index-top-bar">
    <img class="landing-image-top" />
    <p class="title shadow white-text"> A growing community of {{ Helper::numAbbr($user_count) }} users with a focus on creativity and collaboration. </p>
    <a href="/register">
        <button class="button blue no-overflow">REGISTER NOW</button>
    </a>
    <p class="landing-subtext shadow white-text">
         OR <a class="landing-subtext" href="/login">LOG IN</a>
    </p>
</div>
@endsection

@section('content')
<div class="landing-content-section">
    <div class="section-content-left">
        <h1 class="section-content-title">BUILDING A COMMUNITY</h1>
        <p class="section-content-text">Bringing people from all over the world together to create, collaborate, and play together built on values that put the player first. Join our community and help shape its future and your place in it!</p>
    </div>
    <div class="section-content-right section-image">
        <img class="section-image sandcastle">
    </div>
</div>
<div class="landing-content-section" style="margin-bottom: 60px;">
    <div class="section-content-left section-image">
        <img class="section-image workshop">
    </div>
    <div class="section-content-right">
        <h1 class="section-content-title">A RISING PLATFORM</h1>
        <p class="section-content-text">The biggest up-and-coming platform fueled by its community and a commitment to provide the best quality for free. We're constantly pushing out new updates, new features, and growing our audience using creative and talented minds who believe in our platform.</p>
    </div>
</div>
@endsection