<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta name="description" content="bootstrap admin template">
  <meta name="author" content="">
  <title>404 | Profit Miner</title>
  <link rel="apple-touch-icon" href="{{ secure_url('/images/apple-touch-icon.png') }}">
  <link rel="shortcut icon" href="{{ secure_url('/images/favicon.ico') }}">
  <!-- Stylesheets -->
  <link rel="stylesheet" href="{{ secure_url('/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ secure_url('/css/bootstrap-extend.min.css') }}">
  <link rel="stylesheet" href="{{ secure_url('/css/site.css') }}">
  <!-- Plugins -->
  <link rel="stylesheet" href="{{ secure_url('/vendor/animsition/animsition.css') }}">
  <link rel="stylesheet" href="{{ secure_url('/vendor/asscrollable/asScrollable.css') }}">
  <link rel="stylesheet" href="{{ secure_url('/vendor/switchery/switchery.css') }}">
  <link rel="stylesheet" href="{{ secure_url('/vendor/intro-js/introjs.css') }}">
  <link rel="stylesheet" href="{{ secure_url('/vendor/slidepanel/slidePanel.css') }}">
  <link rel="stylesheet" href="{{ secure_url('/vendor/flag-icon-css/flag-icon.css') }}">
  <link rel="stylesheet" href="{{ secure_url('/vendor/waves/waves.css') }}">
  <!-- Fonts -->
  <link rel="stylesheet" href="{{ secure_url('/fonts/material-design/material-design.min.css') }}">
  <link rel="stylesheet" href="{{ secure_url('/fonts/brand-icons/brand-icons.min.css') }}">
  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,300italic'>
  <!--[if lt IE 9]>
    <script src="{{ secure_url('/vendor/html5shiv/html5shiv.min.js') }}"></script>
    <![endif]-->
  <!--[if lt IE 10]>
    <script src="{{ secure_url('/vendor/media-match/media.match.min.js') }}"></script>
    <script src="{{ secure_url('/vendor/respond/respond.min.js') }}"></script>
    <![endif]-->
  <!-- Scripts -->
  <script src="{{ secure_url('/vendor/breakpoints/breakpoints.js') }}"></script>
  <script>
  Breakpoints();
  </script>
  <style type="text/css">
.page {
    background: transparent url('{{ secure_url('images/debut_light.png') }}') repeat;
}
  .page-error .error-mark {
  margin-bottom: 33px;
}

.page-error header h1 {
  font-size: 10em;
  font-weight: 400;
}

.page-error header p {
  margin-bottom: 30px;
  font-size: 30px;
  text-transform: uppercase;
}

.page-error h2 {
  margin-bottom: 30px;
}

.page-error .error-advise {
  margin-bottom: 25px;
  color: #a9afb5;
}
</style>
</head>
<body class="animsition page-error page-error-404 layout-full">
  <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
  <!-- Page -->
  <div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">
    <div class="page-content vertical-align-middle">
      <header>
        <h1 class="animation-slide-top">404</h1>
        <p>Page Not Found !</p>
      </header>
      <p class="error-advise">YOU SEEM TO BE LOST, CLICK BELOW TO GET BACK ON TRACK.</p>
      <a class="btn btn-primary btn-round" href="{{ secure_url('/') }}">GO TO HOME PAGE</a>
    </div>
  </div>
  <!-- End Page -->
  <!-- Core  -->
  <script src="{{ secure_url('/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
  <script src="{{ secure_url('/vendor/jquery/jquery.js') }}"></script>
  <script src="{{ secure_url('/vendor/tether/tether.js') }}"></script>
  <script src="{{ secure_url('/vendor/bootstrap/bootstrap.js') }}"></script>
  <script src="{{ secure_url('/vendor/animsition/animsition.js') }}"></script>
  <script src="{{ secure_url('/vendor/mousewheel/jquery.mousewheel.js') }}"></script>
  <script src="{{ secure_url('/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
  <script src="{{ secure_url('/vendor/asscrollable/jquery-asScrollable.js') }}"></script>
  <script src="{{ secure_url('/vendor/waves/waves.js') }}"></script>
  <!-- Plugins -->
  <script src="{{ secure_url('/vendor/switchery/switchery.min.js') }}"></script>
  <script src="{{ secure_url('/vendor/intro-js/intro.js') }}"></script>
  <script src="{{ secure_url('/vendor/screenfull/screenfull.js') }}"></script>
  <script src="{{ secure_url('/vendor/slidepanel/jquery-slidePanel.js') }}"></script>
  <!-- Scripts -->
  <script src="{{ secure_url('/js/State.js') }}"></script>
  <script src="{{ secure_url('/js/Component.js') }}"></script>
  <script src="{{ secure_url('/js/Plugin.js') }}"></script>
  <script src="{{ secure_url('/js/Base.js') }}"></script>
  <script src="{{ secure_url('/js/Config.js') }}"></script>
  <script src="{{ secure_url('/js/Section/Menubar.js') }}"></script>
  <script src="{{ secure_url('/js/Section/Sidebar.js') }}"></script>
  <script src="{{ secure_url('/js/Section/PageAside.js') }}"></script>
  <script src="{{ secure_url('/js/Plugin/menu.js') }}"></script>
  <!-- Config -->
  <script src="{{ secure_url('/js/config/colors.js') }}"></script>
  <script src="{{ secure_url('/js/config/tour.js') }}"></script>
  <script>
  Config.set('assets', '/');
  </script>
  <!-- Page -->
  <script src="{{ secure_url('/js/Site.js') }}"></script>
  <script src="{{ secure_url('/js/Plugin/asscrollable.js') }}"></script>
  <script src="{{ secure_url('/js/Plugin/slidepanel.js') }}"></script>
  <script src="{{ secure_url('/js/Plugin/switchery.js') }}"></script>
  <script>
  (function(document, window, $) {
    'use strict';
    var Site = window.Site;
    $(document).ready(function() {
      Site.run();
    });
  })(document, window, jQuery);
  </script>
</body>
</html>
