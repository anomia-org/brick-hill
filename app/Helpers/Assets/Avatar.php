<?php

use App\Helpers\Assets;

use App\Models\Item\Item;
use App\Models\Polymorphic\Asset;

/**
 * Class to help with the modification and validation of avatar data
 * 
 * @package App\Helpers\Assets
 */
class Avatar
{
    /**
     * Amount of assets a given avatar can wear by type
     * itemTypeId => count
     * 
     * @var int[]
     */
    protected array $typeCountLimits = [
        1 => 3,
        2 => 1,
        3 => 1,
        4 => 1,
        5 => 1,
        6 => 1,
        7 => 1,
        8 => 1
    ];

    /**
     * Default colors of a new avatar
     * 
     * @var string[]
     */
    protected array $defaultColors = [
        'head' => 'f3b700',
        'torso' => '85ad00',
        'right_arm' => 'f3b700',
        'left_arm' => 'f3b700',
        'right_leg' => '76603f',
        'left_leg' => '76603f'
    ];

    /**
     * Some parts, like the torso and legs have randomized starting values to give distinctions in new user avatars
     * 
     * @var string[][]
     */
    protected array $randomDefaultColors = [
        'torso' => ['c60000', '3292d3', '85ad00', 'e58700'],
        'leg' => ['650013', '1c4399', '1d6a19', '76603f']
    ];

    /**
     * Asset IDs of the randomized t-shirts given to you on account creation
     * 
     * @var int[]
     */
    protected array $startingTShirts = [
        1, 2, 3, 4, 5, 6, 7, 8
    ];

    /**
     * Asset IDs worn in the Avatar
     * 
     * @var array
     */
    public array $assetIds = [];

    /**
     * Colors of the Avatar
     * 
     * @var array
     */
    public array $colors = [];

    public function __construct(array $items = [], array $colors = $this->defaultColors)
    {
        $this->assetIds = $items;
        $this->colors = $colors;
    }

    /**
     * store in db as raw array, denormalize and save or parse to retrieveAvatar array on request
     * can parse to something node-hill can accept also
     * 
     * @param \App\Models\Polymorphic\Asset $asset 
     * @return void 
     */
    public function wearAsset(Asset $asset): void
    {
    }

    /**
     * Modify the stored avatar to wear the randomized colors
     * 
     * @return void 
     */
    public function randomizeColors(): void
    {
        $torsoKey = array_rand($this->randomDefaultColors['torso'], 1);
        $legKey = array_rand($this->randomDefaultColors['leg'], 1);

        $torsoColor = $this->randomDefaultColors['torso'][$torsoKey];
        $legColor = $this->randomDefaultColors['leg'][$legKey];

        $this->colors['torso'] = $torsoColor;
        $this->colors['left_leg'] = $legColor;
        $this->colors['right_leg'] = $legColor;
    }

    /**
     * Modify the stored avatar to wear a randomized starting t-shirt
     * 
     * @return void 
     */
    public function wearStartingTShirt(): void
    {
        $tshirtKey = array_rand($this->startingTShirts, 1);

        $tshirtId = $this->startingTShirts[$tshirtKey];

        $item = Item::findOrFail($tshirtId)->load('latestAsset');

        $this->wearAsset($item->latestAsset);
    }
}
