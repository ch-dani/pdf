@extends('layouts.layout')

@section('content')

    <div class="upload-top-info">
        <div class="container">
            <div class="app-title">
                <div class="wrapper">
                    <h1>Free For Education</h1>
                    <p>Teachers, thanks for everything you do</p>
                </div>
            </div>
            <div class="app-welcome teacher-welcome">
                <img class="scholl-image" src="img/school-image.svg" alt="image">
                <div class="upload-welcom-descr">DeftPDF has proven popular with teachers. We know how important your work is. This is our way of showing our appreciation, and saying thanks</div>
            </div>
        </div>
    </div>

    <section class="how-it-works">

        <div class="title-section"><h2>Create Teacher Account</h2></div>

        <div class="container centered">
            <div class="post create-account-box">
                <input type="text" placeholder="Email address">
                <p>Use your work email. Eg: dumbledore@wizardscollege.edu<br/>You'll get an email with steps to confirm your teacher status.</p>
                <input type="text" placeholder="Password">
                <p class="check-line"><input type="checkbox">I agree to the terms of service and privacy policy</p>
                <a class="button-green" href="#">Create Account</a>
                <p>We do not offer refunds for any fees paid prior to being approved for an education discount.</p>
                <p>Available for public education institutions only. We reserve the right to revoke the account or remove the free status if abuse is discovered.</p>
            </div>
        </div>

        <div class="title-section"><h2>IT Admins & G Suite for Education</h2></div>

        <div class="container centered">
            <div class="post create-account-box">
                <p>DeftPDF PDF is available in the G Suite Marketplace™</p>
                <a href="#" class="images-link">
                    <img src="img/gsuite.png" alt="image">
                    <img src="img/icon_128.png" alt="image">
                </a>
                <p>We can activate entire domains (eg: @wizardscollege.edu) — so all teachers within your organization can access DeftPDF For Education without having to activate each account separately.</p>
                <a class="button-green" href="#">Apply here</a>
                <p>DeftPDF is not associated with Google. G Suite and all related logos are trademarks of Google.com, Inc. or its affiliates.</p>
            </div>
        </div>

        <div class="title-section"><h2>Let other teachers know</h2></div>

        <div class="container centered noline">
            <div class="post create-account-box">

                <div class="social-icons">
                    <a href="#" class="fb"><img src="img/fb-white-icon.svg" alt="facebook"></a>
                    <a href="#" class="gp"><img src="img/google-plus-white-icon.svg" alt="google plus"></a>
                    <a href="#" class="tw"><img src="img/tw-white-icon.svg" alt="twitter"></a>
                    <a href="#" class="ml"><img src="img/mail-white-icon.svg" alt="mail"></a>
                </div>
                <a class="button-green contact-btn-popup" href="#contactFormModal">Contact Support</a>
            </div>
        </div>

    </section>

@endsection
