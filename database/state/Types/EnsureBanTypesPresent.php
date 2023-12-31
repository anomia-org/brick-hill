<?php

namespace Database\State\Types;

use Database\State\State;

class EnsureBanTypesPresent extends State
{
    /**
     * Name of table to check
     * 
     * @var string
     */
    protected string $table = 'ban_types';

    /**
     * Array of columns that the $rows array must use
     * 
     * @var array<string>
     */
    protected array $columns = [
        "id", "name", "default_note", "default_length"
    ];

    /**
     * Array of rows that should exist
     * 
     * @var array
     */
    protected array $rows = [
        ['id' => 1, 'name' => 'Warning', 'default_note' => NULL, 'default_length' => 0],
        ['id' => 2, 'name' => 'Uploading content with faces', 'default_note' => 'Do not upload images containing faces.', 'default_length' => 0],
        ['id' => 3, 'name' => 'Uploading incorrect templates', 'default_note' => 'Use the Brick Hill clothing template to create your shirts and pants.', 'default_length' => 0],
        ['id' => 4, 'name' => 'Uploading inappropriate images', 'default_note' => 'Do not upload inappropriate images.', 'default_length' => 0],
        ['id' => 5, 'name' => 'Uploading inappropriate content', 'default_note' => 'Do not upload inappropriate content.', 'default_length' => 0],
        ['id' => 6, 'name' => 'Excessive inappropriate language', 'default_note' => 'Do not use inappropriate language or swear excessively.', 'default_length' => 60],
        ['id' => 7, 'name' => 'Inappropriate links', 'default_note' => 'Do not post inappropriate links.', 'default_length' => 0],
        ['id' => 8, 'name' => 'Sexual/derogatory language', 'default_note' => 'Do not use sexual or derogatory language.', 'default_length' => 1440],
        ['id' => 9, 'name' => 'Inappropriate username', 'default_note' => 'Do not sign up with or purchase an inappropriate username.', 'default_length' => 37317600],
        ['id' => 10, 'name' => 'False reporting', 'default_note' => 'Do not intentionally report content that does not violate the Terms of Service.', 'default_length' => 0],
        ['id' => 11, 'name' => 'Harassing users', 'default_note' => 'Do not harass or bully other users.', 'default_length' => 1440],
        ['id' => 12, 'name' => 'Spamming', 'default_note' => 'Do not post spam or send unsolicited content to other users.', 'default_length' => 60],
        ['id' => 13, 'name' => 'Posting in the incorrect subforum', 'default_note' => 'Do not post irrelevant content in subject-specific forums.', 'default_length' => 0],
        ['id' => 14, 'name' => 'Necrobumping', 'default_note' => 'Do not post on inactive forum threads without adding valuable content to the discussion.', 'default_length' => 0],
        ['id' => 15, 'name' => 'Exploiting vulnerabilities', 'default_note' => 'Do not exploit glitches or bugs. Correctly reporting vulnerabilities will be rewarded.', 'default_length' => 1440],
        ['id' => 16, 'name' => 'Scamming users', 'default_note' => 'Do not scam other users.', 'default_length' => 10080],
        ['id' => 17, 'name' => 'Exploiting users', 'default_note' => 'Do not exploit other users for your own benefit.', 'default_length' => 10080],
        ['id' => 18, 'name' => 'Purchasing/selling/trading items using third-party resources', 'default_note' => 'Do not purchase, sell or trade items on Brick Hill using third-party resources or methods outside those provided to you.', 'default_length' => 37317600],
        ['id' => 19, 'name' => 'Account transfer', 'default_note' => 'Do not purchase, sell, trade, borrow or lend accounts.', 'default_length' => 37317600],
        ['id' => 20, 'name' => 'Requesting personal information', 'default_note' => 'Do not ask for personal information from other users.', 'default_length' => 720],
        ['id' => 21, 'name' => 'Giving personal information', 'default_note' => 'Do not give your or someone else\'s personal information to other users.', 'default_length' => 720],
        ['id' => 22, 'name' => 'Impersonation', 'default_note' => 'Do not impersonate other users.', 'default_length' => 720],
        ['id' => 23, 'name' => 'Coin farming', 'default_note' => 'Do not use currency generated by alternate accounts for your own gain.', 'default_length' => 10080],
        ['id' => 24, 'name' => 'Alternate account hoarding', 'default_note' => 'Do not use alternate accounts to obtain special items before they sell out.', 'default_length' => 10080],
        ['id' => 25, 'name' => 'Unauthorised purchases', 'default_note' => 'Making payments without permission from the payment method owner is against the law.', 'default_length' => 37317600],
        ['id' => 26, 'name' => 'Payment reversal', 'default_note' => 'Reversing a payment you made after receiving the goods or services you paid for is against the law.', 'default_length' => 37317600],
        ['id' => 27, 'name' => 'Discussing inappropriate topics', 'default_note' => 'Do not discuss topics that are sexual, discriminatory or otherwise inappropriate on Brick Hill.', 'default_length' => 0],
        ['id' => 28, 'name' => 'Discussing distressing topics', 'default_note' => 'Do not discuss expressly personal or distressing topics. If you need assistance, please reach out to a qualified adult.', 'default_length' => 0],
        ['id' => 29, 'name' => 'Unauthorised account access', 'default_note' => 'Do not access accounts other than those which you created.', 'default_length' => 37317600],
        ['id' => 30, 'name' => 'Leaking information', 'default_note' => 'Do not reveal information that is intended to be kept secret.', 'default_length' => 0],
        ['id' => 31, 'name' => 'Account disabled', 'default_note' => 'Account disabled. Contact us at help@brick-hill.com to restore it.', 'default_length' => 37317600],
    ];
}
