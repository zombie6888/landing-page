<?php
include_once dirname(__FILE__) .'/payment_plugins/helper.php';
$orderid = filter_input(INPUT_GET, 'orderid', FILTER_VALIDATE_INT);
$transactionid = filter_input(INPUT_GET, 'transactionid', FILTER_SANITIZE_STRING);

if( $transactionid ) {
    $link = wsp\Functions::getLink() . '/downloads.php?id=' . $transactionid;
    wsp\Functions::sendMail($link, $orderid);
}
$list = wsp\Functions::getLocations();
$select = wsp\HtmlHelper::getCountriesList($list['countries']);
$states = json_encode($list['states']);
$products = json_encode(wsp\Functions::getProducts());
$player = '$f';
$js = <<<JS
var items = $products, orderid = parseInt($orderid), player = $player('player1'), allstates = $states,
    plimg = document.getElementById('player-img'), iframe = document.getElementById('player1');

countdown({
    'day': 15,
    'month': 10,
    'year': 2015,
    'hour': 0,
    'minute': 0,
    'second': 0
},{}, true);

if (orderid) {
    var popup2 = document.getElementById('popup2');
    popup_show(popup2);
    addEvent(document.getElementById('close2'), 'click', function () {
         popup_hide(popup2);
    });
}

player.addEvent('ready', function() {
    player.addEvent('finish', function () {
        iframe.style.display = 'none';
        plimg.style.display = 'block';
    });
});

addEvent(document.getElementById('player-img'), 'click', function() {
    iframe.style.height = plimg.clientHeight + 'px';
    plimg.style.display = 'none';
    iframe.style.display = 'block';
    player.api('play');
}, false);

var buttons = document.getElementsByClassName('pre-order-btn');

for(var i=0;i < buttons.length; i++) {

    addEvent(buttons[i], 'click', function() {
        var price = document.getElementById(this.id+'-price1').innerHTML,
            itemname = items[this.id][0], itemdesc = items[this.id][1],
            popup = document.getElementById('popup');

        popup_show(popup);

        addEvent(document.getElementById('country'), 'change', function() {
           setStateOptions(this);
        });

        addEvent(document.getElementById('close'), 'click', function () {
           popup_hide(popup);
        });

        document.getElementById('itemname').value = itemname;
        document.getElementById('itemdesc').value = itemdesc;
        document.getElementById('price').value = price;
    });
}

var elem = document.getElementById('contactform'), opened = false;

document.getElementById('contact-button').onclick = function() {
  if (opened) {
    Animate(elem, 'right', 800, 0, -320);
    opened = false;
  } else {
    document.getElementById('notice-msg').style.display = 'none';
    Animate(elem, 'right', 800, -320, 0);
    opened = true;
  }
}
JS;
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>The Mystery of Crystal Worlds | Global Sect Music</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link href='https://fonts.googleapis.com/css?family=Aclonica' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Port+Lligat+Slab' rel='stylesheet' type='text/css'>
    <script src="js/innersvg.js"></script>
    <script src="js/functions.js"></script>
    <script src="js/froogaloop2.min.js"></script>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta property="og:title"         content="The Mystery of Crystal Worlds | Global Sect Music"/>
    <meta property="og:description"   content="landing page description"/>
    <meta property="og:image"  content="http://globalsect.ru/lp/images/video-player.jpg"/>
    <meta property="og:url" content="http://globalsect.ru/lp/"/>
    <meta property="og:type" content="article"/>
    <meta name="description" content="Landing page description"/>
    <link rel="image_src" href="http://globalsect.ru/lp/images/video-player.jpg" />
</head>

