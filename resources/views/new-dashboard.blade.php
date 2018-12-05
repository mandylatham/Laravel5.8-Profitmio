<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profit Miner</title>

    <script src="{{ asset('js/app.js') }}" defer></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<!-- Navigation Bar-->
<div id="main-header">
    <a href="index.html" class="logo">
        <img src="/img/logo.png">
    </a>
    <nav class="navbar">
        <div class="navbar-menu-extra">
            <ul class="nav">
                <li class="menu-item">
                    <a href="">
                        <img src="/img/help.png" alt="Help">
                    </a>
                </li>
                <li class="menu-item">
                    <a href="">
                        <img src="/img/notification.png" alt="Notifications">
                    </a>
                </li>
                <li class="menu-item-profile">
                    <a href="">
                        <span>Jhon Doe</span>
                        <img src="http://lorempixel.com/60/60/" alt="Avatar">
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>
<!-- End Navigation Bar-->

<div id="wrapper">
    <div class="row no-gutters">
        <div class="d-none d-lg-block col-lg-4 col-xl-3 wrapper-aside">
            <div class="wrapper-aside--content">
                <div class="calendar-filters">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="option2" checked>
                        <label class="form-check-label" for="exampleRadios2">
                            Filter
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option1" checked>
                        <label class="form-check-label" for="exampleRadios2">
                            Filter
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                        <label class="form-check-label" for="exampleRadios1">
                            Filter
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-xl-9 wrapper-content">
            <div class="campaign">
                <div class="row no-gutters">
                    <div class="col-12 col-xl-6 campaign-resume">
                        <header>
                            <span class="campaign-resume--status"></span>
                            <div class="campaign-resume--title">
                                <strong>CAMPAIGN 677</strong>
                                <span>HEALEY FORD LINCOLN “PM6000”</span>
                            </div>
                        </header>
                        <div class="campaign-resume--data">
                            <div class="row no-gutters">
                                <div class="col-5 campaign-resume--data-assets">
                                    <img src="/img/img.png" alt="Image">
                                </div>
                                <div class="col-7 campaign-resume--data-info">
                                    <strong>9 x 12 Postcard</strong>
                                    <p>Card & Buyer BB</p>
                                    <p>Mailer - TXT for Value</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3 campaign-date">
                        <div class="campaign-date--date">
                            <span class="campaign-date--date-label">Start Date</span>
                            <span class="campaign-date--date-value">11.21.18</span>
                        </div>
                        <div class="campaign-date--date">
                            <span class="campaign-date--date-label">Start Date</span>
                            <span class="campaign-date--date-value">11.21.18</span>
                        </div>
                        <div class="day-left">
                            <span class="date-label">Days Left:</span>
                            <span class="date-value">6</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3 campaign-chart">
                    </div>
                </div>
            </div>
            <div class="campaign inactive">
                <div class="row no-gutters">
                    <div class="col-lg-12 col-xl-6 campaign-resume">
                        <header>
                            <span class="campaign-resume--status"></span>
                            <div class="campaign-resume--title">
                                <strong>CAMPAIGN 677</strong>
                                <span>HEALEY FORD LINCOLN “PM6000”</span>
                            </div>
                        </header>
                    </div>
                    <div class="col-lg-6 col-xl-3 campaign-date">
                        <div class="row no-gutters">
                            <div class="col-6 campaign-date--asset">
                                <img src="/img/img.png" alt="Image">
                            </div>
                            <div class="col-6 campaign-date--date">
                                <span class="campaign-date--date-label">End Date:</span>
                                <span class="campaign-date--date-value">11.21.18</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-3 campaign-chart">
                        <div class="row no-gutters">
                            <div class="col-6 campaign-chart--charts">
                                <img src="/img/pie.png" alt="Image">
                            </div>
                            <div class="col-6 campaign-chart--labels">
                                <span class="sms">sms</span>
                                <span class="call">call</span>
                                <span class="email">email</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>