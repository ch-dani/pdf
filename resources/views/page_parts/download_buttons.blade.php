<div class="contact-us">
    <a id="start_task" class="contact-us__button btn-gradient" href="#">
		<img src="{{asset('/freeconvert/img/download.svg')}}" width="30" height="30"> {{t('Proccess PDF')}}
	</a>
</div>

<div class="link_convert one_item">
    <div class="link_convert_left">
        <a href="#" class="link_convert_item remove">
            @php include(public_path('freeconvert/img/link_conver-3.svg')) @endphp
            {{ t("Remove") }}
        </a>
    </div>
</div>

<ul class="save hidden" style="margin-top: 10px;">
    <li class="save__li"><a href="#"><img src="{{asset('/freeconvert/img/logo_google-drive.svg')}}" width="26" height="23">{{t('Save to Google Drive')}}</a></li>
    <li class="save__li"><a href="#"><img src="{{asset('/freeconvert/img/logo_dropbox.svg')}}" width="28" height="23">{{t('Save to Dropbox')}}</a></li>
</ul>
