@php

$itemCategories = array(
    "All" => "all",
    "Hats" => "hat",
    "Tools" => "tool",
    "T-Shirts" => "tshirt",
    "Faces" => "face",
    "Shirts" => "shirt",
    "Pants" => "pants",
    "Heads" => "head"
);

@endphp
<div class="col-10-12 push-1-12 shop-bar">
    <div class="col-9-12">
        <div class="card">
            <div class="content">
                <div class="overflow-auto">
                    <div class="col-8-12">
                        <input id="search-bar" type="text" style="height:41px;" class="input rigid width-100" placeholder="Search">
                    </div>
                    <div class="col-2-12 mobile-col-1-2">
                        <button class="button blue mobile-fill" id="search" style="font-size:15px;">Search</button>
                    </div>
                    <div class="col-2-12 mobile-col-1-2">
                        <a class="button green mobile-fill" href="/shop/create" style="font-size:15px;">Create</a>
                    </div>
                </div>
                <hr>
                <div class="shop-categories">
                    @foreach ($itemCategories as $display => $value)
                    <div class="category {{ ($value == 'all') ? 'active' : '' }}">
                        <a data-item-type="{{ $value }}">
                            {{ $display }}
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-3-12">
        <div class="card">
            <div class="content">
                <div class="select-color-text mb1">Advanced Sort</div>
                <hr style="margin-top:-3px;">
                <select id="shopSort" class="select width-100">
                    <option data-sort="updated">Recently Updated</option>
                    <option data-sort="newest">Newest First</option>
                    <option data-sort="oldest">Oldest First</option>
                    <option data-sort="expensive">Most Expensive</option>
                    <option data-sort="inexpensive">Least Expensive</option>
                </select>
            </div>
        </div>
    </div>
</div>
