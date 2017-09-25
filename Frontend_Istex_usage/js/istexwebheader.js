(function() {
  'use strict';

  var CONTENT_DELIVERY_URL = 'https://content-delivery.' + 'istex' + '.fr' + '/web-header', // split istex.fr because of ezproxy automatic parsing (istex-view use case)
      LOCAL_DELIVERY_URL   = 'http://localhost:8080',
      JQUERY_CDN_URL       = 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js',
      ressourcesUrl        = CONTENT_DELIVERY_URL
  ;

  if (window.jQuery) {
    init(window.jQuery);
  } else {
    loadJqueryAndInvokeInit();
  }


  function init ($) {

    // Mode de debug local
    if ((window.localStorage && window.localStorage.getItem('web_header_local'))
        || (window.location.search && window.location.search.match(/web_header_local(=true)?(&|$)/))
        || window.location.hostname.match(/localhost|127\.0\.0\./)
    ) {
      ressourcesUrl = window.location.hostname.match(/localhost|127\.0\.0\.1/)
                      && window.location.origin
                      || LOCAL_DELIVERY_URL
      ;
      console.info('Istex web-header: local mode set on ' + ressourcesUrl);
    }

    var prependToTarget = $('script[data-header-target]').data('headerTarget') || 'body';

    $.ajax({
             url    : ressourcesUrl + '/public/css/main.min.css',
             success: function(data) {

               $('head').append('<style>' + data + '</style>');

               $.ajax({
                        url    : ressourcesUrl + '/public/views/header.view.html',
                        success: function(_data) {
                          prependToTarget =
                            window.location.hostname === 'article-type.lod.istex.fr'
                              ? '.navbar.navbar-inverse.navbar-fixed-top'
                              : prependToTarget
                          ;

                          var $webHeader =
                                $(jQuery.parseHTML(_data))
                                  .filter('#istex__web-header')
                                  .find('img').each(rebaseImgUrl).end()
                                  .prependTo($(prependToTarget))
                                  .wrap('<div class="sandbox"></div>')
                                  .find('[href*="#"]').click(preventDefaultEvent).end()
                                  .find('[href="' + window.location.href + '"]').addClass('disabled').click(
                                  preventDefaultEvent).end();
                          ;

                          window.location.hostname === 'www.istex.fr' && $webHeader.find('.logoistex').remove();
                        },
                        error  : function(jqXHR, textStatus, errorThrown) {
                          console.error(textStatus, errorThrown);
                        }

                      });
             }
           });

    function rebaseImgUrl () {
      $(this).attr('src', function(index, attr) {
        return attr.replace(/^(?!https?)(?:\/?([^/#"]+))+$/i, ressourcesUrl + '/public/img/$1');
      });
    }

  }


  function loadJqueryAndInvokeInit () {
    var script    = document.createElement('script');
    script.src    = JQUERY_CDN_URL;
    script.onload = function() {
      init(window.jQuery.noConflict());
      document.head.removeChild(script);
    };
    document.head.appendChild(script);

  }

  function preventDefaultEvent (e) {
    e.preventDefault();
  }

}());