<body>
<div class="top center">
    <div class="video-player center wrap">
        <iframe id="player1" src="https://player.vimeo.com/video/113893889?api=1&player_id=player1" class="fullsize" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen style="display:none"></iframe>
        <img class="fullsize" id="player-img" src="images/video-player.jpg"/>
    </div>

    <div id="timer" class="center wrap">
        <div class="deadline">
            <div class="separator left"></div>
            <div class="text">
                <svg>
                    <linearGradient id="gradient2" x1="0%" y1="100%" x2="0%" y2="0%">
                        <stop stop-color='rgba(0, 255, 135, 1)' offset='20%'/>
                        <stop stop-color='rgba(0, 255, 255, 1)' offset='80%'/>
                    </linearGradient>
                    <text fill="url(#gradient2)" y="1em" x="50%" text-anchor="middle" class="deadline-svg">
                        Deadline
                    </text>
                </svg>
            </div>
            <div class="separator right"></div>
        </div>
        <div class="deadline-timer center">
            <table style="width:100%;height:100%;table-layout: fixed;">
                <tr>
                    <th>days:</th><th>hours:</th><th>minutes:</th><th>seconds:</th>
                </tr>
                <tr>
                    <td><svg><text fill="url(#gradient2)" y="0.8em" x="50%" text-anchor="middle" id="days" class="timer-svg"></text></svg></td>
                    <td><svg><text fill="url(#gradient2)" y="0.8em" x="50%" text-anchor="middle" id="hours" class="timer-svg"></text></svg></td>
                    <td><svg><text fill="url(#gradient2)" y="0.8em" x="50%" text-anchor="middle" id="minutes" class="timer-svg"></text></svg></td>
                    <td><svg><text fill="url(#gradient2)" y="0.8em" x="50%" text-anchor="middle" id="seconds" class="timer-svg"></text></svg></td>
                </tr>
            </table>
        </div>
    </div>

    <div id="booklets" class="center wrap">
        <div class="booklets-left">
            <img src="images/booklet.png" class="fullsize"/>
        </div>
        <div class="booklets-right">
            <img src="images/logo.png" class="fullsize"/>
            <p>The music label Global Sect Music is glad to present an experimental project based on a fantasy action-poem
            <h3>"The Mystery of Crystal Worlds".</h3>
            <p>Together, with brave wizard Ivan, you will go into an unforgettable adventure, full of magic and dangerous, to get the freedom and find mysterious Green Emerald.</p>
        </div>
    </div>
</div>

<div class="top-digipack center wrap">
    <img class="fullsize" src="images/ball.png">
    <svg>
        <text fill="url(#gradient2)" y="1.1em" x="50%" text-anchor="middle" class="deadline-svg">
            In a special de luxe edition you'll find:
        </text>
    </svg>
    <div class="text-wrapper"></div>
    <p>- A colourful booklet with a topical psychedelic poem <br/>
        - 4 hours of perfect goa trance made by the best musicians of the world on 3 thematic CD's <br/>
        - 6 perfect magic illustrations by master Ahankara Art and other paintors <br/>
    </p>
    <img class="fullsize" src="images/digipack.jpg" />
</div>

