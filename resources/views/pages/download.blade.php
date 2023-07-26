@extends('layouts.header')

@noAds

@section('title', 'Download')

@section('content-no-grid')
<style>
    body, html {
        background-color: #3C3C3C !important;
    }
</style>
<div class="download-page">
    <div class="main-holder grid holder legacy">
        <div style="margin-bottom:10px;">
            <div class="large-text bold">LEGACY DOWNLOAD</div>
            <a href="{{ config('site.storage.domain') }}/downloads/BrickHillSetup.exe" class="download" style="width:100%;">
                <button class="orange">
                    <h1 style="margin:0.6em 0.8em">Download</h1>
                </button>
                <div class="small-text dark-gray-text">V0.3, 13.58MB</div>
            </a>
        </div>
        <a href="https://brickhill.gitlab.io/open-source/node-hill/" class="download" style="margin-top:30px;">
            <div class="large-text bold">SERVER</div>
            <button class="nh-button">
                <h1 style="margin:0.6em 0.8em">NODE-HILL</h1>
            </button>
            <div class="small-text dark-gray-text">V11.0.3, 492KB</div>
        </a>
        <div class="no-mobile splash shuttle"></div>
    </div>
    <div class="holder experimental">
        <div class="main-holder grid">
            <div class="col-1-2">
                <div class="large-text bold">EXPERIMENTAL BUILD</div>
                If you want to try something new, the beta release of the next Brick Hill Workshop is now open to public testers!
                Keep scrolling to find out more about what's been packed into this build, or dive into it right now by paying for access below.
                <br><br>
                Rest assured; all of what you spend on beta access goes directly back into funding the development of the client and workshop.
                <br><br>
                If you can't afford beta access - don't worry! The finished game will be completely free to play in the future.
            </div>
            <div class="tile2"></div>
            <div class="mobile-col-1-2" style="float:none;">
                <div class="tile4"></div>
            </div>
            
            <download-client 
                id="downloadclient-v"
                latest="{{ $latestVersion }}"
                :authed="{{ Auth::check() ? "true" : "false" }}"
                :has-bought="{{ Auth::user()?->clientAccess()->exists() || Auth::user()?->is_beta_tester ? "true" : "false" }}"
            ></download-client>
        </div>
    </div>
    <div class="holder blogs">
        <div class="main-holder grid">
            <div style="margin-bottom: 125px;">
                <div class="workshop-brick"></div>
                <div class="brick-text">
                    <div class="bold inline" style="font-size:1.7em">Workshop Tester Brick</div>
                    <div>To say thanks for helping us test the beta builds of the new client, you'll also receive the Workshop Beta Brick for your avatar!</div>
                </div>
            </div>
            <div>
                <div class="detailed-info">
                    <div class="performance-upgrades"></div>
                    <div class="bottom-text">
                        <div class="medium-text bold mb2">Performance Upgrades</div>
                        <div class="small-text mb1">You'll no longer be limited by lag when building with thousands of bricks!
                        </div>
                        <div class="creator smaller-text">stanfordlucy.brk</div>
                    </div>
                </div>
                <div class="detailed-info">
                    <div class="mats-surfaces"></div>
                    <div class="bottom-text">
                        <div class="medium-text bold mb2">Materials and Surfaces</div>
                        <div class="small-text mb1">Play around with diverse new mediums when creating your maps.</div>
                        <div class="creator smaller-text">
                            Arctic Research Complex by 
                            <a href="https://www.brick-hill.com/user/484" target="_blank">rowbot</a>
                        </div>
                    </div>
                </div>
                <div class="detailed-info">
                    <div class="intuitive-controls"></div>
                    <div class="bottom-text">
                        <div class="medium-text bold mb2">Intuitive Controls</div>
                        <div class="small-text mb1">Work in a smooth workshop with controls tailored for the best experience.</div>
                        <div class="creator smaller-text">ufocrash.brk</div>
                    </div>
                </div>
                <div class="detailed-info">
                    <div class="new-envs"></div>
                    <div class="bottom-text">
                        <div class="medium-text bold mb2">New Environments</div>
                        <div class="small-text mb1">Tailor everything you make with revamped environment features!</div>
                        <div class="creator smaller-text">
                            spacebuilder's Keep by 
                            <a href="https://www.brick-hill.com/user/41209"target="_blank">Illusionism</a>
                        </div>
                    </div>
                </div>
                <div class="detailed-info">
                    <div class="dynamic-lighting"></div>
                    <div class="bottom-text">
                        <div class="medium-text bold mb2">Dynamic Lighting</div>
                        <div class="small-text mb1">Control the aesthetic you desire with ambience and light sources.</div>
                        <div class="creator smaller-text">Mushroom Caves</div>
                    </div>
                </div>
                <div class="detailed-info">
                    <div class="ongoing-dev"></div>
                    <div class="bottom-text">
                        <div class="medium-text bold mb2">Ongoing Development</div>
                        <div class="small-text mb1">By becoming a tester, you'll influence how we shape the beta build's development.
                        </div>
                        <div class="creator smaller-text">
                            Pirate Ship by 
                            <a href="https://www.brick-hill.com/user/2"target="_blank">spacebuilder</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <a href="https://blog.brick-hill.com/tag/new-client/">
                    <div>Want to stay updated? We've got you covered.</div>
                    <div>Find all blog posts related to the upcoming client's development here.</div>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection