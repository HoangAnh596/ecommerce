<footer class="footer">
    <div class="uk-container uk-container-center">
        <div class="footer-upper">
            <div class="uk-grid uk-grid-medium">
                <div class="uk-width-large-1-5">
                    <div class="footer-contact">
                        <a href="" class="image img-scaledown"><img src="https://themepanthers.com/wp/nest/d1/wp-content/uploads/2022/02/logo.png" alt=""></a>
                        <div class="footer-slogan">Awesome grocery store website template</div>
                        <div class="company-address">
                            <div class="address">{{ $system['contact_office'] }}</div>
                            <div class="phone">Hotline: {{ convert_number_phone($system['contact_phone']) }}</div>
                            <div class="email">Email: {{ $system['contact_email'] }}</div>
                            <div class="hour">Giờ làm việc: 10:00 - 18:00, Mon - Sat</div>
                        </div>
                    </div>
                </div>
                <div class="uk-width-large-3-5">
                    @if(isset($menus['footer-menu']))
                    <div class="footer-menu">
                        <div class="uk-grid uk-grid-medium">
                            @foreach($menus['footer-menu'] as $val)
                            <div class="uk-width-large-1-3">
                                <div class="ft-menu">
                                    <div class="heading">{{ $val['item']->languages->first()->pivot->name }}</div>
                                    <ul class="uk-list uk-clearfix">
                                        @if(isset($val['children']))
                                        @foreach($val['children'] as $children)
                                        @php
                                            $nameChildren = $children['item']->languages->first()->pivot->name;
                                            $canonical = write_url($children['item']->languages->first()->pivot->canonical);
                                        @endphp
                                        <li><a href="">{{ $nameChildren }}</a></li>
                                        @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                <div class="uk-width-large-1-5">
                    <div class="fanpage-facebook">
                        <div class="ft-menu">
                            <div class="heading">Fanpage Facebook</div>
                            <div class="fanpage">
                                <div class="fb-page" data-href="https://www.facebook.com/facebook" data-tabs="" data-width="" data-height="" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true">
                                    <blockquote cite="https://www.facebook.com/facebook" class="fb-xfbml-parse-ignore">
                                        <a href="https://www.facebook.com/facebook">Facebook</a>
                                    </blockquote>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright">
        <div class="uk-container uk-container-center">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <div class="copyright-text">{!! $system['homepage_copyright'] !!}</div>
                <div class="copyright-contact">
                    <div class="uk-flex uk-flex-middle">
                        <div class="phone-item">
                            <div class="p">Hotline: {{ convert_number_phone($system['contact_phone']) }}</div>
                            <div class="worktime">Làm việc: 8:00 - 22:00</div>
                        </div>
                        <div class="phone-item">
                            <div class="p">Support: {{ convert_number_phone($system['contact_technical_phone']) }}</div>
                            <div class="worktime">Hỗ trợ 24/7</div>
                        </div>
                    </div>
                </div>
                <div class="social">
                    <div class="uk-flex uk-flex-middle">
                        <div class="span">Follow us:</div>
                        <div class="social-list">
                            @php
                                $social = ['facebook', 'twitter', 'youtube'];
                            @endphp
                            <div class="uk-flex uk-flex-middle">
                                @foreach($social as $key => $val)
                                <a href="{{ $system['social_'.$val] }}" target="_blank"><i class="fa fa-{{ $val }}"></i></a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>