<div class="cd-top center wrap">
    <div id="cd1">
        <div class="cd-column">
            <h4>Part I</h4>
            <svg class="cd-text-svg">
                <text fill="url(#gradient2)" y="1.1em" x="50%" text-anchor="middle">
                    The Crystal Words
                </text>
            </svg>
            <p> 1. Psy-H Project - Crystal Worlds<br/>
                2. Sirius - Intergalactic<br/>
                3. Filteria - Nyad of the Infinite Sea<br/>
                4. Celestial Intelligence - Infinity<br/>
                5. Artifact303 - Life Support System<br/>
                6. Mindsphere - Divine Intervention<br/>
                7. Katedra - Radiointerference<br/>
                8. Centavra Project - Capsula<br/>
                9. Zirrex - Born Of Osiris<br/>
            </p>
        </div>
        <div class="cd-column"><img class="fullsize" src="images/cd1-min.png"></div>
        <hr class="h-sep">
    </div>
    <div id="cd2">
        <div class="cd-column">
            <h4>Part II</h4>
            <svg class="cd-text-svg">
                <text fill="url(#gradient2)" y="1.1em" x="50%" text-anchor="middle">
                    Endless Glade
                </text>
            </svg>
            <p>
                1. Psy-H Project - Dark Matter<br/>
                2. Kurandini - Brahamantra<br/>
                3. Liquid Flow - Radiation<br/>
                4. Artifact303 - Future Power<br/>
                5. Morphic Resonance - Moonwalker<br/>
                6. Celestial Intelligence - Crystal Gazer<br/>
                7. Artifact303 - Black Light<br/>
                8. Imba & Somnesia - Astral Travellers<br/>
                9. Alienapia vs. Khetzal - Endless Glade<br/>
            </p>
        </div>
        <div class="cd-column"><img class="fullsize" src="images/cd2-min.png"></div>
        <hr class="h-sep">
    </div>
    <div id="cd3">
        <div class="cd-column">
            <h4>Part III</h4>
            <svg class="cd-text-svg">
                <text fill="url(#gradient2)" y="1.1em" x="50%" text-anchor="middle">
                    Grinnish Emerland
                </text>
            </svg>
            <p>
                1. Celestial Intelligence - Minding of the Universe<br/>
                2. Skarma - Animoniae<br/>
                3. Psy-H Project - Precession of the Universe<br/>
                4. MerrOw - Burning Universe<br/>
                5. Nova Fractal & OXI & E-Mantra - Stargate<br/>
                6. Artifact303 - Tropical Sunset (Trance Dance rmx)<br/>
                7. Psy-H Project - Brahma Samhita<br/>
                8. Mindsphere - Seek for Happines<br/>
                9. Artifact303 - Family of Light<br/>
            </p>
        </div>
        <div class="cd-column"><img class="fullsize" src="images/cd3-min.png"></div>
        <hr class="h-sep">
    </div>
</div>


<div class="digital center wrap">
    <img class="fullsize" src="images/ball.png">
    <svg class="svg-text2">
        <defs>
            <linearGradient id="gradient1" x1="0%" y1="100%" x2="0%" y2="0%">
                <stop stop-color='rgba(0, 255, 130, 1)' offset='0%'/>
                <stop stop-color='rgba(0, 255, 225, 1)' offset='100%'/>
            </linearGradient>
        </defs>
        <text fill="url(#gradient1)" x="50%" text-anchor="middle">
            <tspan x="50%" dy="1.1em" text-anchor="middle">Pre-order now and get a free copy of the additional digital release</tspan>
            <tspan x="50%" dy="1.7em" text-anchor="middle">"Mystery of Crystal Worlds: Prologue"</tspan>
        </text>
    </svg>
    <iframe style="border: 0; width: 475px; height: 911px; display: inline-block; max-width: 100%;" src="http://bandcamp.com/EmbeddedPlayer/album=2957232669/size=large/bgcol=000000/linkcol=00ecfb/transparent=true/" seamless=""></iframe>
    <!-- <img class="fullsize center" src="images/album.jpg"/>
    <object  class="center" data="https://player.soundcloud.com/player.swf?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F2347749&amp;show_comments=true&amp;auto_play=false&amp;show_playcount=true&amp;show_artwork=false&amp;theme_color=000000&amp;color=1A2730" type="application/x-shockwave-flash" height="260" style="max-width: 477px" width="100%">
        <param name="movie" value="https://player.soundcloud.com/player.swf?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F2347749&amp;show_comments=true&amp;auto_play=false&amp;show_playcount=true&amp;show_artwork=false&amp;theme_color=000000&amp;color=1A2730"><param name="allowscriptaccess" value="always">
    </object> -->
</div>

