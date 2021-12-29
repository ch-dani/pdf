@extends('layouts.layout')

@section('content')

<section class="single-title-section"><h1>{!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : 'Invoice Generator' !!}</h1></section>

<input type="hidden" id="editor_csrf" value="{{ csrf_token() }}">

<section class="invoice-section">


		@if($ads && $device_is=='phone')
			@include("ads.adx320x100")
			<style>
				.invoice-section{
					padding-top: 0;
				}
			</style>
			
		@endif  


		@if($ads && $device_is=='computer')

			<style>
				.invoice-section{
					padding-top: 0;
				}
			</style>
		
			@include("ads.adx970x90")
		@endif


	<div class="container">
		<div class="invoice-box">
			<div class="invoice-left">
				<div class="invoice-small-box">
					<h3>Resize table</h3>
					<a id="add-row" href="#" class="btn">Add row</a>
					<a id="delete-row" href="#" class="btn">Delete row</a>
				</div>
				<div class="invoice-small-box" style="display: none;">
					<h3>Add logo</h3>
					<div class="input-file-box">
						<input type="file" name="file" id="inputfile" class="inputfile" />
						<div class="file-infp-row">
							<div class="file-name">
								<img src="/img/push-pin-icon.svg" alt="icon">
								<span>No file</span>
							</div>
							<a style="display: none;" href="#" class="delete-file"><img src="/img/delete-file-icon.svg" alt="Delete"></a>
						</div>
						<a href="#" class="btn file-btn">Choose a file</a>
					</div>
				</div>  
				<div class="invoice-small-box">
					<h3>Color tool</h3>
					<p>Choose color you need then click on<br> any object on your invoice</p>
          <ul class="chose-color">
            <li class="c-green"><a href="#" data-bgcolor="rgb(94,189,62)" data-color="rgb(51,51,51)"></a></li>
            <li class="c-yellow"><a href="#" data-bgcolor="rgb(255,185,0)" data-color="rgb(51,51,51)"></a></li>
            <li class="c-orange"><a href="#" data-bgcolor="rgb(247,130,0)" data-color="rgb(51,51,51)"></a></li>
            <li class="c-red"><a href="#" data-bgcolor="rgb(226,56,56)" data-color="rgb(255,255,255)"></a></li>
            <li class="c-purple"><a href="#" data-bgcolor="rgb(151,57,153)" data-color="rgb(255,255,255)"></a></li>
            <li class="c-blue"><a href="#" data-bgcolor="rgb(0,156,223)" data-color="rgb(255,255,255)"></a></li>
            <li class="c-black"><a href="#" data-bgcolor="rgb(0,0,0)" data-color="rgb(255,255,255)"></a></li>
            <li class="c-grey-dark"><a href="#" data-bgcolor="rgb(78,78,78)" data-color="rgb(255,255,255)"></a></li>
            <li class="c-grey-dark-lite"><a href="#" data-bgcolor="rgb(148,148,148)" data-color="rgb(255,255,255)"></a></li>
            <li class="c-grey"><a href="#" data-bgcolor="rgb(188,188,188)" data-color="rgb(51,51,51)"></a></li>
            <li class="c-grey-lite"><a href="#" data-bgcolor="rgb(215,215,215)" data-color="rgb(51,51,51)"></a></li>
            <li class="c-grey-lite-white"><a href="#" data-bgcolor="rgb(239,239,239)" data-color="rgb(51,51,51)"></a></li>
          </ul>
				</div>
				<div class="invoice-small-box">
				  <a href="#" id="generate_invoice" class="btn gradient">Save as PDF</a>
				</div>

				@if($ads && $device_is=='computer')
					<div class="invoice-small-box" style="padding: 0;">
				
						@include("ads.adx250x250")
					</div>
				@endif
				
				
			</div>



			
		<style>
		img[src="#"]{
			display: none;
		}
		</style>
      <div class="invoice-right page" id="invoice_content" style="position: relative;">
        <div class="i-r-wrapp">
          <div class="header-invoice border_bottom_elements" style="">
            <div class="h-i-left">
            	<div class="logo_outer without_image">
              		<img class="grab_img pdf_logo" src="#" alt="infoce-cart-icon" style="max-height: 44px;" />
              	</div>
              <h3><span contenteditable="true" class="input-label form-control grab_it disable_enter" type="text" sname="company_name" placeholder="Your company name" value="">Your company name</span></h3>
              <address>
                <span class="input-label form-control grab_it disable_enter" type="text" sname="address_line_1" placeholder="Your Street" contenteditable="true">Your Street</span>
                <span class="input-label form-control grab_it disable_enter" type="text" name="address_line_2" placeholder="Your Town" contenteditable="true"  value="">Your Town</span>
                <span class="input-label form-control grab_it disable_enter" type="text" name="address_line_3" placeholder="Address Line 3" contenteditable="true" value="Address Line 3">Address Line 3</span>

                <span class="input-label form-control grab_it disable_enter" type="text" name="phone" placeholder="(123) 456 789" value="(123) 456 789" contenteditable="true">(123) 456 789</span>
                <span class="input-label form-control grab_it disable_enter" type="email" name="email" placeholder="email@yourcompany.com" value="email@yourcompany.com" contenteditable="true">email@yourcompany.com</span>
              </address>
            </div>
            <div class="h-i-right">
              <h3 class="grab_it">INVOICE</h3>
              <input type="text" class="grab_it input-label form-control" id="datepicker" name="date" placeholder="23-May-2019" value="{{ date('d-M-Y') }}">
              <span contenteditable=true class="grab_it input-label form-control disable_enter" type="text" name="invoice_number" placeholder="Invoice #0000000" value="Invoice #2334889">Invoice #2334889</span>
              <span contenteditable=true class="grab_it input-label form-control disable_enter" type="text" name="invoice_po" placeholder="PO #000000000" value="PO 456001200">PO 456001200</span>
              <span contenteditable=true class="grab_it input-label form-control disable_enter" type="text" name="terms" placeholder="Terms: Due on receipt" value="Terms: Due on receipt">Terms: Due on receipt</span>

              <span contenteditable="true" class="grab_it input-label form-control input-label-bold mt-28 disable_enter" type="text" name="att" placeholder="Att: Ms. Jane Doe" value="">Att: Ms. Jane Doe</span>
              <span contenteditable="true" class="grab_it input-label form-control input-label-bold disable_enter" type="text" name="client_company_name" placeholder="Client Company Name" value="">Client Company Name</span>

            </div>
          </div>
          <div class="body-invoice">
