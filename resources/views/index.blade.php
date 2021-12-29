@extends('layouts.layout')

@php
    //$accept = 'application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    $accept = 'application/pdf';
@endphp

@section('content-freeconvert')
    <main class="file_not_loaded">
        @include('page_parts.toolheader')

        <section class="section_top converting tool_section pdfloader_section after_upload hidden">
            <div class="container">
                <div class="title-wrapper">
                    <h2 class="h30-title title_main">
                        {!! array_key_exists(1, $PageBlocks) ? $PageBlocks[1] : t('Convert Word to PDF Documents (DOC to PDF)') !!}
                    </h2>
                    <h3 class="sub-title">
                        {!! array_key_exists(2, $PageBlocks) ? $PageBlocks[2] : t('Creates a PDF document from Microsoft Word file (.docx)') !!}
                    </h3>
                </div>
                <div class="convert_docs_wrapper" id="pages-pdf">
                    <?php /*
                    <div class="convert_doc left_doc">
                        <div class="convert_doc_content">
                            <form action="#" enctype="multipart/form-data" method="POST">
                                <input type="file"
                                       accept="{{ $accept ?? 'application/pdf' }}"
                                       title="Upload" multiple="multiple" data-scope="task-file" name="file"
                                       class="fileupload user_pdf">
                            </form>
                            <h4 class="title_convert_doc">{{ t('Choose file') }}</h4>
                            <div class="icon_add_doc">
                                <img src="{{ asset('freeconvert/img/icon-add-file.svg') }}" alt="">
                                <div class="icon_add_select" id="docSelectBtn">
                                    @php include(public_path('freeconvert/img/icon-add-file-arr.svg')) @endphp
                                </div>
                            </div>
                            <h5 class="sub_title_convert_doc">{{ t('or drop files here') }}</h5>
                        </div>
                        <div class="select_wrapper" id="docSelect">
                            <a href="#" class="select_item">
                                @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
                                Dropbox
                            </a>
                            <a href="#" class="select_item">
                                <img src="{{ asset('freeconvert/img/logos_google-drive.svg') }}" alt="">
                                Google Drive
                            </a>
                            <a href="#" class="select_item">
                                @php //include(public_path('freeconvert/img/logo-link.svg')) @endphp
                                {{ t('Web Address (URL)') }}
                            </a>
                        </div>
                    </div>
                    */ ?>
                </div>
                <?php /*
                <div class="downloader">
                    <div class="downloader__upload">
                        <div class="downloader__icon">
                            <img src="{{ asset('freeconvert/img/download_arrow.svg') }}">
                        </div>
                        <div class="downloader__text save-pdf">{{ t('Process PDF') }}</div>
                        <div class="downloader__arrow"></div>
                    </div>
                </div>
                */ ?>
                <?php /*
                <div class="link_convert">
                    <div class="link_convert_left">
                        {{--<a href="#" class="link_convert_item">
                            @php include(public_path('freeconvert/img/link_conver-1.svg')) @endphp
                            {{ t('Merge PDF') }}
                        </a>
                        <a href="#" class="link_convert_item">
                            <img src="{{ asset('freeconvert/img/link_conver-2.svg') }}" alt="">
                            {{ t('Compress') }}
                        </a>--}}
                        {{--<a href="#" class="link_convert_item" id="showRemoveIcons">
                            @php include(public_path('freeconvert/img/link_conver-3.svg')) @endphp
                            {{ t('Remove') }}
                        </a>--}}
                    </div>
                    {{-- <div class="link_convert_right">
                         <a href="#" class="link_convert_item">
                             <img src="{{ asset('freeconvert/img/logos_google-drive.svg') }}" alt="">
                             {{ t('Save to Google Drive') }}
                         </a>
                         <a href="#" class="link_convert_item">
                             @php include(public_path('freeconvert/img/logos_dropbox.svg')) @endphp
                             {{ t('Save to Dropbox') }}
                         </a>
                     </div>--}}
                </div>
                */ ?>
            </div>
        </section>
        
        <?php 
        $PageGuides = \App\Guide::where("id", 20)->get(); //->toArray();
		
		$PageGuidesSite = [];
        foreach ($PageGuides as $key => $PageGuide) {
            $PageGuidesSite[$key] = (object)[
                'title' => $PageGuide->title,
                'subtitle' => $PageGuide->subtitle,
                'content' => $PageGuide->content,
            ];

            $titles = json_decode($PageGuide->title, true);
            $subtitles = json_decode($PageGuide->subtitle, true);
            $contents = json_decode($PageGuide->content, true);




            $PageGuidesSite[$key]->title = (isset($titles[$ActiveLanguage->id]) and !empty($titles[$ActiveLanguage->id])) ? $titles[$ActiveLanguage->id] : (isset($titles[1]) ? $titles[1] : '');
            $PageGuidesSite[$key]->subtitle = (isset($subtitles[$ActiveLanguage->id]) and !empty($subtitles[$ActiveLanguage->id])) ? $subtitles[$ActiveLanguage->id] : (isset($subtitles[1]) ? $subtitles[1] : '');
            $PageGuidesSite[$key]->content = (isset($contents[$ActiveLanguage->id]) and !empty($contents[$ActiveLanguage->id])) ? $contents[$ActiveLanguage->id] : (isset($contents[1]) ? $contents[1] : '');
        }
        $PageGuides = $PageGuidesSite;



        ?>

		<section class="module__how-convert module bg-white pb_5">
		    <div class="container">
		        <div class="title-wrapper">
		            <h2 class="h2-title title_main">{{t("How to convert Word to PDF?")}}</h2>

		        </div>
		        <div class="row">
					@if (count($PageGuides))
						@foreach ($PageGuides as $Guide)
						    @if (!is_null($Guide->content))
						        {!! htmlspecialchars_decode($Guide->content) !!}
						    @endif
						@endforeach
					@endif
					
					
					
					<div class="row testimonial">
                   
                    <div class="container">
                    <div class="title-wrapper">
            <h2 class="h2-title title_main">Subscription Planes</h2>
        </div>
        <div class="row mt-3">
            <div class="col-12"></div>
        
            @foreach($subscriptionPlans as $subscriptionPlan)
                <div class="col-md-6 col-lg-4">
                    <div class="card_box rate_card">
                        <span class="card_months">{{ $subscriptionPlan->name }}</span>
                        <span class="card_price">${{ $subscriptionPlan->price }}</span>
                        <span class="card_period">{{ $subscriptionPlan->description }}</span>
                        <a href="#" class="def_gradient_btn purchase-subscription-trigger"
		                       data-plan="{{ $subscriptionPlan->id }}">{{ t('Subscribe') }}</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


                    </div>
					
					
					
					
					
					
					
					
					
					
					
					@if(!Auth::id())
				        <div class="contact-us">
				            <a class="contact-us__button sign-up-trigger" href="{{route("login")}}">{{t("Sign Up")}}</a>
				        </div>
		            @endif
		        </div>
		    </div>
		</section>

        <div class="popup_wrpr select_converter">
            <div class="popup_wrpr_inner">
                <div class="choose_popup">
                    <h2 class="h2-title title_main">{{ t("Select Converter") }}:</h2>
                    <div class="btns_wrpr">
                        <a href="/pdf-to-word" class="convert_to_btn blue">
                            <svg width="26" height="30" viewBox="0 0 26 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.6287 0H5.57946C3.81912 0 2.38645 1.43367 2.38645 3.19295V14.9999H2.07453C1.36407 14.9999 0.787949 15.5756 0.787949 16.2866V24.0896C0.787949 24.8006 1.36407 25.3762 2.07453 25.3762H2.38645V26.807C2.38645 28.5682 3.81912 29.9999 5.5794 29.9999H22.0199C23.7792 29.9999 25.2119 28.5681 25.2119 26.807V7.55687L17.6287 0ZM2.91646 17.3584C3.36614 17.2869 3.95197 17.2466 4.5704 17.2466C5.59807 17.2466 6.26447 17.431 6.78664 17.8242C7.34857 18.242 7.70156 18.9089 7.70156 19.8641C7.70156 20.8996 7.32449 21.6144 6.80232 22.0558C6.23255 22.5293 5.36523 22.754 4.30564 22.754C3.67122 22.754 3.22154 22.7138 2.91652 22.6736V17.3584H2.91646ZM22.02 28.0445H5.57946C4.89781 28.0445 4.34279 27.4895 4.34279 26.807V25.3762H19.6688C20.3793 25.3762 20.9555 24.8006 20.9555 24.0896V16.2866C20.9555 15.5756 20.3794 14.9999 19.6688 14.9999H4.34279V3.19295C4.34279 2.51236 4.89781 1.95734 5.57946 1.95734L16.8971 1.94551V6.12892C16.8971 7.35085 17.8886 8.34324 19.1113 8.34324L23.2095 8.33148L23.2556 26.8069C23.2557 27.4895 22.7015 28.0445 22.02 28.0445ZM8.25852 20.0326C8.25852 18.4107 9.29415 17.1986 10.8921 17.1986C12.5543 17.1986 13.4614 18.443 13.4614 19.9365C13.4614 21.7105 12.3856 22.7863 10.8038 22.7863C9.19808 22.7863 8.25852 21.5743 8.25852 20.0326ZM17.0373 21.7665C17.4065 21.7665 17.8159 21.686 18.0571 21.5899L18.2415 22.5451C18.0169 22.6579 17.511 22.7785 16.853 22.7785C14.9819 22.7785 14.0184 21.6144 14.0184 20.0729C14.0184 18.2263 15.3354 17.1986 16.973 17.1986C17.6075 17.1986 18.089 17.3271 18.3057 17.4389L18.0571 18.4107C17.808 18.3057 17.4628 18.2097 17.0294 18.2097C16.0576 18.2097 15.303 18.7961 15.303 20.0003C15.3031 21.0839 15.9453 21.7665 17.0373 21.7665Z" fill="white"></path>
                            </svg>
                            {{ t("To Word") }}
                        </a>
                        <a href="/pdf-to-excel" class="convert_to_btn green">
                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.6286 0H7.57935C5.81902 0 4.38634 1.43367 4.38634 3.19295V14.9999H4.07442C3.36396 14.9999 2.78784 15.5756 2.78784 16.2866V24.0896C2.78784 24.8006 3.36396 25.3762 4.07442 25.3762H4.38634V26.807C4.38634 28.5682 5.81902 29.9999 7.57929 29.9999H24.0198C25.7791 29.9999 27.2118 28.5681 27.2118 26.807V7.55687L19.6286 0ZM24.0199 28.0445H7.57935C6.89771 28.0445 6.34268 27.4895 6.34268 26.807V25.3762H21.6687C22.3792 25.3762 22.9554 24.8006 22.9554 24.0896V16.2866C22.9554 15.5756 22.3792 14.9999 21.6687 14.9999H6.34268V3.19295C6.34268 2.51236 6.89771 1.95734 7.57935 1.95734L18.897 1.94551V6.12892C18.897 7.35085 19.8885 8.34324 21.1112 8.34324L25.2094 8.33148L25.2555 26.8069C25.2555 27.4895 24.7014 28.0445 24.0199 28.0445Z" fill="white"></path>
                                <path d="M8.40103 19.9042C8.40887 18.7795 7.75043 18.1862 6.69862 18.1862C6.42547 18.1862 6.24894 18.2097 6.14453 18.2341V21.7743C6.24894 21.7989 6.41769 21.7989 6.5702 21.7989C7.67832 21.8066 8.40103 21.1967 8.40103 19.9042Z" fill="white"></path>
                                <path d="M14.1687 19.9758C14.1687 19.005 13.7029 18.1695 12.8599 18.1695C12.0328 18.1695 11.5508 18.9569 11.5508 20.0082C11.5508 21.0683 12.0489 21.8145 12.8678 21.8145C13.695 21.8145 14.1687 21.0279 14.1687 19.9758Z" fill="white"></path>
                                <path d="M11.2305 23H9.44531L8.33203 21.2148L7.23047 23H5.48438L7.37891 20.0781L5.59766 17.2891H7.30859L8.33984 19.0547L9.33203 17.2891H11.0938L9.28516 20.1992L11.2305 23ZM11.875 23V17.2891H13.418V21.7539H15.6172V23H11.875ZM20.2656 21.2656C20.2656 21.6198 20.1758 21.9349 19.9961 22.2109C19.8164 22.4844 19.5573 22.6979 19.2188 22.8516C18.8802 23.0026 18.4831 23.0781 18.0273 23.0781C17.6471 23.0781 17.3281 23.0521 17.0703 23C16.8125 22.9453 16.5443 22.8516 16.2656 22.7188V21.3438C16.5599 21.4948 16.8659 21.6133 17.1836 21.6992C17.5013 21.7826 17.793 21.8242 18.0586 21.8242C18.2878 21.8242 18.4557 21.7852 18.5625 21.707C18.6693 21.6263 18.7227 21.5234 18.7227 21.3984C18.7227 21.3203 18.7005 21.2526 18.6562 21.1953C18.6146 21.1354 18.5456 21.0755 18.4492 21.0156C18.3555 20.9557 18.1029 20.8333 17.6914 20.6484C17.319 20.4792 17.0391 20.3151 16.8516 20.1562C16.6667 19.9974 16.5286 19.8151 16.4375 19.6094C16.349 19.4036 16.3047 19.1602 16.3047 18.8789C16.3047 18.3529 16.4961 17.9427 16.8789 17.6484C17.2617 17.3542 17.7878 17.207 18.457 17.207C19.0482 17.207 19.651 17.3438 20.2656 17.6172L19.793 18.8086C19.2591 18.5638 18.7982 18.4414 18.4102 18.4414C18.2096 18.4414 18.0638 18.4766 17.9727 18.5469C17.8815 18.6172 17.8359 18.7044 17.8359 18.8086C17.8359 18.9206 17.8932 19.0208 18.0078 19.1094C18.125 19.1979 18.4401 19.3594 18.9531 19.5938C19.4453 19.8151 19.7865 20.0534 19.9766 20.3086C20.1693 20.5612 20.2656 20.8802 20.2656 21.2656Z" fill="#20DA91"></path>
                            </svg>
                            {{ t("To Excel") }}
                        </a>
                        <a href="/pdf-to-ppt" class="convert_to_btn orange">
                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.6286 0H7.57935C5.81902 0 4.38634 1.43367 4.38634 3.19295V14.9999H4.07442C3.36396 14.9999 2.78784 15.5756 2.78784 16.2866V24.0896C2.78784 24.8006 3.36396 25.3762 4.07442 25.3762H4.38634V26.807C4.38634 28.5682 5.81902 29.9999 7.57929 29.9999H24.0198C25.7791 29.9999 27.2118 28.5681 27.2118 26.807V7.55687L19.6286 0ZM24.0199 28.0445H7.57935C6.89771 28.0445 6.34268 27.4895 6.34268 26.807V25.3762H21.6687C22.3792 25.3762 22.9554 24.8006 22.9554 24.0896V16.2866C22.9554 15.5756 22.3792 14.9999 21.6687 14.9999H6.34268V3.19295C6.34268 2.51236 6.89771 1.95734 7.57935 1.95734L18.897 1.94551V6.12892C18.897 7.35085 19.8885 8.34324 21.1112 8.34324L25.2094 8.33148L25.2555 26.8069C25.2555 27.4895 24.7014 28.0445 24.0199 28.0445Z" fill="white"></path>
                                <path d="M8.40103 19.9042C8.40887 18.7795 7.75043 18.1862 6.69862 18.1862C6.42547 18.1862 6.24894 18.2097 6.14453 18.2341V21.7743C6.24894 21.7989 6.41769 21.7989 6.5702 21.7989C7.67832 21.8066 8.40103 21.1967 8.40103 19.9042Z" fill="white"></path>
                                <path d="M14.1687 19.9758C14.1687 19.005 13.7029 18.1695 12.8599 18.1695C12.0328 18.1695 11.5508 18.9569 11.5508 20.0082C11.5508 21.0683 12.0489 21.8145 12.8678 21.8145C13.695 21.8145 14.1687 21.0279 14.1687 19.9758Z" fill="white"></path>
                                <path d="M10.3438 19.1055C10.3438 19.7435 10.1549 20.237 9.77734 20.5859C9.40234 20.9323 8.86849 21.1055 8.17578 21.1055H7.74219V23H6.19922V17.2891H8.17578C8.89714 17.2891 9.4388 17.4466 9.80078 17.7617C10.1628 18.0768 10.3438 18.5247 10.3438 19.1055ZM7.74219 19.8477H8.02344C8.25521 19.8477 8.4388 19.7826 8.57422 19.6523C8.71224 19.5221 8.78125 19.3424 8.78125 19.1133C8.78125 18.7279 8.56771 18.5352 8.14062 18.5352H7.74219V19.8477ZM15.3984 19.1055C15.3984 19.7435 15.2096 20.237 14.832 20.5859C14.457 20.9323 13.9232 21.1055 13.2305 21.1055H12.7969V23H11.2539V17.2891H13.2305C13.9518 17.2891 14.4935 17.4466 14.8555 17.7617C15.2174 18.0768 15.3984 18.5247 15.3984 19.1055ZM12.7969 19.8477H13.0781C13.3099 19.8477 13.4935 19.7826 13.6289 19.6523C13.7669 19.5221 13.8359 19.3424 13.8359 19.1133C13.8359 18.7279 13.6224 18.5352 13.1953 18.5352H12.7969V19.8477ZM18.8281 23H17.2852V18.5508H15.8906V17.2891H20.2188V18.5508H18.8281V23Z" fill="#FF9933"></path>
                            </svg>
                            {{ t("To PowerPoint") }}
                        </a>
                        <a href="/pdf-to-jpg" class="convert_to_btn purple">
                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.6286 0H7.57935C5.81902 0 4.38634 1.43367 4.38634 3.19295V14.9999H4.07442C3.36396 14.9999 2.78784 15.5756 2.78784 16.2866V24.0896C2.78784 24.8006 3.36396 25.3762 4.07442 25.3762H4.38634V26.807C4.38634 28.5682 5.81902 29.9999 7.57929 29.9999H24.0198C25.7791 29.9999 27.2118 28.5681 27.2118 26.807V7.55687L19.6286 0ZM24.0199 28.0445H7.57935C6.89771 28.0445 6.34268 27.4895 6.34268 26.807V25.3762H21.6687C22.3792 25.3762 22.9554 24.8006 22.9554 24.0896V16.2866C22.9554 15.5756 22.3792 14.9999 21.6687 14.9999H6.34268V3.19295C6.34268 2.51236 6.89771 1.95734 7.57935 1.95734L18.897 1.94551V6.12892C18.897 7.35085 19.8885 8.34324 21.1112 8.34324L25.2094 8.33148L25.2555 26.8069C25.2555 27.4895 24.7014 28.0445 24.0199 28.0445Z" fill="white"></path>
                                <path d="M8.40103 19.9042C8.40887 18.7795 7.75043 18.1862 6.69862 18.1862C6.42547 18.1862 6.24894 18.2097 6.14453 18.2341V21.7743C6.24894 21.7989 6.41769 21.7989 6.5702 21.7989C7.67832 21.8066 8.40103 21.1967 8.40103 19.9042Z" fill="white"></path>
                                <path d="M14.1687 19.9758C14.1687 19.005 13.7029 18.1695 12.8599 18.1695C12.0328 18.1695 11.5508 18.9569 11.5508 20.0082C11.5508 21.0683 12.0489 21.8145 12.8678 21.8145C13.695 21.8145 14.1687 21.0279 14.1687 19.9758Z" fill="white"></path>
                                <path d="M6.26953 24.793C5.98828 24.793 5.72135 24.7656 5.46875 24.7109V23.5117C5.55208 23.5273 5.64062 23.5443 5.73438 23.5625C5.82812 23.5833 5.92969 23.5938 6.03906 23.5938C6.29427 23.5938 6.47656 23.5169 6.58594 23.3633C6.69531 23.2096 6.75 22.9466 6.75 22.5742V17.2891H8.30078V22.4414C8.30078 23.2148 8.13151 23.7995 7.79297 24.1953C7.45443 24.5938 6.94661 24.793 6.26953 24.793ZM13.6836 19.1055C13.6836 19.7435 13.4948 20.237 13.1172 20.5859C12.7422 20.9323 12.2083 21.1055 11.5156 21.1055H11.082V23H9.53906V17.2891H11.5156C12.237 17.2891 12.7786 17.4466 13.1406 17.7617C13.5026 18.0768 13.6836 18.5247 13.6836 19.1055ZM11.082 19.8477H11.3633C11.5951 19.8477 11.7786 19.7826 11.9141 19.6523C12.0521 19.5221 12.1211 19.3424 12.1211 19.1133C12.1211 18.7279 11.9076 18.5352 11.4805 18.5352H11.082V19.8477ZM16.8867 19.6562H19.3516V22.7344C18.6823 22.9635 17.9466 23.0781 17.1445 23.0781C16.2643 23.0781 15.5833 22.8229 15.1016 22.3125C14.6224 21.8021 14.3828 21.0742 14.3828 20.1289C14.3828 19.207 14.6458 18.4896 15.1719 17.9766C15.6979 17.4635 16.4349 17.207 17.3828 17.207C17.7422 17.207 18.0807 17.2409 18.3984 17.3086C18.7188 17.3763 18.9974 17.4622 19.2344 17.5664L18.7461 18.7773C18.3346 18.5742 17.8828 18.4727 17.3906 18.4727C16.9401 18.4727 16.5911 18.6198 16.3438 18.9141C16.099 19.2057 15.9766 19.6237 15.9766 20.168C15.9766 20.7018 16.0872 21.1094 16.3086 21.3906C16.5326 21.6693 16.8542 21.8086 17.2734 21.8086C17.5026 21.8086 17.7135 21.7865 17.9062 21.7422V20.8477H16.8867V19.6562Z" fill="#9C21FD"></path>
                            </svg>
                            {{ t("To JPG") }}
                        </a>
                    </div>
                    <div class="choose_more_wrpr">
                        <a href="" class="choose_more_trigger">
                            {{ t("More") }}
                            <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.76664 0.741886L4.99997 3.97522L8.2333 0.741886C8.31046 0.664734 8.40205 0.603535 8.50285 0.56178C8.60365 0.520026 8.71169 0.498535 8.8208 0.498535C8.92991 0.498535 9.03795 0.520026 9.13876 0.56178C9.23956 0.603535 9.33115 0.664734 9.4083 0.741886C9.48546 0.819037 9.54665 0.91063 9.58841 1.01143C9.63016 1.11224 9.65165 1.22028 9.65165 1.32939C9.65165 1.43849 9.63016 1.54654 9.58841 1.64734C9.54665 1.74814 9.48546 1.83973 9.4083 1.91689L5.5833 5.74189C5.50621 5.81914 5.41463 5.88043 5.31382 5.92225C5.21301 5.96406 5.10494 5.98559 4.9958 5.98559C4.88666 5.98559 4.77859 5.96406 4.67778 5.92225C4.57697 5.88043 4.4854 5.81914 4.4083 5.74189L0.583303 1.91689C0.50605 1.83979 0.44476 1.74822 0.402942 1.64741C0.361124 1.54659 0.3396 1.43853 0.3396 1.32939C0.3396 1.22025 0.361124 1.11218 0.402942 1.01137C0.44476 0.910554 0.50605 0.818981 0.583303 0.741886C0.908303 0.425219 1.44164 0.416886 1.76664 0.741886Z" fill="#0C3E70"></path>
                            </svg>
                        </a>
                        <div class="choose_more_dropdown">
                            <div class="column">
                                <ul>
                                    <li>
                                        <h3>{{ t("COMPRESS &amp; CONVERT") }}</h3>
                                    </li>
                                    <li><a href="/pdf-to-png">{{ t("PDF to PNG") }}</a></li>
                                    <li><a href="/pdf-to-epub">{{ t("PDF to EPUB") }}</a></li>
                                    {{--<li><a href="">PDF to TEXT</a></li>--}}
                                </ul>
                                <ul>
                                    <li>
                                        <h3>{{ t("MERGE &amp; COMPRESS") }}</h3>
                                    </li>
                                    {{--<li><a href="">Alternate &amp; Mix</a></li>--}}
                                    <li><a href="/merge-pdf">{{ t("Merge") }}</a></li>
                                    {{--<li><a href="">Combine &amp; Reorder</a></li>--}}
                                </ul>
                            </div>
                            <div class="column">
                                <ul>
                                    <li>
                                        <h3>{{ t("EDIT &amp; SIGN") }}</h3>
                                    </li>
                                    {{--<li><a href="">Bates Numbering</a></li>--}}
                                    <li><a href="/crop-pdf">{{ t("Crop") }}</a> </li>
                                    <li><a href="/delete-pdf-pages">{{ t("Delete Pages") }}</a></li>
                                    {{--<li><a href="">Edit</a></li>--}}
                                    <li><a href="/fill-sign-pdf">{{ t("Fill &amp; Sign") }}</a></li>
                                    {{--<li><a href="">Grayscale</a></li>--}}
                                    {{--<li><a href="">Header &amp; Footer</a></li>--}}
                                    {{--<li><a href="">N-up</a></li>--}}
                                </ul>
                            </div>
                            <div class="column">
                                <ul>
                                    <li>
                                        <h3>{{ t("EDIT PAGES") }}</h3>
                                    </li>
                                    {{--<li><a href="">Extract Pages</a></li>--}}
                                    {{--<li><a href="">Split by bookmarks</a></li>--}}
                                    {{--<li><a href="">Split in half</a></li>--}}
                                    {{--<li><a href="">Split by size</a></li>--}}
                                    {{--<li><a href="">Split by text</a></li>--}}
                                    <li><a href="/split-pdf">{{ t("Split PDF by pages") }}</a></li>
                                </ul>
                            </div>
                            <div class="column">
                                <ul>
                                    <li>
                                        <h3>{{ t("OTHER") }}</h3>
                                    </li>
                                    <li><a href="/encrypt-pdf">{{ t("Protect") }}</a></li>
                                    <li><a href="/rotate-pdf-pages">{{ t("Rotate") }}</a></li>
                                    {{--<li><a href="">Repair</a></li>--}}
                                    <li><a href="/resize-pdf">{{ t("Resize") }}</a></li>
                                    {{--<li><a href="">Sign</a></li>--}}
                                    {{--<li><a href="">Unlock</a></li>--}}
                                    <li><a href="/watermark-pdf">{{ t("Watermark") }}</a></li>
                                    {{--<li><a href="">Translate</a></li>--}}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $h2_title_post = 'Our Blog';
        $sub_title_post = 'Non bibendum nisi aliquet non amet lobortis';
        $number_posts = 2;
        $bg = 'bg-grey';
        
        ?>
        @include('inc-freeconvert.our_blog')
        @include('inc-freeconvert.tools-pd')
        @include('inc-freeconvert.banner')
        @include('inc-freeconvert.accordion')
        @include('inc-freeconvert.testimonial')
    </main>
@endsection

@section('js')
    <script>
		localStorage.removeItem('uploadedFileUrl');
		localStorage.removeItem('uploadedFileName');
    </script>
    <script src="{{ asset('js/pdf-loader.js') }}"></script>
@endsection