<div class="bundles">
    <img class="fullsize wrap center" src="images/ball.png">
    <div class="bundle b-even">
        <div class="b-wrap wrap">
            <div class="b-cover">
                <img class="fullsize" src="images/bundle-1.png">
            </div>
            <div class="b-content">
                <svg class="svg-title">
                    <text fill="url(#gradient1)" y="90%" dx="0" class="svg-title">Crystal 1</text>
                </svg>
                <hr size="1">
                <p class="b-list">Deluxe edition with the 3CD compilation<br/>
                    <span>Colourful Booklet with the Poem</span><br/>
                    <span>Digital release "Prologue"</span>
                </p>
                <p class="b-note">
                    This offer include Free Shipping. You'll save 15 euro and in addition you receive the link to downlad our digital release.
                </p>
                <div class="price">
                    <svg class="svg-price">
                        <text fill="url(#gradient1)" y="90%" dx="0" id="bundle1-price1" class="svg-price1">30€</text>
                    </svg>
                    <span id="bundle1-price2">(45€)</span>
                </div>
                <button id="bundle1" class="pre-order-btn" >pre-order</button>
            </div>
            <hr class="h-sep">
        </div>
    </div>

    <div class="bundle b-odd">
        <div class="b-wrap wrap">
            <div class="b-cover">
                <img class="fullsize" src="images/bundle-2.png">
            </div>
            <div class="b-content">
                <svg class="svg-title">
                    <text fill="url(#gradient1)" y="90%" dx="0"  class="svg-title">Crystal 2</text>
                </svg>
                <hr size="1">
                <p class="b-list">Deluxe edition with the 3CD compilation<br/>
                    <span>Colourful Booklet with the Poem</span><br/>
                    <span>Digital release "Prologue"</span><br/>
                    <span>T-shirt with UV Effect</span>
                </p>
                <p class="b-note">
                    This offer include Free Shipping. You'll save 15 euro and in addition you receive the link to downlad our digital release.
                </p>
                <div class="price">
                    <span id="bundle1-price2">(80€)</span>
                    <svg class="svg-price">
                        <text fill="url(#gradient1)" y="90%" dx="0" id="bundle2-price1" class="svg-price1">55€</text>
                    </svg>
                    <span id="bundle1-price2">(80€)</span>
                </div>
                <button id="bundle2" class="pre-order-btn">pre-order</button>
            </div>
            <hr class="h-sep">
        </div>
    </div>
    
    <div class="t-shirt">
        <img class="" src="images/t-shirt-min.png">
    </div>

    <div class="bundle b-even">
        <div class="b-wrap wrap">
            <div class="b-cover">
                <img class="fullsize" src="images/bundle-3.png">
            </div>
            <div class="b-content">
                <svg class="svg-title">
                    <text fill="url(#gradient1)" y="90%" dx="0"  class="svg-title">Crystal 3</text>
                </svg>
                <hr size="1">
                <p class="b-list">Deluxe edition with the 3CD compilation<br/>
                    <span>Colourful Booklet with the Poem</span><br/>
                    <span>Digital release "Prologue"</span><br/>
                    <span>Backdrop (1x1,5m size) with UV Effect</span>
                </p>
                <p class="b-note">
                    You'll save 30 euro, this offer also include Free Shipping! In addition to the deluxe edition you will receive amazing UV Backdrop with 1x1,5m size! High quality of printing and highest level visual art.
                </p>
                <div class="price">
                    <svg class="svg-price">
                        <text fill="url(#gradient1)" y="90%" dx="0" id="bundle3-price1" class="svg-price1">90€</text>
                    </svg>
                    <span id="bundle1-price2">(120€)</span>
                </div>
                <button id="bundle3" class="pre-order-btn">pre-order</button>
            </div>
            <hr class="h-sep">
        </div>
    </div>

    <div class="bundle b-odd">
        <div class="b-wrap wrap">
            <div class="b-cover">
                <img class="fullsize" src="images/bundle-4.png">
            </div>
            <div class="b-content">
                <svg class="svg-title">
                    <text fill="url(#gradient1)" y="90%" dx="0" class="svg-title">Crystal 4</text>
                </svg>
                <hr size="1">
                <p class="b-list">Deluxe edition with the 3CD compilation<br/>
                    <span>Colourful Booklet with the Poem</span><br/>
                    <span>Digital release "Prologue"</span><br/>
                    <span>T-shirt with UV Effect</span><br/>
                    <span>Backdrop (1x1,5m size) with UV Effect</span>
                </p>
                <p class="b-note">
                    You'll save 45 euro, this offer also include Free Shipping! In addition to the deluxe edition you will receive UV T-shirt with both side high quality print as well as UV Backdrop with 1x1,5m size!
                </p>
                <div class="price">
                    <span id="bundle1-price2">(155€)</span>
                    <svg class="svg-price big">
                        <text fill="url(#gradient1)" y="90%" dx="0" id="bundle4-price1" class="svg-price1">110€</text>
                    </svg>
                    <span id="bundle1-price2">(155€)</span>
                </div>
                <button id="bundle4" class="pre-order-btn">pre-order</button>
            </div>
            <hr class="h-sep">
        </div>
    </div>

    <div class="backdrops wrap">
        <div class="bs">
            <img class="fullsize" src="images/backdrop1.jpg">
        </div>
        <div class="bs">
            <img class="fullsize" src="images/backdrop2.jpg">
        </div>
        <hr class="h-sep">
    </div>

    <div class="special-offer">
        <svg class="svg-text3">
            <text fill="url(#gradient1)" y="60%" x="50%" text-anchor="middle">Special Offer!</text>
        </svg>
    </div>

    <div class="bundle b-even">
        <div class="b-wrap wrap">
            <div class="b-cover">
                <img class="fullsize" src="images/bundle-5.png">
            </div>
            <div class="b-content">
                <svg class="svg-title">
                    <text fill="url(#gradient1)" y="90%" dx="0" class="svg-title">Crystal 5</text>
                </svg>
                <hr size="1">
                <p class="b-list">Deluxe edition with the 3CD compilation<br/>
                    <span>Colourful Booklet with the Poem</span><br/>
                    <span>Digital release "Prologue"</span><br/>
                    <span>T-shirt with UV Effect</span><br/>
                    <span>Backdrop (1,5x2,25m size) with UV Effect</span>
                </p>
                <p class="b-note">
                    You'll save 60 euro! In addition to the deluxe edition you will receive T-shirt with UV Effect as well as big UV Backdrop with 1,5x2,25m size! This offer include Free Shipping.
                </p>
                <div class="price">
                    <svg class="svg-price big">
                        <text fill="url(#gradient1)" y="90%" dx="0" id="bundle5-price1" class="svg-price1">110€</text>
                    </svg>
                    <span id="bundle1-price2">(170€)</span>
                </div>
                <button id="bundle5" class="pre-order-btn">pre-order</button>
            </div>
            <hr class="h-sep">
        </div>
    </div>

    <div class="bundle b-odd">
        <div class="b-wrap wrap">
            <div class="b-cover">
                <img class="fullsize" src="images/bundle-6.png">
            </div>
            <div class="b-content">
                <svg class="svg-title">
                    <text fill="url(#gradient1)" y="90%" dx="0" class="svg-title">Crystal 6</text>
                </svg>
                <hr size="1">
                <p class="b-list">
                    Deluxe edition with the 3CD compilation<br/>
                    <span>Colourful Booklet with the Poem</span><br/>
                    <span>Digital release "Prologue"</span><br/>
                    <span>T-shirt with UV Effect</span><br/>
                    <span>Backdrop (2x3m size) with UV Effect</span>
                </p>
                <p class="b-note">
                    You'll save 90 euro! In addition to the deluxe edition you will receive T-shirt with UV Effect as well as huge UV Backdrop with 2x3m size! This offer include Free Shipping.
                </p>
                <div class="price">
                    <span id="bundle1-price2">(330€)</span>
                    <svg class="svg-price big">
                        <text fill="url(#gradient1)" y="90%" dx="0" id="bundle6-price1" class="svg-price1">240€</text>
                    </svg>
                    <span id="bundle1-price2">(330€)</span>
                </div>
                <button id="bundle6" class="pre-order-btn">pre-order</button>
            </div>
            <hr class="h-sep">
        </div>
    </div>

