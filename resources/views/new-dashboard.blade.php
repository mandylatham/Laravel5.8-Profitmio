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
        <a href="javascript:;" class="sidebar-toggle">B</a>
        <div class="navbar-menu-extra">
            <ul class="nav">
                <li class="menu-item">
                    <b-dropdown variant="link" no-caret>
                        <template slot="button-content">
                            <img src="/img/help.png" alt="Help">
                        </template>
                        <b-dropdown-item href="#">Action</b-dropdown-item>
                        <b-dropdown-item href="#">Another action</b-dropdown-item>
                        <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                    </b-dropdown>
                </li>
                <li class="menu-item">
                    <b-dropdown variant="link" no-caret>
                        <template slot="button-content">
                            <img src="/img/notification.png" alt="Notifications">
                        </template>
                        <b-dropdown-item href="#">Action</b-dropdown-item>
                        <b-dropdown-item href="#">Another action</b-dropdown-item>
                        <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                    </b-dropdown>
                </li>
                <li class="menu-item menu-item-profile">
                    <b-dropdown variant="link" no-caret>
                        <template slot="button-content">
                            <span>Jhon Doe</span>
                            <img src="http://lorempixel.com/60/60/" alt="Avatar">
                        </template>
                        <b-dropdown-item href="#">Profile</b-dropdown-item>
                        <b-dropdown-item href="#">Another action</b-dropdown-item>
                        <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                    </b-dropdown>
                </li>
            </ul>
        </div>
    </nav>
</div>
<!-- End Navigation Bar-->

<div id="wrapper">
    <div class="wrapper-aside" id="wrapper-aside">
        <div class="calendar-filters">
            <div class="form-check">
                <input class="form-check-input" type="radio" value="option2" checked>
                <label class="form-check-label" for="exampleRadios2">
                    Filter
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option1"
                       checked>
                <label class="form-check-label" for="exampleRadios2">
                    Filter
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1"
                       checked>
                <label class="form-check-label" for="exampleRadios1">
                    Filter
                </label>
            </div>
        </div>
        <date-pick class="event-calendar" v-model="selectedDate" :has-input-element="false"></date-pick>
        <div class="events">
            <header>
                <span class="date">@{{ selectedDate | format('DD') }}</span>
                <span class="label">@{{ selectedDate | format('MMMM Do, YYYY') }}</span>
            </header>
            <div class="event-list">
                <div class="event"></div>
                <div class="event"></div>
                <div class="event"></div>
                <div class="event"></div>
                <div class="event"></div>
            </div>
        </div>
    </div>
    <div class="wrapper-content" id="campaign-list">
        <h3>ACTIVE CAMPAIGN</h3>
        <div class="campaign" :class="{'open': open[1]}">
            <div class="row no-gutters">
                <div class="col-12 col-xl-6 campaign-resume">
                    <header @click="toggle(1)">
                        <span class="campaign-resume--status"></span>
                        <div class="campaign-resume--title">
                            <strong>CAMPAIGN 677</strong>
                            <span>HEALEY FORD LINCOLN “PM6000”</span>
                        </div>
                    </header>
                    <div class="campaign-resume--data d-none d-xl-block">
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
                <div class="col-12 col-xl-6 campaign-collapsable">
                    <div class="row no-gutters">
                        <div class="col-12 d-xl-none">
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
                        <div class="col-12 col-md-6 campaign-date">
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
                        <div class="col-12 col-md-6 campaign-chart campaign-button-block">
                            <button class="btn btn-secondary btn-block">A</button>
                            <button class="btn btn-secondary btn-block">B</button>
                            <button class="btn btn-secondary btn-block">C</button>
                            <button class="btn btn-secondary btn-block">D</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h3>EXPIRED CAMPAIGN</h3>
        <div class="campaign inactive" :class="{'open': open[2]}">
            <div class="row no-gutters">
                <div class="col-12 col-xl-6 campaign-resume">
                    <header @click="toggle(2)">
                        <span class="campaign-resume--status"></span>
                        <div class="campaign-resume--title">
                            <strong>CAMPAIGN 677</strong>
                            <span>HEALEY FORD LINCOLN “PM6000”</span>
                        </div>
                    </header>
                </div>
                <div class="col-12 col-xl-6 campaign-collapsable">
                    <div class="row no-gutters">
                        <div class="col-12 col-lg-6 campaign-date">
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
                        <div class="col-12 col-lg-6 campaign-chart">
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
        <div class="campaign inactive" :class="{'open': open[3]}">
            <div class="row no-gutters">
                <div class="col-12 col-xl-6 campaign-resume">
                    <header @click="toggle(3)">
                        <span class="campaign-resume--status"></span>
                        <div class="campaign-resume--title">
                            <strong>CAMPAIGN 677</strong>
                            <span>HEALEY FORD LINCOLN “PM6000”</span>
                        </div>
                    </header>
                </div>
                <div class="col-12 col-xl-6 campaign-collapsable">
                    <div class="row no-gutters">
                        <div class="col-12 col-lg-6 campaign-date">
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
                        <div class="col-12 col-lg-6 campaign-chart">
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
</div>
</body>
</html>
