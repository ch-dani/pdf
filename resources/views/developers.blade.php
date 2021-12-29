@extends('layouts.layout')

@section('content')

    <section id="content">
        <div class="container-fluid">
            <section id="docs-toc">
                <section id="docs-toc-title">
                    <div class="title">
                        <h1>Developer Docs</h1>
                    </div>
                </section>
                <div class="menu-dev">
                    <h3><a href="#web-integration" class="scroll-ancor">Web Integrations</a></h3>
                    <ul>
                        <li>
                            <a href="#web-open-files" class="scroll-ancor">Open your files with our web tools</a>
                        </li>
                        <li>
                            <a href="#web-save-to-pdf-link" class="scroll-ancor">'Save to PDF' link for your website</a>
                        </li>
                    </ul>
                    <h3><a href="#hosted-api" class="scroll-ancor">API Docs</a></h3>
                    <ul>
                        <li>
                            <a href="#api-html-to-pdf" class="scroll-ancor">HTML to PDF</a>
                        </li>
                    </ul>
                </div>
            </section>
            <section id="docs-content">
                <div id="web-integration">
                    <h2>Web Integrations</h2>
                    <h5>Integrate your website with our PDF tools</h5>
                    <h3 id="web-open-files">Open your files with our web tools</h3>
                    <p>Here are some use-cases:</p>
                    <ol class="with-disks">
                        <li>Allow clients to easily fill out your PDF form, using our editor.</li>
                        <li>Allow clients to sign a PDF doc and send it back to you by email.</li>
                        <li>Provide a simple way for your clients to crop your PDF templates.</li>
                    </ol>
                    <section class="article">
                        <h4>How it Works</h4>
                        <div class="max-width grouped-items">
                            <div class="form-group">
                                <label>1. Select File (from URL)</label>
                                <input placeholder="https://www.example.com/path/to/my-file.pdf" value="https://www.pdf2.cgp.systems/assets/sample.pdf" class="form-control input-xs" type="text" id="pdfFileURL">
                            </div>
                            <div class="form-group">
                                <label>2. Select Tool</label>
                                <div>
                                    <div class="btn-group bootstrap-select">
                                        <select data-style="btn-xs btn-select" class="selectpicker" id="selectedTool" tabindex="-98">
                                            <option selected="selected" value="pdf-editor">PDF Editor</option>
                                            <option data-divider="true"></option>
                                            <option value="sign-pdf">Sign PDF</option>
                                            <option value="compress-pdf">Compress PDF</option>
                                            <option value="extract-pdf-pages">Extract Pages</option>
                                            <option value="crop-pdf">Crop PDF</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="returnEmailFormGroup" class="form-group">
                                <label for="returnEmail">3. Return email address (optional)</label>
                                <input placeholder="your@email.address" name="returnEmail" id="returnEmail" class="form-control input-xs" type="email">
                                <p class="small-letters">The edited document will be sent back to this email address</p>
                            </div>
                            <div class="arrow-down-wrap">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 21.825 21.825" style="enable-background:new 0 0 21.825 21.825;" xml:space="preserve" width="24px" height="24px"><path d="M16.791,13.254c0.444-0.444,1.143-0.444,1.587,0c0.429,0.444,0.429,1.143,0,1.587l-6.65,6.651  c-0.206,0.206-0.492,0.333-0.809,0.333c-0.317,0-0.603-0.127-0.81-0.333l-6.65-6.651c-0.444-0.444-0.444-1.143,0-1.587  s1.143-0.444,1.587,0l4.746,4.762V1.111C9.791,0.492,10.299,0,10.918,0c0.619,0,1.111,0.492,1.111,1.111v16.904L16.791,13.254z" fill="#333333"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                            </div>
                            <div style="margin-bottom: 20px;" class="result-area max-width">
                                <label>The web integration link</label>
                                <div class="form-group">
                                    <textarea id="webIntegrationLink" rows="3" class="form-control" readonly="readonly"></textarea>
                                </div>
                                <a style="margin-left: 5px;" data-clipboard-target="#webIntegrationLink" id="clipboard-btn1" class="btn btn-default btn-medium btn-secondary btn-xs">Copy code</a>
                                <a target="_blank" id="clickToTryBtn" class="btn btn-default btn-medium btn-xs" href="https://www.pdf2.cgp.systems/pdf-editor?files=%5B%7B%22downloadUrl%22%3A%22https%3A%2F%2Fpdf2.cgp.systems%2Fassets%2Fsample.pdf%22%7D%5D">Click to try</a>
                            </div>
                            <p class="light">Copy the above integration link and use it in your website or emails. <br>Need help setting it up? Please contact support.</p>
                        </div>
                        <h4>Developer Setup</h4>
                        <h5>Input files</h5>
                        <p>Input files can be passed as a <code>GET</code> parameter, in <code>JSON</code> format.</p>
                        <div class="form-control pre-like">https://pdf2.cgp.systems/pdf-editor?<span class="coloured violet">files=[{"downloadUrl":"https://www.example.com/download/sample.pdf"}]</span></div>
                        <p>You'll need to <code>URL encode</code> the <code>JSON</code> value. Above code displays unencoded <code>JSON</code> for readability.</p>
                        <div class="form-control pre-like">https://pdf2.cgp.systems/pdf-editor?files=<span class="coloured yellow">%5B%7B%22downloadUrl%22%3A%22https%3A%2F%2Fwww.example.com%2Fdownload%2Fsample.pdf%22%7D%5D</span></div>
                        <p>An array of multiple files is accepted for certain tools (eg: merge, compress).</p>
                        <h5>Return by email</h5>
                        <p>To receive the edited documents back by email add a <code>returnEmail</code> parameter to the URL:</p>
                        <div class="form-control pre-like">https://pdf2.cgp.systems/pdf-editor?files=[{"downloadUrl":"https://www.example.com/download/sample.pdf"}]&amp;<span class="coloured blue">returnEmail=your@email.address</span></div>
                        <p>Remember to URL encode the email address value passed in:</p>
                        <div class="form-control pre-like">&amp;<strong>returnEmail=your%40email.address</strong></div>
                    </section>
                </div>
                <hr class="section-break">
                <div id="web-save-to-pdf-link">
                    <h3>'Save to PDF' link for your web page</h3>
                    <p>Let your visitors save pages from your website to PDF. Convert URLs to PDF.</p>
                    <section class="article">
                        <h4>How it works</h4>
                        <div id="webSaveToPdfLink" class="form-control pre-like" contenteditable="true">&lt;a href="https://pdf2.cgp.systems/html-to-pdf?<span class="coloured orange">save-link=https://fs.blog</span>"&gt;Save to PDF&lt;/a&gt;</div>
                        <p>The <code>URL</code> is optional, can be automatically detected and works on any page without configuration.</p>
                        <div class="form-control pre-like" contenteditable="true">&lt;a href="https://pdf2.cgp.systems/html-to-pdf?<span class="coloured gray">save-link</span>"&gt;Save to PDF&lt;/a&gt;</div>
                        <h4>More Options</h4>
                        <div class="form-control pre-like light">https://pdf2.cgp.systems/html-to-pdf?save-link=https://fs.blog&amp;<span class="coloured violet">viewportWidth=1440</span>&amp;<span class="coloured blue">pageSize=A3</span>&amp;<span class="coloured yellow">pageOrientation=landscape</span>&amp;<span class="coloured red">pageMargin=100px</span></div>
                        <h5>List of All Options</h5>
                        <table class="table">
                            <thead>
                            <tr>
                                <td>Name</td>
                                <td colspan="2">Description</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>save-link</td>
                                <td>Web page URL to convert to PDF. Eg: <code>https://fs.blog</code>. Defaults to the referring page.</td>
                            </tr>
                            <tr>
                                <td>pageSize</td>
                                <td>One of the standard page sizes: <code>a0</code>, <code>a1</code>, <code>a2</code>, <code>a3</code>, <code>a4</code>, <code>a5</code>, <code>letter</code>, <code>legal</code>. Defaults to one long page.</td>
                            </tr>
                            <tr>
                                <td>viewportWidth</td>
                                <td>The width, in pixels, for the rendered HTML page. Eg: <code>1440</code>. Defaults to browser's <code>window.innerWidth</code></td>
                            </tr>
                            <tr>
                                <td>pageOrientation</td>
                                <td>One of <code>portrait</code>, <code>landscape</code> or <code>auto</code> (default). Eg: <code>auto</code></td>
                            </tr>
                            <tr>
                                <td>pageMargin</td>
                                <td>Size of page margin, including units (<code>px</code>, <code>in</code>, <code>cm</code> or <code>mm</code>). Eg: <code>100px</code>. Defaults to no margin.</td>
                            </tr>
                            </tbody>
                        </table>
                    </section>
                    <p></p>
                </div>
                <hr class="section-break">
                <div id="hosted-api">
                    <h2>API Docs</h2>
                    <p>Hosted API, running on our servers (api.pdf2.cgp.systems)<br>Currently in BETA.</p>
                    <h4>Pricing</h4>
                    <p>
                        Free to use within the free tier limits.<br>
                        Paid API plans will be announced when going out of BETA.
                    </p>
                    <h4>Limits</h4>
                    <p>Current limit is 30 requests per minute, max 4 concurrent requests.<br>Need more? Please get in touch.</p>
                    <h4>Authentication</h4>
                    <p>No account or api key required to get started with the free tier.</p>
                    <section id="api-html-to-pdf" class="article">
                        <h3>HTML to PDF</h3>
                        <p>Convert HTML to PDF documents.</p>
                        <h4>Get started</h4>
                        <p>Run this CURL command in your console:</p>
                        <pre>$&gt; curl -i https://api.pdf2.cgp.systems/v1/tasks\
                              --fail --silent --show-error \
                              --header "Content-Type: application/json" \
                              --data '{"url": "https://csszengarden.com",
                                       "type": "htmlToPdf" }' &gt; csszengarden_com.pdf

                            </pre>
                        <h4>Endpoint URL</h4>
                        <p>The API is organized around REST.</p>
                        <pre>https://api.pdf2.cgp.systems/v1/tasks</pre>
                        <h4>Request</h4>
                        <p>Make a <code>POST</code> request with <code>JSON</code> body:</p>
                        <pre>Content-Type: application/json</pre>
                        <p>Examples:</p>
                        <pre>{"type":"htmlToPdf","url":"csszengarden.com"}</pre>
                        <p>Converting HTML code (instead of an URL) to PDF:</p>
                        <pre>{"type":"htmlToPdf","htmlCode":"<strong>HTM</strong> rules"}</pre>
                        <h4>HTML to PDF Options</h4>
                        <div class="table-wrapper">
                            <table class="table">
                                <thead>
                                <tr>
                                    <td>Name</td>
                                    <td>Type</td>
                                    <td>Default</td>
                                    <td colspan="2">Description</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>url</td>
                                    <td>string</td>
                                    <td>optional</td>
                                    <td>Web page URL to convert to PDF.</td>
                                    <td>Eg: <code>https://csszengarden.com</code></td>
                                </tr>
                                <tr>
                                    <td>htmlCode</td>
                                    <td>string</td>
                                    <td>optional</td>
                                    <td>HTML source code to convert to PDF.</td>
                                    <td>Eg: <code><strong>HTML</strong> rules</code></td>
                                </tr>
                                <tr>
                                    <td>pageSize</td>
                                    <td>string</td>
                                    <td>
                                        <code>one_long_page</code>
                                    </td>
                                    <td colspan="2">
                                        <code>one_long_page</code> or one of the standard page sizes: <code>a0</code>, <code>a1</code>, <code>a2</code>, <code>a3</code>, <code>a4</code>, <code>a5</code>, <code>letter</code>, <code>legal</code>.
                                    </td>
                                </tr>
                                <tr>
                                    <td>pageOrientation</td>
                                    <td>string</td>
                                    <td>
                                        <code>auto</code>
                                    </td>
                                    <td colspan="2">
                                        <code>landscape</code>, <code>portrait</code> or <code>auto</code>.
                                    </td>
                                </tr>
                                <tr>
                                    <td>viewportWidth</td>
                                    <td>integer</td>
                                    <td>optional</td>
                                    <td>The width in pixels for the rendered web page.</td>
                                    <td>Eg: <code>1600</code></td>
                                </tr>
                                <tr>
                                    <td>pageMargin</td>
                                    <td>double</td>
                                    <td>optional</td>
                                    <td>Specifies the size of the margin around the PDF page. To be used together with <code>pageMarginUnits</code>.</td>
                                    <td>Eg: <code>2.2</code></td>
                                </tr>
                                <tr>
                                    <td>pageMarginUnits</td>
                                    <td>string</td>
                                    <td>optional</td>
                                    <td>Specifies the units to be used for the margin size. One of <code>px</code>, <code>in</code>, <code>cm</code> or <code>mm</code>. To be used together with <code>pageMargin</code>.</td>
                                    <td>Eg: <code>px</code> for a margin size specified in pixels.</td>
                                </tr>
                                <tr>
                                    <td>hideNotices</td>
                                    <td>boolean</td>
                                    <td><code>false</code></td>
                                    <td>Attempt to automatically hide cookie notices and similar overlays.</td>
                                    <td>Eg: <code>true</code></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <h4>HTTP Response Codes</h4>
                        <table class="table">
                            <tbody><tr>
                                <td>200</td>
                                <td>All OK. Response contents will be the PDF document stream.</td>
                            </tr>
                            <tr>
                                <td>429</td>
                                <td>Rate limit reached.</td>
                            </tr>
                            <tr>
                                <td>400</td>
                                <td>Invalid request.</td>
                            </tr>
                            <tr>
                                <td>500</td>
                                <td>An error on our side.</td>
                            </tr>
                            </tbody></table>
                        <h3 id="api-other">Other PDF tools?</h3>
                        <p>API access for other PDF tools (crop, merge, compress, etc) is not yet available.</p>
                    </section>
                </div>
            </section>
        </div>
        <script data-src="/js/pages/developers.min.js?v=2" class="pageScripts"></script>
        <section>
            <script src="/js/libs-and-partials.min.js?v=163" async="async"></script>
        </section>
    </section>

    <style>
        header, footer{
            display: none !important;
        }
        #docs-toc {
            padding: 25px;
            background-color: #F8F8F8;
            width: 300px;
            position: fixed;
            top: 0;
            font-size: 14px;
            height: 100%;
        }
        #docs-toc-title {
            border-bottom: 1px #E9E9E9 solid;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        #docs-toc ul {
            padding: 0;
        }
        #docs-toc ul a{
            color: #6E5DDF;
        }
        #docs-content .max-width {
            max-width: 600px;
        }
        #docs-content {
            margin-left: 300px;
            position: relative;
            padding: 25px;
            padding-left: 50px;
        }
        .grouped-items {
            background-color: #F8F8F8;
            margin-bottom: 40px;
            padding: 20px;
            border-radius: 5px;
        }
        .grouped-items .form-group {
            margin-bottom: 15px;
        }
        .grouped-items label {
            display: inline-block;
            margin-bottom: 5px;
            font-weight: 700;
        }
        .form-control, output {
            display: block;
            font-size: 14px;
            line-height: 1.428571429;
            color: #555;
            vertical-align: middle;
        }
        .form-control {
            width: 100%;
            height: 34px;
            padding: 6px 12px;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .small-letters {
            font-size: 12px;
            margin-top: 6px;
            color: #aaa;
        }
        .form-control[readonly] {
            cursor: default;
            background-color: #FFF;
        }
        #webIntegrationLink {
            font-size: 12px;
            font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;
        }
        .form-control.pre-like {
            font-size: 12px;
            font-family: Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;
            padding: 12px;
            height: auto;
            line-height: 20px;
            border-color: transparent;
            box-shadow: none;
            background-color: #F8F8F8;
            word-wrap: break-word;
        }
        .form-control.pre-like {
            margin-bottom: 20px;
        }
        hr {
            margin-top: 20px;
            margin-bottom: 20px;
            border: 0;
            border-top: 1px solid #eee;
        }
        hr.section-break {
            margin-top: 70px;
            margin-bottom: 70px;
        }
        pre {
            display: block;
            padding: 9.5px;
            margin: 0 0 10px;
            font-size: 13px;
            line-height: 1.428571429;
            color: #333;
            word-break: break-all;
            word-wrap: break-word;
            background-color: #f5f5f5;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        pre {
            background-color: #F8F8F8;
            border-color: transparent;
        }
        code {
            padding: 2px 4px;
            font-size: 90%;
            color: #c7254e;
            white-space: nowrap;
            background-color: #f9f2f4;
            border-radius: 4px;
        }
        code {
            color: inherit;
            background-color: #F8F8F8;
        }
        .coloured {
            padding: 3px 6px;
            border-radius: 3px;
            margin: 3px;
        }
        .coloured.blue {
            background-color: rgba(52, 152, 219, 0.1);
        }
        .coloured.violet {
            background-color: rgba(155, 89, 182, 0.1);
        }
        .coloured.yellow {
            background-color: rgba(241, 196, 15, 0.1);
        }
        .coloured.red {
            background-color: rgba(236, 112, 99, 0.1);
        }
        .coloured.gray {
            background-color: rgba(187, 187, 187, 0.1);
        }
        #docs-toc a,
        #docs-toc a:hover {
            color: #6767E1;
            line-height: 1.4em;
        }
        #docs-toc h3 a {
            font-size: 14px;
            color: black;
        }
        #docs-toc h3 {
            margin-top: 10px;
            font-size: 26px;
            font-weight: 700;
        }
        #docs-content ol {
            display: block;
            list-style-type: decimal;
            margin-block-start: 1em;
            margin-block-end: 1em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            padding-inline-start: 40px;
        }
        #docs-content h2 {
            color: black;
            font-weight: bold;
            font-size: 22px;
            margin-bottom: 20px;
        }
        #docs-content h3 {
            color: #6E5DDF;
            font-weight: bold;
            font-size: 22px;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        #docs-content h4 {
            margin-top: 30px;
            font-size: 18px;
            color: #428bca;
            margin-bottom: 20px;
        }
        #docs-content h5 {
            margin-bottom: 20px;
            margin-top: 20px;
            font-size: 18px;
        }
        h4, h5 {
            color: #9aa5aa;
        }
        p {
            margin: 0 0 10px;
            color: #333;
            font-size: 14px;
        }
        ol.with-disks li {
            list-style: disc !important;
            line-height: 1.4em;
        }

        #docs-content .table {
            margin-top: 20px;
        }
        #docs-content .table thead {
            background-color: #F2F2F2;
        }
        #docs-content .table tbody {
            color: #999;
        }
        #docs-content .table tbody tr td:first-child {
            color: #333;
        }

        .table>caption+thead>tr:first-child>td, .table>caption+thead>tr:first-child>th, .table>colgroup+thead>tr:first-child>td, .table>colgroup+thead>tr:first-child>th, .table>thead:first-child>tr:first-child>td, .table>thead:first-child>tr:first-child>th {
            border-top: 0;
        }
        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
            padding: 8px;
            line-height: 1.428571429;
            vertical-align: top;
            border-top: 1px solid #ddd;
        }
        select#selectedTool {
            height: 34px;
            padding: 6px 12px;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .btn {
            background-color: #6834b7;
            color: #fff;
            border: 2px solid #6834b7;
            outline: none;
            width: 200px;
            height: 45px;
            font-size: 18px;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn:hover {
            background-color: #7c4dc2;
            color: #fff;
        }
        .arrow-down-wrap {
            text-align: center;
            margin: 40px;
        }
        #docs-toc .title:before{
            display: none;
        }
        #docs-toc .menu-dev{
            display: block;
        }
        @media (max-width: 1024px){
            #docs-toc {
                padding: 25px;
                width: 100%;
                position: fixed;
                top: 0;
                height: auto;
                Z-INDEX: 1;
            }
            #docs-toc .title {
                background-image: url(../img/how-to-icc.svg);
                background-repeat: no-repeat;
                background-position: left center;
                padding: 20px 60px 20px;
                font-weight: bold;
                font-size: 30px;
                text-align: left;
                color: #333333;
                position: relative;
                margin: 0;
            }
            #docs-toc .menu-dev{
                display: none;
            }
            #docs-content {
                margin-left: 0px;
                position: relative;
                padding: 25px;
                padding-left: 25px;
            }
        }
        @media (max-width: 768px) {
            #docs-toc .title{padding: 10px 15px 10px;}
        }
    </style>
@endsection