</div>

<div class="footer wrap">
    <p class="contact">With any questions and suggestions, please contact us by e-mail:<br/><a href="mailto:adept@globalsect.ru">adept@globalsect.ru</a></p>
    <img class="fullsize wrap center" src="images/ball.png">
    <p class="poem">
        Listen, friend, the voice of Space,<br/>
        Singing high the words of aeon,<br/>
        Bout Ivan and Sacred Place,<br/>
        Taking you from here to thereon.<br/>
        <br/>
        Bout the fungi trip spiralled<br/>
        In the universe evolving,<br/>
        Bout the Grinnish Emerald<br/>
        Thou is gist of life - key solving.<br/>
    </p>

    <p class="info">
        All parcels will be sended before November 31, 2015.  After your order will be confirmed you'll receive the link to download additional digital release "The Mystery of Crystal Worlds: Prologue"<br>
        Those who made the order, one day before official release, will receive link to download all tracks in WAV as well as ebook with poem and many other magic gifts!<br>
        <br/>
        Don't forget to tell about us to your friends:</p>
    <div class="social-icons">
        <ul>
            <li id="facebook">
                <a href="http://www.facebook.com/share.php?u=http://globalsect.ru/lp/" title="Share It on Facebook" class="fb" target="_blank">
                    <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"width="30px" height="30px" viewBox="0.125 60.125 30 30" enable-background="new 0.125 60.125 30 30" xml:space="preserve"> <g>
                            <path fill="#00FFBC" d="M15.125,60.125c-8.284,0-15,6.716-15,15s6.716,15,15,15s15-6.716,15-15S23.409,60.125,15.125,60.125z M20.848,67.764l-2.075,0.001c-1.627,0-1.941,0.773-1.941,1.908v2.501h3.88l-0.002,3.918h-3.878v10.053h-4.046V76.092H9.402v-3.918 h3.383v-2.89c0-3.354,2.049-5.179,5.04-5.179l3.023,0.005V67.764L20.848,67.764z"/> </g>
                            </svg>
                </a>
            </li>
            <li id="vkontake">
                <a href="http://vk.com/share.php?url=http://globalsect.ru/lp" title="Share It on Vkontakte" class="vk" target="_blank">
                    <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="30px" height="30px" viewBox="15.395 18.875 30 30" enable-background="new 15.395 18.875 30 30" xml:space="preserve"><g>
                            <path fill="#00FFBC" d="M30.395,18.875c-8.284,0-15,6.716-15,15s6.716,15,15,15s15-6.716,15-15S38.679,18.875,30.395,18.875z M38.003,35.497c0.7,0.684,1.439,1.326,2.067,2.08c0.278,0.333,0.54,0.678,0.739,1.064c0.285,0.553,0.028,1.159-0.467,1.192 l-3.073-0.001c-0.793,0.065-1.425-0.254-1.958-0.797c-0.425-0.433-0.819-0.895-1.229-1.342c-0.167-0.183-0.343-0.356-0.554-0.492 c-0.419-0.272-0.783-0.189-1.023,0.248c-0.245,0.445-0.301,0.939-0.324,1.436c-0.034,0.724-0.252,0.913-0.979,0.947 c-1.554,0.073-3.028-0.163-4.397-0.946c-1.209-0.69-2.144-1.666-2.959-2.769c-1.587-2.153-2.803-4.515-3.895-6.944 c-0.246-0.548-0.066-0.84,0.538-0.852c1.003-0.02,2.006-0.017,3.009-0.001c0.408,0.007,0.678,0.24,0.835,0.625 c0.542,1.334,1.207,2.603,2.04,3.779c0.222,0.313,0.448,0.625,0.771,0.847c0.356,0.245,0.628,0.164,0.795-0.234 c0.107-0.252,0.154-0.522,0.177-0.793c0.08-0.927,0.089-1.854-0.049-2.777c-0.086-0.577-0.411-0.951-0.987-1.061 c-0.293-0.056-0.25-0.164-0.108-0.332c0.248-0.289,0.48-0.469,0.944-0.469h3.472c0.547,0.106,0.67,0.353,0.745,0.903l0.003,3.858 c-0.007,0.214,0.107,0.846,0.49,0.986c0.307,0.101,0.509-0.146,0.693-0.339c0.833-0.884,1.426-1.928,1.957-3.007 c0.234-0.476,0.437-0.969,0.633-1.462c0.146-0.365,0.374-0.545,0.785-0.539l3.343,0.005c0.099,0,0.199,0,0.296,0.018 c0.563,0.097,0.718,0.339,0.543,0.888c-0.274,0.864-0.808,1.584-1.328,2.306c-0.559,0.771-1.155,1.517-1.707,2.292 C37.333,34.525,37.373,34.881,38.003,35.497z"/> </g>
                            </svg>
                </a>
            </li>
        </ul>
    </div>
    <p class="copyright">
        All content copyrighted by Thesis Studio LTD, Russia.<br />
        All Rights Reserved.
    </p>
