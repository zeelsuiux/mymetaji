<!DOCTYPE html>
<html lang="en">
<!--<< Header Area >>-->

<head>
    <!-- ========== Meta Tags ========== -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="gramentheme">
    <meta name="description" content="Synex - Saas, Software & Startup HTML Template">
    <!-- ======== Page title ============ -->
    <?php $pageTitle = $pageTitle ?? "MyMetaji"; ?><title><?= htmlspecialchars($pageTitle, ENT_QUOTES, "UTF-8") ?></title>
    <!--<< Favcion >>-->
    <link rel="shortcut icon" href="assets/img/favicon.svg">
    <!--<< Bootstrap min.css >>-->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!--<< All Min Css >>-->
    <link rel="stylesheet" href="assets/css/all.min.css">
    <!--<< Animate.css >>-->
    <link rel="stylesheet" href="assets/css/animate.css">
    <!--<< Magnific Popup.css >>-->
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <!--<< Swiper Bundle.css >>-->
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
    <!--<< Nice Select.css >>-->
    <link rel="stylesheet" href="assets/css/nice-select.css">
    <!--<< Main.css >>-->
    <link rel="stylesheet" href="assets/css/main.css">
</head>

<body>


    <div class="page-wrapper">

        <!-- Preloader Start -->
        <div id="preloader">
            <div class="hexus-loader-inner">
                <div class="hexus-loader">
                    <span class="hexus-loader-item"></span>
                    <span class="hexus-loader-item"></span>
                    <span class="hexus-loader-item"></span>
                    <span class="hexus-loader-item"></span>
                    <span class="hexus-loader-item"></span>
                    <span class="hexus-loader-item"></span>
                    <span class="hexus-loader-item"></span>
                    <span class="hexus-loader-item"></span>
                </div>
            </div>
        </div>

        <!-- Back-To-Top Start -->
        <button id="back-top" class="back-to-top">
            <i class="fa-regular fa-arrow-up"></i>
        </button>

        <!-- GT MouseCursor Start -->
        <div class="mouseCursor cursor-outer"></div>
        <div class="mouseCursor cursor-inner"></div>

        <!-- Header Section Start -->

        <header class="header-section header-1" id="sticky-header">
            <div class="header-main">

                <!-- ===================== DESKTOP NAVBAR ===================== -->
                <nav class="navbar p-0 navbar-expand-xl d-none d-xl-flex">
                    <a class="navbar-brand" href="index.php">
                        <img src="assets/img/logo/black-logo.svg" alt="logo">
                    </a>

                    <button class="navbar-toggler" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent"
                        aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mx-auto mb-lg-0">
                            <li class="nav-item menu-thumb">
                                <a class="nav-link active" href="index.php">
                                    Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="about.php">About Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    Services <i class="fas fa-chevron-down"></i>
                                </a>
                                <ul class="sub-menu list-unstyled">
                                    <li><a href="service.php">Service Page</a></li>
                                    <li><a href="service-details.php">Service Details</a></li>
                                </ul>
                            </li>
                            <li class="has-dropdown nav-item">
                                <a class="nav-link" href="javascript:void(0)">
                                    Pages <i class="fas fa-chevron-down"></i>
                                </a>
                                <ul class="sub-menu list-unstyled">
                                    <li class="has-dropdown">
                                        <a href="javascript:void(0)">
                                            Portfolio <i class="fas fa-angle-right"></i>
                                        </a>
                                        <ul class="sub-menu list-unstyled">
                                            <li><a href="project.php">Portfolio page</a></li>
                                            <li><a href="project-details.php">Portfolio Details</a></li>
                                        </ul>
                                    </li>
                                    <li class="has-dropdown">
                                        <a href="javascript:void(0)">
                                            Team <i class="fas fa-angle-right"></i>
                                        </a>
                                        <ul class="sub-menu list-unstyled">
                                            <li><a href="team.php">Team page</a></li>
                                            <li><a href="team-details.php">Team Details</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="pricing.php">Pricing Page</a></li>
                                    <li><a href="faq.php">Faq Page</a></li>
                                    <li><a href="404.php">404 Error</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    Blog <i class="fas fa-chevron-down"></i>
                                </a>
                                <ul class="sub-menu list-unstyled">
                                    <li><a href="news-grid.php">Blog Grid</a></li>
                                    <li><a href="news.php">Blog Standard</a></li>
                                    <li><a href="news-details.php">Blog Details</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="contact.php">Contact</a>
                            </li>
                        </ul>
                        <div class="menu-right-info">
                            <a class="theme-btn-main style-2" href="contact.php">
                                <span class="theme-btn-arrow-left"> <i class="fa-solid fa-arrow-up-right"></i> </span>
                                <span class="theme-btn">Get started</span>
                                <span class="theme-btn-arrow-right"> <i class="fa-solid fa-arrow-up-right"></i> </span>
                            </a>
                            <div class="sidebar__toggle offcanvas-btn d-xl-none my-auto">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="mobile-menu-area d-block d-xl-none">
                <div class="container">
                    <div class="mobile-topbar">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="logo">
                                <a href="index.php">
                                    <img src="assets/img/logo/black-logo.svg" alt="logo">
                                </a>
                            </div>
                            <div class="menu-search d-flex align-items-cent0r gap-4">
                                <div class="bars">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mobile-menu-overlay"></div>
                <div class="mobile-menu-main">
                    <div class="logo">
                        <a href="index.php">
                            <img src="assets/img/logo/white-logo.svg" alt="logo">
                        </a>
                    </div>
                    <div class="close-mobile-menu">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="menu-body">
                        <div class="menu-list">
                            <ul class="list-unstyled">
                                <li class="sub-mobile-menu">
                                    <a href="index.php">
                                        Home
                                    </a>
                                </li>
                                <li><a href="about.php">About Us</a></li>
                                <li class="sub-mobile-menu">
                                    <a href="javascript:void(0)">
                                        Services <i class="fas fa-chevron-down float-end"></i>
                                    </a>
                                    <ul class="list-unstyled">
                                        <li><a href="service.php">Service Page</a></li>
                                        <li><a href="service-details.php">Service Details</a></li>
                                    </ul>
                                </li>
                                <li class="sub-mobile-menu has-dropdown">
                                    <a href="javascript:void(0)">
                                        Pages <i class="fas fa-chevron-down float-end"></i>
                                    </a>
                                    <ul class="list-unstyled">
                                        <li class="sub-child-menu has-dropdown">
                                            <a href="javascript:void(0)">
                                                Portfolio <i class="fas fa-chevron-down float-end"></i>
                                            </a>
                                            <ul class="list-unstyled">
                                                <li><a href="project.php">Portfolio page</a></li>
                                                <li><a href="project-details.php">Portfolio Details</a></li>
                                            </ul>
                                        </li>
                                        <li class="sub-child-menu has-dropdown">
                                            <a href="javascript:void(0)">
                                                Team <i class="fas fa-chevron-down float-end"></i>
                                            </a>
                                            <ul class="list-unstyled">
                                                <li><a href="team.php">Team page</a></li>
                                                <li><a href="team-details.php">Team Details</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="pricing.php">Pricing Page</a></li>
                                        <li><a href="faq.php">Faq Page</a></li>
                                        <li><a href="404.php">404 Error</a></li>
                                    </ul>
                                </li>
                                <li class="sub-mobile-menu">
                                    <a href="javascript:void(0)">
                                        Blog <i class="fas fa-chevron-down float-end"></i>
                                    </a>
                                    <ul class="list-unstyled">
                                        <li><a href="news-grid.php">Blog Grid</a></li>
                                        <li><a href="news.php">Blog Standard</a></li>
                                        <li><a href="news-details.php">Blog Details</a></li>
                                    </ul>
                                </li>
                                <li><a href="contact.php">Contact</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="off-contact-area">
                        <div class="off-contact-info">
                            <span class="info-title">Contact Info</span>
                            <div class="contact-details">
                                <span class="sub-info">Phone</span>
                                <p>
                                    <a href="tel:+18005550123">+1 (800) 555-0123</a>
                                </p>
                            </div>
                            <div class="contact-details">
                                <span class="sub-info">Email</span>
                                <p>
                                    <a href="mailto:hello@Synex.com">hello@Synex.com</a>
                                </p>
                            </div>
                            <div class="contact-details">
                                <span class="sub-info">Location</span>
                                <p>
                                    374 William S Canning Blvd USA
                                </p>
                            </div>
                        </div>
                        <div class="social-icon-list">
                            <span class="follow-title">
                                Follow us:
                            </span>
                            <div class="social-icon d-flex align-items-center">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-vimeo-v"></i></a>
                                <a href="#"><i class="fab fa-pinterest-p"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>