<div contenteditable="true" name="textarea1" class="grab_it" id="textarea-main" placeholder="Your text">Dear Ms. Jane Doe,<br><br>

Please find below a cost-breakdown for the recent work completed. Please make payment at your earliest convenience, and do not hesitate to contact me with any questions.<br><br>

Many thanks,<br>
Your Name</div>
            <div class="invoice-table-wrapper">
              <table class="invoice-table" style="">
                <thead >
                <tr class="bg_elements">
                  <th><span class="grab_it">#</span></th>
                  <th><span class="grab_it" contenteditable="true">Item Description</span></th>
                  <th><span class="grab_it" contenteditable="true">Quantity</span></th>
                  <th><span class="grab_it" contenteditable="true">Unit price (€)</span></th>
                  <th><span class="grab_it" contenteditable="true">Total (€)</span></th>
                </tr>
                </thead>
                <tbody>
                
                @php 
                	$tr_template = '<tr class="border_elements">
                  <td class="number"><span class="grab_it">%s</span></td>
                  <td class="desc">
                  	<span class="grab_it disable_enter" onfoc1us="document.execCommand(\'selectAll\',false,null)" contenteditable="true">Supporting of in-house project (hours worked)</span>
                  </td>
                  <td class="quantity"><span class="grab_it float_only" contenteditable="true">2</span></td>
                  <td class="price"><span class="grab_it float_only" contenteditable="true">125.00</span></td>
                  <td class="total"><span class="grab_it val">250.00</span></td>
                </tr>';
               	$tr_count = 2;
                @endphp
                
                @for ($i = 1; $i != $tr_count; $i++)
                	{!! sprintf($tr_template, $i) !!}
                
                @endfor
                
                </tbody>
              </table>
            </div>
            <div class="item-price subtotal-item bg_elements">
              <span class="grab_it">Subtotal</span>
              <span class="price grab_it">0.00</span>
            </div>
            <div class="item-price sales-item bg_elements">
              <span class="sales-input grab_it">Sales Tax (<span class="float_only" contenteditable="true" id="salex-tax-input" type="text" value="20">20</span>%)</span>
              <span class="sales-price grab_it">0.00</span>
            </div>
            <div class="item-price total-item bg_elements">
              <span class="grab_it">Total</span>
              <span class="total-count grab_it">0.00</span>
            </div>
            <div class="bottom-text">
<div contenteditable="true" class="grab_it" name="textarea1" id="textarea-bottom" placeholder="Your text">Many thanks for your custom! I look forward to doing business with you again in due course.</div>

            </div>
          </div>
        </div>
      </div>
		</div>
	</div>



		@if($ads && $device_is=='computer')
			@include("ads.adx970x90")
		@endif

		@if($ads && $device_is=='phone')
			@include("ads.adx320x100")
		@endif  


</section>







    @include ('inc.result_block_new')
@endsection
