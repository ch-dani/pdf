<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" >
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">

		<title>Online PDF editor</title>
		<link rel="stylesheet" type="text/css" href="/libs/fancybox/jquery.fancybox.min.css">
		<link rel="stylesheet" href="/assets/select2/select2.min.css">
		<link rel="stylesheet" href="/libs/jquery-ui/jquery-ui.css">				
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
		<link href="https://fonts.googleapis.com/css?family=Amatic+SC|Charmonman|Courgette|Dancing+Script|Dokdo|Gamja+Flower|Gloria+Hallelujah|Indie+Flower|Pacifico|Patrick+Hand|Permanent+Marker|Shadows+Into+Light" rel="stylesheet">
		<link rel="stylesheet" href="/css/fonts.css">
		<link rel="stylesheet" href="/css/reset.css">
		<link rel="stylesheet" href="/css/style.css">
		<link rel="stylesheet" href="/css/main.css">
		<link href="/css/StyleSheet.css" rel="stylesheet" />
		<link href="/css/media.css" rel="stylesheet" />
		<link rel="stylesheet" href="/css/additional-popup-style.css"> 		
		<link rel="stylesheet" type="text/css" href="/libs/pdfjs-dist/web/pdf_viewer.css">
		
		<!-- STYLESHEETS END -->
	</head>
	<body>
		<header>
			<div class="header-top">
				<div class="container">
					<div class="header-wrap">
						<a href="index" class="header-logo">
							<!-- <img src="/img/logo.svg" alt="Alternate Text" /> -->
						</a>
						<div class="menu_mob">
							<span></span>
							<span></span>
							<span></span>
						</div>
						<div class="header-menu-block">
							<ul class="header-menu">
								<li class="open-menu-btn"><a>All tools</a></li>
								<li><a href="pdf-editor">Edit</a></li>
								<li><a href="pdf-to-jpg.php">Convert</a></li>
								<li><a href="pdf-editor-fill-sign">Fill & Sign</a></li>
								<li><a href="merge-pdf">Merge</a></li>
								<li><a href="delete-pdf-pages">Delete Pages</a></li>
							</ul>
							<ul class="header-lists">
								<!-- <li><a href="#">Pricing</a></li> -->
								<li class="login-btn"><a href="#">Login <img src="/img/user-iccon.svg" alt="Alternate Text" /></a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div id="megaMenu" style="" class="container-fluid mega-menu">
				<div class="mega-menu-inner">
					<button aria-label="Close" class="close" type="button"><span aria-hidden="true">×</span></button>
					<div class="container">
						<div class="row">
							<div class="col col-sm-2 col-xs-6">
								<ul class="popular">
									<li class="header">
										<strong>Popular</strong>
									</li>
									<li>
										<a href="split-pdf">
											Split by pages
											<div class="tooltip-block">Get a new document containing only the desired pages</div>
										</a>
									</li>
									<li>
										<a href="split-pdf-by-outline">
											Split by bookmarks
											<div class="tooltip-block">Extract chapters to separate documents based on the bookmarks in the table of contents</div>
										</a>
									</li>
									<li>
										<a href="html-to-pdf">
											HTML to PDF
											<div class="tooltip-block">Convert web pages or HTML files to PDF documents</div>
										</a>
									</li>
									<li>
										<a href="rotate-pdf-pages">
											Rotate
											<div class="tooltip-block">Rotate and save PDF pages permanently</div>
										</a>
									</li>
									<li>
										<a href="alternate-mix-pdf">
											Alternate & Mix
											<div class="tooltip-block">Mixes pages from 2 or more documents, alternating between them</div>
										</a>
									</li>
									<li>
										<a href="delete-pdf-pages">
											Delete Pages
											<div class="tooltip-block">Remove pages from a PDF document</div>
										</a>
									</li>
									<li>
										<a href="compress-pdf">
											Compress
											<div class="tooltip-block">Reduce the size of your PDF</div>
										</a>
									</li>
								</ul>
							</div>
							<div class="col col-sm-2 col-xs-6">
								<ul class="popular">
									<li class="header">
										<strong>MERGE</strong>
									</li>
									<li>
										<a href="alternate-mix-pdf">
											Alternate & Mix
											<div class="tooltip-block">Mixes pages from 2 or more documents, alternating between them</div>
										</a>
									</li>
									<li>
										<a href="merge-pdf">
											Merge
											<div class="tooltip-block">Combine multiple PDFs and images into one</div>
										</a>
									</li>
									<li>
										<a href="visually-combine-reorder-pdf">
											Combine & Reorder
											<div class="tooltip-block">Merge pages from different documents, reorder pages if needed</div>
										</a>
									</li>
								</ul>
							</div>
							<div class="col col-sm-2 col-xs-6">
								<ul>
									<li class="header">
										<strong>Edit &amp; Sign</strong>
									</li>
									<li>
										<a href="bates-numbering-pdf">
											Bates Numbering
											<div class="tooltip-block">Bates stamp multiple files at once</div>
										</a>
									</li>
									<li>
										<a href="crop-pdf">
											Crop
											<div class="tooltip-block">Trim PDF margins, change PDF page size</div>
										</a>
									</li>
									<li>
										<a href="delete-pdf-pages">
											Delete Pages
											<div class="tooltip-block">Remove pages from a PDF document</div>
										</a>
									</li>
									<li>
										<a href="pdf-editor">
											Edit
											<div class="tooltip-block">Edit PDF files for free. Fill & sign PDF. Add text, links, images and shapes. Edit existing PDF text. Annotate PDF</div>
										</a>
									</li>
									<li>
										<a href="pdf-editor-fill-sign">
											Fill &amp; Sign
											<div class="tooltip-block">Fill and Sign PDF Online Free</div>
										</a>
									</li>
									<li>
										<a href="grayscale-pdf">
											Grayscale
											<div class="tooltip-block">Convert PDF text and images to grayscale</div>
										</a>
									</li>
									<li>
										<a href="header-footer-pdf">
											Header &amp; Footer
											<div class="tooltip-block">Apply page numbers or text labels to PDF files</div>
										</a>
									</li>
									<li>
										<a href="n-up-pdf">
											N-up
											<div class="tooltip-block">Print multiple pages per sheet per paper. A5 plan as 4-up on A3 or A4 2-up on A3</div>
										</a>
									</li>
								</ul>
							</div>
							<div class="col col-sm-2 col-xs-6">
								<ul>
									<li class="header">
										<strong> </strong>
									</li>
									<li>
										<a href="encrypt-pdf">
											Protect
											<div class="tooltip-block">Protect file with password and permissions</div>
										</a>
									</li>
									<li>
										<a href="rotate-pdf-pages">
											Rotate
											<div class="tooltip-block">Rotate and save PDF pages permanently</div>
										</a>
									</li>
									<li>
										<a href="repair-pdf">
											Repair
											<div class="tooltip-block">Recover data from a corrupted or damaged PDF document</div>
										</a>
									</li>
									<li>
										<a href="resize-pdf">
											Resize
											<div class="tooltip-block">Add page margins and padding, Change PDF page size</div>
										</a>
									</li>
									<li>
										<a href="pdf-editor-fill-sign">
											Sign
											<div class="tooltip-block">Fill and Sign PDF Online Free</div>
										</a>
									</li>
									<li>
										<a href="unlock-pdf">
											Unlock
											<div class="tooltip-block">Remove restrictions and password from PDF files</div>
										</a>
									</li>
									<li>
										<a href="watermark-pdf">
											Watermark
											<div class="tooltip-block">Add image or text watermark to PDF documents</div>
										</a>
									</li>
								</ul>
							</div>
							<div class="col col-sm-2 col-xs-6">
								<ul class="mob-min-height">
									<li class="header" style="line-height:18px;margin-top:-10px;">
										<strong>COMPRESS &amp; <br/> CONVERT</strong>
									</li>
									<li>
										<a href="compress-pdf">
											Compress
											<div class="tooltip-block">Reduce the size of your PDF</div>
										</a>
									</li>
									<li>
										<a href="pdf-to-jpg">
											PDF to JPG
											<div class="tooltip-block">Get PDF pages converted to JPG, PNG or TIFF images</div>
										</a>
									</li>
									<li>
										<a href="pdf-to-word">
											PDF to Word
											<div class="tooltip-block">Creates a Microsoft Word .docx with text and images from PDF, optimizing for legibility</div>
										</a>
									</li>
									<li>
										<a href="pdf-to-excel">
											PDF to Excel
											<div class="tooltip-block">Convert PDF to Excel or CSV online for free. Extract table data from PDF</div>
										</a>
									</li>
									<li>
										<a href="extract-text-from-pdf">
											PDF to Text
											<div class="tooltip-block">Copies all text from the PDF document and extracts it to a separate text file</div>
										</a>
									</li>
									<li>
										<a href="jpg-to-pdf">
											JPG to PDF
											<div class="tooltip-block">Convert Images to PDF</div>
										</a>
									</li>
									<li>
										<a href="word-to-pdf">
											Word to PDF
											<div class="tooltip-block">Creates a PDF document from Microsoft Word .docx</div>
										</a>
									</li>
									<li>
										<a href="html-to-pdf">
											HTML to PDF
											<div class="tooltip-block">Convert web pages or HTML files to PDF documents</div>
										</a>
									</li>
									<li>
										<a href="ocr-pdf">
											OCR PDF
											<div class="tooltip-block">Convert web pages or HTML files to PDF documents</div>
										</a>
										<span class="label label-success">New</span>
									</li>
								</ul>
							</div>
							<div class="col col-sm-2 col-xs-6">
								<ul class="first">
									<li class="header">
										<strong>SPLIT</strong>
									</li>
									<li>
										<a href="extract-pdf-pages">
											Extract Pages
											<div class="tooltip-block">Get a new document containing only the desired pages</div>
										</a>
									</li>
									<li>
										<a href="split-pdf-by-outline">
											Split by bookmarks
											<div class="tooltip-block">Extract chapters to separate documents based on the bookmarks in the table of contents</div>
										</a>
									</li>
									<li>
										<a href="split-pdf-down-the-middle">
											Split in half
											<div class="tooltip-block">Split two page layout scans, A3 to double A4 or A4 to double A5</div>
										</a>
									</li>
									<li>
										<a href="split-pdf-by-size">
											Split by size
											<div class="tooltip-block">Get multiple smaller documents with specific file sizes</div>
										</a>
									</li>
									<li>
										<a href="split-pdf-by-text">
											Split by text
											<div class="tooltip-block">Extract separate documents when specific text changes from page to page</div>
										</a>
									</li>
								</ul>
							</div>
							<div class="menu-serch">
								<input placeholder="Quickly find a tool"  type="text">
							</div>
						</div>
					</div>
				</div>
			</div>
		</header>
		<div class="overley-bg"></div>

