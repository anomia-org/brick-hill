@extends('layouts.header')

@section('title', 'Rules of Conduct')

@section('content')
<style>
    html {
        scroll-behavior: smooth;
    }
</style>
<div class="new-theme">
<div class="rules">
    <div class="left no-mobile flex flex-column border-right">
        <div class="header-2">CONTENTS</div>
        <div class="section">
            <a href="#1">1. How the rules of conduct works</a>
            <div class="subsection flex flex-column">
                <a href="#1.1">1.1 Appeals</a>
                <a href="#1.2">1.2 How we moderate</a>
            </div>
        </div>
        <div class="section">
            <a href="#2">2. Prohibited content</a>
            <div class="subsection flex flex-column">
                <a href="#2.1">2.1 Immediate account closure</a>
                <a href="#2.2">2.2 Promoting, sharing, or discussing illegal media/activity or violence</a>
                <a href="#2.3">2.3 Impersonation or spreading false/misleading information</a>
                <a href="#2.4">2.4 Discussions, promotion, or encouragement of harmful/distressing behavior</a>
                <a href="#2.5">2.5 Seeking, engaging in, or promoting relationships or sexually explicit content/activity</a>
                <a href="#2.6">2.6 Visual or written depictions of grotesque or obscene content</a>
            </div>
        </div>
        <div class="section">
            <a href="#3">3. Harassment</a>
            <div class="subsection flex flex-column">
                <a href="#3.1">3.1 Discriminatory or derogatory behavior</a>
                <a href="#3.2">3.2 Slurs or pejorative language</a>
                <a href="#3.3">3.3 Bullying or harassing users</a>
                <a href="#3.4">3.4 Sensitive topics</a>
            </div>
        </div>
        <div class="section">
            <a href="#4">4. Virtual goods and off-site activity</a>
            <div class="subsection flex flex-column">
                <a href="#4.1">4.1 Off-site activity</a>
                <a href="#4.2">4.2 Blackmail, extortion, exploiting, and scamming</a>
                <a href="#4.3">4.3 Farming virtual currency and hoarding trade-able goods</a>
            </div>
        </div>
        <div class="section">
            <a href="#5">5. Using the services</a>
            <div class="subsection flex flex-column">
                <a href="#5.1">5.1 Usernames</a>
                <a href="#5.2">5.2 Spamming</a>
                <a href="#5.3">5.3 Trolling</a>
                <a href="#5.4">5.4 Advertising</a>
                <a href="#5.5">5.5 Misusing services and evading restrictions</a>
                <a href="#5.6">5.6 Personal information</a>
                <a href="#5.7">5.7 Art theft, plagiarism, and copyrighted content</a>
            </div>
        </div>
    </div>
    <div class="right flex flex-column">
        <div class="header" id="1">1. How the rules of conduct work</div>
        <p>
        The rules of conduct serve as a non-exhaustive list of useful guides for how you may use our platform as well as other resources that we provide through our platform and services. By using our platform and services you are agreeing to abide by our rules of conduct and our terms of service (from here onward referred to solely as the “terms of use”). Registered users will be notified of major changes to this document, however it is your own responsibility to ensure that you have read and understand the latest iteration of the terms of use.
        <br><br>A breach of any of the rules listed here will result in punitive action. In most cases these will be in the form of a warning, however in some cases certain services offered by our platform may be limited or disabled either permanently or for an extended period of time. If we determine that you have consistently or severely violated our terms of use then your offending account and all current and future accounts that we recognize as owned by you will be permanently closed.
        <br><br>Our staff team ultimately reserves the right to issue any punitive action, both after a team discussion and at their own discretion.<br><br>If any of the information in this document is unclear or you have any inquiries, please contact a moderator and they will be happy to assist you. You can find an introduction to each of our staff members on this page.
        </p>
        <div class="header-2" id="1.1">1.1 Appeals</div>
        <p>If you wish to appeal an account closure, please email <a href="mailto:help@brick-hill.com">help@brick-hill.com</a></p>
        <div class="header-2" id="1.2">1.2 How we moderate</div>
        <p>
        Warning:<br>
        This is our most common form of action, and is issued to users who have violated our terms of use in some way. After a warning, you may reactivate your account immediately.
        <br><br>
        Account restrictions:<br>
        These are temporary restrictions from certain services we may provide on our platform. A mute is usually provided with a warning to explain why it is being given, and is intended to prevent malicious behavior and de-escalate situations that may arise.
        <br><br>
        Account closure:<br>
        This action is applied to users who have repeatedly or severely violated our terms of use. Please be aware that on our platform while you may have multiple accounts, you are still considered to be one user and so closures occur across every current and future account that we recognize as being owned by you.
        </p>
        <div class="header" id="2">2. Prohibited content</div>
        <p>The content outlined below is not tolerated on our platform and action will be taken against those who we determine have violated any of the terms listed here.</p>
        <div class="header-2" id="2.1">2.1 Immediate account closure</div>
        <div class="list">
        <p>• Posting, sharing/linking, discussing pornographic content or gore.</p>
        <p>• Encouraging, glorifying, or wishing harm against yourself or others.</p>
        <p>• Persistent harassment or discrimination.</p>
        <p>• Defamation or belittling of sensitive topics or tragic events.</p>
        <p>• Supporting, engaging in, or encouraging pedophilia/ephebophilia or zoophilia.</p>
        <p>• Any attempts to track, identify, threaten, blackmail, or harm users and any forms of “doxing.”</p>
        <p>• Any attempts to exploit the platform, services we provide, or users on our platform.</p>
        <p>• Any attempts to engage in third-party resources or activity that uses our services but is not monitored by our platform.</p>
        <p>• Evading punitive action taken on other accounts that you own.</p>
        <p>• Excessive or continuous violations of the terms of use.</p>
        </div>
        <div class="header-2" id="2.2">2.2 Promoting, sharing, or discussing illegal media/activity or violence</div>
        <p>Any discussion of illegal activities is prohibited on our platform, which includes encouragement, glorification, graphic depictions, giving advice, or sharing experiences of or related to illegal activity. This also means that activity related to pirated media is prohibited, and any violence or violent depictions are strictly prohibited.</p>
        <div class="header-2" id="2.3">2.3 Impersonation or spreading false/misleading information</div>
        <p>Staff members will never ask for any personal information or identifiable information. As well as this, only use our social media and blog platforms for any news related to our services. Pretending to be staff members or other users and knowingly spreading false or misleading information, especially with malicious intent, is not tolerated on our platform.</p>
        <div class="header-2" id="2.4">2.4 Discussions, promotion, or encouragement of harmful/distressing behavior</div>
        <p>Topics such as suicide, self-harm, eating disorders, and other forms of harmful activity, both physical and mental, are strictly prohibited on our platform.</p>
        <div class="header-2" id="2.5">2.5 Seeking, engaging in, or promoting relationships or sexually explicit content/activity</div>
        <p>Our platform is not a place for romantic or sexual relationships or any activity related to this. Sexually explicit content is prohibited under all circumstances, especially when it involves other community members</p>
        <div class="header-2" id="2.6">2.6 Visual or written depictions of grotesque or obscene content</div>
        <p>Posting anything referring to sexual pornographic content both written or visual is not tolerated whatsoever, including but not limited to:</p>
        <div>
            <p>Bestiality/Zoophilia</p>
            <p>Pedophilia/Ephebophilia</p>
            <p>Depictions of genitalia</p>
            <p>Erotic literature</p>
            <p>Erotic role-play</p>
            <p>Nudity</p>
            <p>Fetishism</p>
        </div>
        <p>Likewise, any content deemed disturbing or upsetting is not allowed on our platform. Such as, but not limited to:</p>
        <div>
            <p>Gore</p>
            <p>Bodily fluids</p>
            <p>Injuries</p>
            <p>Infections</p>
            <p>Self-harm</p>
        </div>
        <div class="header" id="3">3. Harassment</div>
        <p>Any harmful or offensive content directed towards specific users is not tolerated. We have sophisticated filters that filter out content determined to be harmful or offensive and so any instances of harassment on our platform will likely have intentionally bypassed these filters and so we will treat these cases very seriously.</p>
        <div class="header-2" id="3.1">3.1 Discriminatory or derogatory behavior</div>
        <p>Discrimination and prejudice is not tolerated on our platform. This includes comments that are threatening, aggressive, or otherwise distressing or prejudice towards demographics and minorities such as (but not limited to):</p>
        <div>
            <p>Gender/Gender identity</p>
            <p>Sexual orientation/Sexuality</p>
            <p>Religion/Spiritual Beliefs</p>
            <p>Race</p>
            <p>Disability</p>
            <p>Ethnicity</p>
            <p>Nationality</p>
            <p>Political status</p>
            <p>Social status/Socioeconomic status</p>
        </div>
        <p>While we permit discussions of some sensitive topics (see <a href="#3.4">Section 3.4</a>) this is not an opportunity or an excuse to discriminate, and you must engage in all discussions with respect.</p>
        <div class="header-2" id="3.2">3.2 Slurs or pejorative language</div>
        <p>The use of slurs is not tolerated on our platform under any circumstance and its use is prohibited. There are no exceptions to this, such as “reclaiming” words, providing objective information, or referring to yourself.</p>
        <div class="header-2" id="3.3">3.3 Bullying or harassing users</div>
        <p>You must be mindful of how you are treating other users at all times. Instances of bullying or harassment may include (but is not limited to) engaging in or encouraging:</p>
        <div>
            <p>Intimidating users</p>
            <p>Aggression towards users</p>
            <p>Targeting users</p>
            <p>Provoking/Baiting users</p>
            <p>Belittling/Mocking users</p>
            <p>Intimidating/Humiliating users</p>
            <p>Insulting users</p>
            <p>Threatening users</p>
            <p>Blackmailing/Extorting users</p>
            <p>Bypassing attempts to block or restrict your contact with any users</p>
            <p>Personal attacks/call-outs</p>
            <p>Other malicious behavior directed at users</p>
        </div>
        <div class="header-2" id="3.4">3.4 Sensitive topics</div>
        <p>Discussion of sensitive topics or subjects on our platform, while permitted, is strictly moderated and should be treated with care. These can be topics that may result in arguments, inflammatory discussions, or may otherwise cause distress and action will be taken if we determine that a discussion is not civil.</p>
        <p>Topics that are prohibited from discussion under any circumstance include (but are not limited to):</p>
        <div>
            <p>Abortion</p>
            <p>Terrorism</p>
            <p>Tragic events</p>
            <p>Discrimination</p>
        </div>
        <div class="header" id="4">4. Virtual goods and off-site activity</div>
        <p>The virtual goods that our platform offers (which includes but is not limited to virtual currency, accessories, goods, services, or accounts) are strictly regulated and controlled by us and as a result you are agreeing to only use and exchange these virtual goods using services that are monitored and permitted by us. “Off-site activity” refers to anything that directs users away from services on our platform in ways that we cannot regulate, moderate, or otherwise control in order to protect user’s safety and virtual goods and does not necessarily have to occur outside of our platform.</p>
        <div class="header-2" id="4.1">4.1 Off-site activity</div>
        <p>Trading, selling, lending, sharing, borrowing, or giving away any virtual goods on our platform through off-site activity or by using third-party resources is strictly prohibited on our platform. We are not responsible for any virtual goods, accounts, or assets that are lost or compromised due to off-site activity.</p>
        <div class="header-2" id="4.2">4.2 Blackmail, extortion, exploiting, and scamming</div>
        <p>Demanding virtual goods, real-world currency or other goods and services, and any other forms of benefit from a user through blackmailing, extortion, threats, intimidation, or other acts of force is not tolerated whatsoever.</p>
        <p>Tricking users into selling or trading their virtual assets in any way is also prohibited under any circumstances. This includes having users revoke ownership of virtual goods or having a user falsely believe that virtual goods have a different intrinsic value than what is assumed.</p>
        <p>Likewise, exploiting or manipulating metrics on our platform to produce disingenuous assumed values of virtual goods or taking advantage of potential flaws or vulnerabilities in our services is prohibited (see <a href="#5.5">Section 5.5</a> for more information).</p>
        <div class="header-2" id="4.3">4.3 Farming virtual currency and hoarding trade-able goods</div>
        <p>All users are awarded daily virtual currency for logging in each day. Exploiting this reward by using other accounts to farm currency is prohibited.</p>
        <p>Using alternative accounts to purchase trade-able goods before their initial sale stock has expired is also prohibited.</p>
        <p>Please see <a href="#5.5">Section 5.5</a> for more information.</p>
        <div class="header" id="5">5. Using the services</div>
        <p>You are expected to behave in a respectful and friendly manner on our platform. Attempts to disrupt the experience for other users are not tolerated.</p>
        <div class="header-2" id="5.1">5.1 Usernames</div>
        <p>The username that you register with and any changes to your username must abide by all of the terms of use as well as additional restrictions, such as (but not limited to):</p>
        <div>
            <p>No profanity or inappropriate language</p>
            <p>No references sexual activity</p>
            <p>No references to illegal activity</p>
            <p>No insults or slurs</p>
            <p>No identifying personal information</p>
            <p>No references to sensitive topics (everything under <a href="#3.4">Section 3.4</a> is prohibited)</p>
        </div>
        <div class="header-2" id="5.2">5.2 Spamming</div>
        <p>Posting repetitive, meaningless, or low-value content to the platform through our services is considered spam. This includes large blocks of text, chain and group spam, low-effort replies or posts, posting in the incorrect areas, and encouraging others to spam.</p>
        <div class="header-2" id="5.3">5.3 Trolling</div>
        <p>Using our services to intentionally disrupt users or cause discourse is not tolerated. Tactics such as flaming, derailing, sockpuppeting, baiting, and posting content to shock or disturb users are prohibited.</p>
        <div class="header-2" id="5.4">5.4 Advertising</div>
        <p>You may discuss other sites, products, games, and platforms. However you may not use our platform to advertise or promote content that is not regulated or monitored by us. As well as this, if you are advertising content that is on our platform then it must be done with consideration to other rules (such as <a href="#5.2">Section 5.2</a>).</p>
        <div class="header-2" id="5.5">5.5 Misusing services and evading restrictions</div>
        <p>When using our service across multiple accounts you are still considered to be one user. Because of this, using alternative accounts to violate the terms of service, or to use exploits or gain an unfair advantage over other users, or to evade punishment on other accounts is strictly prohibited on our platform.</p>
        <p>Using the services that our platform provides to trick or encourage users into violating the terms of use or to bypass or evade account restrictions such as by trading virtual goods to other accounts in anticipation of a warning, a mute, or an account closure is also not tolerated.</p>
        <p>Abusing the report system and attempting to bypass blocks or filters on our platform is also prohibited.</p>
        <div class="header-2" id="5.6">5.6 Personal information</div>
        <p>You can share non-identifiable information at your own risk but it is up to our own discretion as to what is identifiable. Generally, you are able to share:</p>
        <div>
            <p>Forename</p>
            <p>Birthdate/Age</p>
            <p>Country of residence</p>
        </div>
        <p>With that said, do not share your personal information and likewise do not ask for or encourage other users to share their personal information. Identifiable information includes (but is not limited to):</p>
        <div>
            <p>Where you live</p>
            <p>Phone number</p>
            <p>Monetary information</p>
            <p>Where you work or go to school</p>
            <p>Full Name</p>
            <p>Email address</p>
            <p>Account information</p>
            <p>Pictures of you</p>
        </div>
        <p>Posting pictures of you or others is not tolerated on our platform whatsoever. This includes yourself, friends, family members, people you’ve met online, and celebrities or other popular or well-known individuals.</p>
        <p>Attempts to track or find other users for whatever reason is also strictly prohibited. If you believe a user is in danger or is in need of urgent assistance then please contact a moderator and authorities if appropriate.</p>
        <div class="header-2" id="5.7">5.7 Art theft, plagiarism, and copyrighted content</div>
        <p>Anything that you upload to our platform or use on our services you are confirming that you have explicit permission to use this content and/or that you are the sole creator of said material and that you are allowed to promote, sell, or upload the work to our platform for yourself and anybody else that is permitted to use it.</p>
        <p>Stealing or miscrediting artwork or using material without permission is not tolerated whatsoever. This includes the unauthorized use of copyrighted or trademarked content or other intellectual property. For example, characters from cartoons or video games cannot be uploaded to our platform without explicit and prior approval from the intellectual property owners.</p>
        <p>We attempt to identify and remove plagiarized, stolen, and copyrighted content however you are assuming sole responsibility for the content that you upload to our platform and with that you are agreeing to direct responsibility to any of the consequences of uploading any content that may be infringing.</p>
    </div>
</div>
</div>
@endSection