</div>

<div class="popup" id="popup" style="display:none">
    <div class="popup-content">
        <div class="p-header">
            <div>The Mystery of Crystal Worlds</div>
            <div id="close">&#10006;</div>
        </div>
        <form class="landing-form" id="landing-form" action="checkout.php" method="POST">
            <span>
                Друг, обращаем твое внимание на то, что если имейл, на который зарегистрирован твой пэйпал, отличается от действующего адреса электронной почты, то его необходимо указать в комментариях ниже.<br/><br/>
                После оформления заказа, мы вышлем ссылку на скачивание диджитал релиза «Prologue» и по этому адресу, мы всегда сможем связаться с тобой по любым вопросам.
            </span>
            <div class="controls">
                <label for="buyername">Name:</label>
                <input type="text" name="buyername" id="buyername"/>
            </div>
            <div class="controls" type="required">
                <label for="email">Email:</label>
                <input type="text" name="email" id="email"/>
            </div>
            <div class="controls"><?php echo $select; ?></div>
            <div class="controls">
                <label for="state">State/Province:</label>
                <input type="text" name="state" id="state"><span id="state-loader"></span>
            </div>
            <div class="controls">
                <label for="city">City:</label>
                <input type="text" name="city" id="city">
            </div>
            <div class="controls">
                <label for="address">Address:</label>
                <input type="text" name="address" id="address">
            </div>
            <div class="controls">
                <label for="postcode">Postcode:</label>
                <input type="text" name="postcode" id="postcode" />
            </div>
            <div class="controls">
                <label class="comment" for="state">Note:</label>
                <textarea name="comment" rows="6" id="comment"></textarea>
            </div>
            <div class="p-footer">
                <div class="payment-info">
                    <img src="../../images/tpl-files/payment-methods.gif"><br>
                    PayPal is not required. <a href="http://globalsect.ru/shop/buying_without_paypal" target="_blank">Show me</a>.
                </div>
                <button class="submit-button" onclick="return validate('landing-form');">check-out</button><hr class="h-sep">
                <input type="hidden" name="itemname" id="itemname"/>
                <input type="hidden" name="itemdesc" id="itemdesc"/>
                <input type="hidden" name="price" id="price"/>
                <input type="hidden" name="country-text" id="country-text"/>
                <input type="hidden" name="state-text" id="state-text"/>
            </div>
        </form>
    </div>
