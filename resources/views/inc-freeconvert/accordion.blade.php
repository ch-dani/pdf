@if(isset($Faq))
    <section class="module__how-banner module__need-help module bg-white">
        <div class="container">
            <div class="title-wrapper">
                <h2 class="h2-title title_main">
                    {!! array_key_exists(6, $PageBlocks) ? $PageBlocks[6] : 'We help with your PDF tasks' !!}
                </h2>
                <h3 class="sub-title">
                    {!! array_key_exists(7, $PageBlocks) ? $PageBlocks[7] : 'We help with your PDF tasks' !!}
                </h3>

            </div>

            <div class="how-to-accordion">
                @foreach ($Faq as $faq)
                    @php
                        $titles = json_decode($faq->title, true);
                        $icons = json_decode($faq->icons, true);
                        $link = json_decode($faq->link, true);
                        $link_title = json_decode($faq->link_title, true);

                        $stepsTmp = json_decode($faq->steps, true);
                        if (isset($stepsTmp[$ActiveLanguage->id]))
                            $steps = $stepsTmp[$ActiveLanguage->id];
                        else
                            $steps = $stepsTmp[1];
                    @endphp
                    <div class="accordion-block">
                        <div class="accordion-tittle">
                            <div class="accordion-name">
                                {!! (isset($titles[$ActiveLanguage->id]) and !empty($titles[$ActiveLanguage->id])) ? $titles[$ActiveLanguage->id] : ( (isset($titles[1])) ? $titles[1] : '' ) !!}
                            </div>
                            <div class="accordion-arrow"><img src="{{ asset('freeconvert/img/arrow.svg') }}"
                                                              alt="arrow"></div>
                        </div>
                        <div class="accordion-text">
                            @foreach ($steps as $key => $step)
                                <p><span>{{ !is_null($step) ? $step : $stepsTmp[1][$key] }}</span></p>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            @php
                global $lang_id;
                $languageCode = \App\Language::find($lang_id)->code;
                if ($languageCode === 'en') $languageCode = '';
             @endphp

            <div class="contact-us">
                <a class="contact-us__button" href="{{ $languageCode }}/contact">{{t('Contact Support')}}</a>
            </div>
        </div>
    </section>
@endif