</div>

<div class="popup" id="popup2" style="display:none">
    <div class="popup-content">
        <div class="p-header">
            <div>The Mystery of Crystal Worlds</div>
            <div id="close2">&#10006;</div>
        </div>
        <p>Thank you for your order!</p>
        <p>Your Order id is <span><?php echo $orderid ?></span</p>
        <?php if($transactionid) : ?>
        <p>Album Download link: <a href="<?php echo $link ?>"><?php echo $link ?></a></p>
        <p>This link will expire on <?php echo date('F,d Y,', time()+86400 * 7) ?></p>
        <p>3 downloads to left</p>
        <?php endif; ?>
    </div>
</div>

<div id="contactform">
    <div id="contact-button">
        <div class="rotated-text">Contact us</div>
    </div>
    <form id="contact-form">
        <div class="controls" type="required">
            <label for="email" >Email:</label><input valid="true" name="email" row="6" col="5"></textarea>
        </div>
        <div class="controls" type="required">
            <label for="contact-text">Your message:</label><textarea valid="true" name="contact-text" row="6" col="5"></textarea>
        </div>
        <div class="notice-box"><span id="preloader"></span><span id="notice-msg"></span></div>
        <button class="submit-button" onclick="return sendContactForm('contact-form');">send</button>
    </form>
</div>

</body>
</html>
<script><?php echo $js; ?></script>